<?php

    date_default_timezone_set('Europe/London');

    /**
     * @param $msg
     */
    function nxr($msg) {
        echo $msg . "\n";
    }

    /**
     * NxFitbit
     * @version 0.0.1
     * @author Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link http://nxfifteen.me.uk NxFIFTEEN
     * @copyright 2015 Stuart McCulloch Anderson
     * @license http://stuart.nx15.at/mit/2015 MIT
     */
    class NxFitbit {
        /**
         * @var medoo
         */
        protected $database;
        /**
         * @var fitbit
         */
        protected $fitbitapi;
        /**
         * @var config
         */
        protected $settings;

        /**
         *
         */
        public function __construct() {
            require_once(dirname(__FILE__) . "/config.php");
            $this->setSettings(new config());

            require_once(dirname(__FILE__) . "/../library/medoo.php");
            $this->setDatabase(new medoo(array(
                'database_type' => 'mysql',
                'database_name' => $this->getSetting("db_name"),
                'server'        => $this->getSetting("db_server"),
                'username'      => $this->getSetting("db_username"),
                'password'      => $this->getSetting("db_password"),
                'charset'       => 'utf8'
            )));

            $this->getSettings()->setDatabase($this->getDatabase());
        }

        /**
         * @param config $settings
         */
        private function setSettings($settings) {
            $this->settings = $settings;
        }

        /**
         * Cron job / queue management
         */

        /**
         * @param medoo $database
         */
        private function setDatabase($database) {
            $this->database = $database;
        }

        /**
         * Get settings from config class
         * @param $key
         * @param null $default
         * @param bool $query_db
         * @return string
         */
        public function getSetting($key, $default = NULL, $query_db = TRUE) {
            return $this->getSettings()->get($key, $default, $query_db);
        }

        /**
         * @return config
         */
        public function getSettings() {
            return $this->settings;
        }

        /**
         * Users
         */

        /**
         * @return medoo
         */
        public function getDatabase() {
            return $this->database;
        }

        /**
         * Add new cron jobs to queue
         * @param string $user_fitbit_id
         * @param string $trigger
         * @param bool $force
         */
        public function addCronJob($user_fitbit_id, $trigger, $force = FALSE) {
            if ($force || $this->getSetting('nx_fitbit_ds_' . $trigger . '_cron', FALSE)) {
                if (!$this->getDatabase()->has($this->getSetting("db_prefix", NULL, FALSE) . "queue", array(
                    "AND" => array(
                        "user"    => $user_fitbit_id,
                        "trigger" => $trigger
                    )
                ))
                ) {
                    $this->getDatabase()->insert($this->getSetting("db_prefix", NULL, FALSE) . "queue", array(
                        "user"    => $user_fitbit_id,
                        "trigger" => $trigger,
                        "date"    => date("Y-m-d H:i:s")
                    ));
                } else {
                    nxr("Cron job already present");
                }
            } else {
                nxr("I am not allowed to queue $trigger");
            }
        }

        /**
         * Settings and configuration
         */

        /**
         * Delete cron jobs from queue
         * @param $user_fitbit_id
         * @param $trigger
         */
        public function delCronJob($user_fitbit_id, $trigger) {
            if ($this->getDatabase()->has($this->getSetting("db_prefix", NULL, FALSE) . "queue", array("AND" => array(
                "user"    => $user_fitbit_id,
                "trigger" => $trigger
            )))
            ) {
                if ($this->getDatabase()->delete($this->getSetting("db_prefix", NULL, FALSE) . "queue", array(
                    "AND" => array(
                        "user"    => $user_fitbit_id,
                        "trigger" => $trigger
                    )
                ))
                ) {
                    nxr("Cron job $trigger deleted");
                } else {
                    nxr("Failed to delete $trigger Cron job");
                }
            } else {
                nxr("Failed to delete $trigger Cron job");
            }
        }

        /**
         * Get list of pending cron jobs from database
         * @return array|bool
         */
        public function getCronJobs() {
            return $this->getDatabase()->select($this->getSetting("db_prefix", NULL, FALSE) . "queue", "*", array("ORDER" => "date ASC"));
        }

        /**
         * @return fitbit
         */
        public function getFitbitapi() {
            if (is_null($this->fitbitapi)) {
                require_once(dirname(__FILE__) . "/fitbit.php");
                $this->fitbitapi = new fitbit($this,
                    $this->getSetting("fitbit_consumer_key", NULL, FALSE),
                    $this->getSetting("fitbit_consumer_secret", NULL, FALSE),
                    $this->getSetting("fitbit_debug", FALSE, FALSE),
                    $this->getSetting("fitbit_user_agent", NULL, FALSE));
            }

            return $this->fitbitapi;
        }

        /**
         * @param fitbit $fitbitapi
         */
        public function setFitbitapi($fitbitapi) {
            $this->fitbitapi = $fitbitapi;
        }

        /**
         * Database functions
         */

        /**
         * @param $user_fitbit_id
         * @return int|array
         */
        public function getUserCooldown($user_fitbit_id) {
            if ($this->isUser($user_fitbit_id)) {
                return $this->getDatabase()->get($this->getSetting("db_prefix", NULL, FALSE) . "users", "cooldown", array("fuid" => $user_fitbit_id));
            } else {
                return 0;
            }
        }

        /**
         * @param string $user_fitbit_id
         * @return bool
         */
        public function isUser($user_fitbit_id) {
            if ($this->getDatabase()->has($this->getSetting("db_prefix", NULL, FALSE) . "users", array("fuid" => $user_fitbit_id))) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        /**
         * @param string $user_fitbit_id
         * @return bool
         */
        public function isUserValid($user_fitbit_id, $user_fitbit_password) {
            if ($this->isUser($user_fitbit_id)) {
                if ($this->getDatabase()->has($this->getSetting("db_prefix", NULL, FALSE) . "users", array("AND" => array("fuid" => $user_fitbit_id, "password" => $user_fitbit_password)))) {
                    return 1;
                } else if ($this->getDatabase()->has($this->getSetting("db_prefix", NULL, FALSE) . "users", array("AND" => array("fuid" => $user_fitbit_id, "password" => '')))) {
                    return -1;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }

        /**
         * @param $errCode
         * @param null $user
         * @return string
         */
        public function lookupErrorCode($errCode, $user = NULL) {
            switch ($errCode) {
                case "-143":
                    return "API cool down in effect.";
                case "-142":
                    return "Unable to create required directory.";
                case "429":
                    if (!is_null($user)) {
                        $hour = date("H") + 1;
                        $this->getDatabase()->update($this->getSetting("db_prefix", NULL, FALSE) . "users", array(
                            'cooldown' => date("Y-m-d " . $hour . ":01:00"),
                        ), array('fuid' => $user));
                    }

                    return "Either you hit the rate limiting quota for the client or for the viewer";
                default:
                    return $errCode;
            }
        }

        /**
         * Set value in database/config class
         * @param $key
         * @param $value
         * @return bool
         */
        public function setSetting($key, $value) {
            return $this->getSettings()->set($key, $value);
        }

        /**
         * Helper function to check for supported API calls
         * @param null $key
         * @return array|null|string
         */
        public function supportedApi($key = NULL) {
            $database_array = array(
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
                'activity_log'         => 'Activities'
            );
            asort($database_array);

            if (is_null($key)) {
                return $database_array;
            } else {
                if (array_key_exists($key, $database_array)) {
                    return $database_array[$key];
                } else {
                    return $key;
                }
            }
        }

    }
