<?php

    /**
     * @param $msg
     */
    if (!function_exists("nxr")) {
        /**
         * NXR is a helper function. Past strings are recorded in a text file
         * and when run from a command line output is displayed on screen as
         * well
         *
         * @param string $msg String input to be displayed in logs files
         * @param bool   $includeDate
         * @param bool   $newline
         */
        function nxr($msg, $includeDate = true, $newline = true)
        {
            if ($includeDate) {
                $msg = date("Y-m-d H:i:s") . ": " . $msg;
            }
            if ($newline) {
                $msg = $msg . "\n";
            }

            if (is_writable(PATH_ROOT . "/fitbit.log")) {
                $fh = fopen(PATH_ROOT . "/fitbit.log", "a");
                fwrite($fh, $msg);
                fclose($fh);
            }
        }
    }

    /**
     * Class NxFitAdmin
     */
    class NxFitAdmin
    {

        /** @noinspection PhpUndefinedClassInspection */
        /**
         * @var medoo
         */
        protected $database;

        /**
         * @var string
         */
        protected $activeUser;

        /**
         * @var array
         */
        protected $config;

        /**
         * @var config
         */
        protected $apiSettings;

        /**
         * @var NxFitbit
         */
        protected $nxFit;

        /**
         * @var config
         */
        protected $dbUserProfile;

        /**
         *
         */
        public function __construct()
        {
            if (isset($_SESSION) && array_key_exists("admin_config",
                    $_SESSION) && is_array($_SESSION['admin_config']) && count($_SESSION['admin_config']) > 0
            ) {
                $this->setConfig($_SESSION['admin_config']);
            } else {
                /** @noinspection PhpIncludeInspection */
                require_once(PATH_ADMIN . "/config.inc.php");
                if (isset($config)) {
                    $_SESSION['admin_config'] = $config;
                    $this->setConfig($_SESSION['admin_config']);
                }
            }

            require_once(PATH_ROOT . "/inc/app.php");
            $this->nxFit = new NxFitbit();

            require_once(PATH_ROOT . "/library/medoo.php");
            /** @noinspection PhpUndefinedClassInspection */
            $this->setDatabase(new medoo(array(
                'database_type' => 'mysql',
                'database_name' => $this->getApiSetting("db_name"),
                'server'        => $this->getApiSetting("db_server"),
                'username'      => $this->getApiSetting("db_username"),
                'password'      => $this->getApiSetting("db_password"),
                'charset'       => 'utf8'
            )));

            $this->getApiSettingClass()->setDatabase($this->getDatabase());

            if (!isset($_COOKIE['_nx_fb_usr']) || !isset($_COOKIE['_nx_fb_key'])) {
                header("Location: " . $this->getConfig('url') . $this->getConfig('/admin') . "/login");
            } else if (isset($_COOKIE['_nx_fb_key']) AND $_COOKIE['_nx_fb_key'] != hash("sha256",
                    $this->getApiSetting("salt") . $_COOKIE['_nx_fb_usr'] . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'])
            ) {
                header("Location: " . $this->getConfig('url') . $this->getConfig('/admin') . "/login");
            }

            $this->setActiveUser($_COOKIE['_nx_fb_usr']);

        }

        public function getSyncStatus()
        {
            if (!array_key_exists("SyncProgress",
                    $_SESSION) || !is_numeric($_SESSION['SyncProgress']) || $_SESSION['SyncProgress'] < 0 || $_SESSION['SyncProgress'] > 100 ||
                !array_key_exists("SyncProgressScopes", $_SESSION) || !is_array($_SESSION['SyncProgressScopes'])
            ) {
                $timeToday     = strtotime(date("Y-m-d H:i:s"));
                $timeFirstSeen = strtotime($this->getUserProfile()['seen'] . ' 00:00:00');

                $totalProgress    = 0;
                $allowed_triggers = Array();
                foreach ($this->getNxFit()->supportedApi() as $key => $name) {
                    if ($this->getApiSetting('scope_' . $key) && $this->getNxFit()->getUserSetting($_COOKIE['_nx_fb_usr'],
                            'scope_' . $key) && $key != "all"
                    ) {
                        $allowed_triggers[$key]['name'] = $this->getNxFit()->supportedApi($key);

                        $oldestScope = $this->getOldestScope($key);
                        $timeLastRun = strtotime($oldestScope->format("Y-m-d H:i:s"));

                        $differenceLastRun   = $timeLastRun - $timeToday;
                        $differenceFirstSeen = $timeFirstSeen - $timeToday;
                        $precentageCompleted = round((100 - ($differenceLastRun / $differenceFirstSeen) * 100), 1);
                        if ($precentageCompleted < 0) {
                            $precentageCompleted = 0;
                        }
                        if ($precentageCompleted > 100) {
                            $precentageCompleted = 100;
                        }

                        $allowed_triggers[$key]['precentage'] = $precentageCompleted;
                        $totalProgress                        += $precentageCompleted;
                    }
                }

                $_SESSION['SyncProgressScopes'] = $allowed_triggers;
                $_SESSION['SyncProgress']       = round(($totalProgress / (100 * count($allowed_triggers))) * 100, 1);

            }

            return $_SESSION['SyncProgress'];
        }

        /** @noinspection PhpUndefinedClassInspection *
         * /**
         * @return medoo
         */
        public function getDatabase()
        {
            return $this->database;
        }/** @noinspection PhpUndefinedClassInspection */

        /**
         * @param medoo $database
         */
        public function setDatabase($database)
        {
            $this->database = $database;
        }

        /**
         * @return config
         */
        public function getApiSettingClass()
        {
            return $this->getNxFit()->getSettings();
        }

        /**
         * @param string $key
         * @param null   $default
         * @param bool   $query_db
         *
         * @return string
         */
        public function getApiSetting($key = "", $default = null, $query_db = true)
        {
            return $this->getApiSettingClass()->get($key, $default, $query_db);
        }

        /**
         * @param string $key
         *
         * @return array
         */
        public function getConfig($key = "")
        {
            if (!is_string($key) && $key == "") {
                return $this->config;
            } else {
                return $this->config[$key];
            }
        }

        /**
         * @param array $config
         */
        public function setConfig($config)
        {
            $this->config = $config;
        }

        /**
         * @return NxFitbit
         */
        public function getNxFit()
        {
            return $this->nxFit;
        }

        /**
         * @return string
         */
        public function getActiveUser()
        {
            return $this->activeUser;
        }

        /**
         * @param string $activeUser
         */
        public function setActiveUser($activeUser)
        {
            $this->activeUser = $activeUser;
        }

        /**
         * @return config
         */
        public function getUserProfile()
        {
            if (!isset($this->dbUserProfile)) {
                $userProfile         = $this->getDatabase()->get($this->getApiSetting("db_prefix", null,
                        false) . "users", array(
                    'name',
                    'avatar',
                    'city',
                    'country',
                    'height',
                    'stride_walking',
                    'stride_running',
                    'gender',
                    'rank',
                    'friends',
                    'distance',
                    'seen',
                    'lastrun',
                    'cooldown'
                ), array("fuid" => $this->getActiveUser()));
                $this->dbUserProfile = $userProfile;
            }

            return $this->dbUserProfile;
        }

        /**
         * @return string
         */
        public function getLocalWeatherImage()
        {
            $usrProfile = $this->getUserProfile();
            $usrCity    = $usrProfile['city'];
            $usrCountry = $usrProfile['country'];

            if (isset($usrCity) && (!is_string($usrCity) || $usrCity == "")) {
                unset($usrCity);
            } else {
                $usrCity = str_ireplace(" ", "", str_ireplace(".", "", $usrCity));
            }
            if (isset($usrCountry) && (!is_string($usrCountry) || $usrCountry == "")) {
                unset($usrCountry);
            } else {
                $usrCountry = str_ireplace(" ", "", str_ireplace(".", "", $usrCountry));
            }

            $imagePath = PATH_ADMIN . "img/local/";

            if (isset($usrCity) && isset($usrCountry) && file_exists($imagePath . strtolower($usrCountry) . "/" . strtolower($usrCity) . ".jpg")) {
                return "img/local/" . strtolower($usrCountry) . "/" . strtolower($usrCity) . ".jpg";
            }

            if (isset($usrCity) && file_exists($imagePath . strtolower($usrCity) . ".jpg")) {
                if (isset($usrCountry)) {
                    nxr(" +** No Location Image for " . strtolower($usrCountry) . "/" . strtolower($usrCity) . ".jpg");
                }

                return "img/local/" . strtolower($usrCity) . ".jpg";
            }

            if (isset($usrCountry) && file_exists($imagePath . strtolower($usrCountry) . ".jpg")) {
                if (isset($usrCity)) {
                    nxr(" +** No Location Image for " . strtolower($usrCountry) . "/" . strtolower($usrCity) . ".jpg");
                }

                return "img/local/" . strtolower($usrCountry) . ".jpg";
            }

            return "img/local/default.jpg";
        }

        /**
         * @return string
         */
        public function getLocalWeatherCode()
        {
            $usrProfile = $this->getUserProfile();
            $usrCity    = $usrProfile['city'];
            $usrCountry = $usrProfile['country'];

            if (isset($usrCity) && isset($usrCountry)) {
                return "$usrCity, $usrCountry";
            }

            return "London, GB";
        }

        /**
         * @return string|array
         */
        public function getUserTheme()
        {
            //$themes = array('blue','default','green','orange','purple');
            //return $themes[array_rand($themes, 1)];

            return "default";
        }

        /**
         * @param $trigger
         *
         * @return DateTime
         */
        public function getScopeCoolDown($trigger)
        {
            if ($this->getDatabase()->has($this->getApiSetting("db_prefix", null, false) . "runlog", array(
                "AND" => array(
                    "user"     => $this->getActiveUser(),
                    "activity" => $trigger
                )
            ))
            ) {
                return new DateTime ($this->getDatabase()->get($this->getApiSetting("db_prefix", null,
                        false) . "runlog", "cooldown", array(
                    "AND" => array(
                        "user"     => $this->getActiveUser(),
                        "activity" => $trigger
                    )
                )));
            } else {
                return new DateTime ("1970-01-01");
            }
        }

        /**
         * @param null $scope
         *
         * @return DateTime
         */
        public function getOldestScope($scope = null)
        {
            if (is_null($scope)) {
                if ($this->getDatabase()->has($this->getApiSetting("db_prefix", null, false) . "runlog",
                    array("user" => $this->getActiveUser()))
                ) {
                    return new DateTime ($this->getDatabase()->get($this->getApiSetting("db_prefix", null,
                            false) . "runlog", "lastrun", array(
                        "user"  => $this->getActiveUser(),
                        "ORDER" => "lastrun ASC",
                        "LIMIT" => 1
                    )));
                }
            } else {
                if ($this->getDatabase()->has($this->getApiSetting("db_prefix", null, false) . "runlog", array(
                    "AND" => array(
                        "user"     => $this->getActiveUser(),
                        "activity" => $scope
                    )
                ))
                ) {
                    $returnTime = new DateTime ($this->getDatabase()->get($this->getApiSetting("db_prefix", null,
                            false) . "runlog", "lastrun", array(
                        "AND"   => array(
                            "user"     => $this->getActiveUser(),
                            "activity" => $scope
                        ),
                        "ORDER" => "lastrun ASC",
                        "LIMIT" => 1
                    )));

                    return $returnTime;
                }
            }

            return new DateTime ("1970-01-01");
        }
    }