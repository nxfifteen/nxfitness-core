<?php

    define("FITBIT_COM", "https://api.fitbit.com");

    /**
     * Fitbit Helper class
     *
     * @version   0.0.1
     * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link      http://nxfifteen.me.uk NxFIFTEEN
     * @copyright 2015 Stuart McCulloch Anderson
     * @license   http://stuart.nx15.at/mit/2015 MIT
     */
    class fitbit {
        /**
         * @var NxFitbit
         */
        protected $AppClass;
        /**
         * @var djchen\OAuth2\Client\Provider\Fitbit
         */
        protected $fitbitapi;
        /**
         * @var bool
         */
        protected $forceSync;

        /**
         * @var
         */
        private $holdingVar;

        /**
         * @var
         */
        private $activeUser;

        /**
         * @var
         */
        private $userAccessToken;

        /**
         * @param NxFitbit $fitbitApp
         * @param bool     $personal
         */
        public function __construct($fitbitApp, $personal = FALSE) {
            $this->setAppClass($fitbitApp);

            $personal = $personal ? "_personal" : "";

            $this->setLibrary(new djchen\OAuth2\Client\Provider\Fitbit([
                'clientId'     => $fitbitApp->getSetting("api_clientId" . $personal, NULL, FALSE),
                'clientSecret' => $fitbitApp->getSetting("api_clientSecret" . $personal, NULL, FALSE),
                'redirectUri'  => $fitbitApp->getSetting("api_redirectUri" . $personal, NULL, FALSE)
            ]));

            nxr("clientId: " . $fitbitApp->getSetting("api_clientId" . $personal, NULL, FALSE) . " used");

            $this->forceSync = FALSE;

            if (!defined('IS_CRON_RUN')) define('IS_CRON_RUN', FALSE);

        }

        /**
         * @param      $trigger
         * @param bool $force
         *
         * @return string|bool
         */
        private function pullBabelTimeSeries($trigger, $force = FALSE) {
            if ($force || $this->api_isCooled($trigger)) {
                $currentDate = new DateTime();

                $lastrun = $this->api_getLastCleanrun($trigger);
                $daysSince = (strtotime($currentDate->format("Y-m-d")) - strtotime($lastrun->format("l jS M Y"))) / (60 * 60 * 24);

                nxr("  Last download: $daysSince days ago. ");

                $allRecords = FALSE;
                if ($daysSince < 8) {
                    $daysSince = "7d";
                } elseif ($daysSince < 30) {
                    $daysSince = "30d";
                } elseif ($daysSince < 90) {
                    $daysSince = "3m";
                } elseif ($daysSince < 180) {
                    $daysSince = "6m";
                } elseif ($daysSince < 364) {
                    $daysSince = "1y";
                } else {
                    $allRecords = TRUE;
                    $daysSince = "1y";
                    $lastrun->add(new DateInterval('P360D'));
                }

                if ($allRecords) {
                    nxr("  Requesting $trigger data for $daysSince days");
                    $this->pullBabelTimeSeriesByTrigger($trigger, $daysSince, $lastrun);
                } else {
                    nxr("  Requesting $trigger data for $daysSince days");
                    $this->pullBabelTimeSeriesByTrigger($trigger, $daysSince);
                    $this->api_setLastrun($trigger);
                }
            } else {
                if (!IS_CRON_RUN) nxr("   Error " . $trigger . ": " . $this->getAppClass()->lookupErrorCode(-143));
            }

            return TRUE;
        }

        /**
         * @param               $trigger
         * @param               $daysSince
         * @param DateTime|null $lastrun
         *
         * @return string|bool
         */
        private function pullBabelTimeSeriesByTrigger($trigger, $daysSince, $lastrun = NULL) {
            switch ($trigger) {
                case "steps":
                case "distance":
                case "floors":
                case "elevation":
                case "caloriesOut":
                    $this->pullBabelTimeSeriesForSteps($trigger, $daysSince, $lastrun);
                    break;
                case "minutesVeryActive":
                case "minutesSedentary":
                case "minutesLightlyActive":
                case "minutesFairlyActive":
                    $this->pullBabelTimeSeriesForActivity($trigger, $daysSince, $lastrun);
                    break;
            }

            return TRUE;
        }

        /**
         * @param               $trigger
         * @param               $daysSince
         * @param DateTime|null $lastrun
         *
         * @return string|bool
         */
        private function pullBabelTimeSeriesForSteps($trigger, $daysSince, $lastrun = NULL) {
            if (!is_null($lastrun)) {
                $currentDate = $lastrun;
            } else {
                $currentDate = new DateTime ('now');
            }

            nxr('   Get ' . $this->getAppClass()->supportedApi($trigger) . ' records from ' . $currentDate->format("Y-m-d"));
            $userTimeSeries = $this->getTimeSeries($trigger, $currentDate, $daysSince);

            if (isset($userTimeSeries) and is_array($userTimeSeries)) {
                $FirstSeen = $this->user_getFirstSeen()->format("Y-m-d");

                foreach ($userTimeSeries as $steps) {
                    if (strtotime($steps->dateTime) >= strtotime($FirstSeen)) {
                        if ($steps->value == 0) {
                            $currentDate = new DateTime();
                            $daysSinceReading = (strtotime($currentDate->format("Y-m-d")) - strtotime($steps->dateTime)) / (60 * 60 * 24);
                            nxr("   No recorded data for " . $steps->dateTime . " " . $daysSinceReading . " days ago");
                            if ($daysSinceReading > 180)
                                $this->api_setLastCleanrun($trigger, new DateTime ($steps->dateTime));
                        } else {
                            nxr("   " . $this->getAppClass()->supportedApi($trigger) . " record for " . $steps->dateTime . " is " . $steps->value);
                        }

                        if ($steps->value > 0) $this->api_setLastCleanrun($trigger, new DateTime ($steps->dateTime));

                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array("AND" => array('user' => $this->getActiveUser(), 'date' => (String)$steps->dateTime)))) {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array(
                                $trigger => (String)$steps->value,
                                'syncd'  => $currentDate->format('Y-m-d H:m:s')
                            ), array("AND" => array('user' => $this->getActiveUser(), 'date' => (String)$steps->dateTime)));
                        } else {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", array(
                                'user'   => $this->getActiveUser(),
                                'date'   => (String)$steps->dateTime,
                                $trigger => (String)$steps->value,
                                'syncd'  => $currentDate->format('Y-m-d H:m:s')
                            ));
                        }

                        if ($trigger == "steps")
	                        $this->GoalStreakCheck($steps->dateTime, $trigger, $steps->value);
                    }
                }
            }

            return TRUE;
        }

	    private function GoalStreakCheck($dateTime, $goal, $value) {
            $todaysDate = new DateTime ('now');
		    $dateTime = new DateTime ($dateTime);

		    $db_prefix = $this->getAppClass()->getSetting("db_prefix", NULL, FALSE);

		    $steps_goals = $this->getAppClass()->getDatabase()->get($db_prefix . "steps_goals", array("steps", "date"), array("AND" => array("user" => $this->getActiveUser(), "date" => $dateTime->format("Y-m-d"))));
		    if (!is_numeric($steps_goals['steps'])) {
			    $steps_goals = $this->getAppClass()->getUserSetting($this->getActiveUser(), "goal_steps");
		    } else {
			    $steps_goals = $steps_goals['steps'];
		    }

            if (strtotime($dateTime->format('Y-m-d')) < strtotime($todaysDate->format('Y-m-d')) || $value > $steps_goals) {

                if ($this->getAppClass()->getDatabase()->has($db_prefix . "streak_goal", array("AND" => array("fuid" => $this->getActiveUser(), "goal" => $goal, "end_date" => null)) )) {
                    $streak = true;
                    $streak_start = $this->getAppClass()->getDatabase()->get($db_prefix . "streak_goal", "start_date", array("AND" => array("fuid" => $this->getActiveUser(), "goal" => $goal, "end_date" => null)));
                } else {
                    $streak = false;
                    $streak_start = $dateTime->format("Y-m-d");
                }

                //if ()

                if ($streak && strtotime($dateTime->format("Y-m-d")) <= strtotime($streak_start)) {
                    //nxr( "     Streak started on " . $streak_start . " ignored since were looking at the past " .$dateTime->format("Y-m-d") );
                } else {
                    if ( $value >= $steps_goals ) {
                        //nxr("    Beat your target for " . $dateTime->format("Y-m-d"));
                        if ( $streak ) {
                            nxr( "     Streak continuing from " . $streak_start );

                            $dateTimeStart = new DateTime ( $streak_start );
                            $days_between  = $dateTimeStart->diff( $dateTime )->format( "%a" );
                            $days_between  = $days_between + 1;

                            $this->getAppClass()->getDatabase()->update( $db_prefix . "streak_goal", array(
                                "length" => $days_between
                            ),
                                array(
                                    "AND" => array(
                                        "fuid"       => $this->getActiveUser(),
                                        "goal"       => $goal,
                                        "start_date" => $streak_start
                                    )
                                )
                            );

                        } else {
                            nxr( "     New Streak started" );

                            $this->getAppClass()->getDatabase()->insert( $db_prefix . "streak_goal", array(
                                "fuid"       => $this->getActiveUser(),
                                "goal"       => $goal,
                                "start_date" => $dateTime->format( "Y-m-d" ),
                                "end_date"   => NULL,
                                "length"     => 1
                            ) );
                        }
                    } else if ( $streak && $value < $steps_goals ) {
                        $dateTimeEnd = $dateTime;
                        $dateTimeEnd->add( DateInterval::createFromDateString( 'yesterday' ) );
                        $streak_end = $dateTimeEnd->format( 'Y-m-d' );
                        nxr( "     Steak ran from " . $streak_start . " till " . $streak_end );

                        $days_between = $dateTime->diff( $dateTimeEnd )->format( "%a" );
                        $days_between = $days_between + 1;

                        $this->getAppClass()->getDatabase()->update( $db_prefix . "streak_goal", array(
                            "end_date" => $streak_end,
                            "length"   => $days_between
                        ),
                            array(
                                "AND" => array(
                                    "fuid"       => $this->getActiveUser(),
                                    "goal"       => $goal,
                                    "start_date" => $streak_start
                                )
                            )
                        );
                        //nxr(print_r($this->getAppClass()->getDatabase()->error(), true));
                        //nxr(end($this->getAppClass()->getDatabase()->log()));
                    }
                }
            }
	    }

        /**
         * @param               $trigger
         * @param               $daysSince
         * @param DateTime|null $lastrun
         *
         * @return bool
         */
        private function pullBabelTimeSeriesForActivity($trigger, $daysSince, $lastrun = NULL) {

            switch ($trigger) {
                case "minutesVeryActive":
                    $databaseColumn = "veryactive";
                    break;
                case "minutesSedentary":
                    $databaseColumn = "sedentary";
                    break;
                case "minutesLightlyActive":
                    $databaseColumn = "lightlyactive";
                    break;
                case "minutesFairlyActive":
                    $databaseColumn = "fairlyactive";
                    break;
                default:
                    return FALSE;
            }

            if (!is_null($lastrun)) {
                $currentDate = $lastrun;
            } else {
                $currentDate = new DateTime ('now');
            }

            nxr('   Get ' . $this->getAppClass()->supportedApi($trigger) . ' records ' . $currentDate->format("Y-m-d"));
            $userTimeSeries = $this->getTimeSeries($trigger, $currentDate, $daysSince);

            if (isset($userTimeSeries) and is_array($userTimeSeries)) {
                if (!isset($this->holdingVar) OR !array_key_exists("type", $this->holdingVar) OR !array_key_exists("data", $this->holdingVar) OR $this->holdingVar["type"] != "activities/goals/daily.json" OR $this->holdingVar["data"] == "") {
                    if (isset($this->holdingVar)) {
                        unset($this->holdingVar);
                    }
                    $this->holdingVar = array("type" => "activities/goals/daily.json", "data" => "");
                    $this->holdingVar["data"] = $this->pullBabel('user/-/activities/goals/daily.json', TRUE);

                    if ($trigger == "minutesVeryActive") {
                        $newGoal = $this->thisWeeksGoal("activeMinutes", $this->holdingVar["data"]->goals->activeMinutes);
                        if ($newGoal > 0 && $this->holdingVar["data"]->goals->activeMinutes != $newGoal) {
                            nxr("    Returned activity target was " . $this->holdingVar["data"]->goals->activeMinutes . " but I think it should be " . $newGoal);
                            $this->pushBabel('user/-/activities/goals/daily.json', array('activeMinutes' => $newGoal));
                        } elseif ($newGoal > 0) {
                            nxr("    Returned activity target was " . $this->holdingVar["data"]->goals->activeMinutes . " which is right for this week goal of " . $newGoal);
                        }
                    }
                }

                $FirstSeen = $this->user_getFirstSeen()->format("Y-m-d");
	            $todaysDate = new DateTime ('now');
                foreach ($userTimeSeries as $series) {
                    if (strtotime($series->dateTime) >= strtotime($FirstSeen)) {
                        nxr("    " . $this->getAppClass()->supportedApi($trigger) . " " . $series->dateTime . " is " . $series->value);

                        if ($series->value > 0) $this->api_setLastCleanrun($trigger, new DateTime ($series->dateTime));

                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity", array("AND" => array('user' => $this->getActiveUser(), 'date' => (String)$series->dateTime)))) {
                            $dbStorage = array(
                                $databaseColumn => (String)$series->value,
                                'syncd'         => $currentDate->format('Y-m-d H:m:s')
                            );

                            if ($currentDate->format("Y-m-d") == $series->dateTime) {
                                $dbStorage['target'] = (String)$this->holdingVar["data"]->goals->activeMinutes;
                            }

                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity", $dbStorage, array("AND" => array('user' => $this->getActiveUser(), 'date' => (String)$series->dateTime)));
                        } else {
                            $dbStorage = array(
                                'user'          => $this->getActiveUser(),
                                'date'          => (String)$series->dateTime,
                                $databaseColumn => (String)$series->value,
                                'syncd'         => $currentDate->format('Y-m-d H:m:s')
                            );
                            if ($currentDate->format("Y-m-d") == $series->dateTime) {
                                $dbStorage['target'] = (String)$this->holdingVar["data"]->goals->activeMinutes;
                            }
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity", $dbStorage);
                        }

	                    //if ($databaseColumn == "veryactive" && strtotime($series->dateTime) < strtotime($todaysDate->format('Y-m-d')))
		                 //   $this->GoalStreakCheck($series->dateTime, "veryactive", $series->value);
                    }
                }
            }

            return TRUE;
        }

        /**
         * @return bool
         * @internal param $targetDate
         */
        private function pullBabelActivityLogs() {
            $isAllowed = $this->isAllowed("activity_log");
            if (!is_numeric($isAllowed)) {
                if ($this->api_isCooled("activity_log")) {
                    $targetDateTime = $this->api_getLastCleanrun("activity_log");

                    nxr(' Downloading activity logs from ' . $targetDateTime->format("Y-m-d"));

                    $userActivityLog = $this->pullBabel('user/' . $this->getActiveUser() . '/activities/list.json?afterDate=' . $targetDateTime->format("Y-m-d") . '&sort=asc&limit=100&offset=0', TRUE);

                    if (isset($userActivityLog) and is_object($userActivityLog)) {
                        $activityLog = $userActivityLog->activities;
                        if (isset($activityLog) && is_array($activityLog) && count($activityLog) > 0) {
                            foreach ($activityLog as $activity) {
                                $startTimeRaw = new DateTime ((String)$activity->startTime);
                                $startDate = $startTimeRaw->format("Y-m-d");
                                $startTime = $startTimeRaw->format("H:i:s");

                                if ((String)$activity->activityTypeId != "16010") {
                                    $activityLevel = $activity->activityLevel;
                                    $dbStorage = array(
                                        "user"                   => $this->getActiveUser(),
                                        "logId"                  => (String)$activity->logId,
                                        "logType"                => (String)$activity->logType,
                                        "activityName"           => (String)$activity->activityName,
                                        "activityTypeId"         => (String)$activity->activityTypeId,
                                        "activeDuration"         => (String)$activity->activeDuration,
                                        "startDate"              => $startDate,
                                        "startTime"              => $startTime,
                                        "activityLevelSedentary" => $activityLevel[0]->minutes,
                                        "activityLevelLightly"   => $activityLevel[1]->minutes,
                                        "activityLevelFairly"    => $activityLevel[2]->minutes,
                                        "activityLevelVery"      => $activityLevel[3]->minutes
                                    );

                                    if (isset($activity->activityName)) $dbStorage["activityName"] = (String)$activity->activityName;
                                    if (isset($activity->distanceUnit)) $dbStorage["distanceUnit"] = (String)$activity->distanceUnit;
                                    if (isset($activity->distance)) $dbStorage["distance"] = (String)$activity->distance;
                                    if (isset($activity->speed)) $dbStorage["speed"] = (String)$activity->speed;
                                    if (isset($activity->pace)) $dbStorage["pace"] = (String)$activity->pace;
                                    if (isset($activity->steps)) $dbStorage["steps"] = (String)$activity->steps;
                                    if (isset($activity->calories)) $dbStorage["calories"] = (String)$activity->calories;
                                    if (isset($activity->caloriesLink)) $dbStorage["caloriesLink"] = str_replace("https://api.fitbit.com/1/", "", (String)$activity->caloriesLink);
                                    if (isset($activity->tcxLink)) $dbStorage["tcxLink"] = str_replace("https://api.fitbit.com/1/", "", (String)$activity->tcxLink);
                                    if (isset($activity->source) && isset($activity->source->name)) $dbStorage["sourceName"] = (String)$activity->source->name;
                                    if (isset($activity->source) && isset($activity->source->type)) $dbStorage["sourceType"] = (String)$activity->source->type;

                                    if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity_log", array("AND" => array("user" => $this->getActiveUser(), "logId" => (String)$activity->logId)))) {
                                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity_log", $dbStorage);
                                        nxr("  Activity " . (String)$activity->activityName . " on " . $startDate . " (" . (String)$activity->logId . ") add to the database.");
                                    } else {
                                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity_log", $dbStorage, array("AND" => array("user" => $this->getActiveUser(), "logId" => (String)$activity->logId)));
                                        nxr("  Activity " . (String)$activity->activityName . " on " . $startDate . " (" . (String)$activity->logId . ") updated in the database.");
                                    }

                                    if (isset($activity->tcxLink)) {
                                        $downloadTCX = TRUE;

                                        if (isset($activity->logType) && $activity->logType == "auto_detected") {
                                            $downloadTCX = FALSE;
                                        }

                                        if (isset($activity->source) && isset($activity->source->name) && isset($activity->source->type)) {
                                            if (($activity->source->type == "tracker" && $activity->source->name == "Surge") || ($activity->source->type == "app" && $activity->source->name == "Fitbit for Android")) {
                                                $downloadTCX = TRUE;
                                            } else {
                                                $downloadTCX = FALSE;
                                            }
                                        }

                                        if ($downloadTCX) $this->pullBabelTCX($activity->tcxLink);
                                    }

                                    if ($this->activeUser == $this->getAppClass()->getSetting("ownerFuid", NULL, FALSE)) {
                                        $this->pullBabelHeartIntraday($activity);
                                    }
                                }
                                $this->api_setLastCleanrun("activity_log", new DateTime ($startDate));

                            }
                        } else {
                            nxr("  No recorded activities");
                            $this->api_setLastCleanrun("activity_log", new DateTime ($userActivityLog->pagination->afterDate), 2);
                            $this->api_setLastrun("activity_log");
                        }
                    } else {
                        $this->api_setLastCleanrun("activity_log", new DateTime ((String)$targetDateTime->format("Y-m-d")), 7);
                        $this->api_setLastrun("activity_log");
                    }

                } else {
                    nxr("  Error activity log: " . $this->getAppClass()->lookupErrorCode(-143));
                }
            }

            return TRUE;
        }

        /**
         * @return mixed
         * @internal param $targetDate
         */
        private function pullBabelUserGoals() {
            $isAllowed = $this->isAllowed("goals");
            if (!is_numeric($isAllowed)) {
                if ($this->api_isCooled("goals")) {
                    $userGoals = $this->pullBabel('user/-/activities/goals/daily.json', TRUE);

                    if (isset($userGoals) && isset($userGoals->goals)) {
                        $currentDate = new DateTime();
                        $usr_goals = $userGoals->goals;
                        if (is_object($usr_goals)) {
                            $fallback = FALSE;

                            if (!isset($usr_goals->caloriesOut) OR !isset($usr_goals->distance) OR !isset($usr_goals->floors) OR !isset($usr_goals->activeMinutes) OR !isset($usr_goals->steps) OR $usr_goals->caloriesOut == "" OR $usr_goals->distance == "" OR $usr_goals->floors == "" OR $usr_goals->activeMinutes == "" OR $usr_goals->steps == "") {
                                $this->getAppClass()->addCronJob($this->getActiveUser(), "goals");

                                if (!isset($usr_goals->caloriesOut) OR $usr_goals->caloriesOut == "")
                                    $usr_goals->caloriesOut = -1;

                                if (!isset($usr_goals->distance) OR $usr_goals->distance == "")
                                    $usr_goals->distance = -1;

                                if (!isset($usr_goals->floors) OR $usr_goals->floors == "")
                                    $usr_goals->floors = -1;

                                if (!isset($usr_goals->activeMinutes) OR $usr_goals->activeMinutes == "")
                                    $usr_goals->activeMinutes = -1;

                                if (!isset($usr_goals->steps) OR $usr_goals->steps == "")
                                    $usr_goals->steps = -1;

                                $fallback = TRUE;
                            }

                            if ($usr_goals->steps > 1) {
                                $newGoal = $this->thisWeeksGoal("steps", $usr_goals->steps);
                                if ($newGoal > 0 && $usr_goals->steps != $newGoal) {
                                    nxr("  Returned steps target was " . $usr_goals->steps . " but I think it should be " . $newGoal);
                                    $this->pushBabel('user/-/activities/goals/daily.json', array('steps' => $newGoal));
                                } elseif ($newGoal > 0) {
                                    nxr("  Returned steps target was " . $usr_goals->steps . " which is right for this week goal of " . $newGoal);
                                }

	                            $this->getAppClass()->getUserSetting($this->getActiveUser(), "goal_steps", $newGoal);
                            }

                            if ($usr_goals->floors > 1) {
                                $newGoal = $this->thisWeeksGoal("floors", $usr_goals->floors);
                                if ($newGoal > 0 && $usr_goals->floors != $newGoal) {
                                    nxr("  Returned floor target was " . $usr_goals->floors . " but I think it should be " . $newGoal);
                                    $this->pushBabel('user/-/activities/goals/daily.json', array('floors' => $newGoal));
                                } elseif ($newGoal > 0) {
                                    nxr("  Returned floor target was " . $usr_goals->floors . " which is right for this week goal of " . $newGoal);
                                }
                            }

                            $interval = DateInterval::createFromDateString('1 day');
                            $period = new DatePeriod ($this->api_getLastCleanrun("goals"), $interval, $currentDate);
                            /**
                             * @var DateTime $dt
                             */
                            foreach ($period as $dt) {
                                if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array("AND" => array('user' => $this->getActiveUser(), 'date' => $dt->format("Y-m-d"))))) {

                                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array(
                                        'caloriesOut'   => (String)$usr_goals->caloriesOut,
                                        'distance'      => (String)$usr_goals->distance,
                                        'floors'        => (String)$usr_goals->floors,
                                        'activeMinutes' => (String)$usr_goals->activeMinutes,
                                        'steps'         => (String)$usr_goals->steps,
                                        'syncd'         => date("Y-m-d H:i:s")
                                    ), array("AND" => array('user' => $this->getActiveUser(), 'date' => $dt->format("Y-m-d"))));
                                } else {

                                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array(
                                        'user'          => $this->getActiveUser(),
                                        'date'          => $dt->format("Y-m-d"),
                                        'caloriesOut'   => (String)$usr_goals->caloriesOut,
                                        'distance'      => (String)$usr_goals->distance,
                                        'floors'        => (String)$usr_goals->floors,
                                        'activeMinutes' => (String)$usr_goals->activeMinutes,
                                        'steps'         => (String)$usr_goals->steps,
                                        'syncd'         => date("Y-m-d H:i:s")
                                    ));
                                }
                            }

                            if (!$fallback) $this->api_setLastCleanrun("goals", $currentDate);
                            $this->api_setLastrun("goals");
                        }

                    }

                    return $userGoals;
                } else {
                    return "-143";
                }
            } else {
                return $isAllowed;
            }

        }

        /**
         * @param $targetDate
         *
         * @return mixed
         */
        private function pullBabelMeals($targetDate) {
            $targetDateTime = new DateTime ($targetDate);
            $userFoodLog = $this->pullBabel('user/' . $this->getActiveUser() . '/foods/log/date/' . $targetDateTime->format('Y-m-d') . '.json', TRUE);

            if (isset($userFoodLog)) {
                if (count($userFoodLog->foods) > 0) {
                    foreach ($userFoodLog->foods as $meal) {
                        nxr("  Logging meal " . $meal->loggedFood->name);

                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "food", array("AND" => array('user' => $this->getActiveUser(), 'date' => $targetDate, 'meal' => (String)$meal->loggedFood->name)))) {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "food", array(
                                'calories' => (String)$meal->nutritionalValues->calories,
                                'carbs'    => (String)$meal->nutritionalValues->carbs,
                                'fat'      => (String)$meal->nutritionalValues->fat,
                                'fiber'    => (String)$meal->nutritionalValues->fiber,
                                'protein'  => (String)$meal->nutritionalValues->protein,
                                'sodium'   => (String)$meal->nutritionalValues->sodium
                            ), array("AND" => array('user' => $this->getActiveUser(), 'date' => $targetDate, 'meal' => (String)$meal->loggedFood->name)));
                        } else {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "food", array(
                                'user'     => $this->getActiveUser(),
                                'date'     => $targetDate,
                                'meal'     => (String)$meal->loggedFood->name,
                                'calories' => (String)$meal->nutritionalValues->calories,
                                'carbs'    => (String)$meal->nutritionalValues->carbs,
                                'fat'      => (String)$meal->nutritionalValues->fat,
                                'fiber'    => (String)$meal->nutritionalValues->fiber,
                                'protein'  => (String)$meal->nutritionalValues->protein,
                                'sodium'   => (String)$meal->nutritionalValues->sodium
                            ));
                        }

                        $this->api_setLastCleanrun("foods", $targetDateTime);
                    }
                } else {
	                $currentDate = new DateTime();
	                $daysSinceReading = (strtotime($currentDate->format("Y-m-d")) - strtotime($targetDateTime->format('Y-m-d'))) / (60 * 60 * 24);
	                nxr("   No recorded data for " . $targetDateTime->format('Y-m-d') . " " . $daysSinceReading . " days ago");
	                if ($daysSinceReading > 7)
		               $this->api_setLastCleanrun("foods", $targetDateTime);
                }
            }

            return $userFoodLog;
        }

        /**
         * @param $targetDate
         *
         * @return mixed
         */
        private function pullBabelBody($targetDate) {
            $targetDateTime = new DateTime ($targetDate);
            $userBodyLog = $this->pullBabel('user/' . $this->getActiveUser() . '/body/date/' . $targetDateTime->format('Y-m-d') . '.json', TRUE);

            if (isset($userBodyLog)) {
                $fallback = FALSE;
                $currentDate = new DateTime ();
                if ($currentDate->format("Y-m-d") == $targetDate and ($userBodyLog->body->weight == "0" OR $userBodyLog->body->fat == "0" OR
                        $userBodyLog->body->bmi == "0" OR (isset($userBodyLog->goals) AND ((isset($userBodyLog->goals->weight) AND $userBodyLog->goals->weight == "0") OR
                                                                                           (isset($userBodyLog->goals->fat) AND $userBodyLog->goals->fat == "0"))))
                ) {
                    $this->getAppClass()->addCronJob($this->getActiveUser(), "body");
                    $fallback = TRUE;
                }

                $insertToDB = FALSE;
                if (!isset($userBodyLog->body->weight) or $userBodyLog->body->weight == "0") {
                    nxr('  Weight unrecorded, reverting to previous record');
                    $weight = $this->getDBCurrentBody($this->getActiveUser(), "weight");
                    $fallback = TRUE;
                } else {
                    $weight = (float)$userBodyLog->body->weight;
                    $insertToDB = TRUE;
                }

                if (!isset($userBodyLog->body->fat) or $userBodyLog->body->fat == "0") {
                    nxr('  Body Fat unrecorded, reverting to previous record');
                    $fat = $this->getDBCurrentBody($this->getActiveUser(), "fat");
                    $fallback = TRUE;
                } else {
                    $fat = (float)$userBodyLog->body->fat;
                    $insertToDB = TRUE;
                }

                if ($insertToDB) {
                    if (!isset($userBodyLog->goals->weight) or $userBodyLog->goals->weight == "0") {
                        nxr('  Weight Goal unset, reverting to previous record');
                        $goalsweight = $this->getDBCurrentBody($this->getActiveUser(), "weightGoal");
                    } else {
                        $goalsweight = (float)$userBodyLog->goals->weight;
                    }

                    if (!isset($userBodyLog->goals->fat) or $userBodyLog->goals->fat == "0") {
                        nxr('  Body Fat Goal unset, reverting to previous record');
                        $goalsfat = $this->getDBCurrentBody($this->getActiveUser(), "fatGoal");
                    } else {
                        $goalsfat = (float)$userBodyLog->goals->fat;
                    }

                    $user_height = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", "height", array("fuid" => $this->getActiveUser()));
                    if (is_numeric($user_height) AND $user_height > 0) {
                        $user_height = $user_height / 100;
                        $bmi = round($weight / ($user_height * $user_height), 2);
                    } else {
                        $bmi = "0.0";
                    }

                    $db_insetArray = array(
                        "weight"     => $weight,
                        "weightGoal" => $goalsweight,
                        "fat"        => $fat,
                        "fatGoal"    => $goalsfat,
                        "bmi"        => $bmi
                    );

                    $lastWeight = $this->getDBCurrentBody($this->getActiveUser(), "weight");
                    $lastFat = $this->getDBCurrentBody($this->getActiveUser(), "fat");
                    if ($lastWeight != $weight) {
                        $db_insetArray['weightAvg'] = round(($weight - $lastWeight) / 10, 1, PHP_ROUND_HALF_UP) + $lastWeight;
                    } else {
                        $db_insetArray['weightAvg'] = $this->getDBCurrentBody($this->getActiveUser(), "weightAvg");
                    }
                    if ($lastFat != $fat) {
                        $db_insetArray['fatAvg'] = round(($fat - $lastFat) / 10, 1, PHP_ROUND_HALF_UP) + $lastFat;
                    } else {
                        $db_insetArray['fatAvg'] = $this->getDBCurrentBody($this->getActiveUser(), "fatAvg");
                    }

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body", array("AND" => array('user' => $this->getActiveUser(), 'date' => $targetDate)))) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body", $db_insetArray, array("AND" => array('user' => $this->getActiveUser(), 'date' => $targetDate)));
                    } else {
                        $db_insetArray['user'] = $this->getActiveUser();
                        $db_insetArray['date'] = $targetDate;
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body", $db_insetArray);
                    }

                    if (!$fallback) $this->api_setLastCleanrun("body", new DateTime ($targetDate));
                } else {
	                $currentDate = new DateTime();
	                $daysSinceReading = (strtotime($currentDate->format("Y-m-d")) - strtotime($targetDateTime->format('Y-m-d'))) / (60 * 60 * 24);
	                nxr("   No recorded data for " . $targetDateTime->format('Y-m-d') . " " . $daysSinceReading . " days ago");
	                if ($daysSinceReading > 7)
	                   $this->api_setLastCleanrun("body", new DateTime ($targetDate));
                }
            }

            return $userBodyLog;

        }

        /**
         * @param $targetDate
         *
         * @return mixed|null|SimpleXMLElement|string
         */
        private function pullBabelSleep($targetDate) {
            $targetDateTime = new DateTime ($targetDate);
            $userSleepLog = $this->pullBabel('user/' . $this->getActiveUser() . '/sleep/date/' . $targetDateTime->format('Y-m-d') . '.json', TRUE);

            if (isset($userSleepLog) and is_object($userSleepLog) and is_array($userSleepLog->sleep) and count($userSleepLog->sleep) > 0) {
                $loggedSleep = $userSleepLog->sleep[0];
                if ($loggedSleep->logId != 0) {
                    if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "sleep", array("logId" => (String)$loggedSleep->logId))) {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "sleep", array(
                            "logId"               => (String)$loggedSleep->logId,
                            'awakeningsCount'     => (String)$loggedSleep->awakeningsCount,
                            'duration'            => (String)$loggedSleep->duration,
                            'efficiency'          => (String)$loggedSleep->efficiency,
                            'isMainSleep'         => (String)$loggedSleep->isMainSleep,
                            'minutesAfterWakeup'  => (String)$loggedSleep->minutesAfterWakeup,
                            'minutesAsleep'       => (String)$loggedSleep->minutesAsleep,
                            'minutesAwake'        => (String)$loggedSleep->minutesAwake,
                            'minutesToFallAsleep' => (String)$loggedSleep->minutesToFallAsleep,
                            'startTime'           => (String)$loggedSleep->startTime,
                            'timeInBed'           => (String)$loggedSleep->timeInBed
                        ));
                    }

                    if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "sleep_user", array("AND" => array('user' => $this->getActiveUser(), 'sleeplog' => (String)$loggedSleep->logId)))) {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "sleep_user", array(
                            'user'               => $this->getActiveUser(),
                            'sleeplog'           => (String)$loggedSleep->logId,
                            'totalMinutesAsleep' => (String)$userSleepLog->summary->totalMinutesAsleep,
                            'totalSleepRecords'  => (String)$userSleepLog->summary->totalSleepRecords,
                            'totalTimeInBed'     => (String)$userSleepLog->summary->totalTimeInBed
                        ));
                    }

                    $this->api_setLastCleanrun("sleep", new DateTime ($targetDate));

                    nxr("  Sleeplog " . $loggedSleep->logId . " recorded");
                }
            } else {
                $this->api_setLastCleanrun("sleep", new DateTime ($targetDate), 7);
                $this->api_setLastrun("sleep");
            }

            return $userSleepLog;

        }

        /**
         * @param $targetDate
         *
         * @return mixed
         */
        private function pullBabelWater($targetDate) {
            $targetDateTime = new DateTime ($targetDate);
            $userWaterLog = $this->pullBabel('user/-/foods/log/water/date/' . $targetDateTime->format('Y-m-d') . '.json', TRUE);

            if (isset($userWaterLog)) {
                if (isset($userWaterLog->summary->water)) {

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water", array("AND" => array('user' => $this->getActiveUser(), 'date' => $targetDate)))) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water", array(
                            'id'     => $targetDateTime->format("U"),
                            'liquid' => (String)$userWaterLog->summary->water
                        ), array("AND" => array('user' => $this->getActiveUser(), 'date' => $targetDate)));
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "water", array(
                            'user'   => $this->getActiveUser(),
                            'date'   => $targetDate,
                            'id'     => $targetDateTime->format("U"),
                            'liquid' => (String)$userWaterLog->summary->water
                        ));
                    }

                    $this->api_setLastCleanrun("water", $targetDateTime);
                }
            }

            return $userWaterLog;
        }

        /**
         * @param $AppClass
         */
        private function setAppClass($AppClass) {
            $this->AppClass = $AppClass;
        }

        /**
         * @return NxFitbit
         */
        private function getAppClass() {
            return $this->AppClass;
        }

        /**
         * @param $lastCleanRun
         *
         * @return bool|mixed|string
         */
        private function pullBabelHeartRateSeries($lastCleanRun) {
            // Check we're allowed to pull these records here rather than at each loop
            $isAllowed = $this->isAllowed("heart");
            if (!is_numeric($isAllowed)) {
                if ($this->api_isCooled("heart")) {
                    $lastCleanRun = new DateTime ($lastCleanRun);
                    $userHeartRateLog = $this->pullBabel('user/' . $this->getActiveUser() . '/activities/heart/date/today/' . $lastCleanRun->format('Y-m-d') . '.json', TRUE, TRUE, TRUE);
                    if (isset($userHeartRateLog) and is_numeric($userHeartRateLog)) return "-" . $userHeartRateLog;

                    if (isset($userHeartRateLog)) {
                        $className = "activities-heart";
                        $activities = $userHeartRateLog->$className;
                        if (is_array($activities) && count($activities) > 0) {
                            foreach ($activities as $activity) {
                                $lastDateReturned = $activity->dateTime;
                                if (array_key_exists("restingHeartRate", $activity->value)) {
                                    $databaseArray = array(
                                        'user'    => $this->getActiveUser(),
                                        'date'    => (String)$activity->dateTime,
                                        'resting' => (String)$activity->value->restingHeartRate
                                    );
                                    foreach ($activity->value->heartRateZones as $heartRateZone) {
                                        if (array_key_exists("minutes", $heartRateZone)) {
                                            nxr("  " . $activity->dateTime . " you spent " . $heartRateZone->minutes . " in " . $heartRateZone->name . " zone");
                                            $key = str_replace(" ", "", strtolower($heartRateZone->name));
                                            $databaseArray[ $key ] = (String)$heartRateZone->minutes;
                                            $databaseArray[ $key . '_cals' ] = (String)$heartRateZone->caloriesOut;
                                        } else {
                                            nxr("  " . $activity->dateTime . " does have time spent in '" . $heartRateZone->name . "' zone");
                                        }

                                    }

                                    if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "heartAverage",
                                        array("AND" => array('user' => $this->getActiveUser(), 'date' => (String)$activity->dateTime)))
                                    ) {
                                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "heartAverage", $databaseArray);
                                    } else {
                                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "heartAverage", $databaseArray,
                                            array("AND" => array('user' => $this->getActiveUser(), 'date' => (String)$activity->dateTime)));
                                    }
                                } else {
                                    nxr("  " . $activity->dateTime . " does have a resting heart rate");
                                }
                            }
                        }

                        if (isset($lastDateReturned)) $this->api_setLastCleanrun("heart", new DateTime ($lastDateReturned));
                    }

                    return $userHeartRateLog;
                } else {
                    return "-143";
                }
            } else {
                return $isAllowed;
            }
        }

        /**
         * Add subscription
         *
         * @param string $id   Subscription Id
         * @param string $path Subscription resource path (beginning with slash). Omit to subscribe to all user updates.
         * @param string $subscriberId
         *
         * @return mixed
         */
        private function pushBabelSubscription($id, $path = NULL, $subscriberId = NULL) {
            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $this->getAccessToken();

                $userHeaders = array(
                    "Accept-Header" => "en_GB",
                    "Content-Type"  => "application/x-www-form-urlencoded"
                );
                if ($subscriberId)
                    $userHeaders['X-Fitbit-Subscriber-Id'] = $subscriberId;

                if (isset($path))
                    $path = '/' . $path;
                else
                    $path = '';

                $request = $this->getLibrary()->getAuthenticatedRequest(OAUTH_HTTP_METHOD_POST, FITBIT_COM . "/1/user/-" . $path . "/apiSubscriptions/" . $id . ".json", $accessToken, array("headers" => $userHeaders));
                // Make the authenticated API request and get the response.

                $response = $this->getLibrary()->getResponse($request);
                $response = json_decode(json_encode($response), FALSE);

                //nxr(print_r($request->getUri(), true));
                //nxr(print_r($request->getHeaders(), true));
                //nxr(print_r($request->getBody()->getContents(), true));
                //nxr(print_r($response, true));
                return $response;
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                // Failed to get the access token or user details.
                nxr($e->getMessage());
                die();
            }
        }

        /**
         * @param      $path
         * @param      $pushObject
         * @param bool $returnObject
         *
         * @return mixed
         */
        private function pushBabel($path, $pushObject, $returnObject = FALSE) {
            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $this->getAccessToken();

                if (is_array($pushObject)) $pushObject = http_build_query($pushObject);

                $request = $this->getLibrary()->getAuthenticatedRequest(OAUTH_HTTP_METHOD_POST, FITBIT_COM . "/1/" . $path, $accessToken,
                    array("headers" =>
                              array(
                                  "Accept-Header" => "en_GB",
                                  "Content-Type"  => "application/x-www-form-urlencoded"
                              ),
                          "body"    => $pushObject
                    ));
                // Make the authenticated API request and get the response.

                $response = $this->getLibrary()->getResponse($request);

                if ($returnObject) {
                    $response = json_decode(json_encode($response), FALSE);
                }

                //nxr(print_r("pushObject: " . $pushObject, true));
                //nxr(print_r($request->getUri(), true));
                //nxr(print_r($request->getHeaders(), true));
                //nxr(print_r($request->getBody()->getContents(), true));
                //nxr(print_r($response, true));
                return $response;
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                // Failed to get the access token or user details.
                nxr($e->getMessage());
                die();
            }
        }

        /**
         * @return mixed|null|SimpleXMLElement|string
         */
        private function pullBabelProfile() {
            $isAllowed = $this->isAllowed("profile");
            if (!is_numeric($isAllowed)) {
                if ($this->api_isCooled("profile")) {
                    $userProfile = $this->pullBabel('user/-/profile.json');
                    $userProfile = $userProfile['user'];

                    if (!isset($userProfile['height'])) {
                        $userProfile['height'] = NULL;
                    }
                    if (!isset($userProfile['strideLengthRunning'])) {
                        $userProfile['strideLengthRunning'] = NULL;
                    }
                    if (!isset($userProfile['strideLengthWalking'])) {
                        $userProfile['strideLengthWalking'] = NULL;
                    }
                    if (!isset($userProfile['city'])) {
                        $userProfile['city'] = NULL;
                    }
                    if (!isset($userProfile['country'])) {
                        $userProfile['country'] = NULL;
                    }

                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array(
                        "avatar"         => (String)$userProfile['avatar150'],
                        "city"           => (String)$userProfile['city'],
                        "country"        => (String)$userProfile['country'],
                        "name"           => (String)$userProfile['fullName'],
                        "gender"         => (String)$userProfile['gender'],
                        "height"         => (String)$userProfile['height'],
                        "seen"           => (String)$userProfile['memberSince'],
                        "stride_running" => (String)$userProfile['strideLengthRunning'],
                        "stride_walking" => (String)$userProfile['strideLengthWalking']
                    ), array("fuid" => $this->getActiveUser()));

                    if (!file_exists(dirname(__FILE__) . "/../images/avatars/" . $this->getActiveUser() . ".jpg")) {
                        file_put_contents(dirname(__FILE__) . "/../images/avatars/" . $this->getActiveUser() . ".jpg", fopen((String)$userProfile['avatar150'], 'r'));
                    }

                    $this->api_setLastrun("profile", NULL, TRUE);

                    $subscriptions = $this->pullBabel('user/-/apiSubscriptions.json', TRUE);
                    if (count($subscriptions->apiSubscriptions) == 0) {
                        nxr(" " . $this->getActiveUser() . " is not subscribed to the site");
                        $user_db_id = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", 'uid', array("fuid" => $this->getActiveUser()));
                        $this->pushBabelSubscription($user_db_id);
                        nxr(" " . $this->getActiveUser() . " subscription confirmed with ID: $user_db_id");
                    } else {
                        nxr(" " . $this->getActiveUser() . " subscription is still valid");
                    }

                    return $userProfile;
                } else {
                    return "-143";
                }
            } else {
                return $isAllowed;
            }
        }

        /**
         * Download information about devices associated with the users account. This is then stored in the database
         *
         * @return mixed|null|SimpleXMLElement|string
         */
        private function pullBabelDevices() {
            $isAllowed = $this->isAllowed("devices");
            if (!is_numeric($isAllowed)) {
                if ($this->api_isCooled("devices")) {
                    $userDevices = $this->pullBabel('user/-/devices.json', TRUE);

	                $trackers = array();
                    foreach ($userDevices as $device) {
                        if (isset($device->id) and $device->id != "") {
                            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", array("AND" => array("id" => (String)$device->id)))) {
	                            $current_battery = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", "battery", array("id" => (String)$device->id));

                                $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", array(
                                    'lastSyncTime' => (String)$device->lastSyncTime,
                                    'battery'      => (String)$device->battery
                                ), array("id" => (String)$device->id));

	                            if ($device->battery != $current_battery) {
		                            $charged = 0;
		                            if (
			                            ($current_battery == "Empty" && ($device->battery == "Low" || $device->battery == "Medium" || $device->battery == "High" || $device->battery == "Full"))
			                            || ($current_battery == "Low" && ($device->battery == "Medium" || $device->battery == "High" || $device->battery == "Full"))
			                            || ($current_battery == "Medium" && ($device->battery == "High" || $device->battery == "Full"))
		                            ) {
			                            $charged = 1;
		                            }

		                            $this->getAppClass()->getDatabase()->insert( $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "devices_charges", array(
			                            'id'    => (String) $device->id,
			                            'date'  => (String) $device->lastSyncTime,
			                            'level' => (String) $device->battery,
			                            'charged' => $charged
		                            ) );
	                            }
                            } else {
                                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", array(
                                    'id'            => (String)$device->id,
                                    'deviceVersion' => (String)$device->deviceVersion,
                                    'type'          => (String)$device->type,
                                    'lastSyncTime'  => (String)$device->lastSyncTime,
                                    'battery'       => (String)$device->battery
                                ));
	                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices_user", array(
		                            'user'   => $this->getActiveUser(),
		                            'device' => (String)$device->id
	                            ));
	                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices_charges", array(
		                            'id'            => (String)$device->id,
		                            'date' => (String)$device->lastSyncTime,
		                            'level'          => (String)$device->battery
	                            ));
                            }

                            if (!file_exists(dirname(__FILE__) . "/../images/devices/" . str_ireplace(" ", "", $device->deviceVersion) . ".png")) {
                                nxr(" No device image for " . $device->type . " " . $device->deviceVersion);
                            }

                            if ($device->type == "TRACKER") {
	                            array_push($trackers, $device->deviceVersion);
                            }

                        }
                    }

                    if (count($trackers) > 0) {
	                    $supportedHeart = FALSE;
	                    $supportedFloors = FALSE;

	                    //nxr( " Using ".count($trackers)." Trackers");
	                    if (in_array("Surge", $trackers) || in_array("Charge HR", $trackers)) {
		                    $supportedHeart = TRUE;
		                    $supportedFloors = TRUE;
	                    } else if (in_array("Charge", $trackers)) {
		                    $supportedFloors = TRUE;
	                    }

	                    if ($supportedHeart && $this->getAppClass()->getSetting("ownerFuid", NULL, FALSE) == $this->getActiveUser()) {
		                    //nxr( "  Heartrate Supported " );
		                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_heart", "1");
	                    } else {
		                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_heart", "0");
	                    }

	                    if ($supportedFloors) {
		                    //nxr( "  Floors Supported " );
		                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_floors", "1");
		                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_elevation", "1");
	                    } else {
		                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_floors", "0");
		                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_elevation", "0");
	                    }

	                    if (!is_null($this->getAppClass()->getSetting("nomie_key_" . $this->getActiveUser(), NULL, FALSE))) {
		                    //nxr( "  Nomie Supported " );
		                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_nomie_trackers", "1");
	                    } else {
		                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_nomie_trackers", "0");
	                    }
                    }

                    $this->api_setLastrun("devices", NULL, TRUE);

                    return $userDevices;
                } else {
                    return "-143";
                }
            } else {
                return $isAllowed;
            }
        }

        /**
         * Download information of badges the user has aquired
         *
         * @return mixed|null|SimpleXMLElement|string
         * @internal param $user
         */
        private function pullBabelBadges() {
            $isAllowed = $this->isAllowed("badges");
            if (!is_numeric($isAllowed)) {
                if ($this->api_isCooled("badges")) {
                    $badgeFolder = dirname(__FILE__) . "/../images/badges/";
                    if (file_exists($badgeFolder) AND is_writable($badgeFolder)) {

                        $userBadges = $this->pullBabel('user/' . $this->getActiveUser() . '/badges.json', TRUE);

                        if (isset($userBadges)) {
                            foreach ($userBadges->badges as $badge) {

                                if (is_array($badge)) {
                                    $badge = json_decode(json_encode($badge), FALSE);
                                }

                                if ($badge->badgeType != "") {
                                    /*
                                    * Check to make sure, some badges do not include unit values
                                    */
                                    if (isset($badge->unit)) {
                                        $unit = (String)$badge->unit;
                                    } else {
                                        $unit = "";
                                    }

	                                /*
									* If the badge is not already in the database insert it
									*/
	                                if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages", array(
		                                "encodedId" => (String)$badge->encodedId
	                                ))
	                                ) {
		                                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages", array(
			                                'encodedId'               => (String)$badge->encodedId,
			                                'badgeType'               => (String)$badge->badgeType,
			                                'value'                   => (String)$badge->value,
			                                'category'                   => (String)$badge->category,
			                                'description'                   => (String)$badge->description,
			                                'image'                   => basename((String)$badge->image50px),
			                                'badgeGradientEndColor'   => (String)$badge->badgeGradientEndColor,
			                                'badgeGradientStartColor' => (String)$badge->badgeGradientStartColor,
			                                'earnedMessage'           => (String)$badge->earnedMessage,
			                                'marketingDescription'    => (String)$badge->marketingDescription,
			                                'name'                    => (String)$badge->name
		                                ));
	                                }

	                                if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages_user", array("AND" => array(
		                                "badgeid"      => (String)$badge->encodedId,
		                                "fuid" => $this->getActiveUser()
	                                )))
	                                ) {
		                                nxr(" User " . $this->getActiveUser() . " has been awarded the " . $badge->name . " again");
		                                $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages_user", array(
			                                'dateTime'      => (String)$badge->dateTime,
			                                'timesAchieved' => (String)$badge->timesAchieved
		                                ), array("AND" => array(
			                                "badgeid"      => (String)$badge->encodedId,
			                                "fuid" => $this->getActiveUser()
		                                )));
	                                } else {
		                                nxr(" User " . $this->getActiveUser() . " has been awarded the " . $badge->name . ", " . $badge->timesAchieved . " times.");
		                                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages_user", array(
			                                "badgeid"      => (String)$badge->encodedId,
			                                "fuid" => $this->getActiveUser(),
			                                'dateTime'      => (String)$badge->dateTime,
			                                'timesAchieved' => (String)$badge->timesAchieved
		                                ));
	                                }

                                    $imageFileName = basename((String)$badge->image50px);
                                    if (!file_exists($badgeFolder . "/" . $imageFileName)) {
                                        file_put_contents($badgeFolder . "/" . $imageFileName, fopen((String)$badge->image50px, 'r'));
                                    }

                                    if (!file_exists($badgeFolder . "/75px")) {
                                        mkdir($badgeFolder . "/75px", 0755, TRUE);
                                    }
                                    if (!file_exists($badgeFolder . "/75px/" . $imageFileName)) {
                                        file_put_contents($badgeFolder . "/75px/" . $imageFileName, fopen((String)$badge->image75px, 'r'));
                                    }

                                    if (!file_exists($badgeFolder . "/100px")) {
                                        mkdir($badgeFolder . "/100px", 0755, TRUE);
                                    }
                                    if (!file_exists($badgeFolder . "/100px/" . $imageFileName)) {
                                        file_put_contents($badgeFolder . "/100px/" . $imageFileName, fopen((String)$badge->image100px, 'r'));
                                    }

                                    if (!file_exists($badgeFolder . "/125px")) {
                                        mkdir($badgeFolder . "/125px", 0755, TRUE);
                                    }
                                    if (!file_exists($badgeFolder . "/125px/" . $imageFileName)) {
                                        file_put_contents($badgeFolder . "/125px/" . $imageFileName, fopen((String)$badge->image125px, 'r'));
                                    }

                                    if (!file_exists($badgeFolder . "/300px")) {
                                        mkdir($badgeFolder . "/300px", 0755, TRUE);
                                    }
                                    if (!file_exists($badgeFolder . "/300px/" . $imageFileName)) {
                                        file_put_contents($badgeFolder . "/300px/" . $imageFileName, fopen((String)$badge->image300px, 'r'));
                                    }
                                }
                            }
                        }

                        $this->api_setLastrun("badges", NULL, TRUE);

                        return $userBadges;
                    } else {
                        nxr("Missing: $badgeFolder");

                        return "-142";
                    }
                } else {
                    return "-143";
                }
            } else {
                return $isAllowed;
            }
        }

        /**
         * @return mixed|null|SimpleXMLElement|string
         * @internal param $user
         */
        private function pullBabelLeaderboard() {
            $isAllowed = $this->isAllowed("leaderboard");
            if (!is_numeric($isAllowed)) {
                if ($this->api_isCooled("leaderboard")) {
                    $userFriends = $this->pullBabel('user/-/friends/leaderboard.json', TRUE);

                    if (isset($userFriends)) {
                        $userFriends = $userFriends->friends;

                        if (count($userFriends) > 0) {
                            $youRank = 0;
                            $youDistance = 0;
                            $lastSteps = 0;
	                        $storedLeaderboard = array();
                            foreach ($userFriends as $friend) {
                                $lifetime = floatval($friend->lifetime->steps);
                                $steps = floatval($friend->summary->steps);

                                if ($this->getActiveUser() == $this->getAppClass()->getSetting("ownerFuid", NULL, FALSE)) {
                                    if (!isset($allOwnersFriends)) {
                                        $allOwnersFriends = $friend->user->encodedId;
                                    } else {
                                        $allOwnersFriends = $allOwnersFriends . "," . $friend->user->encodedId;
                                    }
                                }

                                if ($friend->user->encodedId == $this->getActiveUser()) {
                                    $displayName = "* YOU * are";
                                    if ($steps == 0) {
                                        $youRank = count($userFriends);
                                    } else {
                                        $youRank = (String)$friend->rank->steps;
                                    }
                                    $youDistance = ($lastSteps - $steps);
                                    if ($youDistance < 0) $youDistance = 0;
                                } else {
                                    $displayName = $friend->user->displayName . " is";
                                    $lastSteps = $steps;
                                }

                                nxr("  " . $displayName . " ranked " . $friend->rank->steps . " with " . number_format($steps) . " and " . number_format($lifetime) . " lifetime steps");

	                            $friendId = $friend->user->encodedId;
	                            $storedLeaderboard[$friendId] = array();
	                            if (isset($friend->rank->steps) && !empty($friend->rank->steps)) $storedLeaderboard[$friendId]["rank"] = (String)$friend->rank->steps;
	                            if (isset($friend->average->steps) && !empty($friend->average->steps)) $storedLeaderboard[$friendId]["stepsAvg"] = (String)$friend->average->steps;
	                            if (isset($friend->lifetime->steps) && !empty($friend->lifetime->steps)) $storedLeaderboard[$friendId]["stepsLife"] = (String)$friend->lifetime->steps;
	                            if (isset($friend->summary->steps) && !empty($friend->summary->steps)) $storedLeaderboard[$friendId]["stepsSum"] = (String)$friend->summary->steps;
	                            if (isset($friend->user->avatar) && !empty($friend->user->avatar)) $storedLeaderboard[$friendId]["avatar"] = (String)$friend->user->avatar;
	                            if (isset($friend->user->displayName) && !empty($friend->user->displayName)) $storedLeaderboard[$friendId]["displayName"] = (String)$friend->user->displayName;
	                            if (isset($friend->user->gender) && !empty($friend->user->gender)) $storedLeaderboard[$friendId]["gender"] = (String)$friend->user->gender;
	                            if (isset($friend->user->memberSince) && !empty($friend->user->memberSince)) $storedLeaderboard[$friendId]["memberSince"] = (String)$friend->user->memberSince;
	                            if (isset($friend->user->age) && !empty($friend->user->age)) $storedLeaderboard[$friendId]["age"] = (String)$friend->user->age;
	                            if (isset($friend->user->city) && !empty($friend->user->city)) $storedLeaderboard[$friendId]["city"] = (String)$friend->user->city;
	                            if (isset($friend->user->country) && !empty($friend->user->country)) $storedLeaderboard[$friendId]["country"] = (String)$friend->user->country;

                            }

                            if ($this->getActiveUser() == $this->getAppClass()->getSetting("ownerFuid", NULL, FALSE) && isset($allOwnersFriends)) {
                                $this->getAppClass()->setSetting("owners_friends", $allOwnersFriends);
                            }

                            nxr("  * You are " . number_format($youDistance) . " steps away from the next rank and have " . count($userFriends) . " friends");

                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array(
                                'rank'     => $youRank,
                                'friends'  => count($userFriends),
                                'distance' => $youDistance
                            ), array("fuid" => $this->getActiveUser()));

	                        if (count($storedLeaderboard) > 0) $this->getAppClass()->setUserSetting($this->getActiveUser(), "leaderboard", json_encode($storedLeaderboard));

                        }
                    }

                    $this->api_setLastrun("leaderboard", NULL, TRUE);

                    return $userFriends;
                } else {
                    return "-143";
                }
            } else {
                return $isAllowed;
            }

        }

        /**
         * @return mixed|null|SimpleXMLElement|string
         * @internal param $user
         */
        private function pullBabelCaloriesGoals() {
            $isAllowed = $this->isAllowed("goals_calories");
            if (!is_numeric($isAllowed)) {
                if ($this->api_isCooled("goals_calories")) {
                    $userCaloriesGoals = $this->pullBabel('user/-/foods/log/goal.json', TRUE);

                    if (isset($userCaloriesGoals) && isset($userCaloriesGoals->goals) && isset($userCaloriesGoals->foodPlan)) {
                        $fallback = FALSE;


                        $usr_goals = $userCaloriesGoals->goals;

                        $usr_foodplan = $userCaloriesGoals->foodPlan;

                        if (empty($usr_goals->calories)) {
                            $usr_goals_calories = 0;
                            $fallback = TRUE;
                        } else {
                            $usr_goals_calories = (int)$usr_goals->calories;
                        }

                        if (empty($usr_foodplan->intensity)) {
                            $usr_foodplan_intensity = "Unset";
                            $fallback = TRUE;
                        } else {
                            $usr_foodplan_intensity = (string)$usr_foodplan->intensity;
                        }

                        $currentDate = new DateTime ('now');
                        if (empty($usr_foodplan->estimatedDate)) {
                            $usr_foodplan_estimatedDate = $currentDate->format("Y-m-d");
                            $fallback = TRUE;
                        } else {
                            $usr_foodplan_estimatedDate = (string)$usr_foodplan->estimatedDate;
                        }

                        if (empty($usr_foodplan->personalized)) {
                            $usr_foodplan_personalized = "false";
                            $fallback = TRUE;
                        } else {
                            $usr_foodplan_personalized = (string)$usr_foodplan->personalized;
                        }

                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "food_goals", array("AND" => array("user" => $this->getActiveUser(), "date" => $currentDate->format("Y-m-d"))))) {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "food_goals", array(
                                'calories'      => $usr_goals_calories,
                                'intensity'     => $usr_foodplan_intensity,
                                'estimatedDate' => $usr_foodplan_estimatedDate,
                                'personalized'  => $usr_foodplan_personalized,
                            ), array("AND" => array("user" => $this->getActiveUser(), "date" => $currentDate->format("Y-m-d"))));
                        } else {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "food_goals", array(
                                'user'          => $this->getActiveUser(),
                                'date'          => $currentDate->format("Y-m-d"),
                                'calories'      => $usr_goals_calories,
                                'intensity'     => $usr_foodplan_intensity,
                                'estimatedDate' => $usr_foodplan_estimatedDate,
                                'personalized'  => $usr_foodplan_personalized,
                            ));
                        }

                        if ($fallback) {
                            $this->api_setLastrun("goals_calories");
                        } else {
                            $this->api_setLastrun("goals_calories", NULL, TRUE);
                        }
                    }

                    return $userCaloriesGoals;
                } else {
                    return "-143";
                }
            } else {
                return $isAllowed;
            }

        }

        /**
         * @return mixed
         */
        private function getActiveUser() {
            return $this->activeUser;
        }

        /**
         * @param      $activity
         * @param null $cron_delay
         * @param bool $clean
         *
         * @internal param $username
         */
        private function api_setLastrun($activity, $cron_delay = NULL, $clean = FALSE) {
            if (is_null($cron_delay)) {
                $cron_delay_holder = 'scope_' . $activity . '_timeout';
                $cron_delay = $this->getAppClass()->getSetting($cron_delay_holder, 5400);
            }

            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $this->getActiveUser(), "activity" => $activity)))) {
                $fields = array(
                    "date"     => date("Y-m-d H:i:s"),
                    "cooldown" => date("Y-m-d H:i:s", time() + $cron_delay)
                );
                if ($clean) {
                    $fields['lastrun'] = date("Y-m-d H:i:s");
                }

                $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", $fields, array("AND" => array("user" => $this->getActiveUser(), "activity" => $activity)));
            } else {
                $fields = array(
                    "user"     => $this->getActiveUser(),
                    "activity" => $activity,
                    "date"     => date("Y-m-d H:i:s"),
                    "cooldown" => date("Y-m-d H:i:s", time() + $cron_delay)
                );
                if ($clean) {
                    $fields['lastrun'] = date("Y-m-d H:i:s");
                }

                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", $fields);
            }

            $cache_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
            $cache_files = scandir($cache_dir);
            foreach ($cache_files as $file) {
                if (file_exists($cache_dir . $file) && is_writable($cache_dir . $file) && substr($file, 0, strlen($this->getActiveUser()) + 1) === "_" . $this->getActiveUser()) {
                    $cacheNames = $this->getAppClass()->getSettings()->getRelatedCacheNames($activity);
                    if (count($cacheNames) > 0) {
                        foreach ($cacheNames as $cacheName) {
                            if (substr($file, 0, strlen($this->getActiveUser()) + strlen($cacheName) + 2) === "_" . $this->getActiveUser() . "_" . $cacheName) {
                                if (file_exists($cache_dir . $file) && is_writable($cache_dir . $file)) {
                                    nxr("  $file cache file was deleted");
                                    unlink($cache_dir . $file);
                                }
                            }
                        }
                    }
                }
            }
        }

        /**
         * @param      $activity
         * @param bool $reset
         *
         * @return DateTime
         * @internal param $username
         */
        private function api_getCoolDown($activity, $reset = FALSE) {
            if ($reset)
                return new DateTime ("1970-01-01");

            $username = $this->getActiveUser();

            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $username, "activity" => $activity)))) {
                return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", "cooldown", array("AND" => array("user" => $username, "activity" => $activity))));
            } else {
                return new DateTime ("1970-01-01");
            }
        }

        /**
         * @return League\OAuth2\Client\Token\AccessToken
         */
        private function getAccessToken() {
            if (is_null($this->userAccessToken)) {
                $user = $this->getActiveUser();

                $userArray = $this->getAppClass()->getUserOAuthTokens($user);
                if (is_array($userArray)) {
                    $accessToken = new League\OAuth2\Client\Token\AccessToken([
                        'access_token'  => $userArray['tkn_access'],
                        'refresh_token' => $userArray['tkn_refresh'],
                        'expires'       => $userArray['tkn_expires']
                    ]);

                    if ($accessToken->hasExpired()) {
                        nxr("This token as expired and needs refreshed");

                        $newAccessToken = $this->getLibrary()->getAccessToken('refresh_token', [
                            'refresh_token' => $accessToken->getRefreshToken()
                        ]);

                        $this->getAppClass()->setUserOAuthTokens($user, $newAccessToken);

                        // Purge old access token and store new access token to your data store.
                        return $newAccessToken;
                    } else {
                        //nxr("This token still valid");
                        return $accessToken;
                    }
                } else {
                    nxr('User ' . $user . ' does not exist, unable to continue.');
                    exit;
                }
            } else {
                return $this->userAccessToken;
            }
        }

        /**
         * @param $activity
         *
         * @return DateTime
         * @internal param $user
         */
        private function api_getLastCleanrun($activity) {
            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $this->getActiveUser(), "activity" => $activity)))) {
                return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", "lastrun", array("AND" => array("user" => $this->getActiveUser(), "activity" => $activity))));
            } else {
                return $this->user_getFirstSeen();
            }
        }

        /**
         * @return DateTime
         * @internal param $user
         */
        private function user_getFirstSeen() {
            return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", "seen", array("fuid" => $this->getActiveUser())));
        }

        /**
         * @param      $activity
         * @param null $date
         * @param int  $delay
         *
         * @internal param $user
         */
        private function api_setLastCleanrun($activity, $date = NULL, $delay = 0) {
            if (is_null($date)) {
                $date = new DateTime("now");
                nxr("Last run " . $date->format("Y-m-d H:i:s"));
            }
            if ($delay > 0) $date->modify('-' . $delay . ' day');

            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $this->getActiveUser(), "activity" => $activity)))) {
                $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array(
                    'date'    => date("Y-m-d H:i:s"),
                    'lastrun' => $date->format("Y-m-d H:i:s")
                ), array("AND" => array("user" => $this->getActiveUser(), "activity" => $activity)));
            } else {
                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array(
                    'user'     => $this->getActiveUser(),
                    'activity' => $activity,
                    'date'     => date("Y-m-d H:i:s"),
                    'lastrun'  => $date->format("Y-m-d H:i:s")
                ));
            }

            if ($delay == 0) $this->api_setLastrun($activity, NULL, FALSE);
        }

        /**
         * @param $string
         *
         * @return float|int|string
         * @internal param $user
         */
        private function thisWeeksGoal($string, $current_goal = 0) {
            $lastMonday = date('Y-m-d', strtotime('last sunday'));
            $oneWeek = date('Y-m-d', strtotime($lastMonday . ' -6 days'));
            $plusTargetSteps = -1;

            if ($string == "steps") {
                $userPushLength = $this->getAppClass()->getUserSetting($this->getActiveUser(), "push_length", '50');
                $userPushStartString = $this->getAppClass()->getUserSetting($this->getActiveUser(), "push", '12-01 last sunday'); // Default to last Sunday in March
                $userPushStartDate = date("Y-m-d", strtotime(date("Y") . '-' . $userPushStartString)); // Default to last Sunday in March
                $userPushEndDate = date("Y-m-d", strtotime($userPushStartDate . ' +' . $userPushLength . ' day')); // Default to last Sunday in March

                $today = strtotime(date("Y-m-d"));
                if ($today >= strtotime($userPushStartDate) && $today <= strtotime($userPushEndDate)) {
                    nxr("Push is running");

                    return $this->getAppClass()->getUserSetting($this->getActiveUser(), "push_steps", '10000');
                } else {
                    $improvment = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_steps", 0);
                    if ($improvment > 0) {
                        $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'steps',
                            array("AND" => array(
                                "user"     => $this->getActiveUser(),
                                "date[>=]" => $oneWeek,
                                "date[<=]" => $lastMonday
                            ), "ORDER"  => "date DESC", "LIMIT" => 7));

                        if (count($dbSteps) == 0) {
                            $plusTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_steps_max", 1000);
                        } else {
                            $totalSteps = 0;
                            foreach ($dbSteps as $dbStep) {
                                $totalSteps = $totalSteps + $dbStep;
                            }
                            if ($totalSteps == 0) $totalSteps = 1;

                            $maxTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_steps_max", 10000);
                            $minTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_steps_min", ($maxTargetSteps * 0.66));
                            $LastWeeksSteps = round($totalSteps / count($dbSteps), 0);
                            $ProposedNextWeek = $LastWeeksSteps + round($LastWeeksSteps * ($improvment / 100), 0);

                            nxr("  * Min: " . $minTargetSteps . " Max: " . $maxTargetSteps . " LastWeeksSteps: " . $LastWeeksSteps . " ProposedNextWeek: " . $ProposedNextWeek);

                            if ($ProposedNextWeek >= $maxTargetSteps) {
                                $plusTargetSteps = $maxTargetSteps;
                            } else if ($ProposedNextWeek <= $minTargetSteps) {
                                $plusTargetSteps = $minTargetSteps;
                            } else {
                                $plusTargetSteps = $ProposedNextWeek;
                            }
                        }
                    } else {
	                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_steps_max", $current_goal);
	                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_steps_min", $current_goal);
                    }
                }
            } elseif ($string == "floors") {
                $improvment = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_floors", 0);
                if ($improvment > 0) {
                    $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'floors',
                        array("AND" => array(
                            "user"     => $this->getActiveUser(),
                            "date[>=]" => $oneWeek,
                            "date[<=]" => $lastMonday
                        ), "ORDER"  => "date DESC", "LIMIT" => 7));

                    if (count($dbSteps) == 0) {
                        $plusTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_floors_max", 10);
                    } else {
                        $totalSteps = 0;
                        foreach ($dbSteps as $dbStep) {
                            $totalSteps = $totalSteps + $dbStep;
                        }
                        if ($totalSteps == 0) $totalSteps = 1;

                        $maxTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_floors_max", 10);
                        $minTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_floors_min", ($maxTargetSteps * 0.66));
                        $LastWeeksSteps = round($totalSteps / count($dbSteps), 0);
                        $ProposedNextWeek = $LastWeeksSteps + round($LastWeeksSteps * ($improvment / 100), 0);

                        nxr("  * Min: " . $minTargetSteps . " Max: " . $maxTargetSteps . " LastWeeksSteps: " . $LastWeeksSteps . " ProposedNextWeek: " . $ProposedNextWeek);

                        if ($LastWeeksSteps >= $maxTargetSteps) {
                            $plusTargetSteps = $maxTargetSteps;
                        } else if ($LastWeeksSteps <= $minTargetSteps) {
                            $plusTargetSteps = $minTargetSteps;
                        } else {
                            $plusTargetSteps = $ProposedNextWeek;
                        }
                    }
                } else {
	                $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_floors_max", $current_goal);
	                $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_floors_min", $current_goal);
                }
            } elseif ($string == "activeMinutes") {
                $userPushLength = $this->getAppClass()->getUserSetting($this->getActiveUser(), "push_length", '50');
                $userPushStartString = $this->getAppClass()->getUserSetting($this->getActiveUser(), "push", '03-31 last sunday'); // Default to last Sunday in March
                $userPushStartDate = date("Y-m-d", strtotime(date("Y") . '-' . $userPushStartString)); // Default to last Sunday in March
                $userPushEndDate = date("Y-m-d", strtotime($userPushStartDate . ' +' . $userPushLength . ' day')); // Default to last Sunday in March

                $today = strtotime(date("Y-m-d"));
                if ($today >= strtotime($userPushStartDate) && $today <= strtotime($userPushEndDate)) {
                    nxr("Push is running");

                    return $this->getAppClass()->getUserSetting($this->getActiveUser(), "push_activity", '30');
                } else {
                    $improvment = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_active", 0);
                    if ($improvment > 0) {
                        $dbActiveMinutes = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity", array('veryactive', 'fairlyactive'),
                            array("AND" => array(
                                "user"     => $this->getActiveUser(),
                                "date[>=]" => $oneWeek,
                                "date[<=]" => $lastMonday
                            ), "ORDER"  => "date DESC", "LIMIT" => 7));

                        if (count($dbActiveMinutes) == 0) {
                            $plusTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_active_max", 30);
                        } else {
                            $totalMinutes = 0;
                            foreach ($dbActiveMinutes as $dbStep) {
                                $totalMinutes = $totalMinutes + $dbStep['veryactive'] + $dbStep['fairlyactive'];
                            }
                            if ($totalMinutes == 0) $totalMinutes = 1;

                            $maxTargetActive = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_active_max", 30);
                            $minTargetActive = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_active_min", ($maxTargetActive * 0.66));
                            $LastWeeksActive = round($totalMinutes / count($dbActiveMinutes), 0);
                            $ProposedNextWeek = $LastWeeksActive + round($LastWeeksActive * ($improvment / 100), 0);

                            nxr("    * Min: " . $minTargetActive . " Max: " . $maxTargetActive . " LastWeeksSteps: " . $LastWeeksActive . " ProposedNextWeek: " . $ProposedNextWeek);

                            if ($ProposedNextWeek >= $maxTargetActive) {
                                $plusTargetSteps = $maxTargetActive;
                            } else if ($ProposedNextWeek <= $minTargetActive) {
                                $plusTargetSteps = $minTargetActive;
                            } else {
                                $plusTargetSteps = $ProposedNextWeek;
                            }
                        }
                    } else {
	                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_active_max", $current_goal);
	                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_active_min", $current_goal);
                    }
                }
            }

            return $plusTargetSteps;
        }

        /**
         * @param $tcxLink
         */
        private function pullBabelTCX($tcxLink) {
            nxr("   Downloading TCX File");
            if (!file_exists(dirname(__FILE__) . "/../tcx/" . basename($tcxLink))) {
                if (file_exists(dirname(__FILE__) . "/../tcx/") AND is_writable(dirname(__FILE__) . "/../tcx/")) {
                    file_put_contents(dirname(__FILE__) . "/../tcx/" . basename($tcxLink), $this->pullBabel($tcxLink));
                    nxr("    TCX files created: " . dirname(__FILE__) . "/../tcx/" . basename($tcxLink));
                } else {
                    nxr("    Unable to write TCX files created");
                }
            } else {
                nxr("    TCX file present");
            }
        }

        private function pullBabelHeartIntraday($activity) {
            $isAllowed = $this->isAllowed("heart");
            if (!is_numeric($isAllowed)) {
                if ($this->activeUser == $this->getAppClass()->getSetting("ownerFuid", NULL, FALSE)) {
                    $startTimeRaw = new DateTime ((String)$activity->startTime);
                    $startDate = $startTimeRaw->format("Y-m-d");
                    $startTime = $startTimeRaw->format("H:i");

                    $endTimeRaw = new DateTime ((String)$activity->startTime);
                    $endTimeRaw = $endTimeRaw->modify("+" . round($activity->activeDuration / 1000, 0) . " seconds");
                    $endTime = $endTimeRaw->format("H:i");

                    nxr("   Activity Heart Rate on " . $startDate . " for " . $startTime . " till " . $endTime);

                    $hrUrl = "https://api.fitbit.com/1/user/-/activities/heart/date/" . $startDate . "/1d/1sec/time/" . $startTime . "/" . $endTime . ".json";
                    $heartRateValues = $this->pullBabel($hrUrl);

                    if (array_key_exists("activities-heart", $heartRateValues) &&
                        count($heartRateValues['activities-heart']) > 0 &&
                        array_key_exists("heartRateZones", $heartRateValues['activities-heart'][0]) &&
                        is_array($heartRateValues['activities-heart'][0]['heartRateZones'])
                    ) {
                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity_log", array("AND" => array("user" => $this->getActiveUser(), "logId" => (String)$activity->logId)))) {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity_log",
                                array("heartRateZones" => json_encode($heartRateValues['activities-heart'][0]['heartRateZones'])),
                                array("AND" => array("user" => $this->getActiveUser(), "logId" => (String)$activity->logId)));
                            nxr("   Summary Information Added to Activity Log");
                        }
                    }

                    if (count($heartRateValues['activities-heart-intraday']['dataset']) > 0) {
                        $activitiesHeartIntraday = $heartRateValues['activities-heart-intraday']['dataset'];
                        $activitiesHeartIntraday = json_encode($activitiesHeartIntraday);

                        $dbStorage = array(
                            "user"  => $this->activeUser,
                            "logId" => $activity->logId,
                            "json"  => $activitiesHeartIntraday
                        );

                        if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "heart_activity", array("AND" => array("user" => $this->activeUser, "logId" => $activity->logId)))) {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "heart_activity", $dbStorage);
                        } else {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "heart_activity", $dbStorage, array("AND" => array("user" => $this->activeUser, "logId" => $activity->logId)));
                        }
                    }
                }
            }
        }

        // @todo - Make better
	    private function pullNomieTrackers() {
		    $isAllowed = $this->isAllowed("nomie_trackers");
		    if (!is_numeric($isAllowed)) {
			    if ($this->api_isCooled("nomie_trackers")) {

				    $nomie_user_key = $this->getAppClass()->getUserSetting($this->activeUser, "nomie_key", 'nomie');

				    nxr(" Connecting to CouchDB");

				    $path = dirname(__FILE__) . "/../library/couchdb/";

				    $nomie_username = $this->getAppClass()->getSetting("db_nomie_username", NULL, FALSE);
				    $nomie_password = $this->getAppClass()->getSetting("db_nomie_password", NULL, FALSE);
				    $nomie_protocol = $this->getAppClass()->getSetting("db_nomie_protocol", 'http', FALSE);
				    $nomie_host = $this->getAppClass()->getSetting("db_nomie_host", 'localhost', FALSE);
				    $nomie_port = $this->getAppClass()->getSetting("db_nomie_port", '5984', FALSE);

				    require_once $path . 'couch.php';
				    require_once $path . 'couchClient.php';
				    require_once $path . 'couchDocument.php';

				    $couchClient = new couchClient ($nomie_protocol.'://'.$nomie_username.':'.$nomie_password.'@'.$nomie_host.':'.$nomie_port,$nomie_user_key . '_meta');
				    if ( !$couchClient->databaseExists() ) {
					    nxr("  Nomie Meta table missing");
					    return array("error" => "true", "code" => 105, "msg" => "Nomie is not setup correctly");
				    }

				    $trackerGroups = json_decode(json_encode($couchClient->getDoc('groups')->obj), TRUE);
				    if (array_key_exists("NxFITNESS", $trackerGroups)) {
					    nxr("  Downloadnig NxFITNESS Group Trackers");
					    $trackerGroups = $trackerGroups['NxFITNESS'];
				    } else {
					    nxr("  Downloading All Trackers");
					    $trackerGroups = $trackerGroups['All'];
				    }

				    $couchClient->useDatabase($nomie_user_key . '_trackers');
				    if ( !$couchClient->databaseExists() ) {
					    nxr("  Nomie Tracker table missing");
					    return array("error" => "true", "code" => 105, "msg" => "Nomie is not setup correctly");
				    }

				    $trackedTrackers = array();
				    $db_prefix = $this->getAppClass()->getSetting("db_prefix", NULL, FALSE);
				    foreach ($trackerGroups as $tracker) {
					    $doc = $couchClient->getDoc($tracker);

					    nxr("  Storing " . $doc->label);

					    $dbStorage = array(
						    "fuid" => $this->activeUser,
						    "id" => $tracker,
						    "label" => $doc->label,
						    "icon" => trim(str_ireplace("  ", " ", $doc->icon)),
						    "color" => $doc->color,
						    "charge" => $doc->charge
					    );

					    array_push($trackedTrackers, $tracker);

					    if (!$this->getAppClass()->getDatabase()->has($db_prefix . "nomie_trackers", array("AND" => array("fuid" => $this->activeUser, "id" => $tracker)))) {
						    $this->getAppClass()->getDatabase()->insert($db_prefix . "nomie_trackers", $dbStorage);
					    } else {
						    $this->getAppClass()->getDatabase()->update($db_prefix . "nomie_trackers", $dbStorage, array("AND" => array("fuid" => $this->activeUser, "id" => $tracker)));
					    }
				    }

				    $couchClient->useDatabase($nomie_user_key . '_events');
				    if ( !$couchClient->databaseExists() ) {
					    nxr("  Nomie Tracker table missing");
					    return array("error" => "true", "code" => 105, "msg" => "Nomie is not setup correctly");
				    }
				    $trackerEvents = json_decode(json_encode($couchClient->getAllDocs()), TRUE);
				    foreach ($trackerEvents['rows'] as $events) {
					    $event = explode("|", $events['id']);
					    if (in_array($event[2], $trackedTrackers)) {
						    $event[3] = date('Y-m-d H:i:s', $event[3] / 1000);
						    if (!$this->getAppClass()->getDatabase()->has($db_prefix . "nomie_events", array("AND" => array("fuid" => $this->activeUser, "id" => $event[2], "datestamp" => $event[3])))) {

							    $document = json_decode(json_encode($couchClient->getDoc($events['id'])), TRUE);

							    $dbStorage = array(
								    "fuid"      => $this->activeUser,
								    "id"        => $event[2],
								    "type"      => $event[0],
								    "datestamp" => $event[3],
								    "value"     => $document['value'],
								    "score"     => $event[4],
							    );

							    if (is_array($document['geo']) and count($document['geo']) == 2) {
								    $dbStorage["geo_lat"] = $document['geo'][0];
								    $dbStorage["geo_lon"] = $document['geo'][1];
							    }

							    $this->getAppClass()->getDatabase()->insert( $db_prefix . "nomie_events", $dbStorage );
							    nxr( "   Stored event : " . $event[2] . " from " . $event[3]);
							    //nxr( print_r($this->getAppClass()->getDatabase()->log(), true) );
							    //return true;
						    }
					    }
				    }

				    $this->api_setLastrun("nomie_trackers", NULL, TRUE);
			    } else {
				    return "-143";
			    }
		    } else {
			    return $isAllowed;
		    }
	    }

	    /**
         * @param mixed $userAccessToken
         */
        public function setUserAccessToken($userAccessToken) {
            $this->userAccessToken = $userAccessToken;
        }

        /**
         * @param djchen\OAuth2\Client\Provider\Fitbit $fitbitapi
         */
        public function setLibrary($fitbitapi) {
            $this->fitbitapi = $fitbitapi;
        }

        /**
         * @param $user
         * @param $string
         *
         * @return bool|int
         */
        public function getDBCurrentBody($user, $string) {
            if (!$user) return "No default user selected";

            $return = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "body", $string, array("user" => $user, "ORDER" => "date DESC"));

            if (!is_numeric($return)) {
                return 0;
            } else {
                return $return;
            }
        }

        /**
         * @deprecated Use getLibrary() instead
         * @return djchen\OAuth2\Client\Provider\Fitbit
         */
        public function getFitbitLibrary() {
            return $this->getLibrary();
        }

        /**
         * @deprecated Use setLibrary() instead
         *
         * @param djchen\OAuth2\Client\Provider\Fitbit $fitbitapi
         */
        public function setFitbitapi($fitbitapi) {
            $this->setLibrary($fitbitapi);
        }

        /**
         * @return djchen\OAuth2\Client\Provider\Fitbit
         */
        public function getLibrary() {
            return $this->fitbitapi;
        }

        /**
         * @param      $user
         * @param      $trigger
         * @param bool $return
         *
         * @return mixed|null|SimpleXMLElement|string
         */
        public function pull($user, $trigger, $return = FALSE) {
            $this->setActiveUser($user);
            $xml = NULL;

            // Check we have a valid user
            if ($this->getAppClass()->isUser($user)) {
                $userCoolDownTime = $this->getAppClass()->getUserCooldown($this->activeUser);
                if (strtotime($userCoolDownTime) >= date("U")) {
                    nxr("User Cooldown in place. Cooldown will be lift at " . $userCoolDownTime . " please try again after that.");
                    die();
                }

	            // PULL - users profile
	            if (($trigger == "all" || $trigger == "nomie_trackers") && (!is_array($_GET) || !array_key_exists("dev", $_GET)) ) {
		            $pull = $this->pullNomieTrackers();
		            if ($this->isApiError($pull) && !IS_CRON_RUN) {
			            nxr("  Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
		            }
	            }

                // Check this user has valid access to the Fitbit AIP
                if ($this->getAppClass()->valdidateOAuth($this->getAppClass()->getUserOAuthTokens($user, FALSE))) {

                    // If we've asked for a complete update then don't abide by cooldown times
                    if ($trigger == "all") {
                        $this->forceSync = TRUE;
                    }

	                // PULL - users profile
	                if ($trigger == "all" || $trigger == "profile") {
		                $pull = $this->pullBabelProfile();
		                if ($this->isApiError($pull) && !IS_CRON_RUN) {
			                nxr("  Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
		                }
	                }

                    // PULL - Devices
                    if ($trigger == "all" || $trigger == "devices") {
                        $pull = $this->pullBabelDevices();
                        if ($this->isApiError($pull) && !IS_CRON_RUN) {
                            nxr("  Error devices: " . $this->getAppClass()->lookupErrorCode($pull));
                        }
                    }

                    // PULL - Badges
                    if ($trigger == "all" || $trigger == "badges") {
                        $pull = $this->pullBabelBadges();
                        if ($this->isApiError($pull) && !IS_CRON_RUN) {
                            nxr("  Error badges: " . $this->getAppClass()->lookupErrorCode($pull));
                        }
                    }

                    if ($trigger == "all" || $trigger == "leaderboard") {
                        $pull = $this->pullBabelLeaderboard();
                        if ($this->isApiError($pull) && !IS_CRON_RUN) {
                            nxr("  Error leaderboard: " . $this->getAppClass()->lookupErrorCode($pull));
                        }
                    }

                    if ($trigger == "all" || $trigger == "foods" || $trigger == "goals_calories") {
                        $pull = $this->pullBabelCaloriesGoals();
                        if ($this->isApiError($pull) && !IS_CRON_RUN) {
                            nxr("  Error goals_calories: " . $this->getAppClass()->lookupErrorCode($pull));
                        }
                    }

                    if ($trigger == "all" || $trigger == "activity_log") {
                        $pull = $this->pullBabelActivityLogs();
                        if ($this->isApiError($pull) && !IS_CRON_RUN) {
                            nxr("  Error activity_log: " . $this->getAppClass()->lookupErrorCode($pull));
                        }
                    }

                    if ($trigger == "all" || $trigger == "goals") {
                        nxr(' Downloading Goals');
                        $pull = $this->pullBabelUserGoals();
                        if ($this->isApiError($pull) && !IS_CRON_RUN) {
                            nxr("  Error goals: " . $this->getAppClass()->lookupErrorCode($pull));
                        }
                    }

                    /*if ($trigger == "all" || $trigger == "heart") {
                        $lastCleanRun = $this->api_getLastCleanrun("heart");
                        nxr(' Downloading Heart Rate Series Logs fron ' . $lastCleanRun->format("l jS M Y"));
                        $pull = $this->pullBabelHeartRateSeries($lastCleanRun->format("Y-m-d"));
                        if ($this->isApiError($pull) && !IS_CRON_RUN) {
                            nxr("  Error heart: " . $this->getAppClass()->lookupErrorCode($pull));
                        }
                    }*/

                    // Set variables require bellow
                    $currentDate = new DateTime ('now');
                    $interval = DateInterval::createFromDateString('1 day');

                    if ($trigger == "all" || $trigger == "water" || $trigger == "foods") {
                        // Check we're allowed to pull these records here rather than at each loop
                        $isAllowed = $this->isAllowed("water");
                        if (!is_numeric($isAllowed)) {
                            if ($this->api_isCooled("water")) {
                                $period = new DatePeriod ($this->api_getLastCleanrun("water"), $interval, $currentDate);
                                /**
                                 * @var DateTime $dt
                                 */
                                foreach ($period as $dt) {
                                    nxr(' Downloading Water Logs for ' . $dt->format("l jS M Y"));
                                    $pull = $this->pullBabelWater($dt->format("Y-m-d"));
                                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                                        nxr("  Error water: " . $this->getAppClass()->lookupErrorCode($pull));
                                    }
                                }
                            } else {
                                if (!IS_CRON_RUN) nxr("  Error water: " . $this->getAppClass()->lookupErrorCode(-143));
                            }
                        }
                    }

                    if ($trigger == "all" || $trigger == "sleep") {
                        $isAllowed = $this->isAllowed("sleep");
                        if (!is_numeric($isAllowed)) {
                            if ($this->api_isCooled("sleep")) {
                                $period = new DatePeriod ($this->api_getLastCleanrun("sleep"), $interval, $currentDate);
                                /**
                                 * @var DateTime $dt
                                 */
                                foreach ($period as $dt) {
                                    nxr(' Downloading Sleep Logs for ' . $dt->format("l jS M Y"));
                                    $pull = $this->pullBabelSleep($dt->format("Y-m-d"));
                                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                                        nxr("  Error sleep: " . $this->getAppClass()->lookupErrorCode($pull));
                                    }
                                }
                            } else {
                                if (!IS_CRON_RUN) nxr("  Error sleep: " . $this->getAppClass()->lookupErrorCode(-143));
                            }
                        }
                    }

                    if ($trigger == "all" || $trigger == "body") {
                        $isAllowed = $this->isAllowed("body");
                        if (!is_numeric($isAllowed)) {
                            if ($this->api_isCooled("body")) {
                                $period = new DatePeriod ($this->api_getLastCleanrun("body"), $interval, $currentDate);
                                /**
                                 * @var DateTime $dt
                                 */
                                foreach ($period as $dt) {
                                    nxr(' Downloading Body Logs for ' . $dt->format("l jS M Y"));
                                    $pull = $this->pullBabelBody($dt->format("Y-m-d"));
                                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                                        nxr("  Error body: " . $this->getAppClass()->lookupErrorCode($pull));
                                    }
                                }
                            } else {
                                if (!IS_CRON_RUN) nxr("  Error body: " . $this->getAppClass()->lookupErrorCode(-143));
                            }
                        }
                    }

                    if ($trigger == "all" || $trigger == "foods") {
                        $isAllowed = $this->isAllowed("foods");
                        if (!is_numeric($isAllowed)) {
                            if ($this->api_isCooled("foods")) {
                                $period = new DatePeriod ($this->api_getLastCleanrun("foods"), $interval, $currentDate);
                                /**
                                 * @var DateTime $dt
                                 */
                                foreach ($period as $dt) {
                                    nxr(' Downloading Foods Logs for ' . $dt->format("l jS M Y"));
                                    $pull = $this->pullBabelMeals($dt->format("Y-m-d"));
                                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                                        nxr("  Error foods: " . $this->getAppClass()->lookupErrorCode($pull));
                                    }
                                }
                            } else {
                                if (!IS_CRON_RUN) nxr("  Error foods: " . $this->getAppClass()->lookupErrorCode(-143));
                            }
                        }
                    }

                    $timeSeries = Array(
                        "steps"                => "300",
                        "distance"             => "300",
                        "floors"               => "300",
                        "elevation"            => "300",
                        "minutesSedentary"     => "1800",
                        "minutesLightlyActive" => "1800",
                        "minutesFairlyActive"  => "1800",
                        "minutesVeryActive"    => "1800",
                        "caloriesOut"          => "1800");
                    if ($trigger == "all" || $trigger == "activities") {
                        $isAllowed = $this->isAllowed("activities");
                        if (!is_numeric($isAllowed)) {
                            if ($this->api_isCooled("activities")) {
                                nxr(" Downloading Series Info");
                                foreach ($timeSeries as $activity => $timeout) {
                                    $this->pullBabelTimeSeries($activity, TRUE);
                                }
                                if (isset($this->holdingVar)) unset($this->holdingVar);
                                $this->api_setLastrun("activities", NULL, TRUE);
                            }
                        }
                    } else if (array_key_exists($trigger, $timeSeries)) {
                        $isAllowed = $this->isAllowed($trigger);
                        if (!is_numeric($isAllowed)) {
                            $this->pullBabelTimeSeries($trigger);
                        }
                    }

                    if ($trigger == "all") {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array(
                            "lastrun" => $currentDate->format("Y-m-d H:i:s")
                        ), array("fuid" => $this->getActiveUser()));
                    }

                } else {
                    nxr("User has not yet authenticated with Fitbit");
                }

            }

            if ($return) {
                return $xml;
            } else {
                return TRUE;
            }
        }

        /**
         * Launch TimeSeries requests
         * Allowed types are:
         *            'caloriesIn', 'water'
         *            'caloriesOut', 'steps', 'distance', 'floors', 'elevation'
         *            'minutesSedentary', 'minutesLightlyActive', 'minutesFairlyActive', 'minutesVeryActive',
         *            'activityCalories',
         *            'tracker_caloriesOut', 'tracker_steps', 'tracker_distance', 'tracker_floors', 'tracker_elevation'
         *            'startTime', 'timeInBed', 'minutesAsleep', 'minutesAwake', 'awakeningsCount',
         *            'minutesToFallAsleep', 'minutesAfterWakeup',
         *            'efficiency'
         *            'weight', 'bmi', 'fat'
         *
         * @param string $type
         * @param        $baseDate  DateTime or 'today', to_period
         * @param        $to_period DateTime or '1d, 7d, 30d, 1w, 1m, 3m, 6m, 1y, max'
         *
         * @return array
         */
        public function getTimeSeries($type, $baseDate, $to_period) {
            switch ($type) {
                case 'caloriesIn':
                    $path = '/foods/log/caloriesIn';
                    break;
                case 'water':
                    $path = '/foods/log/water';
                    break;

                case 'caloriesOut':
                    $path = '/activities/log/calories';
                    break;
                case 'steps':
                    $path = '/activities/log/steps';
                    break;
                case 'distance':
                    $path = '/activities/log/distance';
                    break;
                case 'floors':
                    $path = '/activities/log/floors';
                    break;
                case 'elevation':
                    $path = '/activities/log/elevation';
                    break;
                case 'minutesSedentary':
                    $path = '/activities/log/minutesSedentary';
                    break;
                case 'minutesLightlyActive':
                    $path = '/activities/log/minutesLightlyActive';
                    break;
                case 'minutesFairlyActive':
                    $path = '/activities/log/minutesFairlyActive';
                    break;
                case 'minutesVeryActive':
                    $path = '/activities/log/minutesVeryActive';
                    break;
                case 'activityCalories':
                    $path = '/activities/log/activityCalories';
                    break;

                case 'tracker_caloriesOut':
                    $path = '/activities/log/tracker/calories';
                    break;
                case 'tracker_steps':
                    $path = '/activities/log/tracker/steps';
                    break;
                case 'tracker_distance':
                    $path = '/activities/log/tracker/distance';
                    break;
                case 'tracker_floors':
                    $path = '/activities/log/tracker/floors';
                    break;
                case 'tracker_elevation':
                    $path = '/activities/log/tracker/elevation';
                    break;

                case 'startTime':
                    $path = '/sleep/startTime';
                    break;
                case 'timeInBed':
                    $path = '/sleep/timeInBed';
                    break;
                case 'minutesAsleep':
                    $path = '/sleep/minutesAsleep';
                    break;
                case 'awakeningsCount':
                    $path = '/sleep/awakeningsCount';
                    break;
                case 'minutesAwake':
                    $path = '/sleep/minutesAwake';
                    break;
                case 'minutesToFallAsleep':
                    $path = '/sleep/minutesToFallAsleep';
                    break;
                case 'minutesAfterWakeup':
                    $path = '/sleep/minutesAfterWakeup';
                    break;
                case 'efficiency':
                    $path = '/sleep/efficiency';
                    break;


                case 'weight':
                    $path = '/body/weight';
                    break;
                case 'bmi':
                    $path = '/body/bmi';
                    break;
                case 'fat':
                    $path = '/body/fat';
                    break;

                default:
                    return FALSE;
            }

            $response = $this->pullBabel('user/' . $this->getActiveUser() . $path . '/date/' . (is_string($baseDate) ? $baseDate : $baseDate->format('Y-m-d')) . "/" . (is_string($to_period) ? $to_period : $to_period->format('Y-m-d')) . '.json', TRUE);

            switch ($type) {
                case 'caloriesOut':
                    $objectKey = "activities-log-calories";
                    break;
                default:
                    $objectKey = "activities-log-" . $type;
                    break;
            }

            $response = $response->$objectKey;

            return $response;
        }

        /**
         * @param $xml
         *
         * @return bool
         */
        public function isApiError($xml) {
            if (is_numeric($xml) AND $xml < 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        /**
         * @param      $trigger
         * @param bool $quiet
         *
         * @return bool|string
         */
        public function isAllowed($trigger, $quiet = FALSE) {
            if ($trigger == "profile") return TRUE;

            $usrConfig = $this->getAppClass()->getUserSetting($this->getActiveUser(), 'scope_' . $trigger, TRUE);
            if (!is_null($usrConfig) AND $usrConfig != 1) {
                if (!$quiet) nxr(" Aborted $trigger disabled in user config");

                return "-145";
            }

            $sysConfig = $this->getAppClass()->getSetting('scope_' . $trigger, TRUE);
            if ($sysConfig != 1) {
                if (!$quiet) nxr(" Aborted $trigger disabled in system config");

                return "-146";
            }

            return TRUE;
        }

        /**
         * @param      $trigger
         * @param bool $reset
         *
         * @return bool
         */
        public function api_isCooled($trigger, $reset = FALSE) {
            if ($this->forceSync) {
                return TRUE;
            } else {
                $currentDate = new DateTime ('now');
                $coolDownTill = $this->api_getCoolDown($trigger, $reset);

                //            nxr("coolDownTill " . $coolDownTill->format("Y-m-d H:i:s"));
                //            nxr("currentDate " . $currentDate->format("Y-m-d H:i:s"));

                if ($coolDownTill->format("U") < $currentDate->format("U")) {
                    //                nxr(" Is cool");
                    return TRUE;
                } else {
                    //                nxr(" Stil hot");
                    return FALSE;
                }
            }
        }

        /**
         * @return bool
         * @internal param bool $forceSync
         */
        public function getForceSync() {
            return $this->forceSync;
        }

        /**
         * @param boolean $forceSync
         */
        public function setForceSync($forceSync) {
            $this->forceSync = $forceSync;
        }

        /**
         * @param $_nx_fb_usr
         *
         * @return bool
         */
        public function validateOAuth($_nx_fb_usr) {
            $this->setActiveUser($_nx_fb_usr);

            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $this->getAccessToken();

                $request = $this->getLibrary()->getResourceOwner($accessToken);
                if ($request->getId() == $_nx_fb_usr) {
                    return TRUE;
                } else {
                    nxr(" User miss match " . $request->getId() . " should equal " . $_nx_fb_usr);

                    return FALSE;
                }

            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                // Failed to get the access token or user details.
                nxr(" User validation test failed: " . print_r($e->getMessage(), TRUE));
                if ($e->getCode() == 400) {
                    $this->getAppClass()->delUserOAuthTokens($_nx_fb_usr);
                }

                return FALSE;
            }
        }

        /**
         * @param mixed $activeUser
         */
        public function setActiveUser($activeUser) {
            $this->activeUser = $activeUser;
        }

        /**
         * @param $newUserProfile
         *
         * @return bool
         */
        public function createNewUser($newUserProfile) {
            /*

                    'api' => $newUserProfile->encodedId,
                    'name' => $newUserProfile->fullName,
                    'dob' => $newUserProfile->dateOfBirth,
                    'avatar' => $newUserProfile->avatar150,
                    'seen' => $newUserProfile->memberSince,
                    'gender' => $newUserProfile->gender,
                    'height' => $newUserProfile->height,
                    'stride_running' => $newUserProfile->strideLengthRunning,
                    'stride_walking' => $newUserProfile->strideLengthWalking,
                    'country' => $newUserProfile->country,
             */
            //nxr(print_r($newUserProfile, TRUE));
            if ($this->getAppClass()->isUser($newUserProfile->encodedId)) {
                nxr("User already present");

                return FALSE;
            } else {

                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array(
                    'fuid'           => $newUserProfile->encodedId,
                    'group'          => 'user',
                    'api'            => $newUserProfile->encodedId,
                    'name'           => $newUserProfile->fullName,
                    'dob'            => $newUserProfile->dateOfBirth,
                    'avatar'         => $newUserProfile->avatar150,
                    'seen'           => $newUserProfile->memberSince,
                    'lastrun'        => $newUserProfile->memberSince,
                    'gender'         => $newUserProfile->gender,
                    'height'         => $newUserProfile->height,
                    'stride_running' => $newUserProfile->strideLengthRunning,
                    'stride_walking' => $newUserProfile->strideLengthWalking,
                    'country'        => $newUserProfile->country,
                    'tkn_access'     => $this->getAccessToken()->getToken(),
                    'tkn_refresh'    => $this->getAccessToken()->getRefreshToken(),
                    'tkn_expires'    => $this->getAccessToken()->getExpires(),
                    'rank'           => 0,
                    'friends'        => 0,
                    'distance'       => 0,
                ));

                return TRUE;
            }
        }

        /**
         * @param      $path
         * @param bool $returnObject
         * @param bool $debugOutput
         * @param bool $supportFailures
         *
         * @return mixed
         */
        public function pullBabel($path, $returnObject = FALSE, $debugOutput = FALSE, $supportFailures = FALSE) {
            $getRateRemaining = $this->getLibrary()->getRateRemaining();
            if (is_numeric($getRateRemaining) && $getRateRemaining <= 2) {
                $restMinutes = round($this->getLibrary()->getRateReset() / 60, 0);
                nxr(" *** Rate limit reached. Please try again in about " . $restMinutes . " minutes ***");

                $currentDate = new DateTime();
                $currentDate = $currentDate->modify("+" . ($restMinutes + 5) . " minutes");
                $this->getAppClass()->setUserCooldown($this->activeUser, $currentDate);

                die();
            } else if (is_numeric($getRateRemaining) && $getRateRemaining < 50) {
                nxr(" *** Down to your last " . $getRateRemaining . " calls ***");
            }

            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $this->getAccessToken();

                $path = str_replace(FITBIT_COM . "/1/", "", $path);

                $request = $this->getLibrary()->getAuthenticatedRequest('GET', FITBIT_COM . "/1/" . $path, $accessToken);
                // Make the authenticated API request and get the response.
                $response = $this->getLibrary()->getResponse($request);

                if ($returnObject) {
                    $response = json_decode(json_encode($response), FALSE);
                }

                if ($debugOutput) nxr(print_r($response, TRUE));

                return $response;
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                // Failed to get the access token or user details.

                if ($e->getCode() == 429) {
                    nxr(" Rate limit reached. Please try again later");

                    $currentDate = new DateTime();
                    $currentDate = $currentDate->modify("+1 hours");
                    $this->getAppClass()->setUserCooldown($this->activeUser, $currentDate->format("Y-m-d H:05:00"));

                    die();
                } else {
                    nxr("Error " . $e->getCode() . ": " . $e->getMessage());
                    nxr($e->getFile() . " @" . $e->getLine());
                    nxr($e->getTraceAsString());
                    if ($supportFailures) {
                        return $e->getCode();
                    } else {
                        die();
                    }
                }
            }
        }

    }
