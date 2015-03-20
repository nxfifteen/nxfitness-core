<?php

    /**
     * Fitbit Helper class
     * @version 0.0.1
     * @author Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link http://nxfifteen.me.uk NxFIFTEEN
     * @copyright 2015 Stuart McCulloch Anderson
     * @license http://stuart.nx15.at/mit/2015 MIT
     */
    class fitbit {
        /**
         * @var NxFitbit
         */
        protected $AppClass;
        /**
         * @var FitBitPHP
         */
        protected $fitbitapi;
        /**
         * @var bool
         */
        protected $forceSync;

        /**
         * @param $fitbitApp
         * @param $consumer_key
         * @param $consumer_secret
         * @param int $debug
         * @param null $user_agent
         */
        public function __construct($fitbitApp, $consumer_key, $consumer_secret, $debug = 1, $user_agent = NULL) {
            $this->setAppClass($fitbitApp);

            require_once(dirname(__FILE__) . "/../library/fitbitphp.php");
            $this->setLibrary(new FitBitPHP($consumer_key, $consumer_secret, $debug, $user_agent));

            $this->forceSync = FALSE;
        }

        /**
         * @param $AppClass
         */
        private function setAppClass($AppClass) {
            $this->AppClass = $AppClass;
        }

        /**
         * @param FitBitPHP $fitbitapi
         */
        public function setLibrary($fitbitapi) {
            $this->fitbitapi = $fitbitapi;
        }

        /**
         * @deprecated Use getLibrary() instead
         * @return FitBitPHP
         */
        public function getFitbitapi() {
            return $this->getLibrary();
        }

        /**
         * @deprecated Use setLibrary() instead
         * @param FitBitPHP $fitbitapi
         */
        public function setFitbitapi($fitbitapi) {
            $this->setLibrary($fitbitapi);
        }

        /**
         * @return FitBitPHP
         */
        public function getLibrary() {
            return $this->fitbitapi;
        }

        /**
         * @param $user
         * @param $trigger
         * @param bool $return
         * @return mixed|null|SimpleXMLElement|string
         */
        public function pull($user, $trigger, $return = FALSE) {
            $xml = NULL;

            if ($this->getAppClass()->isUser($user)) {
                if (!$this->isAuthorised()) {
                    $this->oAuthorise($user);
                }

                if ($trigger == "all") {
                    $this->forceSync = TRUE;
                }

                if ($trigger == "all" || $trigger == "profile") {
                    $pull = $this->api_pull_profile($user);
                    if ($this->isApiError($pull)) {
                        echo "  Error profile: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                    }
                }

                if ($trigger == "all" || $trigger == "devices") {
                    $pull = $this->api_pull_devices($user);
                    if ($this->isApiError($pull)) {
                        echo "  Error devices: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                    }
                }

                if ($trigger == "all" || $trigger == "badges") {
                    $pull = $this->api_pull_badges($user);
                    if ($this->isApiError($pull)) {
                        echo "  Error badges: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                    }
                }

                if ($trigger == "all" || $trigger == "leaderboard") {
                    $pull = $this->api_pull_leaderboard($user);
                    if ($this->isApiError($pull)) {
                        echo "  Error leaderboard: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                    }
                }

                if ($trigger == "all" || $trigger == "foods" || $trigger == "goals_calories") {
                    $pull = $this->api_pull_goals_calories($user);
                    if ($this->isApiError($pull)) {
                        echo "  Error profile: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                    }
                }

                // Set variables require bellow
                $currentDate = new DateTime ('now');
                $interval = DateInterval::createFromDateString('1 day');

                if ($trigger == "all" || $trigger == "sleep") {
                    if ($this->api_isCooled("sleep", $user)) {
                        $period = new DatePeriod ($this->api_getLastCleanrun("sleep", $user), $interval, $currentDate);
                        /**
                         * @var DateTime $dt
                         */
                        foreach ($period as $dt) {
                            nxr(' Downloading Sleep Logs for ' . $dt->format("l jS M Y"));
                            $pull = $this->api_pull_sleep_logs($user, $dt->format("Y-m-d"));
                            if ($this->isApiError($pull)) {
                                echo "  Error profile: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                            }
                        }
                    } else {
                        echo "  Error sleep: " . $this->getAppClass()->lookupErrorCode(-143) . "\n";
                    }
                }

                if ($trigger == "all" || $trigger == "body") {
                    if ($this->api_isCooled("body", $user)) {
                        $period = new DatePeriod ($this->api_getLastCleanrun("body", $user), $interval, $currentDate);
                        /**
                         * @var DateTime $dt
                         */
                        foreach ($period as $dt) {
                            nxr(' Downloading Body Logs for ' . $dt->format("l jS M Y"));
                            $pull = $this->api_pull_body($user, $dt->format("Y-m-d"));
                            if ($this->isApiError($pull)) {
                                echo "  Error profile: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                            }
                        }
                    } else {
                        echo "  Error body: " . $this->getAppClass()->lookupErrorCode(-143) . "\n";
                    }
                }

                if ($trigger == "all" || $trigger == "heart") {
                    if ($this->api_isCooled("heart", $user)) {
                        $period = new DatePeriod ($this->api_getLastCleanrun("heart", $user), $interval, $currentDate);
                        /**
                         * @var DateTime $dt
                         */
                        foreach ($period as $dt) {
                            nxr(' Downloading Heart Rate Logs for ' . $dt->format("l jS M Y"));
                            $pull = $this->api_pull_body_heart($user, $dt->format("Y-m-d"));
                            if ($this->isApiError($pull)) {
                                echo "  Error profile: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                            }
                        }
                    } else {
                        echo "  Error heart: " . $this->getAppClass()->lookupErrorCode(-143) . "\n";
                    }
                }

                if ($trigger == "all" || $trigger == "water" || $trigger == "foods") {
                    if ($this->api_isCooled("water", $user)) {
                        $period = new DatePeriod ($this->api_getLastCleanrun("water", $user), $interval, $currentDate);
                        /**
                         * @var DateTime $dt
                         */
                        foreach ($period as $dt) {
                            nxr(' Downloading Water Logs for ' . $dt->format("l jS M Y"));
                            $pull = $this->api_pull_food_water($user, $dt->format("Y-m-d"));
                            if ($this->isApiError($pull)) {
                                echo "  Error profile: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                            }
                        }
                    } else {
                        echo "  Error water: " . $this->getAppClass()->lookupErrorCode(-143) . "\n";
                    }
                }

                if ($trigger == "all" || $trigger == "foods") {
                    if ($this->api_isCooled("foods", $user)) {
                        $period = new DatePeriod ($this->api_getLastCleanrun("foods", $user), $interval, $currentDate);
                        /**
                         * @var DateTime $dt
                         */
                        foreach ($period as $dt) {
                            nxr(' Downloading Foods Logs for ' . $dt->format("l jS M Y"));
                            $pull = $this->api_pull_food_eaten($user, $dt->format("Y-m-d"));
                            if ($this->isApiError($pull)) {
                                echo "  Error profile: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                            }
                        }
                    } else {
                        echo "  Error foods: " . $this->getAppClass()->lookupErrorCode(-143) . "\n";
                    }
                }

                if ($trigger == "all" || $trigger == "goals") {
                    if ($this->api_isCooled("goals", $user)) {
                        $period = new DatePeriod ($this->api_getLastCleanrun("goals", $user), $interval, $currentDate);
                        /**
                         * @var DateTime $dt
                         */
                        foreach ($period as $dt) {
                            nxr(' Downloading Goals Logs for ' . $dt->format("l jS M Y"));
                            $pull = $this->api_pull_goals($user, $dt->format("Y-m-d"));
                            if ($this->isApiError($pull)) {
                                echo "  Error profile: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                            }
                        }
                    } else {
                        echo "  Error Goals: " . $this->getAppClass()->lookupErrorCode(-143) . "\n";
                    }
                }

                $timeSeries = Array("steps"                => "300",
                                    "distance"             => "300",
                                    "floors"               => "300",
                                    "elevation"            => "300",
                                    "minutesSedentary"     => "1800",
                                    "minutesLightlyActive" => "1800",
                                    "minutesFairlyActive"  => "1800",
                                    "minutesVeryActive"    => "1800",
                                    "caloriesOut"          => "1800");
                if ($trigger == "all" || $trigger == "activities") {
                    if ($this->api_isCooled("activities", $user)) {
                        nxr(" Downloading Series Info");
                        foreach ($timeSeries as $activity => $timeout) {
                            $this->api_pull_time_series($user, $activity, TRUE);
                        }
                        $this->api_setLastrun("activities", $user, NULL, TRUE);
                    }
                } else if (array_key_exists($trigger, $timeSeries)) {
                    $this->api_pull_time_series($user, $trigger);
                }

                if ($trigger == "all" || $trigger == "activity_log") {
                    if ($this->api_isCooled("activity_log", $user)) {
                        $period = new DatePeriod ($this->api_getLastCleanrun("activity_log", $user), $interval, $currentDate);
                        /**
                         * @var DateTime $dt
                         */
                        foreach ($period as $dt) {
                            nxr(' Downloading activities for ' . $dt->format("l jS M Y"));
                            $pull = $this->api_pull_activity_log($user, $dt->format("Y-m-d"));
                            if ($this->isApiError($pull)) {
                                echo "  Error profile: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                            }
                        }
                    } else {
                        echo "  Error sleep: " . $this->getAppClass()->lookupErrorCode(-143) . "\n";
                    }
                }

                if ($trigger == "all") {
                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array(
                        "lastrun" => $currentDate->format("Y-m-d H:i:s")
                    ), array("fuid" => $user));
                }

            }

            if ($return) {
                return $xml;
            } else {
                return TRUE;
            }
        }

        /**
         * @return NxFitbit
         */
        private function getAppClass() {
            return $this->AppClass;
        }

        /**
         * @return boolean
         */
        private function isAuthorised() {
            if ($this->getLibrary()->getOAuthToken() != "" AND $this->getLibrary()->getOAuthSecret() != "") {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        /**
         * @param $user
         */
        public function oAuthorise($user) {
            $oAuth = $this->get_oauth($user);
            if (!$oAuth OR !is_array($oAuth) OR $oAuth['token'] == "" OR $oAuth['secret'] == "") {
                nxr('Unable to setup the user OAuth credentials. Have they authorised this app?');
                exit;
            }
            $this->getLibrary()->setOAuthDetails($oAuth['token'], $oAuth['secret']);
        }

        /**
         * @param $user
         * @return array
         */
        private function get_oauth($user) {
            $userArray = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array('token', 'secret'), array("fuid" => $user));
            if (is_array($userArray)) {
                return $userArray;
            } else {
                nxr('User ' . $user . ' does not exist, unable to continue.');
                exit;
            }
        }

        /**
         * @param $user
         * @return mixed|null|SimpleXMLElement|string
         */
        private function api_pull_profile($user) {
            if ($this->api_isCooled("profile", $user)) {
                try {
                    $userProfile = $this->getLibrary()->getProfile();
                } catch (Exception $E) {
                    /**
                     * @var FitBitException $E
                     */
                    echo $user . "\n";
                    echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                    print_r($E);
                    die();
                }

                if (!isset($userProfile->user->height)) {
                    $userProfile->user->height = NULL;
                }
                if (!isset($userProfile->user->strideLengthRunning)) {
                    $userProfile->user->strideLengthRunning = NULL;
                }
                if (!isset($userProfile->user->strideLengthWalking)) {
                    $userProfile->user->strideLengthWalking = NULL;
                }
                if (!isset($userProfile->user->city)) {
                    $userProfile->user->city = NULL;
                }
                if (!isset($userProfile->user->country)) {
                    $userProfile->user->country = NULL;
                }

                $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array(
                    "avatar"         => (String)$userProfile->user->avatar150,
                    "city"           => (String)$userProfile->user->city,
                    "country"        => (String)$userProfile->user->country,
                    "name"           => (String)$userProfile->user->fullName,
                    "gender"         => (String)$userProfile->user->gender,
                    "height"         => (String)$userProfile->user->height,
                    "seen"           => (String)$userProfile->user->memberSince,
                    "stride_running" => (String)$userProfile->user->strideLengthRunning,
                    "stride_walking" => (String)$userProfile->user->strideLengthWalking
                ), array("fuid" => $user));

                if (!file_exists(dirname(__FILE__) . "/../images/avatars/" . $user . ".jpg")) {
                    file_put_contents(dirname(__FILE__) . "/../images/avatars/" . $user . ".jpg", fopen((String)$userProfile->user->avatar150, 'r'));
                }

                $this->api_setLastrun("profile", $user, NULL, TRUE);

                return $userProfile;
            } else {
                return "-143";
            }
        }

        /**
         * @param $trigger
         * @param $user
         * @param bool $reset
         * @return bool
         */
        public function api_isCooled($trigger, $user, $reset = FALSE) {
            if ($this->forceSync) {
                return TRUE;
            } else {
                $currentDate = new DateTime ('now');
                $lastRun = $this->api_getCoolDown($trigger, $user, $reset);

                //nxr($currentDate->format("Y-m-d H:i:s") . ": CoolDown test for $trigger - cooled at " . $lastRun->format("Y-m-d H:i:s"));

                if ($lastRun->format("U") < $currentDate->format("U")) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        }

        /**
         * @param $activity
         * @param $username
         * @param bool $reset
         * @return DateTime
         */
        private function api_getCoolDown($activity, $username, $reset = FALSE) {
            if ($reset)
                return new DateTime ("1970-01-01");

            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $username, "activity" => $activity)))) {
                return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", "cooldown", array("AND" => array("user" => $username, "activity" => $activity))));
            } else {
                return new DateTime ("1970-01-01");
            }
        }

        /**
         * @param $activity
         * @param $username
         * @param null $cron_delay
         * @param bool $clean
         */
        private function api_setLastrun($activity, $username, $cron_delay = NULL, $clean = FALSE) {
            if (is_null($cron_delay)) {
                $cron_delay_holder = 'nx_fitbit_ds_' . $activity . '_timeout';
                $cron_delay = $this->getAppClass()->getSetting($cron_delay_holder, 5400);
            }

            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $username, "activity" => $activity)))) {
                $fields = array(
                    "date"     => date("Y-m-d H:i:s"),
                    "cooldown" => date("Y-m-d H:i:s", time() + $cron_delay)
                );
                if ($clean) {
                    $fields['lastrun'] = date("Y-m-d H:i:s");
                }

                $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", $fields, array("AND" => array("user" => $username, "activity" => $activity)));
            } else {
                $fields = array(
                    "user"     => $username,
                    "activity" => $activity,
                    "date"     => date("Y-m-d H:i:s"),
                    "cooldown" => date("Y-m-d H:i:s", time() + $cron_delay)
                );
                if ($clean) {
                    $fields['lastrun'] = date("Y-m-d H:i:s");
                }

                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", $fields);
            }

            $cache_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
            $cache_files = scandir($cache_dir);
            foreach ($cache_files as $file) {
                if (file_exists($cache_dir . $file) && is_writable($cache_dir . $file) && substr($file, 0, strlen($username) + 1) === "_" . $username) {
                    $cacheNames = $this->getAppClass()->getSettings()->getRelatedCacheNames($activity);
                    foreach ($cacheNames as $cacheName) {
                        if (substr($file, 0, strlen($username) + strlen($cacheName) + 2) === "_" . $username . "_" . $cacheName) {
                            nxr("  $file cache file was deleted");
                            unlink($cache_dir . $file);
                        }
                    }
                }
            }
        }

        /**
         * @param $xml
         * @return bool
         */
        private function isApiError($xml) {
            if (is_numeric($xml) AND $xml < 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        /**
         * Download information about devices associated with the users account. This is then stored in the database
         * @param $user
         * @return mixed|null|SimpleXMLElement|string
         */
        private function api_pull_devices($user) {
            if ($this->api_isCooled("devices", $user)) {
                try {
                    $userDevices = $this->getLibrary()->getDevices();
                } catch (Exception $E) {
                    /**
                     * @var FitBitException $E
                     */
                    echo $user . "\n";
                    echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                    print_r($E);
                    die();
                }

                foreach ($userDevices as $device) {
                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", array("AND" => array("id" => (String)$device->id)))) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", array(
                            'lastSyncTime' => (String)$device->lastSyncTime,
                            'battery'      => (String)$device->battery
                        ), array("id" => (String)$device->id));
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", array(
                            'id'            => (String)$device->id,
                            'deviceVersion' => (String)$device->deviceVersion,
                            'type'          => (String)$device->type,
                            'lastSyncTime'  => (String)$device->lastSyncTime,
                            'battery'       => (String)$device->battery
                        ));
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_dev2usr", array(
                            'user'   => $user,
                            'device' => (String)$device->id
                        ));
                    }

                    if (!file_exists(dirname(__FILE__) . "/../images/devices/" . str_ireplace(" ", "", $device->deviceVersion) . ".png")) {
                        nxr(" No device image for " . $device->type . " " . $device->deviceVersion);
                    }
                }

                $this->api_setLastrun("devices", $user, NULL, TRUE);

                return $userDevices;
            } else {
                return "-143";
            }
        }

        /**
         * Download information of badges the user has aquired
         * @param $user
         * @return mixed|null|SimpleXMLElement|string
         */
        private function api_pull_badges($user) {
            if ($this->api_isCooled("badges", $user)) {
                $badgeFolder = dirname(__FILE__) . "/../images/badges/";
                if (file_exists($badgeFolder) AND is_writable($badgeFolder)) {
                    try {
                        $userBadges = $this->getLibrary()->getBadges();
                    } catch (Exception $E) {
                        /**
                         * @var FitBitException $E
                         */
                        echo $user . "\n";
                        echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                        print_r($E);
                        die();
                    }

                    if (isset($userBadges)) {
                        foreach ($userBadges->badges as $badge) {
                            if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages", array(
                                "AND" => array(
                                    "badgeType" => (String)$badge->badgeType,
                                    "value"     => (String)$badge->value
                                )
                            ))
                            ) {
                                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages", array(
                                    'badgeType'               => (String)$badge->badgeType,
                                    'value'                   => (String)$badge->value,
                                    'image'                   => basename((String)$badge->image50px),
                                    'badgeGradientEndColor'   => (String)$badge->badgeGradientEndColor,
                                    'badgeGradientStartColor' => (String)$badge->badgeGradientStartColor,
                                    'earnedMessage'           => (String)$badge->earnedMessage,
                                    'marketingDescription'    => (String)$badge->marketingDescription,
                                    'name'                    => (String)$badge->name
                                ));
                            }

                            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_badge2usr", array("AND" => array(
                                "user"      => $user,
                                "badgeType" => (String)$badge->badgeType,
                                "value"     => (String)$badge->value
                            )))
                            ) {
                                $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_badge2usr", array(
                                    'dateTime'      => (String)$badge->dateTime,
                                    'timesAchieved' => (String)$badge->timesAchieved
                                ), array("AND" => array(
                                    "user"      => $user,
                                    "badgeType" => (String)$badge->badgeType,
                                    "value"     => (String)$badge->value
                                )));
                            } else {
                                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_badge2usr", array(
                                    'user'          => $user,
                                    'badgeType'     => (String)$badge->badgeType,
                                    'dateTime'      => (String)$badge->dateTime,
                                    'timesAchieved' => (String)$badge->timesAchieved,
                                    'value'         => (String)$badge->value,
                                    'unit'          => (String)$badge->unit
                                ));
                            }

                            $imageFileName = basename((String)$badge->image50px);
                            if (!file_exists($badgeFolder . "/" . $imageFileName)) {
                                file_put_contents($badgeFolder . "/" . $imageFileName, fopen((String)$badge->image50px, 'r'));
                            }

                            if (!file_exists($badgeFolder . "/75px")) {
                                mkdir($badgeFolder . "/75px", 0755, TRUE);
                            }
                            if (!file_exists($badgeFolder . "/75px/" . $imageFileName)) {
                                file_put_contents($badgeFolder . "/75px/" . $imageFileName, fopen((String)$badge->image75px, 'r'));
                            }

                            if (!file_exists($badgeFolder . "/100px")) {
                                mkdir($badgeFolder . "/100px", 0755, TRUE);
                            }
                            if (!file_exists($badgeFolder . "/100px/" . $imageFileName)) {
                                file_put_contents($badgeFolder . "/100px/" . $imageFileName, fopen((String)$badge->image100px, 'r'));
                            }

                            if (!file_exists($badgeFolder . "/125px")) {
                                mkdir($badgeFolder . "/125px", 0755, TRUE);
                            }
                            if (!file_exists($badgeFolder . "/125px/" . $imageFileName)) {
                                file_put_contents($badgeFolder . "/125px/" . $imageFileName, fopen((String)$badge->image125px, 'r'));
                            }

                            if (!file_exists($badgeFolder . "/300px")) {
                                mkdir($badgeFolder . "/300px", 0755, TRUE);
                            }
                            if (!file_exists($badgeFolder . "/300px/" . $imageFileName)) {
                                file_put_contents($badgeFolder . "/300px/" . $imageFileName, fopen((String)$badge->image300px, 'r'));
                            }

                        }
                    }

                    $this->api_setLastrun("badges", $user, NULL, TRUE);

                    return $userBadges;
                } else {
                    echo "Missing: $badgeFolder\n";

                    return "-142";
                }
            } else {
                return "-143";
            }
        }

        /**
         * @param $user
         * @return mixed|null|SimpleXMLElement|string
         */
        private function api_pull_leaderboard($user) {
            if ($this->api_isCooled("leaderboard", $user)) {
                try {
                    $userFriends = $this->getLibrary()->getFriendsLeaderboard();
                } catch (Exception $E) {
                    /**
                     * @var FitBitException $E
                     */
                    echo $user . "\n";
                    echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                    print_r($E);
                    die();
                }

                if (isset($userFriends)) {
                    $userFriends = $userFriends->friends;
                    if (is_array($userFriends) and count($userFriends) > 0) {
                        $youRank = 0;
                        $youDistance = 0;
                        $lastSteps = 0;
                        foreach ($userFriends as $friend) {
                            $lifetime = floatval($friend->lifetime->steps);
                            $steps = floatval($friend->summary->steps);

                            if ($friend->user->encodedId == $user) {
                                $displayName = "* YOU * are";
                                if ($steps == 0) {
                                    $youRank = count($userFriends);
                                } else {
                                    $youRank = (String)$friend->rank->steps;
                                }
                                $youDistance = ($lastSteps - $steps);
                                if ($youDistance < 0) $youDistance = 0;
                            } else {
                                $displayName = $friend->user->displayName . " is";
                                $lastSteps = $steps;
                            }

                            nxr("  " . $displayName . " ranked " . $friend->rank->steps . " with " . number_format($steps) . " and " . number_format($lifetime) . " lifetime steps");
                        }

                        nxr("  * You are " . number_format($youDistance) . " steps away from the next rank and have " . count($userFriends) . " friends");

                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array(
                            'rank'     => $youRank,
                            'friends'  => count($userFriends),
                            'distance' => $youDistance
                        ), array("fuid" => $user));

                    }
                }

                $this->api_setLastrun("leaderboard", $user, NULL, TRUE);

                return $userFriends;
            } else {
                return "-143";
            }

        }

        /**
         * @param $user
         * @return mixed|null|SimpleXMLElement|string
         */
        private function api_pull_goals_calories($user) {
            if ($this->api_isCooled("goals_calories", $user)) {
                try {
                    $userCaloriesGoals = $this->getLibrary()->customCall("user/-/foods/log/goal.xml", NULL, OAUTH_HTTP_METHOD_GET);
                } catch (Exception $E) {
                    /**
                     * @var FitBitException $E
                     */
                    echo $user . "\n";
                    echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                    print_r($E);
                    die();
                }

                if (isset($userCaloriesGoals)) {
                    $userCaloriesGoals = simplexml_load_string($userCaloriesGoals->response);
                    $fallback = FALSE;

                    /** @noinspection PhpUndefinedFieldInspection */
                    $usr_goals = $userCaloriesGoals->goals;
                    /** @noinspection PhpUndefinedFieldInspection */
                    $usr_foodplan = $userCaloriesGoals->foodPlan;

                    if (empty($usr_goals->calories)) {
                        $usr_goals_calories = 0;
                        $fallback = TRUE;
                    } else {
                        $usr_goals_calories = (int)$usr_goals->calories;
                    }

                    if (empty($usr_foodplan->intensity)) {
                        $usr_foodplan_intensity = "Unset";
                        $fallback = TRUE;
                    } else {
                        $usr_foodplan_intensity = (string)$usr_foodplan->intensity;
                    }

                    $currentDate = new DateTime ('now');
                    if (empty($usr_foodplan->estimatedDate)) {
                        $usr_foodplan_estimatedDate = $currentDate->format("Y-m-d");
                        $fallback = TRUE;
                    } else {
                        $usr_foodplan_estimatedDate = (string)$usr_foodplan->estimatedDate;
                    }

                    if (empty($usr_foodplan->personalized)) {
                        $usr_foodplan_personalized = "false";
                        $fallback = TRUE;
                    } else {
                        $usr_foodplan_personalized = (string)$usr_foodplan->personalized;
                    }

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "goals_calories", array("AND" => array("user" => $user, "date" => $currentDate->format("Y-m-d"))))) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "goals_calories", array(
                            'calories'      => $usr_goals_calories,
                            'intensity'     => $usr_foodplan_intensity,
                            'estimatedDate' => $usr_foodplan_estimatedDate,
                            'personalized'  => $usr_foodplan_personalized,
                        ), array("AND" => array("user" => $user, "date" => $currentDate->format("Y-m-d"))));
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "goals_calories", array(
                            'user'          => $user,
                            'date'          => $currentDate->format("Y-m-d"),
                            'calories'      => $usr_goals_calories,
                            'intensity'     => $usr_foodplan_intensity,
                            'estimatedDate' => $usr_foodplan_estimatedDate,
                            'personalized'  => $usr_foodplan_personalized,
                        ));
                    }

                    if ($fallback) {
                        $this->api_setLastrun("goals_calories", $user);
                    } else {
                        $this->api_setLastrun("goals_calories", $user, NULL, TRUE);
                    }
                }

                return $userCaloriesGoals;
            } else {
                return "-143";
            }

        }

        /**
         * @param $activity
         * @param $user
         * @return DateTime
         */
        private function api_getLastCleanrun($activity, $user) {
            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $user, "activity" => $activity)))) {
                return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", "lastrun", array("AND" => array("user" => $user, "activity" => $activity))));
            } else {
                return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", "seen", array("fuid" => $user)));
            }
        }

        /**
         * @param $user
         * @param $targetDate
         * @return mixed|null|SimpleXMLElement|string
         */
        private function api_pull_sleep_logs($user, $targetDate) {
            $targetDateTime = new DateTime ($targetDate);
            try {
                $userSleepLog = $this->getLibrary()->getSleep($targetDateTime);
            } catch (Exception $E) {
                /**
                 * @var FitBitException $E
                 */
                echo $user . "\n";
                echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                print_r($E);
                die();
            }

            if (isset($userSleepLog) and is_object($userSleepLog) and is_array($userSleepLog->sleep)) {
                foreach ($userSleepLog->sleep as $loggedSleep) {
                    if (is_object($loggedSleep)) {
                        if ($loggedSleep->logId != 0) {
                            if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logSleep", array("logId" => (String)$loggedSleep->logId))) {
                                if ($loggedSleep->isMainSleep == 1) {
                                    $loggedSleep->isMainSleep = "true";
                                } else {
                                    $loggedSleep->isMainSleep = "false";
                                }

                                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logSleep", array(
                                    "logId"               => (String)$loggedSleep->logId,
                                    'awakeningsCount'     => (String)$loggedSleep->awakeningsCount,
                                    'duration'            => (String)$loggedSleep->duration,
                                    'efficiency'          => (String)$loggedSleep->efficiency,
                                    'isMainSleep'         => (String)$loggedSleep->isMainSleep,
                                    'minutesAfterWakeup'  => (String)$loggedSleep->minutesAfterWakeup,
                                    'minutesAsleep'       => (String)$loggedSleep->minutesAsleep,
                                    'minutesAwake'        => (String)$loggedSleep->minutesAwake,
                                    'minutesToFallAsleep' => (String)$loggedSleep->minutesToFallAsleep,
                                    'startTime'           => (String)$loggedSleep->startTime,
                                    'timeInBed'           => (String)$loggedSleep->timeInBed
                                ));
                            }

                            if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_sleep2usr", array("AND" => array('user' => $user, 'sleeplog' => (String)$loggedSleep->logId)))) {
                                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_sleep2usr", array(
                                    'user'               => $user,
                                    'sleeplog'           => (String)$loggedSleep->logId,
                                    'totalMinutesAsleep' => (String)$userSleepLog->summary->totalMinutesAsleep,
                                    'totalSleepRecords'  => (String)$userSleepLog->summary->totalSleepRecords,
                                    'totalTimeInBed'     => (String)$userSleepLog->summary->totalTimeInBed
                                ));
                            }

                            $this->api_setLastCleanrun("sleep", $user, new DateTime ($targetDate));
                        }
                    }
                }
            }

            return $userSleepLog;

        }

        /**
         * @param $activity
         * @param $user
         * @param null $date
         * @param int $delay
         */
        private function api_setLastCleanrun($activity, $user, $date = NULL, $delay = 0) {
            if (is_null($date)) {
                $date = new DateTime("now");
                nxr("Last run " . $date->format("Y-m-d H:i:s"));
            }
            if ($delay > 0) $date->modify('-' . $delay . ' day');

            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $user, "activity" => $activity)))) {
                $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array(
                    'date'    => date("Y-m-d H:i:s"),
                    'lastrun' => $date->format("Y-m-d H:i:s")
                ), array("AND" => array("user" => $user, "activity" => $activity)));
            } else {
                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array(
                    'user'     => $user,
                    'activity' => $activity,
                    'date'     => date("Y-m-d H:i:s"),
                    'lastrun'  => $date->format("Y-m-d H:i:s")
                ));
            }

            if ($delay == 0) $this->api_setLastrun($activity, $user, NULL, FALSE);
        }

        /**
         * @param $user
         * @param $targetDate
         * @return mixed
         */
        private function api_pull_body($user, $targetDate) {
            $targetDateTime = new DateTime ($targetDate);
            try {
                $userBodyLog = $this->getLibrary()->getBody($targetDateTime);
            } catch (Exception $E) {
                /**
                 * @var FitBitException $E
                 */
                echo $user . "\n";
                echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                print_r($E);
                die();
            }

            if (isset($userBodyLog)) {
                $fallback = FALSE;
                $currentDate = new DateTime ();
                if ($currentDate->format("Y-m-d") == $targetDate and ($userBodyLog->body->weight == "0" OR $userBodyLog->body->fat == "0" OR
                        $userBodyLog->body->bmi == "0" OR $userBodyLog->goals->weight == "0" OR
                        $userBodyLog->goals->fat == "0")
                ) {
                    $this->getAppClass()->addCronJob($user, "body");
                    $fallback = TRUE;
                }

                $insertToDB = FALSE;
                if (!isset($userBodyLog->body->weight) or $userBodyLog->body->weight == "0") {
                    nxr('  Weight unrecorded, reverting to previous record');
                    $weight = $this->getDBCurrentBody($user, "weight");
                    $fallback = TRUE;
                } else {
                    $weight = (float)$userBodyLog->body->weight;
                    $insertToDB = TRUE;
                }

                if (!isset($userBodyLog->body->fat) or $userBodyLog->body->fat == "0") {
                    nxr('  Body Fat unrecorded, reverting to previous record');
                    $fat = $this->getDBCurrentBody($user, "fat");
                    $fallback = TRUE;
                } else {
                    $fat = (float)$userBodyLog->body->fat;
                    $insertToDB = TRUE;
                }

                if ($insertToDB) {
                    $weightAvg = round(($weight - $this->getDBCurrentBody($user, "weight")) / 10, 1, PHP_ROUND_HALF_UP) + $this->getDBCurrentBody($user, "weight");
                    $fatAvg = round(($fat - $this->getDBCurrentBody($user, "fat")) / 10, 1, PHP_ROUND_HALF_UP) + $this->getDBCurrentBody($user, "fat");

                    if (!isset($userBodyLog->goals->weight) or $userBodyLog->goals->weight == "0") {
                        nxr('  Weight Goal unset, reverting to 0');
                        $goalsweight = $this->getDBCurrentBody($user, "weightGoal", TRUE);
                    } else {
                        $goalsweight = (float)$userBodyLog->goals->weight;
                    }

                    if (!isset($userBodyLog->goals->fat) or $userBodyLog->goals->fat == "0") {
                        nxr('  Body Fat Goal unset, reverting to 0');
                        $goalsfat = $this->getDBCurrentBody($user, "fatGoal", TRUE);
                    } else {
                        $goalsfat = (float)$userBodyLog->goals->fat;
                    }

                    if (!isset($userBodyLog->body->bmi) or $userBodyLog->body->bmi == "0") {
                        nxr('  BMI unrecorded, reverting to previous record');
                        $bmi = $this->getDBCurrentBody($user, "bmi");
                        $fallback = TRUE;
                    } else {
                        $bmi = (float)$userBodyLog->body->bmi;
                    }

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body", array("AND" => array('user' => $user, 'date' => $targetDate)))) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body", array(
                            "weight"     => $weight,
                            "weightGoal" => $goalsweight,
                            "weightAvg"  => $weightAvg,
                            "fat"        => $fat,
                            "fatGoal"    => $goalsfat,
                            "fatAvg"     => $fatAvg,
                            "bmi"        => $bmi,
                            "bicep"      => (String)$userBodyLog->body->bicep,
                            "calf"       => (String)$userBodyLog->body->calf,
                            "chest"      => (String)$userBodyLog->body->chest,
                            "forearm"    => (String)$userBodyLog->body->forearm,
                            "hips"       => (String)$userBodyLog->body->hips,
                            "neck"       => (String)$userBodyLog->body->neck,
                            "thigh"      => (String)$userBodyLog->body->thigh,
                            "waist"      => (String)$userBodyLog->body->waist
                        ), array("AND" => array('user' => $user, 'date' => $targetDate)));
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body", array(
                            'user'       => $user,
                            'date'       => $targetDate,
                            "weight"     => $weight,
                            "weightGoal" => $goalsweight,
                            "weightAvg"  => $weightAvg,
                            "fat"        => $fat,
                            "fatGoal"    => $goalsfat,
                            "fatAvg"     => $fatAvg,
                            "bmi"        => $bmi,
                            "bicep"      => (String)$userBodyLog->body->bicep,
                            "calf"       => (String)$userBodyLog->body->calf,
                            "chest"      => (String)$userBodyLog->body->chest,
                            "forearm"    => (String)$userBodyLog->body->forearm,
                            "hips"       => (String)$userBodyLog->body->hips,
                            "neck"       => (String)$userBodyLog->body->neck,
                            "thigh"      => (String)$userBodyLog->body->thigh,
                            "waist"      => (String)$userBodyLog->body->waist
                        ));
                    }

                    if (!$fallback) $this->api_setLastCleanrun("body", $user, new DateTime ($targetDate));
                }
            }

            return $userBodyLog;

        }

        /**
         * @param $user
         * @param $string
         * @return bool|int
         */
        public function getDBCurrentBody($user, $string) {
            if (!$user) return "No default user selected";

            $return = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body", $string, array("user" => $user, "ORDER" => "date DESC", "LIMIT" => 1));

            if (!is_numeric($return)) {
                return 0;
            } else {
                return $return;
            }
        }

        /**
         * @param $user
         * @param $targetDate
         * @return mixed
         */
        private function api_pull_body_heart($user, $targetDate) {
            $targetDateTime = new DateTime ($targetDate);
            try {
                //TODO Soon to be deprecated API
                $userBodyHeart = $this->getLibrary()->getHeartRate($targetDateTime);
            } catch (Exception $E) {
                /**
                 * @var FitBitException $E
                 */
                echo $user . "\n";
                echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                print_r($E);
                die();
            }

            if (isset($userBodyHeart)) {
                if (count($userBodyHeart->average) == 3) {
                    $resting = 0;
                    $normal = 0;
                    $exertive = 0;

                    foreach ($userBodyHeart->average as $heartAverage) {
                        switch ($heartAverage->tracker) {
                            case "Resting Heart Rate":
                                $resting = (string)$heartAverage->heartRate;
                                break;
                            case "Normal Heart Rate":
                                $normal = (string)$heartAverage->heartRate;
                                break;
                            case "Exertive Heart Rate":
                                $exertive = (string)$heartAverage->heartRate;
                                break;
                        }
                    }
                    if ($resting > 0 or $normal > 0 or $exertive > 0) {
                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "heartAverage", array("AND" => array('user' => $user, 'date' => $targetDate)))) {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "heartAverage", array(
                                'resting'  => $resting,
                                'normal'   => $normal,
                                'exertive' => $exertive
                            ), array("AND" => array('user' => $user, 'date' => $targetDate)));
                        } else {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "heartAverage", array(
                                'user'     => $user,
                                'date'     => $targetDate,
                                'resting'  => $resting,
                                'normal'   => $normal,
                                'exertive' => $exertive
                            ));
                        }
                    }

                    if ($resting > 0 or $normal > 0 or $exertive > 0) {
                        $this->api_setLastCleanrun("heart", $user, new DateTime ($targetDate));
                    } else {
                        $this->api_setLastCleanrun("heart", $user, new DateTime ($targetDate), 7);
                        $this->api_setLastrun("heart", $user);
                    }
                }
            }

            return $userBodyHeart;
        }

        /**
         * @param $user
         * @param $targetDate
         * @return mixed
         */
        private function api_pull_food_water($user, $targetDate) {
            $targetDateTime = new DateTime ($targetDate);
            try {
                $userWaterLog = $this->getLibrary()->getWater($targetDateTime);
            } catch (Exception $E) {
                /**
                 * @var FitBitException $E
                 */
                echo $user . "\n";
                echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                print_r($E);
                die();
            }

            if (isset($userWaterLog)) {
                if (isset($userWaterLog->summary->water)) {

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water", array("AND" => array('user' => $user, 'date' => $targetDate)))) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water", array(
                            'id'     => $targetDateTime->format("U"),
                            'liquid' => (String)$userWaterLog->summary->water
                        ), array("AND" => array('user' => $user, 'date' => $targetDate)));
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water", array(
                            'user'   => $user,
                            'date'   => $targetDate,
                            'id'     => $targetDateTime->format("U"),
                            'liquid' => (String)$userWaterLog->summary->water
                        ));
                    }

                    $this->api_setLastCleanrun("water", $user, $targetDateTime);
                }
            }

            return $userWaterLog;
        }

        /**
         * @param $user
         * @param $targetDate
         * @return mixed
         */
        private function api_pull_food_eaten($user, $targetDate) {
            $targetDateTime = new DateTime ($targetDate);
            try {
                $userFoodLog = $this->getLibrary()->getFoods($targetDateTime);
            } catch (Exception $E) {
                /**
                 * @var FitBitException $E
                 */
                echo $user . "\n";
                echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                print_r($E);
                die();
            }

            if (isset($userFoodLog)) {
                if (count($userFoodLog->foods) > 0) {
                    foreach ($userFoodLog->foods as $meal) {
                        nxr("  Logging meal " . $meal->loggedFood->name);

                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood", array("AND" => array('user' => $user, 'date' => $targetDate, 'meal' => (String)$meal->loggedFood->name)))) {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood", array(
                                'calories' => (String)$meal->nutritionalValues->calories,
                                'carbs'    => (String)$meal->nutritionalValues->carbs,
                                'fat'      => (String)$meal->nutritionalValues->fat,
                                'fiber'    => (String)$meal->nutritionalValues->fiber,
                                'protein'  => (String)$meal->nutritionalValues->protein,
                                'sodium'   => (String)$meal->nutritionalValues->sodium
                            ), array("AND" => array('user' => $user, 'date' => $targetDate, 'meal' => (String)$meal->loggedFood->name)));
                        } else {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood", array(
                                'user'     => $user,
                                'date'     => $targetDate,
                                'meal'     => (String)$meal->loggedFood->name,
                                'calories' => (String)$meal->nutritionalValues->calories,
                                'carbs'    => (String)$meal->nutritionalValues->carbs,
                                'fat'      => (String)$meal->nutritionalValues->fat,
                                'fiber'    => (String)$meal->nutritionalValues->fiber,
                                'protein'  => (String)$meal->nutritionalValues->protein,
                                'sodium'   => (String)$meal->nutritionalValues->sodium
                            ));
                        }

                        $this->api_setLastCleanrun("foods", $user, $targetDateTime);
                    }
                }
            }

            return $userFoodLog;
        }

        /**
         * @param $user
         * @param $targetDate
         * @return mixed
         */
        private function api_pull_goals($user, $targetDate) {
            try {
                $userGoals = $this->getLibrary()->customCall("user/-/activities/goals/daily.json", NULL, OAUTH_HTTP_METHOD_GET);
            } catch (Exception $E) {
                /**
                 * @var FitBitException $E
                 */
                echo $user . "\n";
                echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                print_r($E);
                die();
            }

            if (isset($userGoals)) {
                $currentDate = new DateTime();
                $userGoals = json_decode($userGoals->response);
                $usr_goals = $userGoals->goals;
                if (is_object($usr_goals)) {
                    $fallback = FALSE;

                    /** @noinspection PhpUndefinedFieldInspection */
                    if ($usr_goals->caloriesOut == "" OR $usr_goals->distance == "" OR $usr_goals->floors == "" OR $usr_goals->activeMinutes == "" OR $usr_goals->steps == "") {
                        $this->getAppClass()->addCronJob($user, "goals");

                        /** @noinspection PhpUndefinedFieldInspection */
                        if ($usr_goals->caloriesOut == "") /** @noinspection PhpUndefinedFieldInspection */
                            $usr_goals->caloriesOut = -1;
                        /** @noinspection PhpUndefinedFieldInspection */
                        if ($usr_goals->distance == "") /** @noinspection PhpUndefinedFieldInspection */
                            $usr_goals->distance = -1;
                        /** @noinspection PhpUndefinedFieldInspection */
                        if ($usr_goals->floors == "") /** @noinspection PhpUndefinedFieldInspection */
                            $usr_goals->floors = -1;
                        /** @noinspection PhpUndefinedFieldInspection */
                        if ($usr_goals->activeMinutes == "") /** @noinspection PhpUndefinedFieldInspection */
                            $usr_goals->activeMinutes = -1;
                        /** @noinspection PhpUndefinedFieldInspection */
                        if ($usr_goals->steps == "") /** @noinspection PhpUndefinedFieldInspection */
                            $usr_goals->steps = -1;
                        $fallback = TRUE;
                    }

                    if ($currentDate->format("Y-m-d") == $targetDate) {
                        if ($usr_goals->steps > 1) {
                            $newGoal = $this->thisWeeksGoal($user, "steps");
                            if ($newGoal > 0 && $usr_goals->steps != $newGoal) {
                                nxr("  Returned steps target was " . $usr_goals->steps . " but I think it should be " . $newGoal);
                                try {
                                    $userGoals = $this->getLibrary()->customCall("user/-/activities/goals/daily.json", array('type' => 'steps', 'value' => $newGoal), OAUTH_HTTP_METHOD_POST);
                                } catch (Exception $E) {
                                    /**
                                     * @var FitBitException $E
                                     */
                                    echo $user . "\n";
                                    echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                                    print_r($E);
                                    die();
                                }
                            } elseif ($newGoal > 0) {
                                nxr("  Returned steps target was " . $usr_goals->steps . " which is right for this week goal of " . $newGoal);
                            }
                        }

                        if ($usr_goals->floors > 1) {
                            $newGoal = $this->thisWeeksGoal($user, "floors");
                            if ($newGoal > 0 && $usr_goals->floors != $newGoal) {
                                nxr("  Returned floor target was " . $usr_goals->floors . " but I think it should be " . $newGoal);
                                try {
                                    $userGoals = $this->getLibrary()->customCall("user/-/activities/goals/daily.json", array('type' => 'floors', 'value' => $newGoal), OAUTH_HTTP_METHOD_POST);
                                } catch (Exception $E) {
                                    /**
                                     * @var FitBitException $E
                                     */
                                    echo $user . "\n";
                                    echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                                    print_r($E);
                                    die();
                                }
                            } elseif ($newGoal > 0) {
                                nxr("  Returned floor target was " . $usr_goals->floors . " which is right for this week goal of " . $newGoal);
                            }
                        }
                    }

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array("AND" => array('user' => $user, 'date' => $targetDate)))) {
                        /** @noinspection PhpUndefinedFieldInspection */
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array(
                            'caloriesOut'   => (String)$usr_goals->caloriesOut,
                            'distance'      => (String)$usr_goals->distance,
                            'floors'        => (String)$usr_goals->floors,
                            'activeMinutes' => (String)$usr_goals->activeMinutes,
                            'steps'         => (String)$usr_goals->steps,
                            'syncd'         => date("Y-m-d H:i:s")
                        ), array("AND" => array('user' => $user, 'date' => $targetDate)));
                    } else {
                        /** @noinspection PhpUndefinedFieldInspection */
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array(
                            'user'          => $user,
                            'date'          => $targetDate,
                            'caloriesOut'   => (String)$usr_goals->caloriesOut,
                            'distance'      => (String)$usr_goals->distance,
                            'floors'        => (String)$usr_goals->floors,
                            'activeMinutes' => (String)$usr_goals->activeMinutes,
                            'steps'         => (String)$usr_goals->steps,
                            'syncd'         => date("Y-m-d H:i:s")
                        ));
                    }

                    if (!$fallback) $this->api_setLastCleanrun("goals", $user, new DateTime($targetDate));
                }

                if ($currentDate->format("Y-m-d") == $targetDate)
                    $this->api_setLastrun("goals", $user);
            }

            return $userGoals;
        }

        private function thisWeeksGoal($user, $string) {
            $lastMonday = date('Y-m-d', strtotime('last monday -7 days'));
            $oneWeek = date('Y-m-d', strtotime($lastMonday . ' +6 days'));
            $plusTargetSteps = -1;

            if ($string == "steps") {
                $improvment = $this->getAppClass()->getSetting("improvments_" . $user . "_steps", 10000);
                if ($improvment > 0) {
                    $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'steps',
                        array("AND" => array(
                            "user"     => $user,
                            "date[<=]" => $oneWeek,
                            "date[>=]" => $lastMonday
                        ), "ORDER"  => "date DESC", "LIMIT" => 7));

                    $totalSteps = 0;
                    foreach ($dbSteps as $dbStep) {
                        $totalSteps = $totalSteps + $dbStep;
                    }

                    $newTargetSteps = round($totalSteps / count($dbSteps), 0);
                    if ($newTargetSteps < $this->getAppClass()->getSetting("improvments_" . $user . "_steps_max", 10000)) {
                        $plusTargetSteps = $newTargetSteps + round($newTargetSteps * ($this->getAppClass()->getSetting("improvments_" . $user . "_steps", 10) / 100), 0);
                    } else {
                        $plusTargetSteps = $this->getAppClass()->getSetting("improvments_" . $user . "_steps_max", 10000);
                    }
                }
            } elseif ($string == "floors") {
                $improvment = $this->getAppClass()->getSetting("improvments_" . $user . "_floors", 10);
                if ($improvment > 0) {
                    $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'floors',
                        array("AND" => array(
                            "user"     => $user,
                            "date[<=]" => $oneWeek,
                            "date[>=]" => $lastMonday
                        ), "ORDER"  => "date DESC", "LIMIT" => 7));

                    $totalSteps = 0;
                    foreach ($dbSteps as $dbStep) {
                        $totalSteps = $totalSteps + $dbStep;
                    }

                    $newTargetSteps = round($totalSteps / count($dbSteps), 0);
                    if ($newTargetSteps < $this->getAppClass()->getSetting("improvments_" . $user . "_floors_max", 10)) {
                        $plusTargetSteps = $newTargetSteps + round($newTargetSteps * ($this->getAppClass()->getSetting("improvments_" . $user . "_floors", 10) / 100), 0);
                    } else {
                        $plusTargetSteps = $this->getAppClass()->getSetting("improvments_" . $user . "_floors_max", 10);
                    }
                }
            }

            return $plusTargetSteps;
        }

        /**
         * @param $user
         * @param $trigger
         * @param bool $force
         */
        private function api_pull_time_series($user, $trigger, $force = FALSE) {
            if ($force || $this->api_isCooled($trigger, $user)) {
                $currentDate = new DateTime();

                $lastrun = $this->api_getLastCleanrun($trigger, $user);
                $daysSince = (strtotime($currentDate->format("Y-m-d")) - strtotime($lastrun->format("l jS M Y"))) / (60 * 60 * 24);

                nxr("  Last download: $daysSince days ago. ");

                if ($daysSince < 2) {
                    $daysSince = "1d";
                } elseif ($daysSince < 8) {
                    $daysSince = "7d";
                } elseif ($daysSince < 30) {
                    $daysSince = "30d";
                } elseif ($daysSince < 90) {
                    $daysSince = "3m";
                } elseif ($daysSince < 180) {
                    $daysSince = "6m";
                } else {
                    $daysSince = "1y";
                }

                nxr("  Requesting $trigger data for $daysSince days");
                $this->api_pull_time_series_by_trigger($user, $trigger, $daysSince);
                $this->api_setLastrun($trigger, $user);
            } else {
                echo "   Error " . $trigger . ": " . $this->getAppClass()->lookupErrorCode(-143) . "\n";
            }
        }

        /**
         * @param $user
         * @param $trigger
         * @param $daysSince
         */
        private function api_pull_time_series_by_trigger($user, $trigger, $daysSince) {
            switch ($trigger) {
                case "steps":
                case "distance":
                case "floors":
                case "elevation":
                case "caloriesOut":
                    $this->api_pull_time_series_for_steps($user, $trigger, $daysSince);
                    break;
                case "minutesVeryActive":
                case "minutesSedentary":
                case "minutesLightlyActive":
                case "minutesFairlyActive":
                    $this->api_pull_time_series_for_activity($user, $trigger, $daysSince);
                    break;
            }
        }

        /**
         * @param $user
         * @param $trigger
         * @param $daysSince
         */
        private function api_pull_time_series_for_steps($user, $trigger, $daysSince) {
            nxr('   Get ' . $this->getAppClass()->supportedApi($trigger) . ' records');

            $currentDate = new DateTime ('now');

            try {
                $userTimeSeries = $this->getLibrary()->getTimeSeries($trigger, $currentDate, $daysSince);
            } catch (Exception $E) {
                /**
                 * @var FitBitException $E
                 */
                echo $user . "\n";
                echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                print_r($E);
                die();
            }

            if (isset($userTimeSeries) and is_array($userTimeSeries)) {
                foreach ($userTimeSeries as $steps) {
                    if ($steps->value == 0) {
                        nxr("   No recorded data for " . $steps->dateTime);
                    } else {
                        nxr("   " . $this->getAppClass()->supportedApi($trigger) . " record for " . $steps->dateTime . " is " . $steps->value);
                    }

                    if ($steps->value > 0) $this->api_setLastCleanrun($trigger, $user, new DateTime ($steps->dateTime));

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array("AND" => array('user' => $user, 'date' => (String)$steps->dateTime)))) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array(
                            $trigger => (String)$steps->value,
                            'syncd'  => $currentDate->format('Y-m-d H:m:s')
                        ), array("AND" => array('user' => $user, 'date' => (String)$steps->dateTime)));
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array(
                            'user'   => $user,
                            'date'   => (String)$steps->dateTime,
                            $trigger => (String)$steps->value,
                            'syncd'  => $currentDate->format('Y-m-d H:m:s')
                        ));
                    }

                }
            }
        }

        /**
         * @param $user
         * @param $trigger
         * @param $daysSince
         */
        private function api_pull_time_series_for_activity($user, $trigger, $daysSince) {
            switch ($trigger) {
                case "minutesVeryActive":
                    $databaseColumn = "veryactive";
                    break;
                case "minutesSedentary":
                    $databaseColumn = "sedentary";
                    break;
                case "minutesLightlyActive":
                    $databaseColumn = "lightlyactive";
                    break;
                case "minutesFairlyActive":
                    $databaseColumn = "fairlyactive";
                    break;
                default:
                    return FALSE;
            }

            nxr('   Get ' . $this->getAppClass()->supportedApi($trigger) . ' records');

            $currentDate = new DateTime ('now');

            try {
                $userTimeSeries = $this->getLibrary()->getTimeSeries($trigger, $currentDate, $daysSince);
            } catch (Exception $E) {
                /**
                 * @var FitBitException $E
                 */
                echo $user . "\n";
                echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                print_r($E);
                die();
            }

            if (isset($userTimeSeries) and is_array($userTimeSeries)) {
                foreach ($userTimeSeries as $series) {
                    nxr("   " . $this->getAppClass()->supportedApi($trigger) . " " . $series->dateTime . " is " . $series->value);

                    if ($series->value > 0) $this->api_setLastCleanrun($trigger, $user, new DateTime ($series->dateTime));

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity", array("AND" => array('user' => $user, 'date' => (String)$series->dateTime)))) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity", array(
                            $databaseColumn => (String)$series->value,
                            'syncd'         => $currentDate->format('Y-m-d H:m:s')
                        ), array("AND" => array('user' => $user, 'date' => (String)$series->dateTime)));
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity", array(
                            'user'          => $user,
                            'date'          => (String)$series->dateTime,
                            $databaseColumn => (String)$series->value,
                            'syncd'         => $currentDate->format('Y-m-d H:m:s')
                        ));
                    }
                }
            }

            return TRUE;
        }

        /**
         * @param $user
         * @param $targetDate
         * @return bool
         */
        private function api_pull_activity_log($user, $targetDate) {
            $targetDateTime = new DateTime ($targetDate);
            try {
                $userActivityLog = $this->getLibrary()->getActivities($targetDateTime);
            } catch (Exception $E) {
                /**
                 * @var FitBitException $E
                 */
                echo $user . "\n";
                echo "Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user) . "\n\n";
                print_r($E);
                die();
            }

            if (isset($userActivityLog) and is_object($userActivityLog) and is_array($userActivityLog->activities)) {
                foreach ($userActivityLog->activities as $activity) {
                    if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity_log", array("AND" => array("user"       => $user,
                                                                                                                                                                    "logId"      => (String)$activity->logId,
                                                                                                                                                                    "activityId" => (String)$activity->activityId,
                                                                                                                                                                    "startDate"  => (String)$activity->startDate,
                                                                                                                                                                    "startTime"  => (String)$activity->startTime)))
                    ) {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity_log", array(
                            "activityId"         => (String)$activity->activityId,
                            "activityParentId"   => (String)$activity->activityParentId,
                            "activityParentName" => (String)$activity->activityParentName,
                            "calories"           => (String)$activity->calories,
                            "description"        => (String)$activity->description,
                            "duration"           => (String)$activity->duration,
                            "hasStartTime"       => (String)$activity->hasStartTime,
                            "isFavorite"         => (String)$activity->isFavorite,
                            "logId"              => (String)$activity->logId,
                            "name"               => (String)$activity->name,
                            "startDate"          => (String)$activity->startDate,
                            "startTime"          => (String)$activity->startTime,
                            "user"               => $user,
                            "date"               => $targetDate
                        ));
                    }

                    $this->api_setLastCleanrun("activity_log", $user, new DateTime ($targetDate));
                }
            }

            return TRUE;
        }

        /**
         * @param boolean $forceSync
         */
        public function setForceSync($forceSync) {
            $this->forceSync = $forceSync;
        }

        public function subscribeUser($user) {
            if ($this->getAppClass()->isUser($user)) {
                if (!$this->getAppClass()->getFitbitapi()->isAuthorised()) {
                    $this->getAppClass()->getFitbitapi()->oAuthorise($user);
                }
                $this->getAppClass()->getFitbitapi()->getLibrary()->addSubscription(1);
                print_r($this->getAppClass()->getFitbitapi()->getLibrary()->getSubscriptions());
            }
        }

    }
