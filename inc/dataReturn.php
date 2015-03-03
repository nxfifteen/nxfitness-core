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

        /**
         * @param $userFid
         */
        public function __construct($userFid) {
            require_once(dirname(__FILE__) . "/app.php");
            $this->setAppClass(new NxFitbit());
            $this->setUserID($userFid);

            if (is_array($_SERVER) && array_key_exists("SERVER_NAME", $_SERVER)) {
                require_once(dirname(__FILE__) . "/tracking.php");
                $this->setTracking(new tracking($this->getAppClass()->getSetting("trackingId"), $this->getAppClass()->getSetting("trackingPath")));
            }
        }

        /**
         * @param NxFitbit $paramClass
         */
        private function setAppClass($paramClass) {
            $this->AppClass = $paramClass;
        }

        /**
         * @return NxFitbit
         */
        private function getAppClass() {
            return $this->AppClass;
        }

        /**
         * @return bool
         */
        public function isUser() {
            return $this->getAppClass()->isUser((String)$this->getUserID());
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
            if (method_exists($this, $functionName)) {
                $dbUserName = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", 'name', array("fuid" => $this->getUserID()));
                $resultsArray = array("error" => "false", "user" => $this->getUserID(), 'username' => $dbUserName, "data" => $get['data'], "period" => $this->getParamPeriod(), "date" => $this->getParamDate());
                $resultsArray['results'] = $this->$functionName();

                if (is_array($_SERVER) && array_key_exists("SERVER_NAME", $_SERVER))
                    $this->getTracking()->endEvent('JSON/' . $this->getUserID() . '/' . $this->getParamDate() . '/' . $get['data']);

                return $resultsArray;
            } else {
                if (is_array($_SERVER) && array_key_exists("SERVER_NAME", $_SERVER)) {
                    $this->getTracking()->track("Error", 103);
                    $this->getTracking()->endEvent('Error/' . $this->getUserID() . '/' . $this->getParamDate() . '/' . $get['data']);
                }

                return array("error" => "true", "code" => 103, "msg" => "Unknown dataset");
            }
        }

        /**
         * @return String
         */
        public function getParamPeriod() {
            if (is_null($this->paramPeriod)) {
                $this->paramPeriod = "single";
            } else if ($this->paramPeriod == "all") {
                $dbUserFirstSeen = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", 'seen', array("fuid" => $this->getUserID()));
                $now = time(); // or your date as well
                $your_date = strtotime($dbUserFirstSeen);
                $datediff = $now - $your_date;
                $this->paramPeriod = "last" . floor($datediff / (60 * 60 * 24));
            }

            return $this->paramPeriod;
        }

        /**
         * @param String $paramPeriod
         */
        public function setParamPeriod($paramPeriod) {
            $this->paramPeriod = $paramPeriod;
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

        /**
         * @return array
         */
        public function returnUserRecordFood() {
            //TODO Added support for multi record returned

            $dbFoodLog = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood",
                array('meal', 'calories'),
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

        /**
         * @param int $limit
         * @return array
         */
        public function dbWhere($limit = 1) {
            if ($this->getParamPeriod() == "single") {
                return array("AND" => array("user" => $this->getUserID(), "date" => $this->getParamDate()), "LIMIT" => $limit);
            } else if (substr($this->getParamPeriod(), 0, strlen("last")) === "last") {
                $days = $this->getParamPeriod();
                $days = str_ireplace("last", "", $days);
                $then = date('Y-m-d', strtotime($this->getParamDate() . " -" . $days . " day"));

                return array("AND" => array("user" => $this->getUserID(), "date[<=]" => $this->getParamDate(), "date[>=]" => $then), "ORDER" => "date DESC", "LIMIT" => $days);
            } else {
                return array("user" => $this->getUserID(), "ORDER" => "date DESC", "LIMIT" => $limit);
            }
        }

        /**
         * @return array|bool
         */
        public function returnUserRecordWater() {
            $dbWater = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water",
                array('date', 'liquid'),
                $this->dbWhere());

            $dbWater[0]['liquid'] = (String)round($dbWater[0]['liquid'], 2);
            $dbWater[0]['goal'] = $this->getAppClass()->getSetting("usr_goal_water_" . $this->getUserID(), '200');

            if (!is_null($this->getTracking())) {
                $this->getTracking()->track("JSON Get", $this->getUserID(), "Water");
                $this->getTracking()->track("JSON Goal", $this->getUserID(), "Water");
            }

            return $dbWater;
        }

        /**
         * @return array
         */
        public function returnUserRecordFoodDiary() {
            $returnArray = array();

            $where = $this->dbWhere();
            unset($where['AND']['date[<=]']);
            unset($where['AND']['date[>=]']);
            unset($where['LIMIT']);
            $where['AND']['date'] = $this->getParamDate();

            $dbWater = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water", 'liquid', $where);
            /** @var float $dbWater */
            $returnArray['water'] = array("liquid" => (String)round($dbWater, 2), "goal" => $this->getAppClass()->getSetting("usr_goal_water_" . $this->getUserID(), '200'));

            $dbFood = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood",
                array('date', 'meal', 'calories', 'carbs', 'fat', 'fiber', 'protein', 'sodium'),
                $where);

            $returnArray['food'] = array();
            $returnArray['food']['meals'] = array();
            $returnArray['food']['summary'] = array();
            $returnArray['food']['summary']['calories'] = 0;
            $returnArray['food']['summary']['carbs'] = 0;
            $returnArray['food']['summary']['fat'] = 0;
            $returnArray['food']['summary']['fiber'] = 0;
            $returnArray['food']['summary']['protein'] = 0;
            $returnArray['food']['summary']['sodium'] = 0;
            foreach ($dbFood as $meal) {
                $returnArray['food']['meals'][$meal['meal']] = array('calories' => $meal['calories'],
                                                                     'carbs' => $meal['carbs'],
                                                                     'fat' => $meal['fat'],
                                                                     'fiber' => $meal['fiber'],
                                                                     'protein' => $meal['protein'],
                                                                     'sodium' => $meal['sodium']
                );
                $returnArray['food']['summary']['calories'] += $meal['calories'];
                $returnArray['food']['summary']['carbs'] += $meal['carbs'];
                $returnArray['food']['summary']['fat'] += $meal['fat'];
                $returnArray['food']['summary']['fiber'] += $meal['fiber'];
                $returnArray['food']['summary']['protein'] += $meal['protein'];
                $returnArray['food']['summary']['sodium'] += $meal['sodium'];
            }

            return $returnArray;
        }

        /**
         * @return array
         */
        public function returnUserRecordSteps() {
            //TODO Added support for multi record returned

            $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('distance', 'floors', 'steps'),
                $this->dbWhere());

            if (count($dbSteps) > 0) {
                $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals",
                    array('distance', 'floors', 'steps'),
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

        /**
         * @return array|bool
         */
        public function returnUserRecordStepsGoal() {
            $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals",
                array('date', 'distance', 'floors', 'steps'),
                $this->dbWhere());

            $dbGoals[0]['distance'] = (String)round($dbGoals[0]['distance'], 2);

            $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");

            return $dbGoals;
        }

        /**
         * @return array|bool
         */
        public function returnUserRecordBody() {
            $return = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body",
                array('date', 'weight', 'weightGoal', 'fat', 'fatGoal', 'bmi', 'calf', 'bicep', 'chest', 'forearm', 'hips', 'neck', 'thigh', 'waist'),
                $this->dbWhere());

            return $return;
        }

        /**
         * @return bool
         */
        public function returnUserRecordActivity() {
            $userActivity = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity",
                array('sedentary', 'lightlyactive', 'fairlyactive', 'veryactive'),
                $this->dbWhere());

            $userActivity['total'] = $userActivity['sedentary'] + $userActivity['lightlyactive'] + $userActivity['fairlyactive'] + $userActivity['veryactive'];

            return $userActivity;
        }

        /**
         * @return bool
         */
        public function returnUserRecordActivityHistory() {
            $sqlFilter = $this->dbWhere();
            $sqlFilter['AND']['name[!]'] = "Driving";
            $sqlFilter['ORDER'] = "logId DESC";

            unset($sqlFilter['AND']['date[<=]']);
            unset($sqlFilter['AND']['date[>=]']);
            unset($sqlFilter['LIMIT']);

            if (substr($this->getParamPeriod(), 0, strlen("last")) === "last") {
                $days = $this->getParamPeriod();
                $sqlFilter['LIMIT'] = str_ireplace("last", "", $days);
            }

            $userActivity = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity_log",
                array('date', 'name', 'logId', 'startDate', 'startTime', 'calories', 'duration'),
                $sqlFilter);

            $daysStats = array();
            $returnArray = array();
            foreach ($userActivity as $record) {
                $startTime = new DateTime($record['startDate'] . " " . $record['startTime']);
                $recKey = $startTime->format("F, Y");
                if (!array_key_exists($recKey,$returnArray) || !is_array($returnArray[$recKey])) {
                    $returnArray[$recKey] = array();
                }

                $record['name'] = str_ireplace(" (MyFitnessPal)","",$record['name']);
                $endTime = date("U", strtotime($record['startTime']));
                $endTime = $endTime + ($record['duration'] / 1000);
                $record['endTime'] = date("Y-m-d H:i", $endTime);
                $record['duration'] = (($record['duration'] / 1000) / 60);
                $record['startTime'] = date("F dS \@H:i", strtotime($record['startDate'] . " " . $record['startTime']));

                $record['calPerMinute'] = round($record['calories'] / $record['duration'], 1);

                if (strpos(strtolower($record['name']), 'push-ups') !== FALSE || strpos(strtolower($record['name']), 'sit-ups') !== FALSE || strpos(strtolower($record['name']), 'strength') !== FALSE) {
                    $record['colour'] = "teal";
                } else if (strpos(strtolower($record['name']), 'run') !== FALSE || strpos(strtolower($record['name']), 'walking') !== FALSE) {
                    $record['colour'] = "green";
                } else if (strpos(strtolower($record['name']), 'skiing') !== FALSE) {
                    $record['colour'] = "purple";
                } else {
                    $record['colour'] = "bricky";
                }

                if (!array_key_exists($record['startDate'], $daysStats)) {
                    $dbDaysStats = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity", array(
                            "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps" => array("user" => "user"),
                            "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps" => array("date" => "date")
                        ),
                        array(
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'activity.fairlyactive',
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'activity.veryactive',
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'steps.caloriesOut'
                        ), array("AND" => array($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity.user" => $this->getUserID(),
                                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity.date" => $record['startDate']
                        )));

                    $dbDaysStats['active'] = number_format($dbDaysStats['fairlyactive'] + $dbDaysStats['veryactive'], 0);
                    $dbDaysStats['caloriesOut'] = number_format($dbDaysStats['caloriesOut'], 0);

                    unset($dbDaysStats['fairlyactive']);
                    unset($dbDaysStats['veryactive']);

                    $daysStats[$record['startDate']] = $dbDaysStats;
                }

                $record['stats'] = $daysStats[$record['startDate']];

                unset($record['startDate']);
                unset($record['date']);
                array_push($returnArray[$recKey], $record);
            }

            return $returnArray;
        }

        /**
         * @return array|bool
         */
        public function returnUserRecordWeekPedometer() {
            $userActivity = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('date', 'steps', 'distance', 'floors'),
                $this->dbWhere());

            foreach ($userActivity as $key => $value) {
                $userActivity[$key]['distance'] = (String)round($value['distance'], 2);
                $userActivity[$key]['returnDate'] = explode("-", $value['date']);
            }

            return $userActivity;
        }

        /**
         * @return array
         */
        public function returnUserRecordTopBadges() {
            $userBadges = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_badge2usr",
                array('badgeType', 'value', 'dateTime', 'timesAchieved'),
                array("user" => $this->getUserID()));

            $badges = array();
            foreach ($userBadges as $userBadge) {
                if (!array_key_exists($userBadge['badgeType'], $badges)) {
                    $badges[$userBadge['badgeType']] = array();
                    $badges[$userBadge['badgeType']]['type'] = $userBadge['badgeType'];
                    $badges[$userBadge['badgeType']]['value'] = $userBadge['value'];
                    $badges[$userBadge['badgeType']]['dateTime'] = $userBadge['dateTime'];
                    $badges[$userBadge['badgeType']]['timesAchieved'] = $userBadge['timesAchieved'];
                } else if ($userBadge['value'] > $badges[$userBadge['badgeType']]['value']) {
                    $badges[$userBadge['badgeType']]['value'] = $userBadge['value'];
                    $badges[$userBadge['badgeType']]['dateTime'] = $userBadge['dateTime'];
                    $badges[$userBadge['badgeType']]['timesAchieved'] = $userBadge['timesAchieved'];
                }
            }

            foreach ($badges as $badge) {
                $dbBadge = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages",
                    array('image', 'badgeGradientEndColor', 'badgeGradientStartColor', 'earnedMessage', 'marketingDescription', 'name'),
                    array("AND" => array("badgeType" => $badge['type'], "value" => $badge['value'])));
                $badges[$badge['type']] = array_merge($badges[$badge['type']], $dbBadge);
            }

            return array("images" => "images/badges/", "badges" => $badges);
        }

        /**
         * @return array
         */
        public function returnUserRecordTrend() {
            $trendArray = array();

            $dbBody = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body", array('date', 'weight', 'weightGoal', 'fat', 'fatGoal'), array("user" => $this->getUserID(), "ORDER" => "date  ASC", "LIMIT" => 1));
            $trendArray['weeksWeightTracked'] = round(abs(strtotime($this->getParamDate()) - strtotime($dbBody['date'])) / 604800, 0);

            $trendArray['weightToLose'] = $dbBody['weight'] - $dbBody['weightGoal'];
            $trendArray['fatToLose'] = $dbBody['fat'] - $dbBody['fatGoal'];

            $dbGoalsCalories = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "goals_calories", array('estimatedDate'), array("user" => $this->getUserID(), "ORDER" => "date DESC", "LIMIT" => 1));
            $trendArray['estimatedDate'] = date("l", strtotime($dbGoalsCalories['estimatedDate'])) . " the " . date("jS \of F Y", strtotime($dbGoalsCalories['estimatedDate']));
            $trendArray['estimatedWeeks'] = round(abs(strtotime($dbGoalsCalories['estimatedDate']) - strtotime($this->getParamDate())) / 604800, 0);

            $dbUsers = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array('name', 'rank', 'friends', 'distance', 'gender'), array("fuid" => $this->getUserID()));
            $trendArray['rank'] = $dbUsers['rank'];
            $trendArray['friends'] = $dbUsers['friends'];
            $trendArray['nextRank'] = number_format($dbUsers['distance'], 0);
            $trendArray['name'] = explode(" ", $dbUsers['name']);
            $trendArray['name'] = $trendArray['name'][0];

            $dbSteps = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('caloriesOut'), array("user" => $this->getUserID(), "ORDER" => "date DESC", "LIMIT" => 1));
            $dbLogFood = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood", array('calories'), array("AND" => array("user" => $this->getUserID(), "date" => $this->getParamDate()), "ORDER" => "date DESC", "LIMIT" => 1));

            $trendArray['caldef'] = (String)($dbSteps['caloriesOut'] - $dbLogFood);

            if ($dbUsers['gender'] == "MALE") {
                $trendArray['he'] = "he";
                $trendArray['his'] = "his";
            } else {
                $trendArray['he'] = "she";
                $trendArray['his'] = "her";
            }

            return $trendArray;
        }

        /**
         * @return array
         */
        public function returnUserRecordDashboard() {
            // Achivment
            $dbSteps = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('distance', 'floors', 'steps', 'syncd'), $this->dbWhere());
            $dbStepsGoal = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array('distance', 'floors', 'steps'), $this->dbWhere());

            // Life time sum
            $dbStepsAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'steps', array("user" => $this->getUserID()));
            $dbDistanceAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'distance', array("user" => $this->getUserID()));
            $dbFloorsAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'floors', array("user" => $this->getUserID()));

            $thisDate = $this->getParamDate();
            $thisDate = explode("-", $thisDate);

            if ($dbSteps['distance'] > 0 && $dbStepsGoal['distance'] > 0) {
                $progdistance = number_format((($dbSteps['distance'] / $dbStepsGoal['distance']) * 100), 2);
            } else {
                $progdistance = 0;
            }
            if ($dbSteps['floors'] > 0 && $dbStepsGoal['floors'] > 0) {
                $progfloors = number_format((($dbSteps['floors'] / $dbStepsGoal['floors']) * 100), 2);
            } else {
                $progfloors = 0;
            }
            if ($dbSteps['steps'] > 0 && $dbStepsGoal['steps'] > 0) {
                $progsteps = number_format((($dbSteps['steps'] / $dbStepsGoal['steps']) * 100), 2);
            } else {
                $progsteps = 0;
            }

            $return = array('returnDate'      => $thisDate,
                            'syncd'           => $dbSteps['syncd'],
                            'distance'        => number_format($dbSteps['distance'], 2),
                            'floors'          => number_format($dbSteps['floors'], 0),
                            'steps'           => number_format($dbSteps['steps'], 0),
                            'progdistance'    => $progdistance,
                            'progfloors'      => $progfloors,
                            'progsteps'       => $progsteps,
                            'distanceAllTime' => number_format($dbDistanceAllTime, 2),
                            'floorsAllTime'   => number_format($dbFloorsAllTime, 0),
                            'stepsAllTime'    => number_format($dbStepsAllTime, 0));

            return $return;
        }

        /**
         * @return array
         */
        public function returnUserRecordDevices() {
            $dbDevices = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", array(
                "[>]".$this->getAppClass()->getSetting("db_prefix", NULL, FALSE)."lnk_dev2usr" => array("id" => "device")),
                array(
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'devices.deviceVersion',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'devices.battery',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'devices.lastSyncTime',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'devices.type',
                ), array($this->getAppClass()->getSetting("db_prefix", NULL, FALSE)."lnk_dev2usr.user" => $this->getUserID()));

            foreach ($dbDevices as $key => $dev) {
                $dbDevices[$key]['image'] = 'images/devices/' . str_ireplace(" ","",$dbDevices[$key]['deviceVersion']) . ".png";
                $dbDevices[$key]['imageSmall'] = 'images/devices/' . str_ireplace(" ","",$dbDevices[$key]['deviceVersion']) . "_small.png";
                if (strtolower($dbDevices[$key]['battery']) == "high") {
                    $dbDevices[$key]['precentage'] = 100;
                } else if (strtolower($dbDevices[$key]['battery']) == "medium") {
                    $dbDevices[$key]['precentage'] = 50;
                } else if (strtolower($dbDevices[$key]['battery']) == "low") {
                    $dbDevices[$key]['precentage'] = 10;
                }

                $dbDevices[$key]['unixtime'] = strtotime($dbDevices[$key]['lastSyncTime']);
            }

            return $dbDevices;
        }

        /**
         * @return array
         */
        public function returnUserRecordWeight() {
            $days = 7;
            $returnWeight = array();

            if (substr($this->getParamPeriod(), 0, strlen("last")) === "last") {
                $days = $this->getParamPeriod();
                $days = str_ireplace("last", "", $days);
            }

            $dbWeight = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body",
                array('date', 'weight', 'weightGoal', 'fat', 'fatGoal'),
                array("AND" => array("user"     => $this->getUserID(),
                                     "date[<=]" => $this->getParamDate(),
                                     "date[>=]" => date('Y-m-d', strtotime($this->getParamDate() . " -" . ($days - 1) . " day"))
                ), "ORDER"  => "date DESC", "LIMIT" => $days));

            $latestDate = 0;
            foreach ($dbWeight as $daysWeight) {
                if (strtotime($daysWeight['date']) > strtotime($latestDate)) {
                    $latestDate = $daysWeight['date'];
                }

                $returnWeight[$daysWeight['date']] = $daysWeight;
                $returnWeight[$daysWeight['date']]['source'] = "Database";
            }

            if (count($dbWeight) == 0) {
                /*
                 * If no weights are returned by the we use the last recored weight and just propegate it forward
                 */

                /** @var DateTime $currentDate */
                $currentDate = new DateTime (date('Y-m-d', strtotime($this->getParamDate() . " +1 day")));
                /** @var DateTime $sevenDaysAgo */
                $sevenDaysAgo = new DateTime(date('Y-m-d', strtotime($this->getParamDate() . " -" . ($days - 1) . " day")));
                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod ($sevenDaysAgo, $interval, $currentDate);

                $weight = $this->getAppClass()->getFitbitapi()->getDBCurrentBody($this->getUserID(), "weight");
                $weightGoal = $this->getAppClass()->getFitbitapi()->getDBCurrentBody($this->getUserID(), "weightGoal");
                $fat = $this->getAppClass()->getFitbitapi()->getDBCurrentBody($this->getUserID(), "fat");
                $fatGoal = $this->getAppClass()->getFitbitapi()->getDBCurrentBody($this->getUserID(), "fatGoal");

                foreach ($period as $dt) {
                    /** @var DateTime $dt */
                    $returnWeight[$dt->format("Y-m-d")] = array("date"       => $dt->format("Y-m-d"),
                                                                "weight"     => $weight,
                                                                "weightGoal" => $weightGoal,
                                                                "fat"        => $fat,
                                                                "fatGoal"    => $fatGoal,
                                                                "source"     => "LatestRecord");
                }

            } else if (count($dbWeight) < $days) {
                /*
                 * If there are missing records try filling in the blanks
                 */

                /** @var DateTime $currentDate */
                $currentDate = new DateTime (date('Y-m-d', strtotime($this->getParamDate() . " +1 day")));
                /** @var DateTime $sevenDaysAgo */
                $sevenDaysAgo = new DateTime(date('Y-m-d', strtotime($this->getParamDate() . " -" . ($days - 1) . " day")));
                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod ($sevenDaysAgo, $interval, $currentDate);

                $recordsLoopedThru = 0;
                $lastRecord = NULL;
                $foundMissingRecord = FALSE;
                $arrayOfMissingDays = array();
                foreach ($period as $dt) {
                    /** @var DateTime $dt */
                    if (!array_key_exists($dt->format("Y-m-d"), $returnWeight)) {
                        if (strtotime($dt->format("Y-m-d")) > strtotime($latestDate)) {
                            $returnWeight[$dt->format("Y-m-d")] = $lastRecord;
                            $returnWeight[$dt->format("Y-m-d")]['source'] = "LatestRecord";
                        } else {
                            $foundMissingRecord = TRUE;
                            array_push($arrayOfMissingDays, $dt->format("Y-m-d"));
                            $returnWeight[$dt->format("Y-m-d")] = 'Calc deviation';
                        }
                    } else {
                        if ($foundMissingRecord) {
                            if (is_null($lastRecord)) {
                                $lastRecord = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body",
                                    array('date', 'weight', 'weightGoal', 'fat', 'fatGoal'),
                                    array("AND" => array("user"     => $this->getUserID(),
                                                         "date[<=]" => date('Y-m-d', strtotime($this->getParamDate() . " -" . ($days - 1) . " day"))
                                    ), "ORDER"  => "date DESC", "LIMIT" => 1));
                            }

                            $returnWeight = $this->fillMissingBodyRecords($returnWeight, $arrayOfMissingDays, $lastRecord, $returnWeight[$dt->format("Y-m-d")]);

                            $foundMissingRecord = FALSE;
                            $arrayOfMissingDays = array();
                        }
                        $lastRecord = $returnWeight[$dt->format("Y-m-d")];
                    }
                    $recordsLoopedThru = $recordsLoopedThru + 1;
                }
                ksort($returnWeight);

                $returnWeight = array_reverse($returnWeight);
            }

            $fatMin = 0;
            $fatMax = 0;
            $fat = array();
            $fatGoal = array();
            $weightMin = 0;
            $weightMax = 0;
            $weights = array();
            $weightGoal = array();
            foreach ($returnWeight as $db) {
                //array_push($weights, (String)round($db['weight'], 2) . " " . $db['source']);
                if ($db['weight'] < $weightMin || $weightMin == 0) {
                    $weightMin = $db['weight'];
                }
                if ($db['weight'] > $weightMax || $weightMax == 0) {
                    $weightMax = $db['weight'];
                }
                array_push($weights, (String)round($db['weight'], 2));
                array_push($weightGoal, (String)$db['weightGoal']);

                if ($db['fat'] < $fatMin || $fatMin == 0) {
                    $fatMin = $db['fat'];
                }
                if ($db['fat'] > $fatMax || $fatMax == 0) {
                    $fatMax = $db['fat'];
                }
                array_push($fat, (String)round($db['fat'], 2));
                array_push($fatGoal, (String)$db['fatGoal']);
            }

            return array('returnDate'       => explode("-", $this->getParamDate()),
                         'graph_weight_min' => $weightMin,
                         'graph_weight_max' => $weightMax,
                         'graph_fat_min'    => $fatMin,
                         'graph_fat_max'    => $fatMax,
                         'graph_weight'     => $weights,
                         'graph_weightGoal' => $weightGoal,
                         'graph_fat'        => $fat,
                         'graph_fatGoal'    => $fatGoal);
        }

        /**
         * @param array $returnWeight
         * @param array $arrayOfMissingDays
         * @param array|NULL $lastRecord
         * @param array $nextRecord
         * @return array
         */
        private function fillMissingBodyRecords($returnWeight, $arrayOfMissingDays, $lastRecord, $nextRecord) {
            $xDistance = count($arrayOfMissingDays) + 1;

            $yStartWeight = $lastRecord['weight'];
            $yEndWeight = $nextRecord['weight'];
            $dailyChangeWeight = ($yEndWeight - $yStartWeight) / $xDistance;

            $yStartFat = $lastRecord['fat'];
            $yEndFat = $nextRecord['fat'];
            $dailyChangeFat = ($yEndFat - $yStartFat) / $xDistance;

            $dayNumber = 0;
            foreach ($arrayOfMissingDays as $date) {
                $dayNumber = $dayNumber + 1;
                $calcWeight = (String)round(($dailyChangeWeight * $dayNumber) + $yStartWeight, 2);
                $calcFat = (String)round(($dailyChangeFat * $dayNumber) + $yStartFat, 2);
                $returnWeight[$date] = array("date"       => $date,
                                             "weight"     => $calcWeight,
                                             "weightGoal" => $nextRecord['weightGoal'],
                                             "fat"        => $calcFat,
                                             "fatGoal"    => $nextRecord['fatGoal'],
                                             "source"     => "CalcDeviation");
            }

            return $returnWeight;
        }
    }