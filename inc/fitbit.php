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
     * @var FitBitPHP
     */
    protected $fitbitapi;

    /**
     * @var NxFitbit
     */
    protected $AppClass;

    /**
     * @param $fitbitApp
     * @param $consumer_key
     * @param $consumer_secret
     * @param int $debug
     * @param null $user_agent
     * @param string $response_format
     */
    public function __construct($fitbitApp, $consumer_key, $consumer_secret, $debug = 1, $user_agent = null, $response_format = 'xml')
    {
        $this->setAppClass($fitbitApp);

        require_once(dirname(__FILE__) . "/../library/fitbitphp.php");
        $this->setLibrary(new FitBitPHP($consumer_key, $consumer_secret, $debug, $user_agent, $response_format));
    }

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
     * @param $trigger
     * @param bool $return
     * @return mixed|null|SimpleXMLElement|string
     */
    public function pull($user, $trigger, $return = false)
    {
        $xml = null;

        if ($this->getAppClass()->isUser($user)) {
            if (!$this->isAuthorised()) {
                $this->oAuthorise($user);
            }

            /*if ($trigger == "all" || $trigger == "profile") {
                $pull = $this->api_pull_profile($user);
                if ($this->isApiError($pull)) {
                    echo "Error profile: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                }
            }*/

            /*if ($trigger == "all" || $trigger == "devices") {
                $pull = $this->api_pull_devices($user);
                if ($this->isApiError($pull)) {
                    echo "Error devices: " . $this->getAppClass()->lookupErrorCode($pull) . "\n";
                }
            }*/

            if ($trigger == "all" || $trigger == "badges") {
                if ($this->isApiError($this->api_pull_badges($user))) {
                    echo "Error badges: " . $this->getAppClass()->lookupErrorCode($xml);
                }
            }

            /*if ($trigger == "all" || $trigger == "leaderboard") {
                if ($this->isApiError($this->api_pull_profile($user))) {
                    echo "Error profile: " . $this->getAppClass()->lookupErrorCode($xml);
                }
            }*/

            /*if ($trigger == "all" || $trigger == "foods") {
                //nx_fitbit_api_goals_calories($username, $fitbit);

                if ($this->isApiError($this->api_pull_profile($user))) {
                    echo "Error profile: " . $this->getAppClass()->lookupErrorCode($xml);
                }
            }*/

            /*if ($trigger == "all" || $trigger == "sleep") {
                if ($this->isApiError($this->api_pull_profile($user))) {
                    echo "Error profile: " . $this->getAppClass()->lookupErrorCode($xml);
                }
            }*/

            /*if ($trigger == "all" || $trigger == "body") {
                if ($this->isApiError($this->api_pull_profile($user))) {
                    echo "Error profile: " . $this->getAppClass()->lookupErrorCode($xml);
                }
            }*/

            /*if ($trigger == "all" || $trigger == "heart") {
                if ($this->isApiError($this->api_pull_profile($user))) {
                    echo "Error profile: " . $this->getAppClass()->lookupErrorCode($xml);
                }
            }*/

            /*if ($trigger == "all" || $trigger == "water" || $trigger == "foods") {
                if ($this->isApiError($this->api_pull_profile($user))) {
                    echo "Error profile: " . $this->getAppClass()->lookupErrorCode($xml);
                }
            }*/

            /*if ($trigger == "all" || $trigger == "goals") {
                if ($this->isApiError($this->api_pull_profile($user))) {
                    echo "Error profile: " . $this->getAppClass()->lookupErrorCode($xml);
                }
            }*/

            /*if ($trigger == "all" || $trigger == "activities") {
                if ($this->isApiError($this->api_pull_profile($user))) {
                    echo "Error profile: " . $this->getAppClass()->lookupErrorCode($xml);
                }
            }*/
        }

        if ($return) {
            return $xml;
        } else {
            return true;
        }
    }

    /**
     * @param $xml
     * @return bool
     */
    private function isApiError($xml) {
        if (is_numeric($xml) AND $xml < 0) {
            return true;
        } else {
            return false;
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
            if(file_exists($badgeFolder) AND is_writable($badgeFolder)) {
                try {
                    $userBadges = $this->getLibrary()->getBadges();
                } catch (Exception $E) {
                    echo "<pre>";
                    echo $user . "\n\n";
                    print_r($E);
                    echo "</pre>";
                    return null;
                }

                if (isset($userBadges)) {
                    foreach ($userBadges->badges->badge as $badge) {
                        if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null, false) . "bages", array(
                            "AND" => array(
                                "badgeType" => (String)$badge->badgeType,
                                "value" => (String)$badge->value
                            )
                        ))) {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null, false) . "bages", array(
                                'badgeType' => (String)$badge->badgeType,
                                'value' => (String)$badge->value,
                                'image' => basename((String)$badge->image50px),
                                'badgeGradientEndColor' => (String)$badge->badgeGradientEndColor,
                                'badgeGradientStartColor' => (String)$badge->badgeGradientStartColor,
                                'earnedMessage' => (String)$badge->earnedMessage,
                                'marketingDescription' => (String)$badge->marketingDescription,
                                'name' => (String)$badge->name
                            ));
                        }

                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null, false) . "lnk_badge2usr", array("AND" => array(
                            "user" => $user,
                            "badgeType" => (String)$badge->badgeType,
                            "value" => (String)$badge->value
                        )))) {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", null, false) . "lnk_badge2usr", array(
                                'dateTime' => (String)$badge->dateTime,
                                'timesAchieved' => (String)$badge->timesAchieved
                            ));
                        } else {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null, false) . "lnk_badge2usr", array(
                                'user' => $user,
                                'badgeType' => (String)$badge->badgeType,
                                'dateTime' => (String)$badge->dateTime,
                                'timesAchieved' => (String)$badge->timesAchieved,
                                'value' => (String)$badge->value,
                                'unit' => (String)$badge->unit
                            ));
                        }

                        $imageFileName = basename((String)$badge->image50px);
                        if(!file_exists($badgeFolder . "/" . $imageFileName)) {
                            file_put_contents($badgeFolder . "/" . $imageFileName, fopen((String)$badge->image50px, 'r'));
                        }

                        if(!file_exists($badgeFolder . "/75px")) { mkdir($badgeFolder . "/75px", 0755, true); }
                        if(!file_exists($badgeFolder . "/75px/" . $imageFileName)) {
                            file_put_contents($badgeFolder . "/75px/" . $imageFileName, fopen((String)$badge->image75px, 'r'));
                        }

                        if(!file_exists($badgeFolder . "/100px")) { mkdir($badgeFolder . "/100px", 0755, true); }
                        if(!file_exists($badgeFolder . "/100px/" . $imageFileName)) {
                            file_put_contents($badgeFolder . "/100px/" . $imageFileName, fopen((String)$badge->image100px, 'r'));
                        }

                        if(!file_exists($badgeFolder . "/125px")) { mkdir($badgeFolder . "/125px", 0755, true); }
                        if(!file_exists($badgeFolder . "/125px/" . $imageFileName)) {
                            file_put_contents($badgeFolder . "/125px/" . $imageFileName, fopen((String)$badge->image125px, 'r'));
                        }

                        if(!file_exists($badgeFolder . "/300px")) { mkdir($badgeFolder . "/300px", 0755, true); }
                        if(!file_exists($badgeFolder . "/300px/" . $imageFileName)) {
                            file_put_contents($badgeFolder . "/300px/" . $imageFileName, fopen((String)$badge->image300px, 'r'));
                        }

                    }
                }

                $this->api_setLastrun("badges", $user, NULL, true);

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
     * Download information about devices associated with the users account. This is then stored in the database
     * @param $user
     * @return mixed|null|SimpleXMLElement|string
     */
    private function api_pull_devices($user) {
        if ($this->api_isCooled("devices", $user)) {
            try {
                $userDevices = $this->getLibrary()->getDevices();
            } catch (Exception $E) {
                echo "<pre>";
                echo $user . "\n\n";
                print_r($E);
                echo "</pre>";
                return null;
            }

            foreach ($userDevices->device as $device) {
                if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null, false) . "devices", array("AND" => array("id" => (String)$device->id)))) {
                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", null, false) . "devices", array(
                        'lastSyncTime' => (String)$device->lastSyncTime,
                        'battery' => (String)$device->battery
                    ), array("id" => (String)$device->id));
                } else {
                    echo $this->getAppClass()->getDatabase()->last_query();

                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null, false) . "devices", array(
                        'id' => (String)$device->id,
                        'deviceVersion' => (String)$device->deviceVersion,
                        'type' => (String)$device->type,
                        'lastSyncTime' => (String)$device->lastSyncTime,
                        'battery' => (String)$device->battery
                    ));
                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null, false) . "dev2usr", array(
                        'user' => $user,
                        'device' => (String)$device->id
                    ));
                }
            }

            $this->api_setLastrun("devices", $user, NULL, true);

            return $userDevices;
        } else {
            return "-143";
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
                echo "<pre>";
                echo $user . "\n\n";
                print_r($E);
                echo "</pre>";
                return null;
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

            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", null, false) . "users", array(
                "avatar" => (String)$userProfile->user->avatar150,
                "city" => (String)$userProfile->user->city,
                "country" => (String)$userProfile->user->country,
                "name" => (String)$userProfile->user->fullName,
                "gender" => (String)$userProfile->user->gender,
                "height" => (String)$userProfile->user->height,
                "seen" => (String)$userProfile->user->memberSince,
                "stride_running" => (String)$userProfile->user->strideLengthRunning,
                "stride_walking" => (String)$userProfile->user->strideLengthWalking
            ), array("fuid" => $user));

            if(!file_exists(dirname(__FILE__) . "/../images/avatars/" . $user . ".jpg")) {
                file_put_contents(dirname(__FILE__) . "/../images/avatars/" . $user . ".jpg", fopen((String)$userProfile->user->avatar150, 'r'));
            }

            $this->api_setLastrun("profile", $user, NULL, true);

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
    private function api_isCooled($trigger, $user, $reset = false) {
        $currentDate = new DateTime ('now');
        $lastRun     = $this->api_getLastrun($trigger, $user, $reset);
        if ($lastRun->format("U") < $currentDate->format("U") - $this->getAppClass()->getSetting('nx_fitbit_ds_' . $trigger . '_timeout', 5400)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $activity
     * @param $username
     * @param bool $reset
     * @return DateTime
     */
    private function api_getLastrun($activity, $username, $reset = false) {
        if ($reset)
            return new DateTime ("1970-01-01");

        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null, false) . "runlog", array("AND" => array("user" => $username, "activity" => $activity)))) {
            return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, false) . "runlog", "date", array("AND" => array("user" => $username, "activity" => $activity))));
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
    private function api_setLastrun($activity, $username, $cron_delay = NULL, $clean = false) {
        if (is_null($cron_delay)) {
            $cron_delay_holder = 'nx_fitbit_ds_' . $activity . '_timeout';
            $cron_delay        = $this->getAppClass()->getSetting($cron_delay_holder, 5400);
        }

        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null, false) . "runlog", array("AND" => array("user" => $username, "activity" => $activity)))) {
            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", null, false) . "runlog", array(
                "date" => date("Y-m-d H:i:s"),
                "cooldown" => date("Y-m-d H:i:s", time() + $cron_delay)
            ), array("AND" => array("user" => $username, "activity" => $activity)));
        } else {
            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null, false) . "runlog", array(
                "user" => $username,
                "activity" => $activity,
                "date" => date("Y-m-d H:i:s"),
                "lastrun" => date("Y-m-d H:i:s"),
                "cooldown" => date("Y-m-d H:i:s", time() + $cron_delay)
            ));
        }
    }

    /**
     * @param $user
     * @return array
     */
    private function get_oauth($user)
    {
        $userArray = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null, false) . "users", array('token', 'secret'), array("fuid" => $user));
        if (is_array($userArray)) {
            return $userArray;
        } else {
            nxr('User ' . $user . ' does not exist, unable to continue.');
            exit;
        }
    }

    /**
     * @deprecated Use getLibrary() instead
     * @return FitBitPHP
     */
    public function getFitbitapi()
    {
        return $this->getLibrary();
    }

    /**
     * @deprecated Use setLibrary() instead
     * @param FitBitPHP $fitbitapi
     */
    public function setFitbitapi($fitbitapi)
    {
        $this->setLibrary($fitbitapi);
    }

    /**
     * @return FitBitPHP
     */
    public function getLibrary()
    {
        return $this->fitbitapi;
    }

    /**
     * @param FitBitPHP $fitbitapi
     */
    public function setLibrary($fitbitapi)
    {
        $this->fitbitapi = $fitbitapi;
    }

    /**
     * @return boolean
     */
    private function isAuthorised()
    {
        if ($this->getLibrary()->getOAuthToken() != "" AND $this->getLibrary()->getOAuthSecret() != "") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return NxFitbit
     */
    private function getAppClass()
    {
        return $this->AppClass;
    }

    /**
     * @param $AppClass
     */
    private function setAppClass($AppClass)
    {
        $this->AppClass = $AppClass;
    }

}