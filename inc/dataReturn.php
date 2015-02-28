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
                $resultsArray = array("error" => "false", "user" => $this->getUserID(), "data" => $get['data'], "period" => $this->getParamPeriod(), "date" => $this->getParamDate());
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

            $this->getTracking()->track("JSON Get", $this->getUserID(), "Water");
            $this->getTracking()->track("JSON Goal", $this->getUserID(), "Water");

            return $dbWater;
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
            // User profile
            $dbUser = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array('name'), array("fuid" => $this->getUserID()));

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

            $return = array('username'         => $dbUser['name'],
                            'returnDate'       => $thisDate,
                            'syncd'            => $dbSteps['syncd'],
                            'distance'         => number_format($dbSteps['distance'], 2),
                            'floors'           => number_format($dbSteps['floors'], 0),
                            'steps'            => number_format($dbSteps['steps'], 0),
                            'progdistance'     => $progdistance,
                            'progfloors'       => $progfloors,
                            'progsteps'        => $progsteps,
                            'distanceAllTime'  => number_format($dbDistanceAllTime, 2),
                            'floorsAllTime'    => number_format($dbFloorsAllTime, 0),
                            'stepsAllTime'     => number_format($dbStepsAllTime, 0));

            return $return;
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
                array("AND" => array("user" => $this->getUserID(),
                                     "date[<=]" => $this->getParamDate(),
                                     "date[>=]" => date('Y-m-d', strtotime($this->getParamDate() . " -" . ($days - 1) . " day"))
                ), "ORDER" => "date DESC", "LIMIT" => $days));

            $latestDate = 0;
            foreach ($dbWeight as $daysWeight) {
                if (strtotime($daysWeight['date']) > strtotime($latestDate)) {
                    $latestDate = $daysWeight['date'];
                }

                $returnWeight[$daysWeight['date']] = $daysWeight;
                $returnWeight[$daysWeight['date']]['source'] = "Database";
            }

            if (count($dbWeight) == 0) {
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
                    $returnWeight[$dt->format("Y-m-d")] = array("date" => $dt->format("Y-m-d"),
                                                                "weight" => $weight,
                                                                "weightGoal" => $weightGoal,
                                                                "fat" => $fat,
                                                                "fatGoal" => $fatGoal,
                                                                "source" => "LatestRecord");
                }

            } else if (count($dbWeight) < $days) {
                /** @var DateTime $currentDate */
                $currentDate = new DateTime (date('Y-m-d', strtotime($this->getParamDate() . " +1 day")));
                /** @var DateTime $sevenDaysAgo */
                $sevenDaysAgo = new DateTime(date('Y-m-d', strtotime($this->getParamDate() . " -" . ($days - 1) . " day")));
                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod ($sevenDaysAgo, $interval, $currentDate);

                $missingDays = 0;
                $lastRecord = array();
                foreach ($period as $dt) {
                    /** @var DateTime $dt */
                    if (!array_key_exists($dt->format("Y-m-d"), $returnWeight)) {
                        if (strtotime($dt->format("Y-m-d")) > strtotime($latestDate)) {
                            $returnWeight[$dt->format("Y-m-d")] = $lastRecord;
                            $returnWeight[$dt->format("Y-m-d")]['source'] = "LatestRecord";
                        } else {
                            $missingDays = $missingDays + 1;
                            $returnWeight[$dt->format("Y-m-d")] = 'Calc deviation';
                        }
                    } else {
                        $lastRecord = $returnWeight[$dt->format("Y-m-d")];
                    }
                }

                if ($missingDays > 0) {
                    ksort($returnWeight);

                    $minAndMax = array();
                    $minAndMax['startGap'] = 0;
                    $minAndMax['endGap'] = 0;
                    $markFound = false;
                    foreach ($returnWeight as $dateKey => $daysWeight) {
                        if (is_array($daysWeight) && array_key_exists("weight", $daysWeight)) {
                            if (!$markFound) {
                                $minAndMax['startGap'] = $returnWeight[$dateKey];
                            } else {
                                $minAndMax['endGap'] = $returnWeight[$dateKey];
                            }
                        } else {
                            $markFound = true;
                        }
                    }

                    if (!is_array($minAndMax['startGap'])) {
                        $minAndMax['startGap'] = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body",
                            array('date', 'weight', 'weightGoal', 'fat', 'fatGoal'),
                            array("AND" => array("user" => $this->getUserID(),
                                                 "date[<=]" => date('Y-m-d', strtotime($this->getParamDate() . " -" . ($days - 1) . " day"))
                            ), "ORDER" => "date DESC", "LIMIT" => 1));
                    }

                    print_r($minAndMax['startGap']);echo "\n";
                    print_r($minAndMax['endGap']);echo "\n";

                    $xDistance = $missingDays + 1;

                    $yStartWeight = $minAndMax['startGap']['weight'];
                    $yEndWeight = $minAndMax['endGap']['weight'];
                    $dailyChangeWeight = ($yEndWeight - $yStartWeight) / $xDistance;

                    $yStartFat = $minAndMax['startGap']['fat'];
                    $yEndFat = $minAndMax['endGap']['fat'];
                    $dailyChangeFat = ($yEndFat - $yStartFat) / $xDistance;

                    $dayNumber = 0;
                    foreach ($returnWeight as $dateKey => $daysWeight) {
                        if (!is_array($daysWeight) && $daysWeight == "Calc deviation") {
                            $dayNumber = $dayNumber + 1;
                            $calcWeight = ($dailyChangeWeight * $dayNumber) + $minAndMax['startGap']['weight'];
                            $calcFat = ($dailyChangeFat * $dayNumber) + $minAndMax['startGap']['fat'];
                            $returnWeight[$dateKey] = array("date" => $dateKey,
                                                            "weight" => $calcWeight,
                                                            "weightGoal" => $minAndMax['endGap']['weightGoal'],
                                                            "fat" => $calcFat,
                                                            "fatGoal" => $minAndMax['endGap']['fatGoal'],
                                                            "source" => "CalcDeviation");
                        }
                    }
                }

                ksort($returnWeight);
                $returnWeight = array_reverse($returnWeight);
            }

            $weights = array();
            $weightGoal = array();
            $fat = array();
            $fatGoal = array();
            foreach ($returnWeight as $db) {
                array_push($weights, (String)round($db['weight'],2));
                array_push($weightGoal, (String)$db['weightGoal']);
                array_push($fat, (String)round($db['fat'],2));
                array_push($fatGoal, (String)$db['fatGoal']);
            }

            return array('returnDate'       => explode("-", $this->getParamDate()),
                         'graph_weight'     => $weights,
                         'graph_weightGoal' => $weightGoal,
                         'graph_fat'        => $fat,
                         'graph_fatGoal'    => $fatGoal);

            //return array('graph_weight'     => $weights);
        }
    }