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
        protected $forCache;
        /**
         * @var String
         */
        protected $paramDate;
        /**
         * @var String
         */
        protected $paramPeriod;
        /**
         * @var tracking
         */
        protected $tracking;

        /**
         * @param $userFid
         */
        public function __construct($userFid) {
            require_once(dirname(__FILE__) . "/app.php");
            $this->setAppClass(new NxFitbit());
            $this->setUserID($userFid);
            $this->setForCache(TRUE);

            if (is_array($_SERVER) && array_key_exists("SERVER_NAME", $_SERVER)) {
                require_once(dirname(__FILE__) . "/tracking.php");
                $this->setTracking(new tracking($this->getAppClass()->getSetting("trackingId"), $this->getAppClass()->getSetting("trackingPath")));
            }
        }

        /**
         * @param          $userChallengeStartDate
         * @param          $userChallengeEndDate
         * @param DateTime $range_start
         *
         * @return array
         */
        private function calculateChallengeDays($userChallengeStartDate, $userChallengeEndDate, $range_start) {
            $userChallengeTrgSteps = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_steps", '10000');
            $userChallengeTrgDistance = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_distance", '5');
            $userChallengeTrgUnit = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_unit", 'km');
            $userChallengeTrgActivity = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_activity", '30');

            $db_steps = $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps";
            $db_activity = $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity";

            $dbEvents = $this->getAppClass()->getDatabase()->query(
                "SELECT `$db_steps`.`date`,`$db_steps`.`distance`,`$db_steps`.`steps`,`$db_activity`.`fairlyactive`,`$db_activity`.`veryactive`"
                . " FROM `$db_steps`"
                . " JOIN `$db_activity` ON (`$db_steps`.`date` = `$db_activity`.`date`) AND (`$db_steps`.`user` = `$db_activity`.`user`)"
                . " WHERE `$db_steps`.`user` = '" . $this->getUserID() . "' AND `$db_steps`.`date` <= '" . $userChallengeEndDate . "' AND `$db_steps`.`date` >= '$userChallengeStartDate' "
                . " ORDER BY `$db_steps`.`date` DESC");

            $days = 0;
            $score = 0;
            $scoreDistance = 0;
            $scoreVeryactive = 0;
            $scoreSteps = 0;
            $startDateCovered = FALSE;
            $calenderEvents = array();
            foreach ($dbEvents as $dbEvent) {
                if (strtotime($dbEvent['date']) >= strtotime($userChallengeStartDate) && strtotime($dbEvent['date']) <= strtotime($userChallengeEndDate)) {
                    $days += 1;

                    if ($userChallengeTrgUnit == "km") {
                        $dbEvent['distance'] = $dbEvent['distance'] * 1.609344;
                    }
                    if (strtotime($dbEvent['date']) == strtotime($userChallengeStartDate)) {
                        $startDateCovered = TRUE;
                    }

                    $dbEvent['veryactive'] = $dbEvent['fairlyactive'] + $dbEvent['veryactive'];

                    $scoreVeryactive += $dbEvent['veryactive'];
                    $scoreDistance += $dbEvent['distance'];
                    $scoreSteps += $dbEvent['steps'];

                    if (strtotime($dbEvent['date']) == strtotime(date("Y-m-d"))) {
                        if ($days > 0) {
                            $score = round(($score / $days) * 100, 2);
                        } else {
                            $score = 0;
                        }
                        array_push($calenderEvents, array("title"     => $dbEvent['steps'] . " steps"
                            . "\n" . $dbEvent['veryactive'] . " min"
                            . "\n" . number_format($dbEvent['distance'], 2) . $userChallengeTrgUnit,
                                                          "start"     => $dbEvent['date'],
                                                          'className' => 'label-today',
                                                          'distance'  => round($dbEvent['distance'], 2),
                                                          'active'    => $dbEvent['veryactive'],
                                                          'steps'     => $dbEvent['steps']));
                    } else if ($dbEvent['steps'] >= $userChallengeTrgSteps) {
                        $score = $score + 1;
                        array_push($calenderEvents, array("title" => "Past!\nSteps: " . number_format($dbEvent['steps'], 0), "start" => $dbEvent['date'], 'className' => 'label-pass'));
                    } else if ($dbEvent['veryactive'] >= $userChallengeTrgActivity) {
                        $score = $score + 1;
                        array_push($calenderEvents, array("title" => "Past!\nActivity: " . $dbEvent['veryactive'] . " min", "start" => $dbEvent['date'], 'className' => 'label-pass'));
                    } else if ($dbEvent['distance'] >= $userChallengeTrgDistance) {
                        $score = $score + 1;
                        array_push($calenderEvents, array("title" => "Past!\nDistance: " . number_format($dbEvent['distance'], 2) . $userChallengeTrgUnit, "start" => $dbEvent['date'], 'className' => 'label-pass'));
                    } else {
                        array_push($calenderEvents, array("title"                       => "Steps: " . number_format($dbEvent['steps'], 0)
                            . "\nDistance: " . number_format($dbEvent['distance'], 2) . $userChallengeTrgUnit
                            . "\nActivity: " . $dbEvent['veryactive'] . " min", "start" => $dbEvent['date'], 'className' => 'label-failed'));
                    }

                }
            }

            if (!$startDateCovered) {
                array_push($calenderEvents, array("title" => "Challenge " . $range_start->format("Y") . "\nStart!", "start" => $userChallengeStartDate, 'className' => 'label-nochallenge'));
            }

            if ($days > 0) {
                $score = round(($score / $days) * 100, 2);
            } else {
                $score = 0;
            }

            return array('score' => $score, 'veryactive' => $scoreVeryactive, 'steps' => $scoreSteps, 'distance' => $scoreDistance, 'events' => $calenderEvents);
        }

        /**
         * @param array      $returnWeight
         * @param array      $arrayOfMissingDays
         * @param array|NULL $lastRecord
         * @param array      $nextRecord
         *
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
                $returnWeight[ $date ] = array("date"       => $date,
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

        /**
         * @return NxFitbit
         */
        private function getAppClass() {
            return $this->AppClass;
        }

        /**
         * @param $input_num
         *
         * @return string
         */
        private function ordinal_suffix($input_num) {
            $num = $input_num % 100; // protect against large numbers
            if ($num < 11 || $num > 13) {
                switch ($num % 10) {
                    case 1:
                        return $input_num . 'st';
                    case 2:
                        return $input_num . 'nd';
                    case 3:
                        return $input_num . 'rd';
                }
            }

            return $input_num . 'th';
        }

        /**
         * @param NxFitbit $paramClass
         */
        private function setAppClass($paramClass) {
            $this->AppClass = $paramClass;
        }

        /**
         * @param int    $limit
         * @param string $tableName
         *
*@return array
         */
        public function dbWhere($limit = 1, $tableName = '') {
            if ($limit < 1) {
                $limit = 1;
            }
            if ($tableName != "") {
                $tableName = $tableName . ".";
            }

            if ($this->getParamPeriod() == "single") {
                return array("AND" => array($tableName . "user" => $this->getUserID(), $tableName . "date" => $this->getParamDate()));
            } else if (substr($this->getParamPeriod(), 0, strlen("last")) === "last") {
                $days = $this->getParamPeriod();
                $days = str_ireplace("last", "", $days);
                $then = date('Y-m-d', strtotime($this->getParamDate() . " -" . $days . " day"));

                return array("AND" => array($tableName . "user" => $this->getUserID(), $tableName . "date[<=]" => $this->getParamDate(), $tableName . "date[>=]" => $then), "ORDER" => $tableName . "date DESC", "LIMIT" => $days);
            } else {
                return array($tableName . "user" => $this->getUserID(), "ORDER" => $tableName . "date DESC", "LIMIT" => $limit);
            }
        }

        /**
         * @return int
         */
        public function getForCache() {
            if ($this->forCache) {
                return 1;
            } else {
                return 0;
            }
        }

        /**
         * @param bool $forCache
         */
        public function setForCache($forCache) {
            $this->forCache = $forCache;
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
         * @param $start_date
         *
*@return bool|int
         */
        public function getUserMilesSince($start_date) {
            $dbMiles = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('distance'),
                array("AND" => array("user" => $this->getUserID(), "date[>=]" => $start_date)));

            return $dbMiles;
        }

        /**
         * @return bool
         */
        public function isUser() {
            return $this->getAppClass()->isUser((String)$this->getUserID());
        }

        /**
         * @return array
         */
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
                "steps"         => round($dbSteps, 0),
                "floors"        => round($dbFloors, 0),
                "distance"      => round($dbDistance, 0),
                "stepsThisYear" => round($dbStepsYearThis, 0),
                "stepsLastYear" => round($dbStepsYearLast, 0),
            );
        }

        /**
         * @return array
         */
        public function returnUserRecordActiveGoal() {
            $lastMonday = date('Y-m-d', strtotime('last sunday'));
            $oneWeek = date('Y-m-d', strtotime($lastMonday . ' -6 days'));

            $dbActiveMinutes = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity", array('veryactive', 'fairlyactive'),
                array("AND" => array(
                    "user"     => $this->getUserID(),
                    "date[>=]" => $oneWeek,
                    "date[<=]" => $lastMonday
                ), "ORDER"  => "date DESC", "LIMIT" => 7));

            $totalMinutes = 0;
            foreach ($dbActiveMinutes as $dbStep) {
                $totalMinutes = $totalMinutes + $dbStep['veryactive'] + $dbStep['fairlyactive'];
            }

            $newTargetActive = round($totalMinutes / count($dbActiveMinutes), 0);
            if ($newTargetActive < $this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_active_max", 30)) {
                $plusTargetActive = $newTargetActive + round($newTargetActive * ($this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_active", 10) / 100), 0);
            } else {
                $plusTargetActive = $this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_active_max", 30);
            }

            return array(
                "weekStart"        => $lastMonday,
                "weekEnd"          => $oneWeek,
                "totalFloors"      => $totalMinutes,
                "newTargetFloors"  => $newTargetActive,
                "plusTargetFloors" => $plusTargetActive
            );
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
            if (substr($this->getParamPeriod(), 0, strlen("last")) === "last") {
                $days = $this->getParamPeriod();
                $sqlLimit = str_ireplace("last", "", $days);
            } else {
                $sqlLimit = 1;
            }

            $userActivity = $this->getAppClass()->getDatabase()->query("SELECT `date`,`name`,`logId`,`startDate`,`startTime`,`calories`,`duration`,`steps` "
                . "FROM `" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity_log` "
                . "WHERE `user` = '" . $this->getUserID() . "' AND `name` != 'Driving' "
                . "ORDER BY `startDate` DESC, `startTime` DESC LIMIT " . $sqlLimit)->fetchAll();

            $daysStats = array();
            $returnArray = array();
            foreach ($userActivity as $record) {
                unset($record[0]);
                unset($record[1]);
                unset($record[2]);
                unset($record[3]);
                unset($record[4]);
                unset($record[5]);
                unset($record[6]);
                unset($record[7]);

                $startTime = new DateTime($record['startDate'] . " " . $record['startTime']);
                $recKey = $startTime->format("F, Y");
                if (!array_key_exists($recKey, $returnArray) || !is_array($returnArray[ $recKey ])) {
                    $returnArray[ $recKey ] = array();
                }

                if (substr($record['name'], 0, 6) === "Skiing") {
                    $record['name'] = "Skiing";
                } else if (substr($record['name'], 0, 7) === "Sit-ups" || substr($record['name'], 0, 12) === "Calisthenics") {
                    $record['name'] = "Calisthenics (pushups, sit-ups, squats)";
                } else {
                    $record['name'] = str_ireplace(" (MyFitnessPal)", "", $record['name']);
                }
                $endTime = date("U", strtotime($record['startDate'] . " " . $record['startTime']));
                $endTime = $endTime + ($record['duration'] / 1000);
                $record['endTime'] = date("F dS \@H:i", $endTime);
                $record['duration'] = round(($record['duration'] / 1000) / 60, 0, PHP_ROUND_HALF_UP);
                $record['startTime'] = date("F dS \@H:i", strtotime($record['startDate'] . " " . $record['startTime']));

                $record['calPerMinute'] = round($record['calories'] / $record['duration'], 1);

                if (strpos(strtolower($record['name']), 'calisthenics') !== FALSE || strpos(strtolower($record['name']), 'strength') !== FALSE) {
                    $record['colour'] = "teal";
                } else if (strpos(strtolower($record['name']), 'run') !== FALSE || strpos(strtolower($record['name']), 'walk') !== FALSE) {
                    $record['colour'] = "green";
                } else if (strpos(strtolower($record['name']), 'skiing') !== FALSE) {
                    $record['colour'] = "purple";
                } else {
                    $record['colour'] = "bricky";
                }

                $record['calories'] = number_format($record['calories'], 0);
                $record['steps'] = number_format($record['steps'], 0);

                if (!array_key_exists($record['startDate'], $daysStats)) {
                    $daysStats[ $record['startDate'] ] = array();

                    $db_steps = $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps";
                    $db_activity = $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity";
                    $dbDaysStatsDb = $this->getAppClass()->getDatabase()->query(
                        "SELECT `$db_steps`.`caloriesOut`,`$db_steps`.`steps`,`$db_activity`.`fairlyactive`,`$db_activity`.`veryactive`"
                        . " FROM `$db_steps`"
                        . " JOIN `$db_activity` ON (`$db_steps`.`date` = `$db_activity`.`date`) AND (`$db_steps`.`user` = `$db_activity`.`user`)"
                        . " WHERE `$db_activity`.`user` = '" . $this->getUserID() . "' AND `$db_activity`.`date` = '" . $record['startDate'] . "'"
                        . " ORDER BY `$db_activity`.`date` DESC");

                    foreach ($dbDaysStatsDb as $dbValue) {
                        $daysStats[ $record['startDate'] ]['active'] = number_format($dbValue['fairlyactive'] + $dbValue['veryactive'], 0);
                        $daysStats[ $record['startDate'] ]['caloriesOut'] = number_format($dbValue['caloriesOut'], 0);
                        $daysStats[ $record['startDate'] ]['steps'] = number_format($dbValue['steps'], 0);
                    }
                }

                $record['stats'] = $daysStats[ $record['startDate'] ];

                unset($record['startDate']);
                unset($record['date']);


                $tcxFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tcx' . DIRECTORY_SEPARATOR . $record['logId'] . '.tcx';
                if (!file_exists($tcxFile)) {
                    if (isset($_COOKIE['_nx_fb_key']) AND $_COOKIE['_nx_fb_key'] == hash("sha256", $this->getAppClass()->getSetting("salt") . $_SERVER['SERVER_SIGNATURE'] . $_COOKIE['_nx_fb_usr'] . $_SERVER['SERVER_NAME'])) {
                        $record['link'] = "https://www.fitbit.com/activities/exercise/" . $record['logId'] . "?export=tcx";
                        $record['gpx'] = "download";
                        $this->setForCache(FALSE);
                    } else {
                        $record['gpx'] = "none";
                    }
                } else {
                    if (!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $record['logId'] . '.gpx')) {
                        if (is_writable(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache')) {
                            $this->returnUserRecordActivityTCX($record['logId'], $record['name'] . ": " . $record['startTime']);
                            $record['gpx'] = DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "fitbit" . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $record['logId'] . '.gpx';
                        } else {
                            $record['gpx'] = "none";
                        }
                    } else {
                        $record['gpx'] = DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "fitbit" . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $record['logId'] . '.gpx';
                    }
                }

                array_push($returnArray[ $recKey ], $record);
            }

            return $returnArray;
        }

        /**
         * @param null $tcxFileName
         * @param null $tcxTrackName
         *
         * @return array
         */
        public function returnUserRecordActivityTCX($tcxFileName = NULL, $tcxTrackName = NULL) {
            if (is_null($tcxFileName)) {
                if (array_key_exists("tcx", $_GET)) {
                    $tcxFileName = $_GET['tcx'];
                }
            }

            if (!is_null($tcxFileName)) {
                if (is_null($tcxTrackName)) {
                    $tcxTrackName = $tcxFileName . " Fitbit Track";
                }

                $tcxFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tcx' . DIRECTORY_SEPARATOR . $tcxFileName . '.tcx';

                if (file_exists($tcxFile)) {
                    $items = simplexml_load_file($tcxFile);
                    if (!is_object($items)) {
                        $items = simplexml_load_file($tcxFile);
                        if (!is_object($items)) {
                            $items = simplexml_load_file($tcxFile);
                            if (!is_object($items)) {
                                return array("error" => "Failed to read $tcxFileName TCX file", "return" => array("Id"               => "Failed to read $tcxFileName TCX file",
                                                                                                                  "TotalTimeSeconds" => 0,
                                                                                                                  "DistanceMeters"   => 0,
                                                                                                                  "Calories"         => 0,
                                                                                                                  "Intensity"        => 0,
                                                                                                                  "LatitudeDegrees"  => "56.462018",
                                                                                                                  "LongitudeDegrees" => "-2.970721",
                                                                                                                  "gpx"              => ""));
                            }
                        }
                    }

                    if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $tcxFileName . '.gpx')) {
                        $gpxFileName = DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "fitbit" . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $tcxFileName . '.gpx';
                    } else {
                        /** @lang XML */
                        $gpx = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
                        $gpx .= "<gpx creator=\"NxFit - http://nxfifteen.me.uk\" ";
                        $gpx .= "\n   xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd\"";
                        $gpx .= "\n   xmlns=\"http://www.topografix.com/GPX/1/1\"";
                        $gpx .= "\n   xmlns:gpxtpx=\"http://www.garmin.com/xmlschemas/TrackPointExtension/v1\"";
                        $gpx .= "\n   xmlns:gpxx=\"http://www.garmin.com/xmlschemas/GpxExtensions/v3\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">";
                        $gpx .= "\n <metadata>";
                        $gpx .= "\n  <name>" . $tcxTrackName . "</name>";
                        $gpx .= "\n  <link href=\"http://nxfifteen.me.uk/\"><text>" . $tcxFileName . " Fitbit Track</text></link>";
                        $gpx .= "\n  <time>" . $items->Activities->Activity->Id . "</time>";
                        $gpx .= "\n </metadata>";
                        $gpx .= "\n <trk>";
                        $gpx .= "\n  <name>" . $tcxTrackName . "</name>";

                        $gpx .= "\n  <trkseg>";

                        foreach ($items->Activities->Activity->Lap->Track->Trackpoint as $trkpt) {
                            $gpx .= "\n   <trkpt lat=\"" . $trkpt->Position->LatitudeDegrees . "\" lon=\"" . $trkpt->Position->LongitudeDegrees . "\">";
                            $gpx .= "\n    <time>" . $trkpt->Time . "</time>";
                            if (isset($trkpt->AltitudeMeters)) {
                                $gpx .= "\n    <ele>" . $trkpt->AltitudeMeters . "</ele>";
                            } else {
                                $gpx .= "\n    <ele>0</ele>";
                            }
                            $gpx .= "\n    <extensions>";
                            $gpx .= "\n     <gpxtpx:TrackPointExtension>";
                            $gpx .= "\n      <gpxtpx:hr>" . $trkpt->HeartRateBpm->Value . "</gpxtpx:hr>";
                            $gpx .= "\n     </gpxtpx:TrackPointExtension>";
                            $gpx .= "\n    </extensions>";
                            $gpx .= "\n   </trkpt>";
                        }

                        $gpx .= "\n  </trkseg>";
                        $gpx .= "\n </trk>";
                        $gpx .= "\n</gpx>";

                        $fh = fopen(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $tcxFileName . '.gpx', 'w');
                        fwrite($fh, $gpx);
                        fclose($fh);

                        if (!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $tcxFileName . '.gpx')) {
                            $gpxFileName = "File Missing";
                        } else {
                            $gpxFileName = DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "fitbit" . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $tcxFileName . '.gpx';
                        }
                    }

                    $trackPoint = $items->Activities->Activity->Lap->Track->Trackpoint;

                    return array("Id"               => (String)$items->Activities->Activity->Id,
                                 "TotalTimeSeconds" => (String)$items->Activities->Activity->Lap->TotalTimeSeconds,
                                 "DistanceMeters"   => (String)$items->Activities->Activity->Lap->DistanceMeters,
                                 "Calories"         => (String)$items->Activities->Activity->Lap->Calories,
                                 "Intensity"        => (String)$items->Activities->Activity->Lap->Intensity,
                                 "LatitudeDegrees"  => (String)$trackPoint[0]->Position->LatitudeDegrees,
                                 "LongitudeDegrees" => (String)$trackPoint[0]->Position->LongitudeDegrees,
                                 "gpx"              => $gpxFileName);
                } else {
                    return array("error" => "TCX file for $tcxFileName is missing", "return" => array("Id"               => "TCX file for $tcxFileName is missing",
                                                                                                      "TotalTimeSeconds" => 0,
                                                                                                      "DistanceMeters"   => 0,
                                                                                                      "Calories"         => 0,
                                                                                                      "Intensity"        => 0,
                                                                                                      "LatitudeDegrees"  => "56.462018",
                                                                                                      "LongitudeDegrees" => "-2.970721",
                                                                                                      "gpx"              => ""));
                }
            } else {
                return array("error" => "You must set an activity id", "return" => array("Id"               => "You must set an activity id",
                                                                                         "TotalTimeSeconds" => 0,
                                                                                         "DistanceMeters"   => 0,
                                                                                         "Calories"         => 0,
                                                                                         "Intensity"        => 0,
                                                                                         "LatitudeDegrees"  => "56.462018",
                                                                                         "LongitudeDegrees" => "-2.970721",
                                                                                         "gpx"              => ""));
            }
        }

        /**
         * @return array
         */
        public function returnUserRecordBadges() {
            $userBadges = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_badge2usr", array(
                "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages" => array("badgeType" => "badgeType"),
                "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages" => array("value" => "value")),
                array(
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'lnk_badge2usr.badgeType',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'lnk_badge2usr.value',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'lnk_badge2usr.dateTime',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'lnk_badge2usr.timesAchieved',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'bages.image',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'bages.badgeGradientEndColor',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'bages.badgeGradientStartColor',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'bages.earnedMessage',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'bages.marketingdescription',
                    $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'bages.name',
                ), array($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_badge2usr.user" => $this->getUserID(),
                         "ORDER"                                                                           => $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_badge2usr.value ASC"));

            $badges = array();
            foreach ($userBadges as $userBadge) {
                $badge_key = $this->getAppClass()->getSetting("badge_key_" . strtolower($userBadge['badgeType']), $userBadge['badgeType']);
                if (!array_key_exists($badge_key, $badges)) {
                    $badges[ $badge_key ] = array();
                }

                array_push($badges[ $badge_key ], $userBadge);
            }

            return array("images" => "images/badges/", "badges" => $badges);
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
         * @return array
         */
        public function returnUserRecordChallenger() {
            $userChallengeLength = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_length", '50');
            $userChallengeStartString = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID(), '03-31 last sunday'); // Default to last Sunday in March
            $userChallengeStartDate = date("Y-m-d", strtotime(date("Y") . '-' . $userChallengeStartString)); // Default to last Sunday in March
            $userChallengeEndDate = date("Y-m-d", strtotime($userChallengeStartDate . ' +' . $userChallengeLength . ' day')); // Default to last Sunday in March

            $userChallengeTrgSteps = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_steps", '10000');
            $userChallengeTrgDistance = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_distance", '5');
            $userChallengeTrgUnit = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_unit", 'km');
            $userChallengeTrgActivity = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_activity", '30');

            $userChallengePassMark = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_passmark", '95');

            $dbChallenge = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "challenge",
                array('startDate', 'endDate', 'score', 'steps', 'distance', 'veryactive'),
                array("user" => $this->getUserID()));

            if (!$dbChallenge) {
                $dbChallenge = array();
            }

            if (count($dbChallenge) > 0) {
                foreach ($dbChallenge as $index => $challenge) {
                    $dbChallenge[ $index ]['score'] = round($dbChallenge[ $index ]['score'], 0, PHP_ROUND_HALF_UP);
                    if ($challenge['score'] >= 98) {
                        $dbChallenge[ $index ]['pass'] = 2;
                    } else if ($challenge['score'] >= $userChallengePassMark) {
                        $dbChallenge[ $index ]['pass'] = 1;
                    } else {
                        $dbChallenge[ $index ]['pass'] = 0;
                    }
                }
            }

            $today = strtotime(date("Y-m-d"));
            if ($today >= strtotime($userChallengeStartDate) && $today <= strtotime($userChallengeEndDate)) {
                $currentChallenge = array();

                $days = 0;
                $daysPast = 0;
                $dbChallengeRec = $this->calculateChallengeDays($userChallengeStartDate, $userChallengeEndDate, new DateTime($userChallengeStartDate));
                foreach ($dbChallengeRec['events'] as $dayRecord) {
                    if ($dayRecord['className'] == "label-pass") {
                        $days += 1;
                        $daysPast += 1;
                    } else if ($dayRecord['className'] == "label-today") {
                        $currentChallenge['distance'] = $dayRecord['distance'];
                        $currentChallenge['active'] = $dayRecord['active'];
                        $currentChallenge['steps'] = $dayRecord['steps'];
                    } else {
                        $days += 1;
                    }
                }


                $currentChallenge['steps_g'] = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_steps", '10000');
                $currentChallenge['distance_g'] = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_distance", '5');
                $currentChallenge['active_g'] = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_activity", '30');

                $currentChallenge['day'] = $days;
                $currentChallenge['day_past'] = $daysPast;
                $currentChallenge['score'] = ($currentChallenge['day_past'] / $currentChallenge['day']) * 100;

                return array(
                    'challengeActive' => 'active',
                    'challengeLength' => $userChallengeLength,
                    'scores'          => $dbChallenge,
                    'current'         => $currentChallenge,
                    'goals'           => array('Activity' => $userChallengeTrgActivity, 'Steps' => $userChallengeTrgSteps, 'Distance' => $userChallengeTrgDistance, 'Unit' => $userChallengeTrgUnit),
                    'next'            => array('startDate' => $userChallengeStartDate, 'startDateF' => date("jS F, Y", strtotime($userChallengeStartDate)), 'endDate' => $userChallengeEndDate, 'endDateF' => date("jS F, Y", strtotime($userChallengeEndDate)))
                );
            } else if ($today > strtotime($userChallengeStartDate)) {
                $plusOneChallengeStartDate = date("Y-m-d", strtotime((date("Y") + 1) . '-' . $userChallengeStartString)); // Default to last Sunday in March
                $plusOneChallengeEndDate = date("Y-m-d", strtotime($plusOneChallengeStartDate . ' +' . $userChallengeLength . ' day')); // Default to last Sunday in March

                return array(
                    'challengeActive' => 'past',
                    'challengeLength' => $userChallengeLength,
                    'showDate'        => $userChallengeStartDate,
                    'scores'          => $dbChallenge,
                    'goals'           => array('Activity' => $userChallengeTrgActivity, 'Steps' => $userChallengeTrgSteps, 'Distance' => $userChallengeTrgDistance, 'Unit' => $userChallengeTrgUnit),
                    'next'            => array('startDate' => $plusOneChallengeStartDate, 'startDateF' => date("jS F, Y", strtotime($plusOneChallengeStartDate)), 'endDate' => $plusOneChallengeEndDate, 'endDateF' => date("jS F, Y", strtotime($plusOneChallengeEndDate))),
                    'last'            => array('startDate' => $userChallengeStartDate, 'startDateF' => date("jS F, Y", strtotime($userChallengeStartDate)), 'endDate' => $userChallengeEndDate, 'endDateF' => date("jS F, Y", strtotime($userChallengeEndDate)))
                );
            } else if ($today < strtotime($userChallengeStartDate)) {

                $nimusOneChallengeStartDate = date("Y-m-d", strtotime((date("Y") - 1) . '-' . $userChallengeStartString)); // Default to last Sunday in March
                $nimusOneChallengeEndDate = date("Y-m-d", strtotime($nimusOneChallengeStartDate . ' +' . $userChallengeLength . ' day')); // Default to last Sunday in March

                return array(
                    'challengeActive' => 'future',
                    'challengeLength' => $userChallengeLength,
                    'showDate'        => $nimusOneChallengeStartDate,
                    'scores'          => $dbChallenge,
                    'goals'           => array('Activity' => $userChallengeTrgActivity, 'Steps' => $userChallengeTrgSteps, 'Distance' => $userChallengeTrgDistance, 'Unit' => $userChallengeTrgUnit),
                    'next'            => array('startDate' => $userChallengeStartDate, 'startDateF' => date("jS F, Y", strtotime($userChallengeStartDate)), 'endDate' => $userChallengeEndDate, 'endDateF' => date("jS F, Y", strtotime($userChallengeEndDate))),
                    'last'            => array('startDate' => $nimusOneChallengeStartDate, 'startDateF' => date("jS F, Y", strtotime($nimusOneChallengeStartDate)), 'endDate' => $nimusOneChallengeEndDate, 'endDateF' => date("jS F, Y", strtotime($nimusOneChallengeEndDate)))
                );
            }

            return array();
        }

        /**
         * @return array
         */
        public function returnUserRecordChallengerCalendar() {
            // Short-circuit if the client did not give us a date range.
            if (!isset($_GET['start']) || !isset($_GET['end'])) {
                return array("error" => "true", "code" => 105, "msg" => "No start or end date given");
            }

            $range_start = new DateTime($_GET['start']);
            $range_end = new DateTime($_GET['end']);

            $userChallengeLength = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID() . "_length", '50');
            $userChallengeStartDate = $this->getAppClass()->getSetting("usr_challenger_" . $this->getUserID(), '03-31 last sunday'); // Default to last Sunday in March
            $userChallengeStartDate = date("Y-m-d", strtotime($range_end->format("Y") . '-' . $userChallengeStartDate)); // Default to last Sunday in March
            $userChallengeEndDate = date("Y-m-d", strtotime($userChallengeStartDate . ' +' . $userChallengeLength . ' day')); // Default to last Sunday in March

            $calenderEvents = array();
            if (strtotime($userChallengeEndDate) <= strtotime(date("Y-m-d"))) {
                $dbChallenge = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "challenge",
                    'dayData',
                    array("AND" => array("user"      => $this->getUserID(),
                                         "startDate" => $userChallengeStartDate,
                                         "endDate"   => $userChallengeEndDate
                    ), "LIMIT"  => 1));

                if (!$dbChallenge) {
                    $calenderEvents = $this->calculateChallengeDays($userChallengeStartDate, $userChallengeEndDate, $range_start);
                    if (!array_key_exists("debug", $_GET) or $_GET['debug'] != "true") {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "challenge", array(
                            'user'       => $this->getUserID(),
                            'startDate'  => $userChallengeStartDate,
                            'endDate'    => $userChallengeEndDate,
                            'score'      => $calenderEvents['score'],
                            'veryactive' => $calenderEvents['veryactive'],
                            'steps'      => $calenderEvents['steps'],
                            'distance'   => $calenderEvents['distance'],
                            'dayData'    => json_encode($calenderEvents['events'])
                        ));
                    }
                } else {
                    $calenderEvents['events'] = json_decode($dbChallenge[0]);
                }
            } else {
                $calenderEvents = $this->calculateChallengeDays($userChallengeStartDate, $userChallengeEndDate, $range_start);
            }

            return array('sole' => TRUE, 'return' => $calenderEvents['events']);
        }

        /**
         * @return array
         */
        public function returnUserRecordConky() {
            $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('distance', 'floors', 'steps'),
                $this->dbWhere());

            if (count($dbSteps) > 0) {
                $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals",
                    array('distance', 'floors', 'steps'),
                    $this->dbWhere());
                if (count($dbGoals) == 0) {
                    // If todays goals are missing download the most recent goals
                    $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals",
                        array('distance', 'floors', 'steps'),
                        array("user" => $this->getUserID(), "ORDER" => "date DESC"));
                }

                $dbActiveMinutes = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity",
                    array(
                        'target',
                        'fairlyactive',
                        'veryactive'
                    ),
                    array("AND" => array(
                        "user" => $this->getUserID(),
                        "date" => date("Y-m-d")
                    ), "ORDER"  => "date ASC"));
                $dbActiveMinutes = array_pop($dbActiveMinutes);
                $dbGoals[0]['activity'] = (String)round($dbActiveMinutes['target'], 2);
                $dbActiveMinutes = $dbActiveMinutes['fairlyactive'] + $dbActiveMinutes['veryactive'];

                $dbSteps[0]['activity'] = $dbActiveMinutes;

                $dbSteps[0]['activity_p'] = ($dbActiveMinutes / $dbGoals[0]['activity']) * 100;
                $dbSteps[0]['steps_p'] = ($dbSteps[0]['steps'] / $dbGoals[0]['steps']) * 100;
                $dbSteps[0]['floors_p'] = ($dbSteps[0]['floors'] / $dbGoals[0]['floors']) * 100;
                $dbSteps[0]['distance_p'] = ($dbSteps[0]['distance'] / $dbGoals[0]['distance']) * 100;

                if ($dbSteps[0]['activity_p'] > 100) $dbSteps[0]['activity_p'] = 100;
                if ($dbSteps[0]['steps_p'] > 100) $dbSteps[0]['steps_p'] = 100;
                if ($dbSteps[0]['floors_p'] > 100) $dbSteps[0]['floors_p'] = 100;
                if ($dbSteps[0]['distance_p'] > 100) $dbSteps[0]['distance_p'] = 100;

                $dbSteps[0]['distance'] = (String)round($dbSteps[0]['distance'], 2);
                $dbGoals[0]['distance'] = (String)round($dbGoals[0]['distance'], 2);

                $challange = $this->returnUserRecordChallenger();
                $challange = array(
                    "active"     => $challange['challengeActive'],
                    "startDateF" => $challange['next']['startDateF'],
                    "endDateF"   => $challange['next']['endDateF'],
                    "activity"   => ($challange['current']['active'] / $challange['current']['active_g']) * 100,
                    "distance"   => ($challange['current']['distance'] / $challange['current']['distance_g']) * 100,
                    "steps"      => ($challange['current']['steps'] / $challange['current']['steps_g']) * 100
                );

                if ($challange['activity'] > 100) $challange['activity'] = 100;
                if ($challange['distance'] > 100) $challange['distance'] = 100;
                if ($challange['steps'] > 100) $challange['steps'] = 100;

                $journeys = $this->returnUserRecordJourneysState();
                $journeys = array_pop($journeys);
                $journeys = array(
                    "name"  => $journeys['name'],
                    "blurb" => $journeys['blurb'],
                );

                if (!is_null($this->getTracking())) {
                    $this->getTracking()->track("JSON Get", $this->getUserID(), "Steps");
                    $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");
                }

                return array('recorded' => $dbSteps[0], 'goal' => $dbGoals[0], 'challange' => $challange, 'journeys' => $journeys);
            } else {
                return array("error" => "true", "code" => 104, "msg" => "No results for given date");
            }
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
                $dbDevices[ $key ]['image'] = 'images/devices/' . str_ireplace(" ", "", $dbDevices[ $key ]['deviceVersion']) . ".png";
                $dbDevices[ $key ]['imageSmall'] = 'images/devices/' . str_ireplace(" ", "", $dbDevices[ $key ]['deviceVersion']) . "_small.png";
                if (strtolower($dbDevices[ $key ]['battery']) == "high") {
                    $dbDevices[ $key ]['precentage'] = 100;
                } else if (strtolower($dbDevices[ $key ]['battery']) == "medium") {
                    $dbDevices[ $key ]['precentage'] = 50;
                } else if (strtolower($dbDevices[ $key ]['battery']) == "low") {
                    $dbDevices[ $key ]['precentage'] = 10;
                } else if (strtolower($dbDevices[ $key ]['battery']) == "empty") {
                    $dbDevices[ $key ]['precentage'] = 0;
                }

                $dbDevices[ $key ]['unixTime'] = strtotime($dbDevices[ $key ]['lastSyncTime']);
                if ($dbDevices[ $key ]['type'] == "TRACKER") {
                    $dbDevices[ $key ]['testTime'] = strtotime('now') - (4 * 60 * 60);
                } else {
                    $dbDevices[ $key ]['testTime'] = strtotime('now') - (48 * 60 * 60);
                }

                if ($dbDevices[ $key ]['testTime'] > $dbDevices[ $key ]['unixTime']) {
                    $dbDevices[ $key ]['alertTime'] = 1;
                } else {
                    $dbDevices[ $key ]['alertTime'] = 0;
                }
            }

            return $dbDevices;
        }

        /**
         * @return array
         */
        public function returnUserRecordFloorGoal() {
            $lastMonday = date('Y-m-d', strtotime('last sunday'));
            $oneWeek = date('Y-m-d', strtotime($lastMonday . ' -6 days'));

            $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'floors',
                array("AND" => array(
                    "user"     => $this->getUserID(),
                    "date[>=]" => $oneWeek,
                    "date[<=]" => $lastMonday
                ), "ORDER"  => "date DESC", "LIMIT" => 7));

            $totalSteps = 0;
            foreach ($dbSteps as $dbStep) {
                $totalSteps = $totalSteps + $dbStep;
            }

            $newTargetSteps = round($totalSteps / count($dbSteps), 0);
            if ($newTargetSteps < $this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_floors_max", 20)) {
                $plusTargetSteps = $newTargetSteps + round($newTargetSteps * ($this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_floors", 10) / 100), 0);
            } else {
                $plusTargetSteps = $this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_floors_max", 20);
            }

            return array(
                "weekStart"        => $lastMonday,
                "weekEnd"          => $oneWeek,
                "totalFloors"      => $totalSteps,
                "newTargetFloors"  => $newTargetSteps,
                "plusTargetFloors" => $plusTargetSteps
            );
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
         * @return array
         */
        public function returnUserRecordFoodDiary() {
            $returnArray = array();

            $where = $this->dbWhere();
            if (!array_key_exists("LIMIT", $where) OR $where['LIMIT'] == 1) {
                unset($where['AND']['date[<=]']);
                unset($where['AND']['date[>=]']);
                $where['AND']['date'] = $this->getParamDate();
            }

            $dbWater = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water", 'liquid', $where);
            if (!array_key_exists("LIMIT", $where) OR $where['LIMIT'] == 1) {
                /** @var float $dbWater */
                $returnArray['water'] = array("liquid" => (String)round($dbWater, 2), "goal" => $this->getAppClass()->getSetting("usr_goal_water_" . $this->getUserID(), '200'));
            } else {
                /** @var float $dbWater */
                $returnArray['water'] = array("liquid" => (String)round($dbWater, 2), "goal" => ($this->getAppClass()->getSetting("usr_goal_water_" . $this->getUserID(), '200') * $where['LIMIT']));
            }

            $returnArray['food'] = array();
            $returnArray['food']['summary'] = array();
            $returnArray['food']['goals'] = array();
            $returnArray['food']['meals'] = array();
            $returnArray['food']['summary']['calories'] = 0;
            $returnArray['food']['summary']['carbs'] = 0;
            $returnArray['food']['summary']['fat'] = 0;
            $returnArray['food']['summary']['fiber'] = 0;
            $returnArray['food']['summary']['protein'] = 0;
            $returnArray['food']['summary']['sodium'] = 0;

            $returnArray['food']['goals']['calories'] = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "goals_calories", 'calories', $where);

            if (!array_key_exists("LIMIT", $where) OR $where['LIMIT'] == 1) {
                $returnArray['food']['goals']['carbs'] = $this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_carbs", 310);
                $returnArray['food']['goals']['fat'] = $this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_fat", 70);
                $returnArray['food']['goals']['fiber'] = $this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_fiber", 30);
                $returnArray['food']['goals']['protein'] = $this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_protein", 50);
                $returnArray['food']['goals']['sodium'] = $this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_sodium", 2300);
            } else {
                $returnArray['food']['goals']['carbs'] = ($this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_carbs", 310) * $where['LIMIT']);
                $returnArray['food']['goals']['fat'] = ($this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_fat", 70) * $where['LIMIT']);
                $returnArray['food']['goals']['fiber'] = ($this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_fiber", 30) * $where['LIMIT']);
                $returnArray['food']['goals']['protein'] = ($this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_protein", 50) * $where['LIMIT']);
                $returnArray['food']['goals']['sodium'] = ($this->getAppClass()->getSetting("food_goal_" . $this->getUserID() . "_sodium", 2300) * $where['LIMIT']);
            }

            unset($where['LIMIT']);
            $dbFood = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood",
                array('date', 'meal', 'calories', 'carbs', 'fat', 'fiber', 'protein', 'sodium'),
                $where);

            foreach ($dbFood as $meal) {
                if (!isset($returnArray['food']['meals'][ $meal['meal'] ]['calories'])) $returnArray['food']['meals'][ $meal['meal'] ]['calories'] = 0;
                if (!isset($returnArray['food']['meals'][ $meal['meal'] ]['carbs'])) $returnArray['food']['meals'][ $meal['meal'] ]['carbs'] = 0;
                if (!isset($returnArray['food']['meals'][ $meal['meal'] ]['fat'])) $returnArray['food']['meals'][ $meal['meal'] ]['fat'] = 0;
                if (!isset($returnArray['food']['meals'][ $meal['meal'] ]['fiber'])) $returnArray['food']['meals'][ $meal['meal'] ]['fiber'] = 0;
                if (!isset($returnArray['food']['meals'][ $meal['meal'] ]['protein'])) $returnArray['food']['meals'][ $meal['meal'] ]['protein'] = 0;
                if (!isset($returnArray['food']['meals'][ $meal['meal'] ]['sodium'])) $returnArray['food']['meals'][ $meal['meal'] ]['sodium'] = 0;

                $returnArray['food']['meals'][ $meal['meal'] ]['calories'] = $returnArray['food']['meals'][ $meal['meal'] ]['calories'] + $meal['calories'];
                $returnArray['food']['meals'][ $meal['meal'] ]['carbs'] = $returnArray['food']['meals'][ $meal['meal'] ]['carbs'] + $meal['carbs'];
                $returnArray['food']['meals'][ $meal['meal'] ]['fat'] = $returnArray['food']['meals'][ $meal['meal'] ]['fat'] + $meal['fat'];
                $returnArray['food']['meals'][ $meal['meal'] ]['fiber'] = $returnArray['food']['meals'][ $meal['meal'] ]['fiber'] + $meal['fiber'];
                $returnArray['food']['meals'][ $meal['meal'] ]['protein'] = $returnArray['food']['meals'][ $meal['meal'] ]['protein'] + $meal['protein'];
                $returnArray['food']['meals'][ $meal['meal'] ]['sodium'] = $returnArray['food']['meals'][ $meal['meal'] ]['sodium'] + $meal['sodium'];

                $returnArray['food']['summary']['calories'] += $meal['calories'];
                $returnArray['food']['summary']['carbs'] += $meal['carbs'];
                $returnArray['food']['summary']['fat'] += $meal['fat'];
                $returnArray['food']['summary']['fiber'] += $meal['fiber'];
                $returnArray['food']['summary']['protein'] += $meal['protein'];
                $returnArray['food']['summary']['sodium'] += $meal['sodium'];
            }
            foreach ($dbFood as $meal) {
                $returnArray['food']['meals'][ $meal['meal'] ]['precentage'] = ($meal['calories'] / $returnArray['food']['summary']['calories']) * 100;
            }

            ksort($returnArray['food']['meals']);

            return $returnArray;
        }

        /**
         * @return array
         */
        public function returnUserRecordJourneys() {
            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_travellers", array("fuid" => $this->getUserID()))) {
                $dbJourneys = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_travellers", array(
                    "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys" => array("jid" => "jid")),
                    array(
                        $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys.jid',
                        $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys.name',
                        $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys.blurb',
                        $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_travellers.start_date',
                    ), array($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_travellers.fuid" => $this->getUserID()));

                $journeys = array();
                foreach ($dbJourneys as $dbJourney) {
                    $user_miles_travelled = $this->getUserMilesSince($dbJourney['start_date']);

                    $dbLegs = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_legs", array(
                        "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys" => array("jid" => "jid")),
                        array(
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_legs.lid',
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_legs.name',
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_legs.end_mile',
                        ), array("AND" => array(
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys.jid"                 => $dbJourney['jid'],
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_legs.start_mile[<=]" => $user_miles_travelled,
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_legs.end_mile[>=]"   => $user_miles_travelled
                        )));

                    $legs = array();
                    $legsNames = array();
                    $legsProgress = array();
                    foreach ($dbLegs as $dbLeg) {
                        // Get all narative items the user has completed
                        $dbNarratives = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_narrative", array(
                            "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_legs" => array("lid" => "lid")),
                            array(
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.nid',
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.miles',
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.subtitle',
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.narrative',
                            ), array("AND" => array(
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_narrative.lid" => $dbLeg['lid']
                            )));

                        $narrative = array();
                        $prevNarrativeMiles = 0;
                        foreach ($dbNarratives as $dbNarrative) {
                            $narrativeArray = array(
                                "miles"           => $dbNarrative['miles'],
                                "miles_travelled" => $dbNarrative['miles'] - $prevNarrativeMiles,
                                "miles_off"       => 0,
                                "subtitle"        => $dbNarrative['subtitle'],
                                "narrative"       => $dbNarrative['narrative']
                            );
                            $prevNarrativeMiles = $dbNarrative['miles'];

                            if ($dbNarrative['miles'] > $user_miles_travelled) {
                                $narrativeArray["miles_off"] = number_format($dbNarrative['miles'] - $user_miles_travelled, 2);
                            }

                            array_push($narrative, $narrativeArray);
                        }

                        $legsProgress[ $dbLeg['lid'] ] = number_format((($user_miles_travelled / $dbLeg['end_mile']) * 100), 2);
                        $legsNames[ $dbLeg['lid'] ] = $dbLeg['name'];
                        $legs[ $dbLeg['lid'] ] = $narrative;
                    }

                    $journeys[ $dbJourney['jid'] ] = array(
                        "name"          => $dbJourney['name'],
                        "start_date"    => $dbJourney['start_date'],
                        "usrMiles"      => number_format($this->getUserMilesSince($dbJourney['start_date']), 2),
                        "blurb"         => $dbJourney['blurb'],
                        "legs_names"    => $legsNames,
                        "legs_progress" => $legsProgress,
                        "legs"          => $legs
                    );
                }

                return $journeys;
            } else {
                return array("error" => "true", "code" => 104, "msg" => "Not on any jounry");
            }
        }

        /**
         * @return array
         */
        public function returnUserRecordJourneysState() {
            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_travellers", array("fuid" => $this->getUserID()))) {
                $dbJourneys = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_travellers", array(
                    "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys" => array("jid" => "jid")),
                    array(
                        $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys.jid',
                        $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys.name',
                        $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys.blurb',
                        $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_travellers.start_date',
                    ), array($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_travellers.fuid" => $this->getUserID()));

                $journeys = array();
                foreach ($dbJourneys as $dbJourney) {
                    $user_miles_travelled = $this->getUserMilesSince($dbJourney['start_date']);

                    $dbLegs = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_legs", array(
                        "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys" => array("jid" => "jid")),
                        array(
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_legs.lid',
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_legs.name',
                        ), array("AND" => array(
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys.jid"                 => $dbJourney['jid'],
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_legs.start_mile[<=]" => $user_miles_travelled,
                            $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_legs.end_mile[>=]"   => $user_miles_travelled
                        )));

                    $legs = array();
                    foreach ($dbLegs as $dbLeg) {
                        // Get all narative items the user has completed
                        $dbNarratives = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_narrative", array(
                            "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_legs" => array("lid" => "lid")),
                            array(
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.nid',
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.miles',
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.subtitle',
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.narrative',
                            ), array("AND" => array(
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_narrative.lid"       => $dbLeg['lid'],
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_narrative.miles[<=]" => $user_miles_travelled
                            ), "LIMIT"     => 1, "ORDER" => $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_narrative.miles DESC"));

                        $prevNarrativeMiles = 0;
                        foreach ($dbNarratives as $dbNarrative) {
                            $narrativeArray = array(
                                "legs_names"      => $dbLeg['name'],
                                "miles"           => $dbNarrative['miles'],
                                "miles_travelled" => $dbNarrative['miles'] - $prevNarrativeMiles,
                                "miles_off"       => 0,
                                "subtitle"        => $dbNarrative['subtitle'],
                                "narrative"       => $dbNarrative['narrative']
                            );
                            $prevNarrativeMiles = $dbNarrative['miles'];

                            if ($dbNarrative['miles'] > $user_miles_travelled) {
                                $narrativeArray["miles_off"] = number_format($dbNarrative['miles'] - $user_miles_travelled, 2);
                            }

                            $legs['last'] = $narrativeArray;
                        }

                        $dbNarrativeNext = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_narrative", array(
                            "[>]" . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_legs" => array("lid" => "lid")),
                            array(
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.nid',
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.miles',
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.subtitle',
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'journeys_narrative.narrative',
                            ), array("AND" => array(
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_narrative.lid"       => $dbLeg['lid'],
                                $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_narrative.miles[>=]" => $user_miles_travelled
                            ), "LIMIT"     => 1, "ORDER" => $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "journeys_narrative.miles ASC"));

                        foreach ($dbNarrativeNext as $dbNarrative) {
                            $narrativeArray = array(
                                "legs_names"      => $dbLeg['name'],
                                "miles"           => $dbNarrative['miles'],
                                "miles_travelled" => $dbNarrative['miles'] - $prevNarrativeMiles,
                                "miles_off"       => 0,
                                "subtitle"        => $dbNarrative['subtitle'],
                                "narrative"       => $dbNarrative['narrative']
                            );
                            $prevNarrativeMiles = $dbNarrative['miles'];

                            if ($dbNarrative['miles'] > $user_miles_travelled) {
                                $narrativeArray["miles_off"] = number_format($dbNarrative['miles'] - $user_miles_travelled, 2);
                            }

                            $legs['next'] = $narrativeArray;
                        }
                    }

                    $journeys[ $dbJourney['jid'] ] = array(
                        "name"       => $dbJourney['name'],
                        "start_date" => $dbJourney['start_date'],
                        "usrMiles"   => number_format($this->getUserMilesSince($dbJourney['start_date']), 2),
                        "blurb"      => $dbJourney['blurb'],
                        "legs"       => $legs
                    );
                }

                return $journeys;
            } else {
                return array("error" => "true", "code" => 104, "msg" => "Not on any jounry");
            }
        }

        /**
         * @return array
         */
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
                    $keyPoints[ $point['category'] ] = array();
                }

                array_push($keyPoints[ $point['category'] ], array(
                    "value" => $point['value'],
                    "less"  => $point['less'],
                    "more"  => $point['more']
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
                    $times = number_format($dbDistanceAllTime / $values['value'], 0);
                    if ($times == 1) {
                        $times = "";
                    } else if ($times == 2) {
                        $times = "twice";
                    } else {
                        $times = $times . " times";
                    }
                    if (array_key_exists("more", $values) && !is_null($values['more']) && $values['more'] != "") {
                        $msg = $hes . " walked " . $values['more'] . " " . $times;
                    } else {
                        $msg = $hes . " walked " . $values['less'] . " " . $times;
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
                $lessItems += 1;
            }

            for ($iMore = ($lessItems - 1); $iMore >= 0; $iMore = $iMore - 1) {
                if (count($more) > $iMore) {
                    array_push($returnStats['distance'], $more[ (count($more) - 1) - $iMore ]);
                    $maxItems = $maxItems - 1;
                }
            }

            for ($iLess = $maxItems; $iLess >= 0; $iLess = $iLess - 1) {
                if (count($less) > $iLess) {
                    array_push($returnStats['distance'], $less[ (count($less) - 1) - $iLess ]);
                }
            }

            /**
             * Set key points for Floors
             */
            $dbFloorsAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'elevation', array("user" => $this->getUserID()));

            $less = array();
            $more = array();
            foreach ($keyPoints['elevation'] as $values) {
                if ($dbFloorsAllTime < $values['value']) {
                    array_push($less, number_format(($values['value'] - $dbFloorsAllTime), 0) . " meters more until " . $hes . " climbed " . $values['less']);
                } else if ($dbFloorsAllTime > $values['value']) {
                    $times = number_format($dbFloorsAllTime / $values['value'], 0);
                    if ($times == 1) {
                        $times = "";
                    } else if ($times == 2) {
                        $times = "twice";
                    } else {
                        $times = $times . " times";
                    }
                    if (array_key_exists("more", $values) && !is_null($values['more']) && $values['more'] != "") {
                        $msg = $hes . " climbed " . $values['more'] . " " . $times;
                    } else {
                        $msg = $hes . " climbed " . $values['less'] . " " . $times;
                    }
                    if ($times > 1) {
                        $msg .= "s";
                    }
                    array_push($more, $msg);
                }
            }

            $maxItems = $this->getAppClass()->getSetting("kp_maxItems", 8);
            $lessItems = $this->getAppClass()->getSetting("kp_lessItems", 2);
            if (count($less) <= ($maxItems - $lessItems)) {
                $lessItems = $maxItems - count($less);
                $lessItems += 1;
            }

            for ($iMore = ($lessItems - 1); $iMore >= 0; $iMore = $iMore - 1) {
                if (count($more) > $iMore) {
                    array_push($returnStats['floors'], $more[ (count($more) - 1) - $iMore ]);
                    $maxItems = $maxItems - 1;
                }
            }

            for ($iLess = $maxItems; $iLess >= 0; $iLess = $iLess - 1) {
                if (count($less) > $iLess) {
                    array_push($returnStats['floors'], $less[ (count($less) - 1) - $iLess ]);
                }
            }

            /**
             * Set Max values
             */
            $dbMaxSteps = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('steps', 'date'), array("user" => $this->getUserID(), "ORDER" => "steps DESC"));
            array_push($returnStats["max"], $his . " highest step count, totalling " . number_format($dbMaxSteps['steps'], 0) . ", for a day was on " . date("jS F, Y", strtotime($dbMaxSteps['date'])) . ".");

            $dbMaxDistance = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('distance', 'date'), array("user" => $this->getUserID(), "ORDER" => "distance DESC"));
            if ($dbMaxDistance['date'] == $dbMaxSteps['date']) {
                $returnStats["max"][ (count($returnStats["max"]) - 1) ] .= " That's an impressive " . number_format($dbMaxDistance['distance'], 0) . " miles.";
            } else {
                array_push($returnStats["max"], $he . " traveled the furthest, " . number_format($dbMaxDistance['distance'], 0) . " miles, on " . date("jS F, Y", strtotime($dbMaxDistance['date'])) . ".");
            }

            $dbMaxFloors = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('floors', 'date'), array("user" => $this->getUserID(), "ORDER" => "floors DESC"));
            array_push($returnStats["max"], $he . " walked up, " . number_format($dbMaxFloors['floors'], 0) . " floors, on " . date("jS F, Y", strtotime($dbMaxFloors['date'])) . ".");

            $dbMaxElevation = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('elevation', 'date'), array("user" => $this->getUserID(), "ORDER" => "elevation DESC"));
            if ($dbMaxFloors['date'] == $dbMaxElevation['date']) {
                $returnStats["max"][ (count($returnStats["max"]) - 1) ] .= " That's a total of " . number_format($dbMaxElevation['elevation'], 2) . " meters.";
            } else {
                array_push($returnStats["max"], $he . " climed the highest on " . date("jS F, Y", strtotime($dbMaxElevation['date'])) . ", a total of " . number_format($dbMaxElevation['elevation'], 2) . " meters.");
            }

            return $returnStats;
        }

        /**
         * @return array
         */
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
                ), array($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_sleep2usr.user" => $this->getUserID(),
                         "ORDER"                                                                           => $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logSleep.startTime DESC"));

            $returnSleep = array(
                "lastSleep"           => array(),
                "efficiency"          => 0,
                "timeInBed"           => 0,
                "minutesToFallAsleep" => 0,
                "minutesAsleep"       => 0,
                "awakeningsCount"     => 0
            );
            foreach ($dbSleepRecords as $record) {
                if (count($returnSleep['lastSleep']) == 0) {
                    $returnSleep['lastSleep'] = array(
                        "efficiency"          => $record['efficiency'],
                        "timeInBed"           => $record['timeInBed'],
                        "minutesToFallAsleep" => $record['minutesToFallAsleep'],
                        "minutesAsleep"       => $record['minutesAsleep'],
                        "awakeningsCount"     => $record['awakeningsCount']
                    );
                }

                $returnSleep['efficiency'] = $returnSleep['efficiency'] + $record['efficiency'];
                $returnSleep['timeInBed'] = $returnSleep['timeInBed'] + $record['timeInBed'];
                $returnSleep['minutesToFallAsleep'] = $returnSleep['minutesToFallAsleep'] + $record['minutesToFallAsleep'];
                $returnSleep['minutesAsleep'] = $returnSleep['minutesAsleep'] + $record['minutesAsleep'];
                $returnSleep['awakeningsCount'] = $returnSleep['awakeningsCount'] + $record['awakeningsCount'];
            }

            $returnSleep['efficiency'] = round($returnSleep['efficiency'] / count($dbSleepRecords), 0);
            $returnSleep['timeInBedAvg'] = round($returnSleep['timeInBed'] / count($dbSleepRecords), 0);
            $returnSleep['minutesToFallAsleep'] = round($returnSleep['minutesToFallAsleep'] / count($dbSleepRecords), 0);
            $returnSleep['minutesAsleep'] = round($returnSleep['minutesAsleep'] / count($dbSleepRecords), 0);
            $returnSleep['awakeningsCount'] = round($returnSleep['awakeningsCount'] / count($dbSleepRecords), 0);

            return $returnSleep;
        }

        /**
         * @return array
         */
        public function returnUserRecordStepGoal() {
            $lastMonday = date('Y-m-d', strtotime('last sunday'));
            $oneWeek = date('Y-m-d', strtotime($lastMonday . ' -6 days'));

            $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'steps',
                array("AND" => array(
                    "user"     => $this->getUserID(),
                    "date[>=]" => $oneWeek,
                    "date[<=]" => $lastMonday
                ), "ORDER"  => "date DESC", "LIMIT" => 7));

            $totalSteps = 0;
            foreach ($dbSteps as $dbStep) {
                $totalSteps = $totalSteps + $dbStep;
            }

            $newTargetSteps = round($totalSteps / count($dbSteps), 0);
            if ($newTargetSteps < $this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_steps_max", 10000)) {
                $plusTargetSteps = $newTargetSteps + round($newTargetSteps * ($this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_steps", 10) / 100), 0);
            } else {
                $plusTargetSteps = $this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_steps_max", 10000);
            }

            return array(
                "weekStart"       => $lastMonday,
                "weekEnd"         => $oneWeek,
                "totalSteps"      => $totalSteps,
                "newTargetSteps"  => $newTargetSteps,
                "plusTargetSteps" => $plusTargetSteps
            );
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
                if (count($dbGoals) == 0) {
                    // If todays goals are missing download the most recent goals
                    $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals",
                        array('distance', 'floors', 'steps'),
                        array("user" => $this->getUserID(), "ORDER" => "date DESC"));
                }

                $dbSteps[0]['steps_p'] = ($dbSteps[0]['steps'] / $dbGoals[0]['steps']) * 100;
                $dbSteps[0]['floors_p'] = ($dbSteps[0]['floors'] / $dbGoals[0]['floors']) * 100;
                $dbSteps[0]['distance_p'] = ($dbSteps[0]['distance'] / $dbGoals[0]['distance']) * 100;

                $dbSteps[0]['distance'] = (String)round($dbSteps[0]['distance'], 2);
                $dbGoals[0]['distance'] = (String)round($dbGoals[0]['distance'], 2);

                $cheer = array("distance" => 0, "floors" => 0, "steps" => 0);
                foreach ($cheer as $key => $value) {
                    if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 3) {
                        $cheer[ $key ] = 7;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 2.5) {
                        $cheer[ $key ] = 6;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 2) {
                        $cheer[ $key ] = 5;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 1.5) {
                        $cheer[ $key ] = 4;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ]) {
                        $cheer[ $key ] = 3;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 0.75) {
                        $cheer[ $key ] = 2;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 0.5) {
                        $cheer[ $key ] = 1;
                    } else {
                        $cheer[ $key ] = 0;
                    }
                }

                if (!is_null($this->getTracking())) {
                    $this->getTracking()->track("JSON Get", $this->getUserID(), "Steps");
                    $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");
                }

                return array('recorded' => $dbSteps[0], 'goal' => $dbGoals[0], 'cheer' => $cheer);
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
         * @return array
         */
        public function returnUserRecordTasker() {
            $taskerDataArray = array();

            $returnUserRecordWater = $this->returnUserRecordWater();
            $taskerDataArray['today']['water'] = round(($returnUserRecordWater[0]['liquid'] / $returnUserRecordWater[0]['goal']) * 100, 0);
            $taskerDataArray['cheer']['water'] = $returnUserRecordWater[0]['cheer'];

            $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('distance', 'floors', 'steps', 'syncd'), $this->dbWhere());
            $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array('distance', 'floors', 'steps', 'syncd'), $this->dbWhere());
            $taskerDataArray['today']['steps'] = round(($dbSteps[0]['steps'] / $dbGoals[0]['steps']) * 100, 0);
            $taskerDataArray['today']['distance'] = round((round($dbSteps[0]['distance'], 2) / round($dbGoals[0]['distance'], 2)) * 100, 0);
            $taskerDataArray['today']['floors'] = round(($dbSteps[0]['floors'] / $dbGoals[0]['floors']) * 100, 0);

            $taskerDataArray['goals']['steps'] = $dbGoals[0]['steps'];
            $taskerDataArray['goals']['distance'] = $dbGoals[0]['distance'];
            $taskerDataArray['goals']['floors'] = $dbGoals[0]['floors'];

            $dbActive = $this->getAppClass()->getDatabase()->query("SELECT target, fairlyactive, veryactive, syncd FROM "
                . $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity WHERE user = '" . $this->getUserID() . "' AND date = '" . date("Y-m-d") . "'")->fetchAll();
            $dbActive = $dbActive[0];
            $taskerDataArray['today']['active'] = round((($dbActive['fairlyactive'] + $dbActive['veryactive']) / $dbActive['target']) * 100, 2);
            $taskerDataArray['goals']['active'] = $dbActive['target'];

            $taskerDataArray['syncd']['active'] = $dbActive['syncd'];
            $taskerDataArray['syncd']['steps'] = $dbSteps[0]['syncd'];
            $taskerDataArray['syncd']['distance'] = $dbSteps[0]['syncd'];
            $taskerDataArray['syncd']['floors'] = $dbSteps[0]['syncd'];
            $taskerDataArray['syncd']['goals'] = $dbGoals[0]['syncd'];

            $cheer = array("distance" => 0, "floors" => 0, "steps" => 0);
            foreach ($cheer as $key => $value) {
                $taskerDataArray['raw'][ $key ] = $dbSteps[0][ $key ];

                if ($dbGoals[0][ $key ] > 0) {
                    if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 3) {
                        $taskerDataArray['cheer'][ $key ] = 7;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 2.5) {
                        $taskerDataArray['cheer'][ $key ] = 6;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 2) {
                        $taskerDataArray['cheer'][ $key ] = 5;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 1.5) {
                        $taskerDataArray['cheer'][ $key ] = 4;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ]) {
                        $taskerDataArray['cheer'][ $key ] = 3;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 0.75) {
                        $taskerDataArray['cheer'][ $key ] = 2;
                    } else if ($dbSteps[0][ $key ] >= $dbGoals[0][ $key ] * 0.5) {
                        $taskerDataArray['cheer'][ $key ] = 1;
                    } else {
                        $taskerDataArray['cheer'][ $key ] = 0;
                    }
                } else {
                    $taskerDataArray['cheer'][ $key ] = 0;
                }
            }

            $returnUserRecordChallenger = $this->returnUserRecordChallenger();
            $taskerDataArray['challenge']['active'] = ($returnUserRecordChallenger['current']['active'] / $returnUserRecordChallenger['current']['active_g']) * 100;

            $taskerDataArray['challenge']['state'] = $returnUserRecordChallenger['challengeActive'];

            $taskerDataArray['challenge']['start_date'] = $returnUserRecordChallenger['next']['startDateF'];
            $taskerDataArray['challenge']['end_date'] = $returnUserRecordChallenger['next']['endDateF'];

            $taskerDataArray['challenge']['length'] = round(($returnUserRecordChallenger['current']['day'] / $returnUserRecordChallenger['challengeLength']) * 100, 0);
            $taskerDataArray['challenge']['day'] = round(($returnUserRecordChallenger['current']['day_past'] / $returnUserRecordChallenger['current']['day']) * 100, 0);

            $taskerDataArray['challenge']['distance'] = round(($returnUserRecordChallenger['current']['distance'] / $returnUserRecordChallenger['current']['distance_g']) * 100, 0);
            $taskerDataArray['challenge']['active'] = round(($returnUserRecordChallenger['current']['active'] / $returnUserRecordChallenger['current']['active_g']) * 100, 0);
            $taskerDataArray['challenge']['steps'] = round(($returnUserRecordChallenger['current']['steps'] / $returnUserRecordChallenger['current']['steps_g']) * 100, 0);

            $taskerDataArray['devices'] = $this->returnUserRecordDevices();

            $returnUserRecordFood = $this->returnUserRecordFood();
            $taskerDataArray['today']['food'] = round(($returnUserRecordFood['total'] / $returnUserRecordFood['goal']) * 100, 2);

            if (!is_null($this->getTracking())) {
                $this->getTracking()->track("JSON Get", $this->getUserID(), "Tasker");
                $this->getTracking()->track("JSON Goal", $this->getUserID(), "Tasker");
            }

            ksort($taskerDataArray['today']);
            ksort($taskerDataArray['cheer']);
            ksort($taskerDataArray['goals']);
            ksort($taskerDataArray['syncd']);
            ksort($taskerDataArray['raw']);

            return $taskerDataArray;
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
                    $badges[ $userBadge['badgeType'] ] = array();
                    $badges[ $userBadge['badgeType'] ]['type'] = $userBadge['badgeType'];
                    $badges[ $userBadge['badgeType'] ]['value'] = $userBadge['value'];
                    $badges[ $userBadge['badgeType'] ]['dateTime'] = $userBadge['dateTime'];
                    $badges[ $userBadge['badgeType'] ]['timesAchieved'] = $userBadge['timesAchieved'];
                } else if ($userBadge['value'] > $badges[ $userBadge['badgeType'] ]['value']) {
                    $badges[ $userBadge['badgeType'] ]['value'] = $userBadge['value'];
                    $badges[ $userBadge['badgeType'] ]['dateTime'] = $userBadge['dateTime'];
                    $badges[ $userBadge['badgeType'] ]['timesAchieved'] = $userBadge['timesAchieved'];
                }
            }

            foreach ($badges as $badge) {
                /** @var array $dbBadge */
                $dbBadge = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages",
                    array('image', 'badgeGradientEndColor', 'badgeGradientStartColor', 'earnedMessage', 'marketingDescription', 'name'),
                    array("AND" => array("badgeType" => $badge['type'], "value" => $badge['value'])));
                $badges[ $badge['type'] ] = array_merge($badges[ $badge['type'] ], $dbBadge);
            }

            return array("images" => "images/badges/", "badges" => $badges);
        }

        /**
         * @return array
         */
        public function returnUserRecordTracked() {
            $nx_fitbit_steps = $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'steps';
            $nx_fitbit_steps_goals = $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . 'steps_goals';

            $days = $this->getParamPeriod();
            $days = str_ireplace("last", "", $days);
            $then = date('Y-m-d', strtotime($this->getParamDate() . " -" . $days . " day"));

            $dbSteps = $this->getAppClass()->getDatabase()->query(
                "SELECT `$nx_fitbit_steps`.`date`,`$nx_fitbit_steps`.`floors`,`$nx_fitbit_steps`.`steps`,`$nx_fitbit_steps_goals`.`floors` AS `floors_g`,`$nx_fitbit_steps_goals`.`steps` AS `steps_g`"
                . " FROM `$nx_fitbit_steps`"
                . " JOIN `$nx_fitbit_steps_goals` ON (`$nx_fitbit_steps`.`date` = `$nx_fitbit_steps_goals`.`date`) AND (`$nx_fitbit_steps`.`user` = `$nx_fitbit_steps_goals`.`user`)"
                . " WHERE `$nx_fitbit_steps`.`user` = '" . $this->getUserID() . "' AND `$nx_fitbit_steps`.`date` <= '" . $this->getParamDate() . "' AND `$nx_fitbit_steps`.`date` >= '$then' "
                . " ORDER BY `$nx_fitbit_steps`.`date` DESC LIMIT $days");

            $returnDate = NULL;
            $graph_floors = array();
            $graph_floors_g = array();
            $graph_floors_min = 0;
            $graph_floors_max = 0;
            $graph_steps = array();
            $graph_steps_g = array();
            $graph_steps_min = 0;
            $graph_steps_max = 0;
            foreach ($dbSteps as $dbValue) {
                if (is_null($returnDate))
                    $returnDate = explode("-", $dbValue['date']);

                array_push($graph_floors, (String)round($dbValue['floors'], 0));
                array_push($graph_floors_g, (String)round($dbValue['floors_g'], 0));
                if ($dbValue['floors'] < $graph_floors_min || $graph_floors_min == 0) {
                    $graph_floors_min = $dbValue['floors'];
                }
                if ($dbValue['floors'] > $graph_floors_max || $graph_floors_max == 0) {
                    $graph_floors_max = $dbValue['floors'];
                }

                array_push($graph_steps, (String)round($dbValue['steps'], 0));
                array_push($graph_steps_g, (String)round($dbValue['steps_g'], 0));
                if ($dbValue['steps'] < $graph_steps_min || $graph_steps_min == 0) {
                    $graph_steps_min = $dbValue['steps'];
                }
                if ($dbValue['steps'] > $graph_steps_max || $graph_steps_max == 0) {
                    $graph_steps_max = $dbValue['steps'];
                }
            }

            $dbActive = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity",
                array(
                    'target',
                    'fairlyactive',
                    'veryactive'
                ),
                array("AND" => array(
                    "user"     => $this->getUserID(),
                    "date[>=]" => $then,
                    "date[<=]" => $this->getParamDate()
                ), "ORDER"  => "date DESC"));

            $graph_active = array();
            $graph_active_g = array();
            $graph_active_min = 0;
            $graph_active_max = 0;

            foreach ($dbActive as $dbValue) {
                array_push($graph_active, (String)round($dbValue['fairlyactive'] + $dbValue['veryactive'], 0));
                array_push($graph_active_g, (String)round($dbValue['target'], 0));

                if (($dbValue['fairlyactive'] + $dbValue['veryactive']) < $graph_active_min) {
                    $graph_active_min = $dbValue['fairlyactive'] + $dbValue['veryactive'];
                }
                if (($dbValue['fairlyactive'] + $dbValue['veryactive']) > $graph_active_max) {
                    $graph_active_max = $dbValue['fairlyactive'] + $dbValue['veryactive'];
                }
            }

            $goalCalcSteps = $this->returnUserRecordStepGoal();
            $goalCalcFloors = $this->returnUserRecordFloorGoal();
            $goalCalcActive = $this->returnUserRecordActiveGoal();

            return array(
                'returnDate' => $returnDate,

                'graph_steps'     => $graph_steps,
                'graph_steps_g'   => $graph_steps_g,
                'graph_steps_min' => $graph_steps_min,
                'graph_steps_max' => $graph_steps_max,
                'imp_steps'       => $this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_steps", 10) . "%",
                'avg_steps'       => number_format($goalCalcSteps['newTargetSteps'], 0),
                'newgoal_steps'   => number_format($goalCalcSteps['plusTargetSteps'], 0),
                'maxgoal_steps'   => number_format($this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_steps_max", 10000), 0),

                'graph_floors'     => $graph_floors,
                'graph_floors_g'   => $graph_floors_g,
                'graph_floors_min' => $graph_floors_min,
                'graph_floors_max' => $graph_floors_max,
                'imp_floors'       => $this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_floors", 10) . "%",
                'avg_floors'       => number_format($goalCalcFloors['newTargetFloors'], 0),
                'newgoal_floors'   => number_format($goalCalcFloors['plusTargetFloors'], 0),
                'maxgoal_floors'   => number_format($this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_floors_max", 20), 0),

                'graph_active'     => $graph_active,
                'graph_active_g'   => $graph_active_g,
                'graph_active_min' => $graph_active_min,
                'graph_active_max' => $graph_active_max,
                'imp_active'       => $this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_active", 10) . "%",
                'avg_active'       => number_format($goalCalcActive['newTargetFloors'], 0),
                'newgoal_active'   => number_format($goalCalcActive['plusTargetFloors'], 0),
                'maxgoal_active'   => number_format($this->getAppClass()->getSetting("improvments_" . $this->getUserID() . "_active_max", 30), 0)
            );
        }

        /**
         * @return array
         */
        public function returnUserRecordTrend() {
            $trendArray = array();

            $dbBody = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body", array('date', 'weight', 'weightGoal', 'fat', 'fatGoal'), array("user" => $this->getUserID(), "ORDER" => "date  ASC"));
            $trendArray['weeksWeightTracked'] = round(abs(strtotime($this->getParamDate()) - strtotime($dbBody['date'])) / 604800, 0);

            $trendArray['weightToLose'] = $dbBody['weight'] - $dbBody['weightGoal'];
            $trendArray['fatToLose'] = $dbBody['fat'] - $dbBody['fatGoal'];

            $dbGoalsCalories = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "goals_calories", array('estimatedDate'), array("user" => $this->getUserID(), "ORDER" => "date DESC"));
            $trendArray['estimatedDate'] = date("l", strtotime($dbGoalsCalories['estimatedDate'])) . " the " . date("jS \of F Y", strtotime($dbGoalsCalories['estimatedDate']));
            $trendArray['estimatedWeeks'] = round(abs(strtotime($dbGoalsCalories['estimatedDate']) - strtotime($this->getParamDate())) / 604800, 0);

            $dbUsers = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array('name', 'rank', 'friends', 'distance', 'gender'), array("fuid" => $this->getUserID()));
            $trendArray['rank'] = $dbUsers['rank'];
            $trendArray['friends'] = $dbUsers['friends'];
            $trendArray['nextRank'] = number_format($dbUsers['distance'], 0);
            $trendArray['name'] = explode(" ", $dbUsers['name']);
            $trendArray['name'] = $trendArray['name'][0];

            $dbSteps = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array('caloriesOut'), array("user" => $this->getUserID(), "ORDER" => "date DESC"));
            $dbLogFood = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood", array('calories'), array("AND" => array("user" => $this->getUserID(), "date" => $this->getParamDate()), "ORDER" => "date DESC"));

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
         * @return array|bool
         */
        public function returnUserRecordWater() {
            $dbWater = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water",
                array('date', 'liquid'),
                $this->dbWhere());

            $dbWater[0]['liquid'] = (String)round($dbWater[0]['liquid'], 2);
            $dbWater[0]['goal'] = $this->getAppClass()->getSetting("usr_goal_water_" . $this->getUserID(), '200');

            if ($dbWater[0]['liquid'] >= $dbWater[0]['goal'] * 3) {
                $dbWater[0]['cheer'] = 5;
            } else if ($dbWater[0]['liquid'] >= $dbWater[0]['goal'] * 2.5) {
                $dbWater[0]['cheer'] = 4;
            } else if ($dbWater[0]['liquid'] >= $dbWater[0]['goal'] * 2) {
                $dbWater[0]['cheer'] = 3;
            } else if ($dbWater[0]['liquid'] >= $dbWater[0]['goal'] * 1.5) {
                $dbWater[0]['cheer'] = 2;
            } else if ($dbWater[0]['liquid'] >= $dbWater[0]['goal']) {
                $dbWater[0]['cheer'] = 1;
            } else {
                $dbWater[0]['cheer'] = 0;
            }

            if (!is_null($this->getTracking())) {
                $this->getTracking()->track("JSON Get", $this->getUserID(), "Water");
                $this->getTracking()->track("JSON Goal", $this->getUserID(), "Water");
            }

            return $dbWater;
        }

        /**
         * @return array|bool
         */
        public function returnUserRecordWeekPedometer() {
            $userActivity = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps",
                array('date', 'steps', 'distance', 'floors'),
                $this->dbWhere());

            foreach ($userActivity as $key => $value) {
                $userActivity[ $key ]['distance'] = (String)round($value['distance'], 2);
                $userActivity[ $key ]['returnDate'] = explode("-", $value['date']);
            }

            return $userActivity;
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

                $returnWeight[ $daysWeight['date'] ] = $daysWeight;
                $returnWeight[ $daysWeight['date'] ]['source'] = "Database";
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

                $weight = $this->getAppClass()->getFitbitAPI($this->getUserID())->getDBCurrentBody($this->getUserID(), "weight");
                $weightGoal = $this->getAppClass()->getFitbitAPI($this->getUserID())->getDBCurrentBody($this->getUserID(), "weightGoal");
                $fat = $this->getAppClass()->getFitbitAPI($this->getUserID())->getDBCurrentBody($this->getUserID(), "fat");
                $fatGoal = $this->getAppClass()->getFitbitAPI($this->getUserID())->getDBCurrentBody($this->getUserID(), "fatGoal");

                foreach ($period as $dt) {
                    /** @var DateTime $dt */
                    $returnWeight[ $dt->format("Y-m-d") ] = array("date"       => $dt->format("Y-m-d"),
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

                $recordsLoopedThru = 0; // TODO; do we need this
                $lastRecord = NULL;
                $foundMissingRecord = FALSE;
                $arrayOfMissingDays = array();
                foreach ($period as $dt) {
                    /**
                     * Find all missing dates
                     *
                     * @var DateTime $dt
                     */
                    if (!array_key_exists($dt->format("Y-m-d"), $returnWeight)) {
                        if (strtotime($dt->format("Y-m-d")) > strtotime($latestDate)) {
                            // If missing date is after latest record use that

                            $returnWeight[ $dt->format("Y-m-d") ] = $lastRecord;
                            $returnWeight[ $dt->format("Y-m-d") ]['source'] = "LatestRecord";
                        } else {
                            // If missing date is before last record add it to list of missing dates

                            $foundMissingRecord = TRUE;
                            array_push($arrayOfMissingDays, $dt->format("Y-m-d"));
                            $returnWeight[ $dt->format("Y-m-d") ] = 'Calc deviation';
                        }
                    } else {
                        // if there are missing dates still pending
                        if ($foundMissingRecord) {
                            // If no last record has been set get it from database
                            if (is_null($lastRecord)) {
                                /** @var array $lastRecord */
                                $lastRecord = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body",
                                    array('date', 'weight', 'weightAvg', 'weightGoal', 'fat', 'fatAvg', 'fatGoal'),
                                    array("AND" => array("user"     => $this->getUserID(),
                                                         "date[<=]" => date('Y-m-d', strtotime($this->getParamDate() . " -" . (($days + 10) - 1) . " day"))
                                    ), "ORDER"  => "date DESC"));
                            }

                            // Fill in missing records between now and last recorded 'good' date
                            $returnWeight = $this->fillMissingBodyRecords($returnWeight, $arrayOfMissingDays, $lastRecord, $returnWeight[ $dt->format("Y-m-d") ]);

                            // reset missing markers
                            $foundMissingRecord = FALSE;
                            $arrayOfMissingDays = array();
                        }

                        // update last record with this one
                        $lastRecord = $returnWeight[ $dt->format("Y-m-d") ];
                    }
                    $recordsLoopedThru = $recordsLoopedThru + 1; // TODO; do we need this
                }
                if ($foundMissingRecord) {
                    print "There are still missing dates\n";
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
                        $weightSum = $weightSum + $returnWeight[ $returnWeightKeys[ $interval + $intervalTwo ] ]['weight'];
                        $fatSum = $fatSum + $returnWeight[ $returnWeightKeys[ $interval + $intervalTwo ] ]['fat'];
                    }
                    $returnWeight[ $returnWeightKeys[ $interval ] ]['weightTrend'] = round($weightSum / $averageRange, 2);
                    $returnWeight[ $returnWeightKeys[ $interval ] ]['fatTrend'] = $fatSum / $averageRange;
                } else {
                    $returnWeight[ $returnWeightKeys[ $interval ] ]['weightTrend'] = round($returnWeight[ $returnWeightKeys[ $interval ] ]['weight'], 2);
                    $returnWeight[ $returnWeightKeys[ $interval ] ]['fatTrend'] = $returnWeight[ $returnWeightKeys[ $interval ] ]['fat'];
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
            $loopMonths = TRUE;
            do {
                $timestamp = strtotime('now -' . $monthsBack . ' month');
                if (array_key_exists(date('Y-m-t', $timestamp), $returnWeight) AND array_key_exists(date('Y-m-01', $timestamp), $returnWeight)) {
                    $loss["weight"][ date('Y F', $timestamp) ] = round(($returnWeight[ date('Y-m-t', $timestamp) ]['weightTrend'] - $returnWeight[ date('Y-m-01', $timestamp) ]['weightTrend']) / 4, 2);
                    $loss["fat"][ date('Y F', $timestamp) ] = round(($returnWeight[ date('Y-m-t', $timestamp) ]['fatTrend'] - $returnWeight[ date('Y-m-01', $timestamp) ]['fatTrend']) / 4, 2);
                    $monthsBack += 1;
                } else {
                    $loopMonths = FALSE;
                }
            } while ($loopMonths);

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

        /**
         * @param $get
         *
*@return array
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
                $resultsArray = array("error"    => "false",
                                      "user"     => $this->getUserID(),
                                      'username' => $dbUserName,
                                      "cache"    => TRUE,
                                      "data"     => $get['data'],
                                      "period"   => $this->getParamPeriod(),
                                      "date"     => $this->getParamDate());
                $resultsArray['results'] = $this->$functionName();
                if (array_key_exists("sole", $resultsArray['results']) && $resultsArray['results']['sole']) {
                    $resultsArray = $resultsArray['results']['return'];
                } else {
                    $resultsArray['cache'] = $this->getForCache();
                }

                if (array_key_exists("debug", $_GET) and $_GET['debug'] == "true") {
                    $resultsArray['dbLog'] = $this->getAppClass()->getDatabase()->log();
                }

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
    }