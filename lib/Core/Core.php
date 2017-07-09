<?php
/*
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

namespace Core;

require_once( dirname( __FILE__ ) . "/../autoloader.php" );
require_once( dirname( __FILE__ ) . "/../../config/config.def.dist.php" );

use Core\Analytics\ErrorRecording;
use Core\Babel\ApiBabel;
use Core\Deploy\Upgrade;
use DateTime;
use League\OAuth2\Client\Token\AccessToken as AccessToken;
use Medoo\Medoo;

date_default_timezone_set( 'Europe/London' );
error_reporting( E_ALL );

/**
 * Main app class
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-NxFitbit phpDocumentor
 *            wiki for Core.
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class Core {

    /**
     * @var Medoo
     */
    protected $database;
    /**
     * @var ApiBabel
     */
    protected $fitbitapi;
    /**
     * @var Config
     */
    protected $settings;
    /**
     * @var ErrorRecording
     */
    protected $errorRecording;

    /**
     * Create the core Core class, storing child classes as required
     */
    public function __construct() {
        $this->setSettings( new Config() );

        $this->setDatabase( new medoo( [
            'database_type' => 'mysql',
            'database_name' => $this->getSetting( "db_name" ),
            'server'        => $this->getSetting( "db_server" ),
            'username'      => $this->getSetting( "db_username" ),
            'password'      => $this->getSetting( "db_password" ),
            'charset'       => 'utf8'
        ] ) );

        $this->getSettings()->setDatabase( $this->getDatabase() );

        $installedVersion = $this->getSetting( "version", "0.0.0.1", true );
        if ( $installedVersion != APP_VERSION ) {
            $this->updateCore( $installedVersion );
        }

        $this->errorRecording = new ErrorRecording( $this );

    }

    /**
     * @param Config $settings
     */
    private function setSettings( $settings ) {
        $this->settings = $settings;
    }

    /**
     * @param Medoo $database
     */
    private function setDatabase( $database ) {
        $this->database = $database;
    }

    /**
     * @param string $installedVersion
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    private function updateCore( $installedVersion ) {
        nxr( 0, "Installed version $installedVersion and should be " . APP_VERSION );
        $dataReturnClass = new Upgrade( $this );

        echo "Upgrading from " . $dataReturnClass->getInstallVersion() . " to " . $dataReturnClass->getInstallingVersion() . ". ";
        echo $dataReturnClass->getNumUpdates() . " updates outstanding\n";

        if ( $dataReturnClass->getNumUpdates() > 0 ) {
            $dataReturnClass->runUpdates();
        }

        unset( $dataReturnClass );
        nxr( 0, "Update completed, please re-run the command" );
        die();
    }

    /**
     * Cron job / queue management
     */

    /**
     * Get settings from config class
     *
     * @param string $key        Settings key to return
     * @param null   $default    Default value, if nothing already held in settings
     * @param bool   $rawQueryBb Should the DB be checked
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return string
     */
    public function getSetting( $key, $default = null, $rawQueryBb = true ) {
        return $this->getSettings()->get( $key, $default, $rawQueryBb );
    }

    /**
     * @return Config
     */
    public function getSettings() {
        return $this->settings;
    }

    /**
     * @return Medoo
     */
    public function getDatabase() {
        return $this->database;
    }

    /**
     * Get settings from config class
     *
     * @param string $fuid
     * @param string $key
     * @param null   $default
     * @param bool   $rawQueryBb
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return string
     */
    public function getUserSetting( $fuid, $key, $default = null, $rawQueryBb = true ) {
        return $this->getSettings()->getUser( $fuid, $key, $default, $rawQueryBb );
    }

    /**
     * Get settings from config class
     *
     * @param string $fuid Fitbit user ID
     * @param string $key  Trigger ID to add to cron
     *
     * @return string
     * @internal param null $default
     * @internal param bool $rawQueryBb
     */
    public function delUserSetting( $fuid, $key ) {
        return $this->getSettings()->delUser( $fuid, $key );
    }

    /**
     * Users
     */

    /**
     * Add new cron jobs to queue
     *
     * @param string $userFUID Fitbit user ID
     * @param string $trigger  Trigger ID to add to cron
     * @param bool   $force    Should we honnor hot API
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCronJob( $userFUID, $trigger, $force = false ) {
        if ( $force || $this->getSetting( 'scope_' . $trigger . '_cron', false ) ) {
            if ( ! $this->getDatabase()->has( $this->getSetting( "db_prefix", null, false ) . "queue", [
                "AND" => [
                    "user"    => $userFUID,
                    "trigger" => $trigger
                ]
            ] )
            ) {
                $this->getDatabase()->insert( $this->getSetting( "db_prefix", null, false ) . "queue", [
                    "user"    => $userFUID,
                    "trigger" => $trigger,
                    "date"    => date( "Y-m-d H:i:s" )
                ] );
                $this->getErrorRecording()->postDatabaseQuery( $this->getDatabase(), [
                    "METHOD" => __METHOD__,
                    "LINE"   => __LINE__
                ] );
            } /*else {
                nxr(0, "Cron job already present");
            }*/
        } else {
            nxr( 0, "I am not allowed to queue $trigger" );
        }
    }

    /**
     * @return ErrorRecording
     */
    public function getErrorRecording() {
        return $this->errorRecording;
    }

    /**
     * Settings and configuration
     */

    /**
     * Delete cron jobs from queue
     *
     * @param string $userFUID Fitbit user ID
     * @param string $trigger  Trigger ID to add to cron
     */
    public function delCronJob( $userFUID, $trigger ) {
        if ( $this->getDatabase()->has( $this->getSetting( "db_prefix", null, false ) . "queue", [
            "AND" => [
                "user"    => $userFUID,
                "trigger" => $trigger
            ]
        ] )
        ) {
            if ( ! $this->getDatabase()->delete( $this->getSetting( "db_prefix", null, false ) . "queue", [
                "AND" => [
                    "user"    => $userFUID,
                    "trigger" => $trigger
                ]
            ] )
            ) {
                $this->getErrorRecording()->postDatabaseQuery( $this->getDatabase(), [
                    "METHOD" => __METHOD__,
                    "LINE"   => __LINE__
                ] );
                nxr( 0, "Failed to delete $trigger Cron job" );
            }
        } else {
            $this->getErrorRecording()->postDatabaseQuery( $this->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE"   => __LINE__
            ] );
            nxr( 0, "Failed to delete $trigger Cron job" );
        }
    }

    /**
     * Get list of pending cron jobs from database
     *
     * @return array|bool
     */
    public function getCronJobs() {
        return $this->getDatabase()->select( $this->getSetting( "db_prefix", null, false ) . "queue", "*",
            [ "ORDER" => [ "date" => "ASC" ] ] );
    }

    /**
     * @param string $userFitbitId
     * @param bool   $reset
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return ApiBabel
     */
    public function getFitbitAPI( $userFitbitId = "", $reset = false ) {
        if ( is_null( $this->fitbitapi ) || $reset ) {
            if ( $userFitbitId == $this->getSetting( "ownerFuid", null, false ) ) {
                $this->fitbitapi = new ApiBabel( $this, true );
            } else {
                $this->fitbitapi = new ApiBabel( $this, false );
            }
        }

        return $this->fitbitapi;
    }

    /**
     * @param ApiBabel $fitbitapi
     */
    public function setFitbitapi( $fitbitapi ) {
        $this->fitbitapi = $fitbitapi;
    }

    /**
     * Database functions
     */

    /**
     * @param string          $userFUID Fitbit user ID
     * @param string|DateTime $datetime DataTime the cooldown will end
     *
     * @return array|int
     */
    public function setUserCooldown( $userFUID, $datetime ) {
        if ( $this->isUser( $userFUID ) ) {
            if ( is_string( $datetime ) ) {
                $datetime = new DateTime ( $datetime );
            }

            return $this->getDatabase()->update( $this->getSetting( "db_prefix", null, false ) . "users", [
                'cooldown' => $datetime->format( "Y-m-d H:i:s" )
            ], [ "AND" => [ 'fuid' => $userFUID ] ] );
        } else {
            return 0;
        }
    }

    /**
     * @param string $userFUID Fitbit user ID
     *
     * @return bool
     */
    public function isUser( $userFUID ) {
        if ( $this->getDatabase()->has( $this->getSetting( "db_prefix", null, false ) . "users",
            [ "fuid" => $userFUID ] )
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $userFUID Fitbit user ID
     *
     * @return int|array
     */
    public function getUserCooldown( $userFUID ) {
        if ( $this->isUser( $userFUID ) ) {
            return $this->getDatabase()->get( $this->getSetting( "db_prefix", null, false ) . "users", "cooldown",
                [ "fuid" => $userFUID ] );
        } else {
            return 0;
        }
    }

    /**
     * @param string      $userFUID Fitbit user ID
     * @param AccessToken $accessToken
     */
    public function setUserOAuthTokens( $userFUID, $accessToken ) {
        $this->getDatabase()->update(
            $this->getSetting( "db_prefix", false ) . "users",
            [
                'tkn_access'  => $accessToken->getToken(),
                'tkn_refresh' => $accessToken->getRefreshToken(),
                'tkn_expires' => $accessToken->getExpires()
            ], [ "fuid" => $userFUID ] );
    }

    /**
     * @param string $userFUID Fitbit user ID
     */
    public function delUserOAuthTokens( $userFUID ) {
        $this->getDatabase()->update( $this->getSetting( "db_prefix", false ) . "users",
            [
                'tkn_access'  => '',
                'tkn_refresh' => '',
                'tkn_expires' => 0
            ], [ "fuid" => $userFUID ] );
    }

    /**
     * @param string $userFUID Fitbit user ID
     * @param string $userPassword
     *
     * @return bool
     */
    public function isUserValid( $userFUID, $userPassword ) {
        if ( strpos( $userFUID, '@' ) !== false ) {
            $userFUID = $this->isUserValidEml( $userFUID );
        }

        if ( $this->isUser( $userFUID ) ) {
            if ( $this->getDatabase()->has( $this->getSetting( "db_prefix", null, false ) . "users", [
                "AND" => [
                    "fuid"     => $userFUID,
                    "password" => $userPassword
                ]
            ] )
            ) {
                return $userFUID;
            } else if ( $this->getDatabase()->has( $this->getSetting( "db_prefix", null, false ) . "users", [
                "AND" => [
                    "fuid"     => $userFUID,
                    "password" => ''
                ]
            ] )
            ) {
                return -1;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $inputEmail Fitbit user ID
     *
     * @return bool
     */
    public function isUserValidEml( $inputEmail ) {
        if ( $this->getDatabase()->has( $this->getSetting( "db_prefix", null, false ) . "users",
            [ "eml" => $inputEmail ] )
        ) {
            $userFUID = $this->getDatabase()->get( $this->getSetting( "db_prefix", null, false ) . "users", "fuid",
                [ "eml" => $inputEmail ] );

            return $userFUID;
        } else {
            return $inputEmail;
        }
    }

    /**
     * @param string|int $errCode
     * @param null       $user
     *
     * @return string
     */
    public function lookupErrorCode( $errCode, $user = null ) {
        switch ( $errCode ) {
            case "-146":
                return "Disabled in user config.";
            case "-145":
                return "Disabled in system config.";
            case "-144":
                return "Username missmatch.";
            case "-143":
                return "API cool down in effect.";
            case "-142":
                return "Unable to create required directory.";
            case "-141":
                return "Fitbit API Error";
            case "429":
                if ( ! is_null( $user ) ) {
                    $hour = date( "H" ) + 1;
                    $this->getDatabase()->update( $this->getSetting( "db_prefix", null, false ) . "users", [
                        'cooldown' => date( "Y-m-d " . $hour . ":01:00" ),
                    ], [ 'fuid' => $user ] );
                }

                return "Either you hit the rate limiting quota for the client or for the viewer";
            default:
                return $errCode;
        }
    }

    /**
     * Set value in database/config class
     *
     * @param string $key
     * @param string $value
     * @param bool   $rawQueryBb
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return bool
     */
    public function setSetting( $key, $value, $rawQueryBb = true ) {
        return $this->getSettings()->set( $key, $value, $rawQueryBb );
    }

    /**
     * Get settings from config class
     *
     * @param string $fuid
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    public function setUserSetting( $fuid, $key, $value ) {
        return $this->getSettings()->setUser( $fuid, $key, $value );
    }

    /**
     * Helper function to check for supported API calls
     *
     * @param null $key
     *
     * @return array|null|string
     */
    public function supportedApi( $key = null ) {
        $supportedApis = [
            'all'                  => 'Everything',
            'floors'               => 'Floors Climed',
            'foods'                => 'Calorie Intake',
            'badges'               => 'Badges',
            'sleep'                => 'Sleep Records',
            'body'                 => 'Weight & Body Fat Records',
            'goals'                => 'Personal Goals',
            'water'                => 'Water Intake',
            'activities'           => 'Pedomitor & Activities',
            'leaderboard'          => 'Friends',
            'devices'              => 'Device Status',
            'caloriesOut'          => 'Calories Out',
            'goals_calories'       => 'Calorie Goals',
            'minutesVeryActive'    => 'Minutes Very Active',
            'minutesFairlyActive'  => 'Minutes Fairly Active',
            'minutesLightlyActive' => 'Minutes Lightly Active',
            'minutesSedentary'     => 'Minutes Sedentary',
            'elevation'            => 'Elevation',
            'distance'             => 'Distance Traveled',
            'steps'                => 'Steps Taken',
            'profile'              => 'User Profile',
            'heart'                => 'Heart Rates',
            'activity_log'         => 'Activities',
            'nomie_trackers'       => "Nomie Trackers",
            'habitica'             => "Habitica"
        ];
        ksort( $supportedApis );

        if ( is_null( $key ) ) {
            return $supportedApis;
        } else {
            if ( array_key_exists( $key, $supportedApis ) ) {
                return $supportedApis[ $key ];
            } else {
                return $key;
            }
        }
    }

    /**
     * @param string $userFUID
     *
     * @return bool
     */
    public function isUserOAuthAuthorised( $userFUID ) {
        $session = new SessionObject();
        if ( $session->getVar( "userIsOAuth_" . $userFUID, FILTER_VALIDATE_BOOLEAN ) && $session->getVar( "userIsOAuth_" . $userFUID, FILTER_VALIDATE_BOOLEAN ) !== false ) {
            return $session->getVar( "userIsOAuth_" . $userFUID, FILTER_VALIDATE_BOOLEAN );
        } else {
            if ( $this->valdidateOAuth( $this->getUserOAuthTokens( $userFUID, false ) ) ) {
                $session->setVar( 'userIsOAuth_' . $userFUID, true );

                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @param array|bool $userArray
     *
     * @return bool
     */
    public function valdidateOAuth( $userArray ) {
        if ( $userArray[ 'tkn_access' ] == "" || $userArray[ 'tkn_refresh' ] == "" || $userArray[ 'tkn_expires' ] == "" ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string $userFUID Fitbit user ID
     * @param bool   $validate
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return array|bool
     */
    public function getUserOAuthTokens( $userFUID, $validate = true ) {
        $userArray = $this->getDatabase()->get( $this->getSetting( "db_prefix", null, false ) . "users", [
            'tkn_access',
            'tkn_refresh',
            'tkn_expires'
        ], [ "fuid" => $userFUID ] );
        if ( is_array( $userArray ) ) {
            if ( $validate && $this->valdidateOAuth( $userArray ) ) {
                return $userArray;
            } else if ( ! $validate ) {
                return $userArray;
            }
        }

        return false;
    }

}
