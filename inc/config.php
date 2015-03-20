<?php

    /**
     * Class config
     * @version 0.0.1
     * @author Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link http://nxfifteen.me.uk NxFIFTEEN
     * @copyright 2015 Stuart McCulloch Anderson
     * @license http://stuart.nx15.at/mit/2015 MIT
     */
    class config {
        /**
         * @var medoo
         */
        private $database;
        /**
         * @var array
         */
        private $settings = array();

        /**
         *
         */
        public function __construct() {
            require_once(dirname(__FILE__) . "/../config.inc.php");
            if (isset($config)) {
                $this->settings = $config;
            } else {
                $this->settings = array();
            }
        }

        /**
         * @param $activityName
         * @return array
         */
        public function getRelatedCacheNames($activityName) {
            $cacheNames = array();
            switch ($activityName) {
                case "activities":
                    $cacheNames = array('activity', 'dashboard', 'weekpedometer', 'aboutme', 'keypoints', 'steps', 'tracked');
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
                    $cacheNames = array();
                    break;
                case "devices":
                    $cacheNames = array('devices');
                    break;
                case "distance":
                    $cacheNames = array('dashboard', 'weekpedometer', 'aboutme', 'keypoints', 'steps', 'tracked');
                    break;
                case "elevation":
                    $cacheNames = array();
                    break;
                case "floors":
                    $cacheNames = array('dashboard', 'weekpedometer', 'aboutme', 'keypoints', 'steps', 'tracked');
                    break;
                case "foods":
                    $cacheNames = array('food', 'fooddiary');
                    break;
                case "goals":
                    $cacheNames = array('dashboard', 'tracked', 'steps');
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
                    $cacheNames = array('activity');
                    break;
                case "minutesLightlyActive":
                    $cacheNames = array('activity');
                    break;
                case "minutesSedentary":
                    $cacheNames = array('activity');
                    break;
                case "minutesVeryActive":
                    $cacheNames = array('activity');
                    break;
                case "profile":
                    $cacheNames = array('trend');
                    break;
                case "sleep":
                    $cacheNames = array('sleep');
                    break;
                case "steps":
                    $cacheNames = array('dashboard', 'weekpedometer', 'aboutme', 'keypoints', 'steps', 'tracked');
                    break;
                case "water":
                    $cacheNames = array('water');
                    break;
                default:
                    nxr("Unknown cache file for $activityName");
                    break;
            }

            return $cacheNames;
        }

        /**
         * @param string $key
         * @param string $value
         * @return bool
         */
        public function set($key, $value) {
            if ($this->database->has($this->get("db_prefix", FALSE) . "settings", array("var" => $key))) {
                return $this->database->update($this->get("db_prefix", FALSE) . "settings", array("data" => $value), array("var" => $key));
            } else {
                return $this->database->insert($this->get("db_prefix", FALSE) . "settings", array("data" => $value, "var" => $key));
            }
        }

        /**
         * @param string $key
         * @param string $default
         * @param bool $query_db
         * @return string
         */
        public function get($key, $default = NULL, $query_db = TRUE) {
            if (array_key_exists($key, $this->settings)) {
                return $this->settings[$key];
            } else if ($query_db && $this->database->has($this->get("db_prefix", NULL, FALSE) . "settings", array("var" => $key))) {
                return $this->database->get($this->get("db_prefix", NULL, FALSE) . "settings", "data", array("var" => $key));
            } else {
                if (!is_null($default)) {
                    $this->set($key, $default);
                }

                return $default;
            }
        }

        /**
         * @param medoo $database
         */
        public function setDatabase($database) {
            $this->database = $database;
        }
    }