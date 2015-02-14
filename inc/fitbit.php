<?php
/**
 * Created by PhpStorm.
 * User: nxad
 * Date: 14/02/15
 * Time: 13:42
 */

class fitbit {
    /**
     * @var FitBitPHP
     */
    protected $fitbitapi;

    public function __construct()
    {
        require_once(dirname(__FILE__) . "/../library/fitbitphp.php");
        $this->setFitbitapi(new FitBitPHP(
            $this->getSetting("fitbit_consumer_key", null, false),
            $this->getSetting("fitbit_consumer_secret", null, false),
            $this->getSetting("fitbit_debug", false, false),
            $this->getSetting("fitbit_user_agent", null, false),
            $this->getSetting("fitbit_response_format", "xml", false)));

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
} 