<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 * Copyright (c) 2017. Stuart McCulloch Anderson
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

use Medoo\Medoo;

/**
 * Config helper class. Reads configuration details from config.inc.php file and the MySQL database
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-config phpDocumentor wiki
 *            for Config.
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class Config {

    /**
     * Medoo class holding database connection
     *
     * @var Medoo
     */
    private $database;

    /**
     * Array holding application settings
     *
     * @var array
     */
    private $settings = [];

    /**
     * Class constructor
     *
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct() {
        $sessionCoreConfig = filter_input( INPUT_SERVER, 'core_config', FILTER_UNSAFE_RAW );

        if ( $sessionCoreConfig && count( $sessionCoreConfig ) > 0 ) {
            $this->settings = $sessionCoreConfig;
        } else {
            require_once( dirname( __FILE__ ) . "/../../config/config.dist.php" );
            if ( isset( $config ) ) {
                $_SESSION[ 'core_config' ] = $config;
                $this->settings            = $_SESSION[ 'core_config' ];
            }
        }
    }

    /**
     * @param array $error
     *
     * @return bool
     */
    private function wasMySQLError( $error ) {
        if ( is_null( $error[ 2 ] ) ) {
            return false;
        } else {
            print_r( $error );

            return true;
        }
    }

    /**
     * Return all cache files by activity name
     * Takes an activity name and returns an array of all cache files generated when activity is queried
     *
     * @param string $activityName Activity key name
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     *
     * @return array String array of cache files names
     */
    public function getRelatedCacheNames( $activityName ) {
        $cacheNames = [];
        switch ( $activityName ) {
            case "activities":
                $cacheNames = [
                    'activity',
                    'dashboard',
                    'weekpedometer',
                    'aboutme',
                    'keypoints',
                    'steps',
                    'tracked',
                    'tracked',
                    'tasker',
                    'challenger',
                    'push',
                    'conky'
                ];
                break;
            case "activity_log":
                $cacheNames = [ 'activityhistory' ];
                break;
            case "badges":
                $cacheNames = [ 'topbadges' ];
                break;
            case "body":
                $cacheNames = [ 'trend', 'weight' ];
                break;
            case "caloriesOut":
                $cacheNames = [ 'tasker' ];
                break;
            case "devices":
                $cacheNames = [ 'devices', 'tasker' ];
                break;
            case "distance":
                $cacheNames = [
                    'dashboard',
                    'weekpedometer',
                    'aboutme',
                    'keypoints',
                    'steps',
                    'tracked',
                    'tasker',
                    'challenger',
                    'push',
                    'conky'
                ];
                break;
            case "elevation":
                $cacheNames = [];
                break;
            case "floors":
                $cacheNames = [
                    'dashboard',
                    'weekpedometer',
                    'aboutme',
                    'keypoints',
                    'steps',
                    'tracked',
                    'tasker',
                    'challenger',
                    'push',
                    'conky'
                ];
                break;
            case "foods":
                $cacheNames = [ 'food', 'fooddiary' ];
                break;
            case "goals":
                $cacheNames = [ 'dashboard', 'tracked', 'steps', 'tasker' ];
                break;
            case "goals_calories":
                $cacheNames = [ 'trend' ];
                break;
            case "heart":
                $cacheNames = [];
                break;
            case "leaderboard":
                $cacheNames = [ 'trend' ];
                break;
            case "minutesFairlyActive":
                $cacheNames = [ 'activity', 'tasker', 'challenger', 'push', 'conky' ];
                break;
            case "minutesLightlyActive":
                $cacheNames = [ 'activity' ];
                break;
            case "minutesSedentary":
                $cacheNames = [ 'activity' ];
                break;
            case "minutesVeryActive":
                $cacheNames = [ 'activity', 'tasker', 'challenger', 'push', 'conky' ];
                break;
            case "nomie_trackers":
                $cacheNames = [ 'nomie' ];
                break;
            case "profile":
                $cacheNames = [ 'trend' ];
                break;
            case "sleep":
                $cacheNames = [ 'sleep' ];
                break;
            case "steps":
                $cacheNames = [
                    'dashboard',
                    'weekpedometer',
                    'aboutme',
                    'keypoints',
                    'steps',
                    'tracked',
                    'tasker',
                    'conky'
                ];
                break;
            case "water":
                $cacheNames = [ 'water', 'tasker' ];
                break;
            default:
                nxr( 0, "Unknown cache file for $activityName" );
                break;
        }

        return $cacheNames;
    }

    /**
     * Delete setting value
     * Function to store/change setting values. Values can be stored in the database or held in memory.
     *
     * @param string $key        Setting to query
     * @param bool   $rawQueryBb Boolean to store in database or not
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return bool was data stored correctly
     */
    public function del( $key, $rawQueryBb = true ) {
        if ( array_key_exists( $key, $this->settings ) ) {
            unset( $this->settings[ $key ] );
        }

        if ( $rawQueryBb ) {
            if ( $this->database->has( $this->get( "db_prefix", false ) . "settings", [ "var" => $key ] ) ) {
                $dbAction = $this->database->delete( $this->get( "db_prefix", false ) . "settings", [ "var" => $key ] );
                if ( $this->wasMySQLError( $dbAction->errorInfo() ) ) {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return true;
    }

    /**
     * Return setting value
     * Main function called to query settings for value. Default value can be provided, if not NULL is returned.
     * Values can be queried in the database or limited to config file and 'live' values
     *
     * @param string $key        Setting to query
     * @param string $default    Default value to return
     * @param bool   $rawQueryBb Boolean to search database or not
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return string Setting value, or default as per defined
     */
    public function get( $key, $default = null, $rawQueryBb = true ) {
        if ( is_array( $this->settings ) && array_key_exists( $key, $this->settings ) ) {
            return $this->settings[ $key ];
        } else if ( $rawQueryBb && $this->database->has( $this->get( "db_prefix", null, false ) . "settings",
                [ "var" => $key ] )
        ) {
            $this->settings[ $key ] = $this->database->get( $this->get( "db_prefix", null, false ) . "settings", "data",
                [ "var" => $key ] );

            return $this->settings[ $key ];
        } else {
            if ( $rawQueryBb && ! is_null( $default ) ) {
                $this->set( $key, $default );
            }

            return $default;
        }
    }

    /**
     * Set setting value
     * Function to store/change setting values. Values can be stored in the database or held in memory.
     *
     * @param string $key        Setting to query
     * @param string $value      Value to store
     * @param bool   $rawQueryBb Boolean to store in database or not
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return bool was data stored correctly
     */
    public function set( $key, $value, $rawQueryBb = true ) {
        $this->settings[ $key ] = $value;
        if ( $rawQueryBb ) {
            if ( $this->database->has( $this->get( "db_prefix", false ) . "settings", [ "var" => $key ] ) ) {
                $dbAction = $this->database->update( $this->get( "db_prefix", false ) . "settings", [ "data" => $value ],
                    [ "var" => $key ] );
                if ( $this->wasMySQLError( $dbAction->errorInfo() ) ) {
                    return false;
                } else {
                    return true;
                }
            } else {
                $dbAction = $this->database->insert( $this->get( "db_prefix", false ) . "settings", [
                    "data" => $value,
                    "var"  => $key
                ] );
                if ( $this->wasMySQLError( $dbAction->errorInfo() ) ) {
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            return true;
        }
    }

    /**
     * Return user setting value
     * Queries user settings for value. Default value can be provided, if not NULL is returned.
     * Values can be queried in the database or limited to config file and 'live' values
     *
     * @param string $fuid       User fuid
     * @param string $key        Setting to query
     * @param string $default    Default value to return
     * @param bool   $rawQueryBb Boolean to search database or not
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return string Setting value, or default as per defined
     */
    public function getUser( $fuid, $key, $default = null, $rawQueryBb = true ) {
        if ( array_key_exists( $key . "_" . $fuid, $this->settings ) ) {
            return $this->settings[ $key . "_" . $fuid ];
        } else if ( $rawQueryBb && $this->database->has( $this->get( "db_prefix", null, false ) . "settings_users", [
                "AND" => [
                    "fuid" => $fuid,
                    "var"  => $key
                ]
            ] )
        ) {
            $this->settings[ $key . "_" . $fuid ] = $this->database->get( $this->get( "db_prefix", null,
                    false ) . "settings_users", "data", [
                "AND" => [
                    "fuid" => $fuid,
                    "var"  => $key
                ]
            ] );

            return $this->settings[ $key . "_" . $fuid ];
        } else {
            if ( ! is_null( $default ) ) {
                $this->setUser( $fuid, $key, $default );
            }

            return $default;
        }
    }

    /**
     * Set user setting value
     * Function to store/change setting values. Values are stored in the database.
     *
     * @param string $fuid  User fuid
     * @param string $key   Setting to query
     * @param string $value Value to store
     *
     * @return bool was data stored correctly
     */
    public function setUser( $fuid, $key, $value ) {
        $this->settings[ $key . "_" . $fuid ] = $value;
        if ( $this->database->has( $this->get( "db_prefix", false ) . "settings_users", [
            "AND" => [
                "fuid" => $fuid,
                "var"  => $key
            ]
        ] )
        ) {
            $dbAction = $this->database->update( $this->get( "db_prefix", false ) . "settings_users",
                [ "data" => $value ], [
                    "AND" => [
                        "fuid" => $fuid,
                        "var"  => $key
                    ]
                ] );
            if ( $this->wasMySQLError( $dbAction->errorInfo() ) ) {
                return false;
            } else {
                return true;
            }
        } else {
            $dbAction = $this->database->insert( $this->get( "db_prefix", false ) . "settings_users", [
                "fuid" => $fuid,
                "data" => $value,
                "var"  => $key
            ] );
            if ( $this->wasMySQLError( $dbAction->errorInfo() ) ) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Set user setting value
     * Function to store/change setting values. Values are stored in the database.
     *
     * @param string $fuid User fuid
     * @param string $key  Setting to query
     *
     * @return bool was data stored correctly
     */
    public function delUser( $fuid, $key ) {
        if ( array_key_exists( $key . "_" . $fuid, $this->settings ) ) {
            unset( $this->settings[ $key . "_" . $fuid ] );
        }

        if ( $this->database->has( $this->get( "db_prefix", false ) . "settings_users", [
            "AND" => [
                "fuid" => $fuid,
                "var"  => $key
            ]
        ] )
        ) {
            $dbAction = $this->database->delete( $this->get( "db_prefix", false ) . "settings_users",
                [ "fuid" => $fuid, "var" => $key ] );
            if ( $this->wasMySQLError( $dbAction->errorInfo() ) ) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * Set class database store
     * Takes medoo paramater and stores for access within the class
     *
     * @param Medoo $database Application database connection
     */
    public function setDatabase( $database ) {
        $this->database = $database;
    }
}
