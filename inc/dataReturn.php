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

                if (!is_null($this->getTracking()) && is_array($_SERVER) && array_key_exists("SERVER_NAME", $_SERVER))
                    $this->getTracking()->endEvent('JSON/' . $this->getUserID() . '/' . $this->getParamDate() . '/' . $get['data']);

                return $resultsArray;
            } else {
                if (!is_null($this->getTracking()) && is_array($_SERVER) && array_key_exists("SERVER_NAME", $_SERVER)) {
                    $this->getTracking()->track("Error", 103);
                    $this->getTracking()->endEvent('Error/' . $this->getUserID() . '/' . $this->getParamDate() . '/' . $get['data']);
                }

                return array("error" => "true", "code" => 103, "msg" => "Unknown dataset: " . $functionName);
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

                if (!is_null($this->getTracking())) {
                    $this->getTracking()->track("JSON Get", $this->getUserID(), "Food");
                    $this->getTracking()->track("JSON Goal", $this->getUserID(), "Food");
                }

                return array('goal' => $dbFoodGoal[0]['calories'], 'total' => $total, "meals" => $dbFoodLog);
            } else {
                return array("error" => "true", "code" => 104, "msg" => "No results for given date");
            }
        }

        /**
         * @param int $limit
         * @param string $tableName
         * @return array
         */
        public function dbWhere($limit = 1, $tableName = '') {
            if ($limit < 1) {$limit = 1;}
            if ($tableName != "") {$tableName = $tableName . ".";}

            if ($this->getParamPeriod() == "single") {
                return array("AND" => array($tableName."user" => $this->getUserID(), $tableName."date" => $this->getParamDate()), "LIMIT" => $limit);
            } else if (substr($this->getParamPeriod(), 0, strlen("last")) === "last") {
                $days = $this->getParamPeriod();
                $days = str_ireplace("last", "", $days);
                $then = date('Y-m-d', strtotime($this->getParamDate() . " -" . $days . " day"));

                return array("AND" => array($tableName."user" => $this->getUserID(), $tableName."date[<=]" => $this->getParamDate(), $tableName."date[>=]" => $then), "ORDER" => $tableName."date DESC", "LIMIT" => $days);
            } else {
                return array($tableName."user" => $this->getUserID(), "ORDER" => $tableName."date DESC", "LIMIT" => $limit);
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
                                                                     'carbs'    => $meal['carbs'],
                                                                     'fat'      => $meal['fat'],
                                                                     'fiber'    => $meal['fiber'],
                                                                     'protein'  => $meal['protein'],
                                                                     'sodium'   => $meal['sodium']
                );
                $returnArray['food']['summary']['calories'] += $meal['calories'];
                $returnArray['food']['summary']['carbs'] += $meal['carbs'];
                $returnArray['food']['summary']['fat'] += $meal['fat'];
                $returnArray['food']['summary']['fiber'] += $meal['fiber'];
                $returnArray['food']['summary']['protein'] += $meal['protein'];
                $returnArray['food']['summary']['sodium'] += $meal['sodium'];
            }
            foreach ($dbFood as $meal) {
                $returnArray['food']['meals'][$meal['meal']]['precentage'] = ($meal['calories']/$returnArray['food']['summary']['calories']) * 100;
            }
            $returnArray['food']['goals'] = array();
            $returnArray['food']['goals']['carbs'] = $this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_carbs", 310);
            $returnArray['food']['goals']['fat'] = $this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_fat", 70);
            $returnArray['food']['goals']['fiber'] = $this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_fiber", 30);
            $returnArray['food']['goals']['protein'] = $this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_protein", 50);
            $returnArray['food']['goals']['sodium'] = $this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_sodium", 2300);

            return $returnArray;
        }

        /**
         * @return array
         */
        public function returnUserRecordSteps() {
            $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('distance', 'floors', 'steps'),
                $this->dbWhere());

            if (count($dbSteps) > 0) {
                $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals",
                    array('distance', 'floors', 'steps'),
                    $this->dbWhere());

                $dbGoals[0]['distance'] = (String)round($dbGoals[0]['distance'], 2);
                $dbSteps[0]['distance'] = (String)round($dbSteps[0]['distance'], 2);

                if (!is_null($this->getTracking())) {
                    $this->getTracking()->track("JSON Get", $this->getUserID(), "Steps");
                    $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");
                }

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

            if (!is_null($this->getTracking())) {
                $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");
            }

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
                if (!array_key_exists($recKey, $returnArray) || !is_array($returnArray[$recKey])) {
                    $returnArray[$recKey] = array();
                }

                $record['name'] = str_ireplace(" (MyFitnessPal)", "", $record['name']);
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
                    "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_dev2usr" => array("id" => "device")),
                array(
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'devices.deviceVersion',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'devices.battery',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'devices.lastSyncTime',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'devices.type',
                ), array($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_dev2usr.user" => $this->getUserID()));

            foreach ($dbDevices as $key => $dev) {
                $dbDevices[$key]['image'] = 'images/devices/' . str_ireplace(" ", "", $dbDevices[$key]['deviceVersion']) . ".png";
                $dbDevices[$key]['imageSmall'] = 'images/devices/' . str_ireplace(" ", "", $dbDevices[$key]['deviceVersion']) . "_small.png";
                if (strtolower($dbDevices[$key]['battery']) == "high") {
                    $dbDevices[$key]['precentage'] = 100;
                } else if (strtolower($dbDevices[$key]['battery']) == "medium") {
                    $dbDevices[$key]['precentage'] = 50;
                } else if (strtolower($dbDevices[$key]['battery']) == "low") {
                    $dbDevices[$key]['precentage'] = 10;
                } else if (strtolower($dbDevices[$key]['battery']) == "empty") {
                    $dbDevices[$key]['precentage'] = 0;
                }

                $dbDevices[$key]['unixTime'] = strtotime($dbDevices[$key]['lastSyncTime']);
                if ($dbDevices[$key]['type'] == "TRACKER") {
                    $dbDevices[$key]['testTime'] = strtotime('now') - (4 * 60 * 60);
                } else {
                    $dbDevices[$key]['testTime'] = strtotime('now') - (48 * 60 * 60);
                }

                if ($dbDevices[$key]['testTime'] > $dbDevices[$key]['unixTime']) {
                    $dbDevices[$key]['alertTime'] = 1;
                } else {
                    $dbDevices[$key]['alertTime'] = 0;
                }
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
                array('date', 'weight', 'weightGoal', 'weightAvg', 'fat', 'fatAvg', 'fatGoal'),
                array("AND" => array("user"     => $this->getUserID(),
                                     "date[<=]" => $this->getParamDate(),
                                     "date[>=]" => date('Y-m-d', strtotime($this->getParamDate() . " -" . (($days + 10) - 1) . " day"))
                ), "ORDER"  => "date DESC", "LIMIT" => ($days + 10)));

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
                $sevenDaysAgo = new DateTime(date('Y-m-d', strtotime($this->getParamDate() . " -" . (($days + 10) - 1) . " day")));
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
                                                                "weightAvg"  => $weight,
                                                                "fat"        => $fat,
                                                                "fatGoal"    => $fatGoal,
                                                                "fatAvg"     => $fat,
                                                                "source"     => "LatestRecord");
                }

            } else if (count($dbWeight) < ($days + 10)) {
                /*
                 * If there are missing records try filling in the blanks
                 */

                /** @var DateTime $currentDate */
                $currentDate = new DateTime (date('Y-m-d', strtotime($this->getParamDate() . " +1 day")));
                /** @var DateTime $sevenDaysAgo */
                $sevenDaysAgo = new DateTime(date('Y-m-d', strtotime($this->getParamDate() . " -" . (($days + 10) - 1) . " day")));
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
                                    array('date', 'weight', 'weightAvg', 'weightGoal', 'fat', 'fatAvg', 'fatGoal'),
                                    array("AND" => array("user"     => $this->getUserID(),
                                                         "date[<=]" => date('Y-m-d', strtotime($this->getParamDate() . " -" . (($days + 10) - 1) . " day"))
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

            $returnWeightKeys = array_keys($returnWeight);

            for ($interval = 0; count($returnWeight) > $interval; $interval++) {
                $averageRange = 15;
                if (count($returnWeight) > $interval + $averageRange) {
                    $fatSum = 0;
                    $weightSum = 0;
                    for ($intervalTwo = 0; $intervalTwo < $averageRange; $intervalTwo++) {
                        $weightSum = $weightSum + $returnWeight[$returnWeightKeys[$interval + $intervalTwo]]['weight'];
                        $fatSum = $fatSum + $returnWeight[$returnWeightKeys[$interval + $intervalTwo]]['fat'];
                    }
                    $returnWeight[$returnWeightKeys[$interval]]['weightTrend'] = $weightSum / $averageRange;
                    $returnWeight[$returnWeightKeys[$interval]]['fatTrend'] = $fatSum / $averageRange;
                } else {
                    $returnWeight[$returnWeightKeys[$interval]]['weightTrend'] = $returnWeight[$returnWeightKeys[$interval]]['weight'];
                    $returnWeight[$returnWeightKeys[$interval]]['fatTrend'] = $returnWeight[$returnWeightKeys[$interval]]['fat'];
                }
            }

            $returnWeight = array_slice($returnWeight, 0, $days);

            $fatMin = 0;
            $fatMax = 0;
            $fat = array();
            $fatAvg = array();
            $fatGoal = array();
            $fatTrend = array();
            $weightMin = 0;
            $weightMax = 0;
            $weights = array();
            $weightAvg = array();
            $weightGoal = array();
            $weightTrend = array();
            foreach ($returnWeight as $db) {
                if ($db['weight'] < $weightMin || $weightMin == 0) {
                    $weightMin = $db['weight'];
                }
                if ($db['weight'] > $weightMax || $weightMax == 0) {
                    $weightMax = $db['weight'];
                }
                array_push($weights, (String)round($db['weight'], 2));
                array_push($weightGoal, (String)$db['weightGoal']);
                array_push($weightTrend, (String)$db['weightTrend']);
                array_push($weightAvg, (String)$db['weightAvg']);

                if ($db['fat'] < $fatMin || $fatMin == 0) {
                    $fatMin = $db['fat'];
                }
                if ($db['fat'] > $fatMax || $fatMax == 0) {
                    $fatMax = $db['fat'];
                }
                array_push($fat, (String)round($db['fat'], 2));
                array_push($fatGoal, (String)$db['fatGoal']);
                array_push($fatTrend, (String)$db['fatTrend']);
                array_push($fatAvg, (String)$db['fatAvg']);
            }

            $loss = array();
            $monthsBack = 1;
            $loopMonths = true;
            do {
                $timestamp = strtotime('now -'.$monthsBack.' month');
                if (array_key_exists(date('Y-m-t', $timestamp), $returnWeight) AND array_key_exists(date('Y-m-01', $timestamp), $returnWeight)) {
                    $loss["weight"][date('Y F', $timestamp)] = round(($returnWeight[date('Y-m-t', $timestamp)]['weightTrend'] - $returnWeight[date('Y-m-01', $timestamp)]['weightTrend']) / 4, 2);
                    $loss["fat"][date('Y F', $timestamp)] = round(($returnWeight[date('Y-m-t', $timestamp)]['fatTrend'] - $returnWeight[date('Y-m-01', $timestamp)]['fatTrend']) / 4, 2);
                    $monthsBack += 1;
                } else {
                    $loopMonths = false;
                }
            } while($loopMonths);

            if (!array_key_exists("weight", $loss)) {
                $loss["weight"] = array();
            }
            if (!array_key_exists("fat", $loss)) {
                $loss["fat"] = array();
            }

            return array('returnDate'        => explode("-", $this->getParamDate()),
                         'graph_weight_min'  => $weightMin,
                         'graph_weight_max'  => $weightMax,
                         'graph_fat_min'     => $fatMin,
                         'graph_fat_max'     => $fatMax,
                         'graph_weight'      => $weights,
                         'graph_weightTrend' => $weightTrend,
                         'graph_weightAvg'   => $weightAvg,
                         'graph_weightGoal'  => $weightGoal,
                         'graph_fat'         => $fat,
                         'graph_fatGoal'     => $fatGoal,
                         'graph_fatTrend'    => $fatTrend,
                         'graph_fatAvg'      => $fatAvg,
                         'loss_rate_weight'  => $loss["weight"],
                         'loss_rate_fat'     => $loss["fat"]);
        }

        public function returnUserRecordAboutMe() {
            $dbSteps = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('steps'),
                array("user" => $this->getUserID())
            );
            $dbFloors = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('floors'),
                array("user" => $this->getUserID())
            );
            $dbDistance = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('distance'),
                array("user" => $this->getUserID())
            );

            $yearThis = date("Y");
            $yearLast = date("Y") - 1;
            $dbStepsYearThis = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('steps'),
                array("AND" => array("user" => $this->getUserID(), "date[~]" => $yearThis . "%"))
            );

            $dbStepsYearLast = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('steps'),
                array("AND" => array("user" => $this->getUserID(), "date[~]" => $yearLast . "%"))
            );

            return array(
                "steps" => round($dbSteps, 0),
                "floors" => round($dbFloors, 0),
                "distance" => round($dbDistance, 0),
                "stepsThisYear" => round($dbStepsYearThis, 0),
                "stepsLastYear" => round($dbStepsYearLast, 0),
            );
        }

        public function returnUserRecordKeyPoints() {
            // Get Users Gender and leaderboard ranking
            $dbUsers = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array('rank', 'friends', 'distance', 'gender'), array("fuid" => $this->getUserID()));
            if (array_key_exists("personal", $_GET) and $_GET['personal'] == "true") {
                $he = "I";
                $is = "am";
                $hes = "I've";
                $his = "my";
            } else {
                $is = "is";
                if ($dbUsers['gender'] == "MALE") {
                    $he = "he";
                    $hes = "he's";
                    $his = "his";
                } else {
                    $he = "she";
                    $hes = "she's";
                    $his = "her";
                }
            }

            /**
             * Get Keypoint records
             */
            $dbKeyPoints = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "keypoints",
                array('category', 'value', 'less', 'more')
            );

            $keyPoints = array();
            foreach ($dbKeyPoints as $point) {
                if (!array_key_exists($point['category'], $keyPoints)) {
                    $keyPoints[$point['category']] = array();
                }

                array_push($keyPoints[$point['category']], array(
                    "value" => $point['value'],
                    "less" => $point['less'],
                    "more" => $point['more']
                ));
            }

            $returnStats = array("distance" => array(), "floors" => array(), "max" => array(), "friends" => array());
            $returnStats["friends"] = $hes . " " . $dbUsers['friends'] . " friends ";
            if ($dbUsers['rank'] > 1) {
                $returnStats["friends"] .= "and " . $is . " currently ranked " . $this->ordinal_suffix($dbUsers['rank']) . ", with another " . number_format($dbUsers['distance'], 0) . " steps " . $he . " could take " . $this->ordinal_suffix($dbUsers['rank'] - 1) . " place.";
            } else {
                $returnStats["friends"] .= "and " . $is . " proudly at the top of the leaderboard.";
            }

            /**
             * Set key points for DISTANCE
             */
            $dbDistanceAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'distance', array("user" => $this->getUserID()));
            $less = array();
            $more = array();
            foreach ($keyPoints['distance'] as $values) {
                if ($dbDistanceAllTime < $values['value']) {
                    array_push($less, number_format(($values['value'] - $dbDistanceAllTime), 0) . " miles until " . $hes . " walked " . $values['less']);
                } else if ($dbDistanceAllTime > $values['value']) {
                    $times = round($dbDistanceAllTime / $values['value'], 2);
                    if (array_key_exists("more", $values) && !is_null($values['more']) && $values['more'] != "") {
                        $msg = $hes . " walked " . $values['more'] . " " . number_format($times, 0) . " time";
                    } else {
                        $msg = $hes . " walked " . $values['less'] . " " . number_format($times, 0) . " time";
                    }
                    if ($times > 1) {
                        $msg .= "s";
                    }
                    array_push($more, $msg);
                }
            }

            $maxItems = $this->getAppClass()->getSetting("kp_maxItems", 8);
            $lessItems = $this->getAppClass()->getSetting("kp_lessItems", 2);
            if (count($less) < $maxItems - $lessItems) {
                $lessItems = $maxItems - count($less);
            }

            for ($iMore = ($lessItems - 1); $iMore >= 0; $iMore = $iMore - 1) {
                if (count($more) > $iMore) {
                    array_push($returnStats['distance'], $more[(count($more) - 1) - $iMore]);
                    $maxItems = $maxItems - 1;
                }
            }

            for ($iLess = $maxItems; $iLess >= 0; $iLess = $iLess - 1) {
                if (count($less) > $iLess) {
                    array_push($returnStats['distance'], $less[(count($less) - 1) - $iLess]);
                }
            }

            /**
             * Set key points for Floors
             */
            $dbFloorsAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'floors', array("user" => $this->getUserID()));

            $less = array();
            $more = array();
            foreach ($keyPoints['floors'] as $values) {
                if ($dbFloorsAllTime < $values['value']) {
                    array_push($less, number_format(($values['value'] - $dbFloorsAllTime), 0) . " more floors until " . $hes . " climbed " . $values['less']);
                } else if ($dbFloorsAllTime > $values['value']) {
                    $times = round($dbFloorsAllTime / $values['value'], 0);
                    if (array_key_exists("more", $values) && !is_null($values['more']) && $values['more'] != "") {
                        $msg = $hes . " climbed " . $values['more'] . " " . number_format($times, 0) . " time";
                    } else {
                        $msg = $hes . " climbed " . $values['less'] . " " . number_format($times, 0) . " time";
                    }
                    if ($times > 1) {
                        $msg .= "s";
                    }
                    array_push($more, $msg);
                }
            }

            $maxItems = $this->getAppClass()->getSetting("kp_maxItems", 8);
            $lessItems = $this->getAppClass()->getSetting("kp_lessItems", 2);
            if (count($less) < $maxItems - $lessItems) {
                $lessItems = $maxItems - count($less);
            }

            for ($iMore = ($lessItems - 1); $iMore >= 0; $iMore = $iMore - 1) {
                if (count($more) > $iMore) {
                    array_push($returnStats['floors'], $more[(count($more) - 1) - $iMore]);
                    $maxItems = $maxItems - 1;
                }
            }

            for ($iLess = $maxItems; $iLess >= 0; $iLess = $iLess - 1) {
                if (count($less) > $iLess) {
                    array_push($returnStats['floors'], $less[(count($less) - 1) - $iLess]);
                }
            }

            /**
             * Set Max values
             */
            $dbMaxSteps = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('steps', 'date'), array("user" => $this->getUserID(), "ORDER" => "steps DESC", "LIMIT" => 1));
            array_push($returnStats["max"], $his . " highest step count, totalling " . number_format($dbMaxSteps['steps'], 0) . ", for a day was on " . date("jS F, Y", strtotime($dbMaxSteps['date'])) . ".");

            $dbMaxDistance = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('distance', 'date'), array("user" => $this->getUserID(), "ORDER" => "distance DESC", "LIMIT" => 1));
            if ($dbMaxDistance['date'] == $dbMaxSteps['date']) {
                $returnStats["max"][(count($returnStats["max"]) -1)] .= " That's an impressive " . number_format($dbMaxDistance['distance'], 0) . " miles.";
            } else {
                array_push($returnStats["max"], $he . " traveled the furthest, " . number_format($dbMaxDistance['distance'], 0) . " miles, on " . date("jS F, Y", strtotime($dbMaxDistance['date'])) . ".");
            }

            $dbMaxFloors = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('floors', 'date'), array("user" => $this->getUserID(), "ORDER" => "floors DESC", "LIMIT" => 1));
            array_push($returnStats["max"], $he . " walked up, " . number_format($dbMaxFloors['floors'], 0) . " floors, on " . date("jS F, Y", strtotime($dbMaxFloors['date'])) . ".");

            $dbMaxElevation = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('elevation', 'date'), array("user" => $this->getUserID(), "ORDER" => "elevation DESC", "LIMIT" => 1));
            if ($dbMaxFloors['date'] == $dbMaxElevation['date']) {
                $returnStats["max"][(count($returnStats["max"]) -1)] .= " That's a total of " . number_format($dbMaxElevation['elevation'], 2) . " meters.";
            } else {
                array_push($returnStats["max"], $he . " climed the highest on " . date("jS F, Y", strtotime($dbMaxElevation['date'])) . ", a total of " . number_format($dbMaxElevation['elevation'], 2) . " meters.");
            }
            return $returnStats;
        }

        public function returnUserRecordSleep() {

            $dbSleepRecords = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logSleep", array(
                    "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_sleep2usr" => array("logId" => "sleeplog")),
                array(
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'logSleep.startTime',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'logSleep.timeInBed',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'logSleep.minutesAsleep',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'logSleep.minutesToFallAsleep',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'logSleep.efficiency',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'logSleep.awakeningsCount',
                ), array($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_sleep2usr.user" => $this->getUserID()));

            $returnSleep = array(
                "efficiency" => 0,
                "timeInBed" => 0,
                "minutesToFallAsleep" => 0,
                "minutesAsleep" => 0,
                "awakeningsCount" => 0
            );
            foreach ($dbSleepRecords as $record) {
                $returnSleep['efficiency'] = $returnSleep['efficiency'] + $record['efficiency'];
                $returnSleep['timeInBed'] = $returnSleep['timeInBed'] + $record['timeInBed'];
                $returnSleep['minutesToFallAsleep'] = $returnSleep['minutesToFallAsleep'] + $record['minutesToFallAsleep'];
                $returnSleep['minutesAsleep'] = $returnSleep['minutesAsleep'] + $record['minutesAsleep'];
                $returnSleep['awakeningsCount'] = $returnSleep['awakeningsCount'] + $record['awakeningsCount'];
            }

            $returnSleep['efficiency'] = round($returnSleep['efficiency'] / count($dbSleepRecords), 0);
            $returnSleep['timeInBedAvg'] = round($returnSleep['timeInBed'] / count($dbSleepRecords), 0);
            $returnSleep['minutesToFallAsleep'] = round($returnSleep['minutesToFallAsleep']/ count($dbSleepRecords), 0);
            $returnSleep['minutesAsleep'] = round($returnSleep['minutesAsleep'] / count($dbSleepRecords), 0);
            $returnSleep['awakeningsCount'] = round($returnSleep['awakeningsCount'] / count($dbSleepRecords), 0);

            return $returnSleep;
        }

        public function returnUserRecordStepGoal() {
            $lastMonday = date('Y-m-d',strtotime('last monday -7 days'));
            $oneWeek = date('Y-m-d',strtotime( $lastMonday . ' +6 days'));

            $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'steps',
                array("AND" => array(
                    "user" => $this->getUserID(),
                    "date[<=]" => $oneWeek,
                    "date[>=]" => $lastMonday
                ), "ORDER" => "date DESC", "LIMIT" => 7));

            $totalSteps = 0;
            foreach ($dbSteps as $dbStep) {
                $totalSteps = $totalSteps + $dbStep;
            }

            $newTargetSteps = round($totalSteps / count($dbSteps), 0);
            $plusTargetSteps = $newTargetSteps + round($newTargetSteps * ($this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_steps", 10) / 100), 0);

            return array(
                "weekStart" => $lastMonday,
                "weekEnd" => $oneWeek,
                "totalSteps" => $totalSteps,
                "newTargetSteps" => $newTargetSteps,
                "plusTargetSteps" => $plusTargetSteps
            );
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

            $yStartWeightAvg = $lastRecord['weightAvg'];
            $yEndWeightAvg = $nextRecord['weightAvg'];
            $dailyChangeWeightAvg = ($yEndWeightAvg - $yStartWeightAvg) / $xDistance;

            $yStartFat = $lastRecord['fat'];
            $yEndFat = $nextRecord['fat'];
            $dailyChangeFat = ($yEndFat - $yStartFat) / $xDistance;

            $yStartFatAvg = $lastRecord['fatAvg'];
            $yEndFatAvg = $nextRecord['fatAvg'];
            $dailyChangeFatAvg = ($yEndFatAvg - $yStartFatAvg) / $xDistance;

            $dayNumber = 0;
            foreach ($arrayOfMissingDays as $date) {
                $dayNumber = $dayNumber + 1;
                $calcWeight = (String)round(($dailyChangeWeight * $dayNumber) + $yStartWeight, 2);
                $calcWeightAvg = (String)round(($dailyChangeWeightAvg * $dayNumber) + $yStartWeightAvg, 2);
                $calcFat = (String)round(($dailyChangeFat * $dayNumber) + $yStartFat, 2);
                $calcFatAvg = (String)round(($dailyChangeFatAvg * $dayNumber) + $yStartFatAvg, 2);
                $returnWeight[$date] = array("date"       => $date,
                                             "weight"     => $calcWeight,
                                             "weightAvg"  => $calcWeightAvg,
                                             "weightGoal" => $nextRecord['weightGoal'],
                                             "fat"        => $calcFat,
                                             "fatAvg"     => $calcFatAvg,
                                             "fatGoal"    => $nextRecord['fatGoal'],
                                             "source"     => "CalcDeviation");
            }

            return $returnWeight;
        }

        private function ordinal_suffix($input_num){
            $num = $input_num % 100; // protect against large numbers
            if($num < 11 || $num > 13){
                switch($num % 10){
                    case 1: return $input_num . 'st';
                    case 2: return $input_num . 'nd';
                    case 3: return $input_num . 'rd';
                }
            }
            return $input_num . 'th';
        }

    }