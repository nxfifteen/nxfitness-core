<?php
/**
 * Created by PhpStorm.
 * User: nxad
 * Date: 19/02/15
 * Time: 21:14
 */

class dataReturn {

    /**
     * @var NxFitbit
     */
    protected $AppClass;

    /**
     * @var String
     */
    protected $UserID;

    /**
     * @var String
     */
    protected $paramPeriod;

    /**
     * @var tracking
     */
    protected $tracking;

    /**
     * @var String
     */
    protected $paramDate;

    public function __construct($userFid) {
        require_once(dirname(__FILE__) . "/app.php");
        $this->setAppClass(new NxFitbit());
        $this->setUserID($userFid);

        require_once(dirname(__FILE__) . "/tracking.php");
        $this->setTracking(new tracking($this->getAppClass()->getSetting("trackingId")), "http://localhost");
    }

    public function isUser() {
        return $this->getAppClass()->isUser((String)$this->getUserID());
    }

    /**
     * @param NxFitbit $paramClass
     */
    private function setAppClass($paramClass) {
        $this->AppClass = $paramClass;
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

    /**
     * @return NxFitbit
     */
    private function getAppClass() {
        return $this->AppClass;
    }

    /**
     * @return String
     */
    public function getParamDate() {
        if (is_null($this->paramDate) || $this->paramDate == "latest") {
            $this->paramDate = date('Y-m-d');
        }

        return $this->paramDate;
    }

    /**
     * @param String $paramDate
     */
    public function setParamDate($paramDate) {
        $this->paramDate = $paramDate;
    }

    /**
     * @return String
     */
    public function getParamPeriod() {
        if (is_null($this->paramPeriod)) {
            $this->paramPeriod = "single";
        }
        return $this->paramPeriod;
    }

    /**
     * @param String $paramPeriod
     */
    public function setParamPeriod($paramPeriod) {
        $this->paramPeriod = $paramPeriod;
    }

    public function dbWhere($limit = 1) {
        if ($this->getParamPeriod() == "single") {
            return array("AND" => array("user" => $this->getUserID(), "date" => $this->getParamDate()), "LIMIT" => $limit);
        } else if (substr($this->getParamPeriod(), 0, strlen("last")) === "last") {
            $days = $this->getParamPeriod();
            $days = str_ireplace("last", "", $days);
            $then = date('Y-m-d', strtotime($this->getParamDate() . " -".$days." day"));
            return array("AND" => array("user" => $this->getUserID(), "date[<=]" => $this->getParamDate(), "date[>=]" => $then), "ORDER" => "date DESC", "LIMIT" => $days);
        } else {
            return array("user" => $this->getUserID(), "ORDER" => "date DESC", "LIMIT" => $limit);
        }
    }

    /**
     * @param $get
     * @return array
     */
    public function returnUserRecords($get) {

        if (array_key_exists("period", $get)) {
            $this->setParamPeriod($get['period']);
        }

        if (array_key_exists("date", $get)) {
            $this->setParamDate($get['date']);
        }

        $functionName = 'returnUserRecord' . $get['data'];
        if (method_exists($this,$functionName)) {
            $resultsArray = array("error" => "false", "user" => $this->getUserID(), "data" => $get['data'], "period" => $this->getParamPeriod(), "date" => $this->getParamDate());
            $resultsArray['results'] = $this->$functionName();

            $this->getTracking()->endEvent('JSON/' . $this->getUserID() . '/' . $this->getParamDate() . '/' . $get['data']);

            return $resultsArray;
        } else {
            $this->getTracking()->track("Error", 103);
            $this->getTracking()->endEvent('Error/' . $this->getUserID() . '/' . $this->getParamDate() . '/' . $get['data']);

            return array("error" => "true", "code" => 103, "msg" => "Unknown dataset");
        }
    }

    public function returnUserRecordFood() {
        //TODO Added support for multi record returned

        $dbFoodLog = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood",
            array('meal','calories'),
            $this->dbWhere(4));

        if (count($dbFoodLog) > 0) {
            $total = 0;
            foreach ($dbFoodLog as $meal) {
                $total = $total + $meal['calories'];
            }


            $dbFoodGoal = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "goals_calories",
                array('calories'),
                $this->dbWhere());

            $this->getTracking()->track("JSON Get", $this->getUserID(), "Food");
            $this->getTracking()->track("JSON Goal", $this->getUserID(), "Food");

            return array('goal' => $dbFoodGoal[0]['calories'], 'total' => $total, "meals" => $dbFoodLog);
        } else {
            return array("error" => "true", "code" => 104, "msg" => "No results for given date");
        }
    }

    public function returnUserRecordWater() {
        $dbWater = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water",
            array('date','liquid'),
            $this->dbWhere());

        $dbWater[0]['liquid'] = (String)round($dbWater[0]['liquid'], 2);
        $dbWater[0]['goal'] = $this->getAppClass()->getSetting("usr_goal_water_" . $this->getUserID(), '200');

        $this->getTracking()->track("JSON Get", $this->getUserID(), "Water");
        $this->getTracking()->track("JSON Goal", $this->getUserID(), "Water");

        return $dbWater;
    }

    public function returnUserRecordSteps() {
        //TODO Added support for multi record returned

        $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
            array('distance','floors','steps'),
            $this->dbWhere());

        if (count($dbSteps) > 0) {
            $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals",
                array('distance','floors','steps'),
                $this->dbWhere());

            $dbGoals[0]['distance'] = (String)round($dbGoals[0]['distance'], 2);
            $dbSteps[0]['distance'] = (String)round($dbSteps[0]['distance'], 2);

            $this->getTracking()->track("JSON Get", $this->getUserID(), "Steps");
            $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");

            return array('recorded' => $dbSteps[0], 'goal' => $dbGoals[0]);
        } else {
            return array("error" => "true", "code" => 104, "msg" => "No results for given date");
        }
    }

    public function returnUserRecordStepsGoal() {
        $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals",
            array('date','distance','floors','steps'),
            $this->dbWhere());

        $dbGoals[0]['distance'] = (String)round($dbGoals[0]['distance'], 2);

        $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");

        return $dbGoals;
    }

    public function returnUserRecordBody() {
        $return = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body",
            array('date','weight','weightGoal','fat','fatGoal','bmi','calf','bicep','chest','forearm','hips','neck','thigh','waist'),
            $this->dbWhere());

        return $return;
    }

    /**
     * @return tracking
     */
    public function getTracking() {
        return $this->tracking;
    }

    /**
     * @param tracking $tracking
     */
    public function setTracking($tracking) {
        $this->tracking = $tracking;
    }
} 