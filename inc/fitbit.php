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

    public function __construct($consumer_key, $consumer_secret, $debug = 1, $user_agent = null, $response_format = 'xml')
    {
        require_once(dirname(__FILE__) . "/../library/fitbitphp.php");
        $this->setFitbitapi(new FitBitPHP($consumer_key, $consumer_secret, $debug, $user_agent, $response_format));

    }

    public function pull($user, $trigger)
    {

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