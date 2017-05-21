<?php
    /*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

    namespace Core;

    require_once(dirname(__FILE__) . "/../autoloader.php");

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
     */
    class Config
    {

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
        private $settings = array();

        /**
         * Class constructor
         *
         * @codeCoverageIgnore
         */
        public function __construct()
        {
            if (isset($_SESSION) && is_array($_SESSION) && array_key_exists("core_config",
                    $_SESSION) && count($_SESSION['core_config']) > 0
            ) {
                $this->settings = $_SESSION['core_config'];
            } else {
                require_once(dirname(__FILE__) . "/../../config.dist.php");
                if (isset($config)) {
                    $_SESSION['core_config'] = $config;
                    $this->settings          = $_SESSION['core_config'];
                }
            }
        }

        /**
         * Return all cache files by activity name
         * Takes an activity name and returns an array of all cache files generated when activity is queried
         *
         * @param string $activityName Activity key name
         *
         * @return array String array of cache files names
         */
        public function getRelatedCacheNames($activityName)
        {
            $cacheNames = array();
            switch ($activityName) {
                case "activities":
                    $cacheNames = array(
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
                    );
                    break;
                case "activity_log":
                    $cacheNames = array('activityhistory');
                    break;
                case "badges":
                    $cacheNames = array('topbadges');
                    break;
                case "body":
                    $cacheNames = array('trend', 'weight');
                    break;
                case "caloriesOut":
                    $cacheNames = array('tasker');
                    break;
                case "devices":
                    $cacheNames = array('devices', 'tasker');
                    break;
                case "distance":
                    $cacheNames = array(
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
                    );
                    break;
                case "elevation":
                    $cacheNames = array();
                    break;
                case "floors":
                    $cacheNames = array(
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
                    );
                    break;
                case "foods":
                    $cacheNames = array('food', 'fooddiary');
                    break;
                case "goals":
                    $cacheNames = array('dashboard', 'tracked', 'steps', 'tasker');
                    break;
                case "goals_calories":
                    $cacheNames = array('trend');
                    break;
                case "heart":
                    $cacheNames = array();
                    break;
                case "leaderboard":
                    $cacheNames = array('trend');
                    break;
                case "minutesFairlyActive":
                    $cacheNames = array('activity', 'tasker', 'challenger', 'push', 'conky');
                    break;
                case "minutesLightlyActive":
                    $cacheNames = array('activity');
                    break;
                case "minutesSedentary":
                    $cacheNames = array('activity');
                    break;
                case "minutesVeryActive":
                    $cacheNames = array('activity', 'tasker', 'challenger', 'push', 'conky');
                    break;
                case "nomie_trackers":
                    $cacheNames = array('nomie');
                    break;
                case "profile":
                    $cacheNames = array('trend');
                    break;
                case "sleep":
                    $cacheNames = array('sleep');
                    break;
                case "steps":
                    $cacheNames = array(
                        'dashboard',
                        'weekpedometer',
                        'aboutme',
                        'keypoints',
                        'steps',
                        'tracked',
                        'tasker',
                        'conky'
                    );
                    break;
                case "water":
                    $cacheNames = array('water', 'tasker');
                    break;
                default:
                    nxr(0, "Unknown cache file for $activityName");
                    break;
            }

            return $cacheNames;
        }

        /**
         * Return setting value
         * Main function called to query settings for value. Default value can be provided, if not NULL is returned.
         * Values can be queried in the database or limited to config file and 'live' values
         *
         * @param string $key      Setting to query
         * @param string $default  Default value to return
         * @param bool   $query_db Boolean to search database or not
         *
         * @return string Setting value, or default as per defined
         */
        public function get($key, $default = null, $query_db = true)
        {
            if (is_array($this->settings) && array_key_exists($key, $this->settings)) {
                return $this->settings[$key];
            } else if ($query_db && $this->database->has($this->get("db_prefix", null, false) . "settings",
                    array("var" => $key))
            ) {
                $this->settings[$key] = $this->database->get($this->get("db_prefix", null, false) . "settings", "data",
                    array("var" => $key));

                return $this->settings[$key];
            } else {
                if ($query_db && !is_null($default)) {
                    $this->set($key, $default);
                }

                return $default;
            }
        }

        /**
         * Set setting value
         * Function to store/change setting values. Values can be stored in the database or held in memory.
         *
         * @param string $key      Setting to query
         * @param string $value    Value to store
         * @param bool   $query_db Boolean to store in database or not
         *
         * @return bool was data stored correctly
         */
        public function set($key, $value, $query_db = true)
        {
            $this->settings[$key] = $value;
            if ($query_db) {
                if ($this->database->has($this->get("db_prefix", false) . "settings", array("var" => $key))) {
                    $dbAction = $this->database->update($this->get("db_prefix", false) . "settings", array("data" => $value),
                        array("var" => $key));
                    if ($this->wasMySQLError($dbAction->errorInfo())) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    $dbAction = $this->database->insert($this->get("db_prefix", false) . "settings", array(
                        "data" => $value,
                        "var"  => $key
                    ));
                    if ($this->wasMySQLError($dbAction->errorInfo())) {
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
         * Delete setting value
         * Function to store/change setting values. Values can be stored in the database or held in memory.
         *
         * @param string $key      Setting to query
         * @param bool   $query_db Boolean to store in database or not
         *
         * @return bool was data stored correctly
         */
        public function del($key, $query_db = true)
        {
            if (array_key_exists($key, $this->settings)) {
                unset($this->settings[$key]);
            }

            if ($query_db) {
                if ($this->database->has($this->get("db_prefix", false) . "settings", array("var" => $key))) {
                    $dbAction = $this->database->delete($this->get("db_prefix", false) . "settings", array("var" => $key));
                    if ($this->wasMySQLError($dbAction->errorInfo())) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }

            return true;
        }

        /**
         * Return user setting value
         * Queries user settings for value. Default value can be provided, if not NULL is returned.
         * Values can be queried in the database or limited to config file and 'live' values
         *
         * @param string $fuid     User fuid
         * @param string $key      Setting to query
         * @param string $default  Default value to return
         * @param bool   $query_db Boolean to search database or not
         *
         * @return string Setting value, or default as per defined
         */
        public function getUser($fuid, $key, $default = null, $query_db = true)
        {
            if (array_key_exists($key . "_" . $fuid, $this->settings)) {
                return $this->settings[$key . "_" . $fuid];
            } else if ($query_db && $this->database->has($this->get("db_prefix", null, false) . "settings_users", array(
                    "AND" => array(
                        "fuid" => $fuid,
                        "var"  => $key
                    )
                ))
            ) {
                $this->settings[$key . "_" . $fuid] = $this->database->get($this->get("db_prefix", null,
                        false) . "settings_users", "data", array(
                    "AND" => array(
                        "fuid" => $fuid,
                        "var"  => $key
                    )
                ));

                return $this->settings[$key . "_" . $fuid];
            } else {
                if (!is_null($default)) {
                    $this->setUser($fuid, $key, $default);
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
        public function setUser($fuid, $key, $value)
        {
            $this->settings[$key . "_" . $fuid] = $value;
            if ($this->database->has($this->get("db_prefix", false) . "settings_users", array(
                "AND" => array(
                    "fuid" => $fuid,
                    "var"  => $key
                )
            ))
            ) {
                $dbAction = $this->database->update($this->get("db_prefix", false) . "settings_users",
                    array("data" => $value), array(
                        "AND" => array(
                            "fuid" => $fuid,
                            "var"  => $key
                        )
                    ));
                if ($this->wasMySQLError($dbAction->errorInfo())) {
                    return false;
                } else {
                    return true;
                }
            } else {
                $dbAction = $this->database->insert($this->get("db_prefix", false) . "settings_users", array(
                    "fuid" => $fuid,
                    "data" => $value,
                    "var"  => $key
                ));
                if ($this->wasMySQLError($dbAction->errorInfo())) {
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
         * @param string $fuid  User fuid
         * @param string $key   Setting to query
         * @param string $value Value to store
         *
         * @return bool was data stored correctly
         */
        public function delUser($fuid, $key)
        {
            if (array_key_exists($key . "_" . $fuid, $this->settings)) {
                unset($this->settings[$key . "_" . $fuid]);
            }

            if ($this->database->has($this->get("db_prefix", false) . "settings_users", array(
                "AND" => array(
                    "fuid" => $fuid,
                    "var"  => $key
                )
            ))
            ) {
                $dbAction = $this->database->delete($this->get("db_prefix", false) . "settings_users",
                    array("fuid" => $fuid, "var" => $key));
                if ($this->wasMySQLError($dbAction->errorInfo())) {
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
        public function setDatabase($database)
        {
            $this->database = $database;
        }

        /**
         * @param array $error
         *
         * @return bool
         */
        private function wasMySQLError($error)
        {
            if (is_null($error[2])) {
                return false;
            } else {
                print_r($error);

                return true;
            }
        }
    }
