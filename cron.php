<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

require_once( dirname( __FILE__ ) . "/lib/autoloader.php" );

use Core\Core;

define( 'IS_CRON_RUN', true );

$fitbitApp = new Core();

$end              = time() + 20;
$repopulate_queue = run_through_queue();

if ( $repopulate_queue ) {
    nxr( 0, "Ready to repopulate the queue" );

    $unfinishedUsers = $fitbitApp->getDatabase()->query( "-- noinspection SqlDialectInspection
        SELECT fuid, name from " . $fitbitApp->getSetting( "db_prefix", null, false ) . "users where
        UNIX_TIMESTAMP(str_to_date(lastrun,'%Y-%m-%d %H:%i:%s')) < UNIX_TIMESTAMP('" . date( "Y-m-d H:i:s",
            strtotime( '-1 day' ) ) . "') AND
        UNIX_TIMESTAMP(str_to_date(cooldown,'%Y-%m-%d %H:%i:%s')) < UNIX_TIMESTAMP('" . date( "Y-m-d H:i:s" ) . "')" )->fetchAll();
    $fitbitApp->getErrorRecording()->postDatabaseQuery( $fitbitApp->getDatabase(), [
        "METHOD" => __METHOD__,
        "LINE"   => __LINE__
    ] );

    if ( ! empty( $unfinishedUsers ) and count( $unfinishedUsers ) > 0 and $fitbitApp->getSetting( 'scope_all_cron',
            true )
    ) {
        foreach ( $unfinishedUsers as $user ) {
            if ( ! $fitbitApp->valdidateOAuth( $fitbitApp->getUserOAuthTokens( $user[ 'fuid' ], false ) ) ) {
                nxr( 0, $user[ 'name' ] . " has not completed the OAuth configuration" );
            } else {
                if ( time() < $end ) {
                    nxr( 0, "Adding all to Q for " . $user[ 'name' ] );
                    $fitbitApp->addCronJob( $user[ 'fuid' ], 'all' );
                }
            }
        }
    }

    $allowed_triggers = [];
    foreach ( $fitbitApp->supportedApi() as $key => $name ) {
        if ( $fitbitApp->getSetting( 'scope_' . $key, false ) && $fitbitApp->getSetting( 'scope_' . $key . '_cron',
                false ) && $key != "all"
        ) {
            $allowed_triggers[] = $key;
        }
    }

    if ( count( $allowed_triggers ) == 0 ) {
        nxr( 0, "I am not allowed to re-queue anything so will re-queue with empty records" );
    } else {
        $unfinishedUsers = $fitbitApp->getDatabase()->query( "-- noinspection SqlDialectInspection
            SELECT fuid, name from " . $fitbitApp->getSetting( "db_prefix", null,
                false ) . "users where UNIX_TIMESTAMP(str_to_date(cooldown,'%Y-%m-%d %H:%i:%s')) < UNIX_TIMESTAMP('" . date( "Y-m-d H:i:s" ) . "')" )->fetchAll();
        $fitbitApp->getErrorRecording()->postDatabaseQuery( $fitbitApp->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE"   => __LINE__
        ] );

        if ( ! empty( $unfinishedUsers ) and count( $unfinishedUsers ) > 0 ) {
            foreach ( $unfinishedUsers as $user ) {
                if ( ! $fitbitApp->valdidateOAuth( $fitbitApp->getUserOAuthTokens( $user[ 'fuid' ], false ) ) ) {
                    nxr( 0, $user[ 'name' ] . " has not completed the OAuth configuration" );
                } else {
                    nxr( 1, " Repopulating for " . $user[ 'name' ] );

                    $fitbitApp->getFitbitAPI( $user[ 'fuid' ] )->setActiveUser( $user[ 'fuid' ] );
                    foreach ( $allowed_triggers as $allowed_trigger ) {
                        if ( ! is_numeric( $fitbitApp->getFitbitAPI()->isAllowed( $allowed_trigger, true ) ) ) {
                            if ( $fitbitApp->getFitbitAPI( $user[ 'fuid' ] )->isTriggerCooled( $allowed_trigger ) ) {
                                nxr( 1, "  + $allowed_trigger added to queue" );
                                $fitbitApp->addCronJob( $user[ 'fuid' ], $allowed_trigger );
                            } else {
                                nxr( 1, "  - $allowed_trigger still too hot" );
                            }
                        }
                    }
                }
            }
        } else {
            nxr( 0, "There is nothing to queue" );
        }
    }

    if ( time() < $end ) {
        run_through_queue();
    }
}

/**
 * @return bool
 */
function run_through_queue() {
    global $fitbitApp, $end;
    $repopulate_queue = true;

    $queuedJobs = $fitbitApp->getCronJobs();
    if ( count( $queuedJobs ) > 0 ) {
        foreach ( $queuedJobs as $job ) {
            if ( time() < $end ) {
                if ( $fitbitApp->isUser( $job[ 'user' ] ) ) {
                    $cooldown = $fitbitApp->getUserCooldown( $job[ 'user' ] );
                    if ( $fitbitApp->getSetting( 'scope_' . $job[ 'trigger' ], true ) ) {
                        if ( strtotime( $cooldown ) < strtotime( date( "Y-m-d H:i:s" ) ) ) {
                            nxr( 0, "Processing queue item " . $fitbitApp->supportedApi( $job[ 'trigger' ] ) . " for " . $job[ 'user' ] );
                            $jobRun = $fitbitApp->getFitbitAPI( $job[ 'user' ], true )->pull( $job[ 'user' ],
                                $job[ 'trigger' ] );
                            if ( $fitbitApp->getFitbitAPI( $job[ 'user' ] )->isApiError( $jobRun ) ) {
                                $fitbitApp->getErrorRecording()->captureMessage( "Cron Error: " . $fitbitApp->lookupErrorCode( $jobRun ),
                                    [ 'api' ], [
                                        'extra' => [
                                            'api_req'      => $job[ 'trigger' ],
                                            'user'         => $job[ 'user' ],
                                            'php_version'  => phpversion(),
                                            'core_version' => $fitbitApp->getSetting( "version", "0.0.0.1", true )
                                        ]
                                    ] );
                                nxr( 0, "* Cron Error: " . $fitbitApp->lookupErrorCode( $jobRun ) );
                            } else {
                                $fitbitApp->delCronJob( $job[ 'user' ], $job[ 'trigger' ] );
                            }
                        } else {
                            $fitbitApp->getErrorRecording()->captureMessage( "API limit reached",
                                [ 'remote_api' ], [
                                    'level' => 'info',
                                    'extra' => [
                                        'api_req'      => $job[ 'trigger' ],
                                        'user'         => $job[ 'user' ],
                                        'cooldown'     => $cooldown,
                                        'php_version'  => phpversion(),
                                        'core_version' => $fitbitApp->getSetting( "version", "0.0.0.1", true )
                                    ]
                                ] );
                            nxr( 0, "Can not process " . $fitbitApp->supportedApi( $job[ 'trigger' ] ) . ". API limit reached for " . $job[ 'user' ] . ". Cooldown period ends " . $cooldown );
                            $fitbitApp->delCronJob( $job[ 'user' ], $job[ 'trigger' ] );
                        }
                    } else {
                        nxr( 1, $fitbitApp->supportedApi( $job[ 'trigger' ] ) . " has been disabled" );
                        $fitbitApp->delCronJob( $job[ 'user' ], $job[ 'trigger' ] );
                    }

                    if ( strtotime( $cooldown ) < strtotime( date( "Y-m-d H:i:s" ) ) ) {
                        $lastrun = strtotime( $fitbitApp->getDatabase()->get( $fitbitApp->getSetting( "db_prefix",
                                null, false ) . "users", "lastrun", [ "fuid" => $job[ 'user' ] ] ) );
                        if ( $lastrun < ( strtotime( 'now' ) - ( 60 * 60 * 24 ) ) ) {
                            $fitbitApp->addCronJob( $job[ 'user' ], 'all' );
                            $repopulate_queue = false;
                        }
                    }
                } else {
                    $fitbitApp->getErrorRecording()->captureMessage( "Unknown User", [ 'authentication' ], [
                        'level' => 'info',
                        'extra' => [
                            'api_req'      => $_GET[ 'get' ],
                            'user'         => $_GET[ 'user' ],
                            'php_version'  => phpversion(),
                            'core_version' => $fitbitApp->getSetting( "version", "0.0.0.1", true )
                        ]
                    ] );
                    nxr( 2, "Can not process " . $fitbitApp->supportedApi( $job[ 'trigger' ] ) . " since " . $job[ 'user' ] . " is no longer a user." );
                    $fitbitApp->delCronJob( $job[ 'user' ], $job[ 'trigger' ] );
                }

                nxr( 0, "Cron " . $fitbitApp->supportedApi( $job[ 'trigger' ] ) . " completed for " . $job[ 'user' ] );
            } else {
                nxr( 0, "Timeout reached skipping " . $fitbitApp->supportedApi( $job[ 'trigger' ] ) . " for " . $job[ 'user' ] );
                $repopulate_queue = false;
            }
        }
    }

    return $repopulate_queue;
}
