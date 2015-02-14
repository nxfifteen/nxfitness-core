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
     * @var bool
     */
    protected $authorised;

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
        $this->setFitbitapi(new FitBitPHP($consumer_key, $consumer_secret, $debug, $user_agent, $response_format));
        $this->setAuthorised(false);
    }

    public function pull($user, $trigger)
    {
        nxr("API request for $user getting $trigger");
        if ($this->getAppClass()->isUser($user)) {
            if (!$this->isAuthorised()) {
                $oAuth = $this->get_oauth($user);
                if (!$oAuth OR !is_array($oAuth) OR $oAuth['token'] == "" OR $oAuth['secret'] == "") {
                    nxr('Unable to setup the user OAuth credentials. Have they authorised this app?');
                    exit;
                }
                $this->getFitbitapi()->setOAuthDetails($oAuth['token'], $oAuth['secret']);
                $this->setAuthorised(true);
            }
        }
    }

    private function get_oauth($user)
    {
        $userArray = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null, false) . "users", ['token', 'secret'], ["fuid" => $user]);
        if (is_array($userArray)) {
            return $userArray;
        } else {
            nxr('User ' . $user . ' does not exist, unable to continue.');
            exit;
        }
    }

    /**
     * @return FitBitPHP
     */
    public function getFitbitapi()
    {
        return $this->fitbitapi;
    }

    /**
     * @param FitBitPHP $fitbitapi
     */
    public function setFitbitapi($fitbitapi)
    {
        $this->fitbitapi = $fitbitapi;
    }

    /**
     * @return boolean
     */
    private function isAuthorised()
    {
        return $this->authorised;
    }

    /**
     * @param boolean $authorised
     */
    private function setAuthorised($authorised)
    {
        $this->authorised = $authorised;
    }

    /**
     * @return NxFitbit
     */
    private function getAppClass()
    {
        return $this->AppClass;
    }

    /**
     * @param NxFitbit $fitbitApp
     */
    private function setAppClass($AppClass)
    {
        $this->AppClass = $AppClass;
    }

}