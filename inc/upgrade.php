<?php

class Upgrade {

    /**
     * @var NxFitbit
     */
    protected $AppClass;

    /**
     * @var String
     */
    protected $UserID;

    public function __construct($userFid) {
        require_once(dirname(__FILE__) . "/app.php");
        $this->setAppClass(new NxFitbit());
        $this->setUserID($userFid);
    }

    /**
     * @return NxFitbit
     */
    public function getAppClass() {
        return $this->AppClass;
    }

    /**
     * @param NxFitbit $AppClass
     */
    public function setAppClass($AppClass) {
        $this->AppClass = $AppClass;
    }

    /**
     * @return String
     */
    public function getUserID() {
        return $this->UserID;
    }

    /**
     * @param String $UserID
     */
    public function setUserID($UserID) {
        $this->UserID = $UserID;
    }

    public function calcUserTrend() {
        $dbWeight = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body",
            array('date', 'weight', 'fat'),
            array("AND" => array("user" => $this->getUserID()),
            "ORDER"  => "date ASC"));

        $iteration = 0;
        foreach ($dbWeight as $weight) {
            if ($iteration == 0) {
                $dbWeight[$iteration]['weightAvg'] = $dbWeight[$iteration]['weight'];
                $dbWeight[$iteration]['fatAvg'] = $dbWeight[$iteration]['fat'];
            } else {
                $dbWeight[$iteration]['weightAvg'] = round(($dbWeight[$iteration]['weight'] - $dbWeight[$iteration - 1]['weightAvg']) / 10, 1, PHP_ROUND_HALF_UP) + $dbWeight[$iteration - 1]['weightAvg'];
                $dbWeight[$iteration]['fatAvg'] = round(($dbWeight[$iteration]['fat'] - $dbWeight[$iteration - 1]['fatAvg']) / 10, 1, PHP_ROUND_HALF_UP) + $dbWeight[$iteration - 1]['fatAvg'];
            }

            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body",
                array('weightAvg' => $dbWeight[$iteration]['weightAvg'], 'fatAvg' => $dbWeight[$iteration]['fatAvg']),
                array("AND" => array("user" => $this->getUserID(), "date" => $dbWeight[$iteration]['date'])));

            $iteration = $iteration + 1;
        }

        echo "\n";
    }

    /**
     * @throws FitBitException
     */
    public function subscribeUser() {
        $this->getAppClass()->getFitbitapi()->subscribeUser($this->getUserID());
    }

}