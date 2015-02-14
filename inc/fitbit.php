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

        //nxr("API request for $user getting $trigger");
        if ($this->getAppClass()->isUser($user)) {
            if (!$this->isAuthorised()) {
                $this->oAuthorise($user);
            }

            if ($trigger == "all" || $trigger == "profile") {
                $xml = $this->api_pull_profile($user);
            }
        }

        if ($return) {
            return $xml;
        } else {
            return true;
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
                "avatar" => $userProfile->user->avatar150,
                "city" => $userProfile->user->city,
                "country" => $userProfile->user->country,
                "name" => $userProfile->user->fullName,
                "gender" => $userProfile->user->gender,
                "height" => $userProfile->user->height,
                "seen" => $userProfile->user->memberSince,
                "stride_running" => $userProfile->user->strideLengthRunning,
                "stride_walking" => $userProfile->user->strideLengthWalking,
            ), array("fuid" => $user));

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