<?php

define("FITBIT_COM", "https://api.fitbit.com");

/**
 * Fitbit Helper class
 * @version 0.0.1
 * @author Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link http://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2015 Stuart McCulloch Anderson
 * @license http://stuart.nx15.at/mit/2015 MIT
 */
class fitbit
{
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
     */
    public function __construct($fitbitApp)
    {
        $this->setAppClass($fitbitApp);

        $this->setLibrary(new djchen\OAuth2\Client\Provider\Fitbit([
            'clientId' => $fitbitApp->getSetting("fitbit_clientId", NULL, FALSE),
            'clientSecret' => $fitbitApp->getSetting("fitbit_clientSecret", NULL, FALSE),
            'redirectUri' => $fitbitApp->getSetting("fitbit_redirectUri", NULL, FALSE)
        ]));

        $this->forceSync = FALSE;
    }

    /**
     * @param djchen\OAuth2\Client\Provider\Fitbit $fitbitapi
     */
    public function setLibrary($fitbitapi)
    {
        $this->fitbitapi = $fitbitapi;
    }

    /**
     * @param $user
     * @param $string
     * @return bool|int
     */
    public function getDBCurrentBody($user, $string)
    {
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
     * @return FitBitPHP
     */
    public function getFitbitapi()
    {
        return $this->getLibrary();
    }

    /**
     * @deprecated Use setLibrary() instead
     * @param FitBitPHP $fitbitapi
     */
    public function setFitbitapi($fitbitapi)
    {
        $this->setLibrary($fitbitapi);
    }

    /**
     * @return djchen\OAuth2\Client\Provider\Fitbit
     */
    public function getLibrary()
    {
        return $this->fitbitapi;
    }

    /**
     * @param $user
     * @param $trigger
     * @param bool $return
     * @return mixed|null|SimpleXMLElement|string
     */
    public function pull($user, $trigger, $return = FALSE)
    {
        $this->setActiveUser($user);
        $xml = NULL;

        // Check we have a valid user
        if ($this->getAppClass()->isUser($user)) {

            // Check this user has valid access to the Fitbit AIP
            if ($this->getAppClass()->valdidateOAuth($this->getAppClass()->getUserOAuthTokens($user, FALSE))) {

                // If we've asked for a complete update then don't abide by cooldown times
                if ($trigger == "all") {
                    $this->forceSync = TRUE;
                }

                // PULL - users profile
                if ($trigger == "all" || $trigger == "profile") {
                    $pull = $this->pullBabelProfile();
                    if ($this->isApiError($pull)) {
                        nxr("  Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                // PULL - Devices
                if ($trigger == "all" || $trigger == "devices") {
                    $pull = $this->pullBabelDevices();
                    if ($this->isApiError($pull)) {
                        nxr("  Error devices: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                // PULL - Badges
                if ($trigger == "all" || $trigger == "badges") {
                    $pull = $this->pullBabelBadges();
                    if ($this->isApiError($pull)) {
                        nxr("  Error badges: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                if ($trigger == "all" || $trigger == "leaderboard") {
                    $pull = $this->pullBabelLeaderboard();
                    if ($this->isApiError($pull)) {
                        nxr("  Error leaderboard: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                if ($trigger == "all" || $trigger == "foods" || $trigger == "goals_calories") {
                    $pull = $this->pullBabelCaloriesGoals();
                    if ($this->isApiError($pull)) {
                        nxr("  Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

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
                                if ($this->isApiError($pull)) {
                                    nxr("  Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
                                }
                            }
                        } else {
                            nxr("  Error water: " . $this->getAppClass()->lookupErrorCode(-143));
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
                                if ($this->isApiError($pull)) {
                                    nxr("  Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
                                }
                            }
                        } else {
                            nxr("  Error sleep: " . $this->getAppClass()->lookupErrorCode(-143));
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
                                if ($this->isApiError($pull)) {
                                    nxr("  Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
                                }
                            }
                        } else {
                            nxr("  Error body: " . $this->getAppClass()->lookupErrorCode(-143));
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
                                if ($this->isApiError($pull)) {
                                    nxr("  Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
                                }
                            }
                        } else {
                            nxr("  Error foods: " . $this->getAppClass()->lookupErrorCode(-143));
                        }
                    }
                }

                // TODO: GitLab #19 - There is not need for this to be looped here, data is only returned for current day not past days
                if ($trigger == "all" || $trigger == "goals") {
                    $isAllowed = $this->isAllowed("goals");
                    if (!is_numeric($isAllowed)) {
                        if ($this->api_isCooled("goals")) {
                            $period = new DatePeriod ($this->api_getLastCleanrun("goals"), $interval, $currentDate);
                            /**
                             * @var DateTime $dt
                             */
                            foreach ($period as $dt) {
                                nxr(' Downloading Goals Logs for ' . $dt->format("l jS M Y"));
                                $pull = $this->pullBabelUserGoals($dt->format("Y-m-d"));
                                if ($this->isApiError($pull)) {
                                    nxr("  Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
                                }
                            }
                        } else {
                            nxr("  Error Goals: " . $this->getAppClass()->lookupErrorCode(-143));
                        }
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
     * @param $targetDate
     * @return mixed
     */
    private function pullBabelUserGoals($targetDate) {
        $userGoals = $this->pullBabel('user/-/activities/goals/daily.json', TRUE);

        if (isset($userGoals)) {
            $currentDate = new DateTime();
            $usr_goals = $userGoals->goals;
            if (is_object($usr_goals)) {
                $fallback = FALSE;

                if ($usr_goals->caloriesOut == "" OR $usr_goals->distance == "" OR $usr_goals->floors == "" OR $usr_goals->activeMinutes == "" OR $usr_goals->steps == "") {
                    $this->getAppClass()->addCronJob($this->getActiveUser(), "goals");

                    if ($usr_goals->caloriesOut == "") 
                        $usr_goals->caloriesOut = -1;
                    
                    if ($usr_goals->distance == "") 
                        $usr_goals->distance = -1;
                    
                    if ($usr_goals->floors == "") 
                        $usr_goals->floors = -1;
                    
                    if ($usr_goals->activeMinutes == "") 
                        $usr_goals->activeMinutes = -1;
                    
                    if ($usr_goals->steps == "") 
                        $usr_goals->steps = -1;

                    $fallback = TRUE;
                }

                if ($currentDate->format("Y-m-d") == $targetDate) {
                    if ($usr_goals->steps > 1) {
                        $newGoal = $this->thisWeeksGoal("steps");
                        if ($newGoal > 0 && $usr_goals->steps != $newGoal) {
                            nxr("  Returned steps target was " . $usr_goals->steps . " but I think it should be " . $newGoal);
                            $this->pushBabel('user/-/activities/goals/daily.json', array('steps' => $newGoal));
                        } elseif ($newGoal > 0) {
                            nxr("  Returned steps target was " . $usr_goals->steps . " which is right for this week goal of " . $newGoal);
                        }
                    }

                    if ($usr_goals->floors > 1) {
                        $newGoal = $this->thisWeeksGoal("floors");
                        if ($newGoal > 0 && $usr_goals->floors != $newGoal) {
                            nxr("  Returned floor target was " . $usr_goals->floors . " but I think it should be " . $newGoal);
                            $this->pushBabel('user/-/activities/goals/daily.json', array('floors' => $newGoal));
                        } elseif ($newGoal > 0) {
                            nxr("  Returned floor target was " . $usr_goals->floors . " which is right for this week goal of " . $newGoal);
                        }
                    }
                }

                if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array("AND" => array('user' => $this->getActiveUser(), 'date' => $targetDate)))) {
                    
                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array(
                        'caloriesOut'   => (String)$usr_goals->caloriesOut,
                        'distance'      => (String)$usr_goals->distance,
                        'floors'        => (String)$usr_goals->floors,
                        'activeMinutes' => (String)$usr_goals->activeMinutes,
                        'steps'         => (String)$usr_goals->steps,
                        'syncd'         => date("Y-m-d H:i:s")
                    ), array("AND" => array('user' => $this->getActiveUser(), 'date' => $targetDate)));
                } else {
                    
                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps_goals", array(
                        'user'          => $this->getActiveUser(),
                        'date'          => $targetDate,
                        'caloriesOut'   => (String)$usr_goals->caloriesOut,
                        'distance'      => (String)$usr_goals->distance,
                        'floors'        => (String)$usr_goals->floors,
                        'activeMinutes' => (String)$usr_goals->activeMinutes,
                        'steps'         => (String)$usr_goals->steps,
                        'syncd'         => date("Y-m-d H:i:s")
                    ));
                }

                if (!$fallback) $this->api_setLastCleanrun("goals", new DateTime($targetDate));
            }

            if ($currentDate->format("Y-m-d") == $targetDate)
                $this->api_setLastrun("goals");
        }

        return $userGoals;
    }

    /**
     * @param $targetDate
     * @return mixed
     */
    private function pullBabelMeals($targetDate) {
        $targetDateTime = new DateTime ($targetDate);
        $userFoodLog = $this->pullBabel('user/' . $this->getActiveUser() . '/foods/log/date/'.$targetDateTime->format('Y-m-d').'.json', TRUE);

        if (isset($userFoodLog)) {
            if (count($userFoodLog->foods) > 0) {
                foreach ($userFoodLog->foods as $meal) {
                    nxr("  Logging meal " . $meal->loggedFood->name);

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood", array("AND" => array('user' => $this->getActiveUser(), 'date' => $targetDate, 'meal' => (String)$meal->loggedFood->name)))) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood", array(
                            'calories' => (String)$meal->nutritionalValues->calories,
                            'carbs'    => (String)$meal->nutritionalValues->carbs,
                            'fat'      => (String)$meal->nutritionalValues->fat,
                            'fiber'    => (String)$meal->nutritionalValues->fiber,
                            'protein'  => (String)$meal->nutritionalValues->protein,
                            'sodium'   => (String)$meal->nutritionalValues->sodium
                        ), array("AND" => array('user' => $this->getActiveUser(), 'date' => $targetDate, 'meal' => (String)$meal->loggedFood->name)));
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logFood", array(
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
            }
        }

        return $userFoodLog;
    }

    /**
     * @param $targetDate
     * @return mixed
     */
    private function pullBabelBody($targetDate) {
        $targetDateTime = new DateTime ($targetDate);
        $userBodyLog = $this->pullBabel('user/' . $this->getActiveUser() . '/body/date/'.$targetDateTime->format('Y-m-d').'.json', TRUE);

        if (isset($userBodyLog)) {
            $fallback = FALSE;
            $currentDate = new DateTime ();
            if ($currentDate->format("Y-m-d") == $targetDate and ($userBodyLog->body->weight == "0" OR $userBodyLog->body->fat == "0" OR
                    $userBodyLog->body->bmi == "0" OR (isset($userBodyLog->goals) AND ($userBodyLog->goals->weight == "0" OR $userBodyLog->goals->fat == "0")))
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
            }
        }

        return $userBodyLog;

    }

    /**
     * @param $targetDate
     * @return mixed|null|SimpleXMLElement|string
     */
    private function pullBabelSleep($targetDate) {
        $targetDateTime = new DateTime ($targetDate);
        $userSleepLog = $this->pullBabel('user/' . $this->getActiveUser() . '/sleep/date/'.$targetDateTime->format('Y-m-d').'.json', TRUE);

        if (isset($userSleepLog) and is_object($userSleepLog) and is_array($userSleepLog->sleep) and count($userSleepLog->sleep) > 0) {
            $loggedSleep = $userSleepLog->sleep[0];
            if ($loggedSleep->logId != 0) {
                if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logSleep", array("logId" => (String)$loggedSleep->logId))) {
                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "logSleep", array(
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

                if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_sleep2usr", array("AND" => array('user' => $this->getActiveUser(), 'sleeplog' => (String)$loggedSleep->logId)))) {
                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_sleep2usr", array(
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
     * @return mixed
     */
    private function pullBabelWater($targetDate) {
        $targetDateTime = new DateTime ($targetDate);
        $userWaterLog = $this->pullBabel('user/-/foods/log/water/date/'.$targetDateTime->format('Y-m-d').'.json', TRUE);

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
     * @param $xml
     * @return bool
     */
    public function isApiError($xml)
    {
        if (is_numeric($xml) AND $xml < 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @param $user
     * @param $trigger
     * @return bool|string
     */
    public function isAllowed($trigger)
    {
        $usrConfig = $this->getAppClass()->getSetting('nx_fitbit_ds_' . $this->getActiveUser() . '_' . $trigger, NULL);
        if (!is_null($usrConfig) AND $usrConfig != 1) {
            nxr(" Aborted $trigger disabled in user config");

            return "-145";
        }

        $sysConfig = $this->getAppClass()->getSetting('nx_fitbit_ds_' . $trigger, 0);
        if ($sysConfig != 1) {
            nxr(" Aborted $trigger disabled in system config");

            return "-146";
        }

        return TRUE;
    }

    /**
     * @param $trigger
     * @param $user
     * @param bool $reset
     * @return bool
     */
    public function api_isCooled($trigger, $reset = FALSE)
    {
        if ($this->forceSync) {
            return TRUE;
        } else {
            $currentDate = new DateTime ('now');
            $lastRun = $this->api_getCoolDown($trigger, $reset);

            if ($lastRun->format("U") < $currentDate->format("U")) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * @param boolean $forceSync
     */
    public function setForceSync($forceSync)
    {
        $this->forceSync = $forceSync;
    }

    /**
     * @param $user
     */
    public function subscribeUser($user)
    {
        if ($this->getAppClass()->isUser($user)) {
            if (!$this->isAuthorised()) {
                $this->oAuthorise($user);
            }
            $this->getLibrary()->addSubscription(1);
        }
    }

    /**
     * @param $AppClass
     */
    private function setAppClass($AppClass)
    {
        $this->AppClass = $AppClass;
    }

    /**
     * @return NxFitbit
     */
    private function getAppClass()
    {
        return $this->AppClass;
    }

    /**
     * @param mixed $activeUser
     */
    private function setActiveUser($activeUser)
    {
        $this->activeUser = $activeUser;
    }

    private function pullBabel($path, $returnObject = FALSE)
    {
        try {
            // Try to get an access token using the authorization code grant.
            $accessToken = $this->getAccessToken();

            $request = $this->getLibrary()->getAuthenticatedRequest('GET', FITBIT_COM . "/1/" . $path, $accessToken);
            // Make the authenticated API request and get the response.
            $response = $this->getLibrary()->getResponse($request);

            if ($returnObject) {
                $response = json_decode(json_encode($response), FALSE);
            }

            // TODO: GitLab Issue #4 - Debug Payload Output
            nxr(print_r($response, true));
            return $response;
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Failed to get the access token or user details.
            nxr($e->getMessage());
            die();
        }
    }

    private function pushBabel($path, $pushObject, $returnObject = FALSE)
    {
        try {
            // Try to get an access token using the authorization code grant.
            $accessToken = $this->getAccessToken();

            if (is_array($pushObject)) $pushObject = http_build_query($pushObject);

            $request = $this->getLibrary()->getAuthenticatedRequest(OAUTH_HTTP_METHOD_POST, FITBIT_COM . "/1/" . $path, $accessToken,
                array("headers" =>
                    array(
                        "Accept-Header" => "en_GB",
                        "Content-Type" => "application/x-www-form-urlencoded"
                    ),
                    "body" => $pushObject
                ));
            // Make the authenticated API request and get the response.

            $response = $this->getLibrary()->getResponse($request);

            if ($returnObject) {
                $response = json_decode(json_encode($response), FALSE);
            }

            // TODO: GitLab Issue #4 - Debug Payload Output
//            nxr(print_r("pushObject: " . $pushObject, true));
//            nxr(print_r($request->getUri(), true));
//            nxr(print_r($request->getHeaders(), true));
//            nxr(print_r($request->getBody()->getContents(), true));
            nxr(print_r($response, true));
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
    private function pullBabelProfile()
    {
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
                    "avatar" => (String)$userProfile['avatar150'],
                    "city" => (String)$userProfile['city'],
                    "country" => (String)$userProfile['country'],
                    "name" => (String)$userProfile['fullName'],
                    "gender" => (String)$userProfile['gender'],
                    "height" => (String)$userProfile['height'],
                    "seen" => (String)$userProfile['memberSince'],
                    "stride_running" => (String)$userProfile['strideLengthRunning'],
                    "stride_walking" => (String)$userProfile['strideLengthWalking']
                ), array("fuid" => $this->getActiveUser()));

                if (!file_exists(dirname(__FILE__) . "/../images/avatars/" . $this->getActiveUser() . ".jpg")) {
                    file_put_contents(dirname(__FILE__) . "/../images/avatars/" . $this->getActiveUser() . ".jpg", fopen((String)$userProfile['avatar150'], 'r'));
                }

                $this->api_setLastrun("profile", NULL, TRUE);

                //TODO GitLab Issue #5 - Subscriptions
                //                try {
                //                    $subscriptions = $this->getLibrary()->getSubscriptions();
                //                } catch (Exception $E) {
                //                    /**
                //                     * @var FitBitException $E
                //                     */
                //                    nxr("Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user));
                //                    nxr(print_r($E, TRUE));
                //                    die();
                //                }
                //
                //                if (count($subscriptions->apiSubscriptions) == 0) {
                //                    nxr(" $user is not subscribed to the site");
                //                    try {
                //                        $user_db_id = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", 'uid', array("fuid" => $user));
                //                        $this->getLibrary()->addSubscription($user_db_id);
                //                    } catch (Exception $E) {
                //                        /**
                //                         * @var FitBitException $E
                //                         */
                //                        nxr("Error code (" . $E->httpcode . "): " . $this->getAppClass()->lookupErrorCode($E->httpcode, $user));
                //                        nxr(print_r($E, TRUE));
                //                        die();
                //                    }
                //                    nxr(" $user subscription confirmed with ID: $user_db_id");
                //                } else {
                //                    nxr(" $user subscription is still valid");
                //                }

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
     * @return mixed|null|SimpleXMLElement|string
     */
    private function pullBabelDevices()
    {
        $isAllowed = $this->isAllowed("devices");
        if (!is_numeric($isAllowed)) {
            if ($this->api_isCooled("devices")) {
                $userDevices = $this->pullBabel('user/-/devices.json', TRUE);

                foreach ($userDevices as $device) {
                    if (isset($device->id) and $device->id != "") {
                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", array("AND" => array("id" => (String)$device->id)))) {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", array(
                                'lastSyncTime' => (String)$device->lastSyncTime,
                                'battery' => (String)$device->battery
                            ), array("id" => (String)$device->id));
                        } else {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "devices", array(
                                'id' => (String)$device->id,
                                'deviceVersion' => (String)$device->deviceVersion,
                                'type' => (String)$device->type,
                                'lastSyncTime' => (String)$device->lastSyncTime,
                                'battery' => (String)$device->battery
                            ));
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_dev2usr", array(
                                'user' => $this->getActiveUser(),
                                'device' => (String)$device->id
                            ));
                        }

                        if (!file_exists(dirname(__FILE__) . "/../images/devices/" . str_ireplace(" ", "", $device->deviceVersion) . ".png")) {
                            nxr(" No device image for " . $device->type . " " . $device->deviceVersion);
                        }
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
     * @param $user
     * @return mixed|null|SimpleXMLElement|string
     */
    private function pullBabelBadges()
    {
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
                                    "AND" => array(
                                        "badgeType" => (String)$badge->badgeType,
                                        "value" => (String)$badge->value
                                    )
                                ))
                                ) {
                                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "bages", array(
                                        'badgeType' => (String)$badge->badgeType,
                                        'value' => (String)$badge->value,
                                        'image' => basename((String)$badge->image50px),
                                        'badgeGradientEndColor' => (String)$badge->badgeGradientEndColor,
                                        'badgeGradientStartColor' => (String)$badge->badgeGradientStartColor,
                                        'earnedMessage' => (String)$badge->earnedMessage,
                                        'marketingDescription' => (String)$badge->marketingDescription,
                                        'name' => (String)$badge->name
                                    ));
                                }

                                if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_badge2usr", array("AND" => array(
                                    "user" => $this->getActiveUser(),
                                    "badgeType" => (String)$badge->badgeType,
                                    "value" => (String)$badge->value
                                )))
                                ) {
                                    nxr(" User " . $this->getActiveUser() . " has been awarded the " . $badge->badgeType . " (" . $badge->value . ") again");
                                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_badge2usr", array(
                                        'dateTime' => (String)$badge->dateTime,
                                        'timesAchieved' => (String)$badge->timesAchieved
                                    ), array("AND" => array(
                                        "user" => $this->getActiveUser(),
                                        "badgeType" => (String)$badge->badgeType,
                                        "value" => (String)$badge->value
                                    )));
                                } else {
                                    nxr(" User " . $this->getActiveUser() . " has been awarded the " . $badge->badgeType . " (" . $badge->value . ") " . $badge->timesAchieved . " times.");
                                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "lnk_badge2usr", array(
                                        'user' => $this->getActiveUser(),
                                        'badgeType' => (String)$badge->badgeType,
                                        'dateTime' => (String)$badge->dateTime,
                                        'timesAchieved' => (String)$badge->timesAchieved,
                                        'value' => (String)$badge->value,
                                        'unit' => $unit
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
     * @param $user
     * @return mixed|null|SimpleXMLElement|string
     */
    private function pullBabelLeaderboard()
    {
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
                        foreach ($userFriends as $friend) {
                            $lifetime = floatval($friend->lifetime->steps);
                            $steps = floatval($friend->summary->steps);

                            if ($this->getActiveUser() == $this->getAppClass()->getSetting("fitbit_owner_id", NULL, FALSE)) {
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
                        }

                        if ($this->getActiveUser() == $this->getAppClass()->getSetting("fitbit_owner_id", NULL, FALSE) && isset($allOwnersFriends)) {
                            $this->getAppClass()->setSetting("owners_friends", $allOwnersFriends);
                        }

                        nxr("  * You are " . number_format($youDistance) . " steps away from the next rank and have " . count($userFriends) . " friends");

                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", array(
                            'rank' => $youRank,
                            'friends' => count($userFriends),
                            'distance' => $youDistance
                        ), array("fuid" => $this->getActiveUser()));

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
     * @param $user
     * @return mixed|null|SimpleXMLElement|string
     */
    private function pullBabelCaloriesGoals()
    {
        $isAllowed = $this->isAllowed("goals_calories");
        if (!is_numeric($isAllowed)) {
            if ($this->api_isCooled("goals_calories")) {
                $userCaloriesGoals = $this->pullBabel('user/-/foods/log/goal.json', TRUE);

                if (isset($userCaloriesGoals)) {
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

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "goals_calories", array("AND" => array("user" => $this->getActiveUser(), "date" => $currentDate->format("Y-m-d"))))) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "goals_calories", array(
                            'calories' => $usr_goals_calories,
                            'intensity' => $usr_foodplan_intensity,
                            'estimatedDate' => $usr_foodplan_estimatedDate,
                            'personalized' => $usr_foodplan_personalized,
                        ), array("AND" => array("user" => $this->getActiveUser(), "date" => $currentDate->format("Y-m-d"))));
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "goals_calories", array(
                            'user' => $this->getActiveUser(),
                            'date' => $currentDate->format("Y-m-d"),
                            'calories' => $usr_goals_calories,
                            'intensity' => $usr_foodplan_intensity,
                            'estimatedDate' => $usr_foodplan_estimatedDate,
                            'personalized' => $usr_foodplan_personalized,
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
    private function getActiveUser()
    {
        return $this->activeUser;
    }

    /**
     * @param $activity
     * @param $username
     * @param null $cron_delay
     * @param bool $clean
     */
    private function api_setLastrun($activity, $cron_delay = NULL, $clean = FALSE)
    {
        //TODO: GitLab Issue #6 - getActiveUser
        $username = $this->getActiveUser();

        if (is_null($cron_delay)) {
            $cron_delay_holder = 'nx_fitbit_ds_' . $activity . '_timeout';
            $cron_delay = $this->getAppClass()->getSetting($cron_delay_holder, 5400);
        }

        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $username, "activity" => $activity)))) {
            $fields = array(
                "date" => date("Y-m-d H:i:s"),
                "cooldown" => date("Y-m-d H:i:s", time() + $cron_delay)
            );
            if ($clean) {
                $fields['lastrun'] = date("Y-m-d H:i:s");
            }

            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", $fields, array("AND" => array("user" => $username, "activity" => $activity)));
        } else {
            $fields = array(
                "user" => $username,
                "activity" => $activity,
                "date" => date("Y-m-d H:i:s"),
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
            if (file_exists($cache_dir . $file) && is_writable($cache_dir . $file) && substr($file, 0, strlen($username) + 1) === "_" . $username) {
                $cacheNames = $this->getAppClass()->getSettings()->getRelatedCacheNames($activity);
                if (count($cacheNames) > 0) {
                    foreach ($cacheNames as $cacheName) {
                        if (substr($file, 0, strlen($username) + strlen($cacheName) + 2) === "_" . $username . "_" . $cacheName) {
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
     * @param $activity
     * @param $username
     * @param bool $reset
     * @return DateTime
     */
    private function api_getCoolDown($activity, $reset = FALSE)
    {
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
    private function getAccessToken()
    {
        if (is_null($this->userAccessToken)) {
            $user = $this->getActiveUser();

            $userArray = $this->getAppClass()->getUserOAuthTokens($user);
            if (is_array($userArray)) {
                $accessToken = new League\OAuth2\Client\Token\AccessToken([
                    'access_token' => $userArray['tkn_access'],
                    'refresh_token' => $userArray['tkn_refresh'],
                    'expires' => $userArray['tkn_expires']
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
                    nxr("This token still valid");
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
     * @param $user
     * @return DateTime
     */
    private function api_getLastCleanrun($activity)
    {
        //TODO: GitLab Issue #6 - getActiveUser
        $user = $this->getActiveUser();

        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $user, "activity" => $activity)))) {
            return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", "lastrun", array("AND" => array("user" => $user, "activity" => $activity))));
        } else {
            return $this->user_getFirstSeen();
        }
    }

    /**
     * @param $user
     * @return DateTime
     */
    private function user_getFirstSeen()
    {
        //TODO: GitLab Issue #6 - getActiveUser
        $user = $this->getActiveUser();

        return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "users", "seen", array("fuid" => $user)));
    }

    /**
     * @param $activity
     * @param $user
     * @param null $date
     * @param int $delay
     */
    private function api_setLastCleanrun($activity, $date = NULL, $delay = 0)
    {
        //TODO: GitLab Issue #6 - getActiveUser
        $user = $this->getActiveUser();

        if (is_null($date)) {
            $date = new DateTime("now");
            nxr("Last run " . $date->format("Y-m-d H:i:s"));
        }
        if ($delay > 0) $date->modify('-' . $delay . ' day');

        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $user, "activity" => $activity)))) {
            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array(
                'date' => date("Y-m-d H:i:s"),
                'lastrun' => $date->format("Y-m-d H:i:s")
            ), array("AND" => array("user" => $user, "activity" => $activity)));
        } else {
            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "runlog", array(
                'user' => $user,
                'activity' => $activity,
                'date' => date("Y-m-d H:i:s"),
                'lastrun' => $date->format("Y-m-d H:i:s")
            ));
        }

        if ($delay == 0) $this->api_setLastrun($activity, NULL, FALSE);
    }

    /**
     * @param $user
     * @param $string
     * @return float|int|string
     */
    private function thisWeeksGoal($string)
    {
        //TODO: GitLab Issue #6 - getActiveUser
        $user = $this->getActiveUser();

        $lastMonday = date('Y-m-d', strtotime('last sunday'));
        $oneWeek = date('Y-m-d', strtotime($lastMonday . ' -6 days'));
        $plusTargetSteps = -1;

        if ($string == "steps") {
            $userChallengeLength = $this->getAppClass()->getSetting("usr_challenger_" . $user . "_length", '50');
            $userChallengeStartString = $this->getAppClass()->getSetting("usr_challenger_" . $user, '03-31 last sunday'); // Default to last Sunday in March
            $userChallengeStartDate = date("Y-m-d", strtotime(date("Y") . '-' . $userChallengeStartString)); // Default to last Sunday in March
            $userChallengeEndDate = date("Y-m-d", strtotime($userChallengeStartDate . ' +' . $userChallengeLength . ' day')); // Default to last Sunday in March

            $today = strtotime(date("Y-m-d"));
            if ($today >= strtotime($userChallengeStartDate) && $today <= strtotime($userChallengeEndDate)) {
                nxr("Challenge is running");

                return $this->getAppClass()->getSetting("usr_challenger_" . $user . "_steps", '10000');
            } else {
                $improvment = $this->getAppClass()->getSetting("improvments_" . $user . "_steps", 2);
                if ($improvment > 0) {
                    $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'steps',
                        array("AND" => array(
                            "user" => $user,
                            "date[>=]" => $oneWeek,
                            "date[<=]" => $lastMonday
                        ), "ORDER" => "date DESC", "LIMIT" => 7));

                    $totalSteps = 0;
                    foreach ($dbSteps as $dbStep) {
                        $totalSteps = $totalSteps + $dbStep;
                    }
                    if ($totalSteps == 0) $totalSteps = 1;

                    $newTargetSteps = round($totalSteps / count($dbSteps), 0);
                    if ($newTargetSteps < $this->getAppClass()->getSetting("improvments_" . $user . "_steps_max", 10000)) {
                        $plusTargetSteps = $newTargetSteps + round($newTargetSteps * ($this->getAppClass()->getSetting("improvments_" . $user . "_steps", 10) / 100), 0);
                    } else {
                        $plusTargetSteps = $this->getAppClass()->getSetting("improvments_" . $user . "_steps_max", 10000);
                    }
                }
            }
        } elseif ($string == "floors") {
            $improvment = $this->getAppClass()->getSetting("improvments_" . $user . "_floors", 2);
            if ($improvment > 0) {
                $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "steps", 'floors',
                    array("AND" => array(
                        "user" => $user,
                        "date[>=]" => $oneWeek,
                        "date[<=]" => $lastMonday
                    ), "ORDER" => "date DESC", "LIMIT" => 7));

                $totalSteps = 0;
                foreach ($dbSteps as $dbStep) {
                    $totalSteps = $totalSteps + $dbStep;
                }
                if ($totalSteps == 0) $totalSteps = 1;

                $newTargetSteps = round($totalSteps / count($dbSteps), 0);
                if ($newTargetSteps < $this->getAppClass()->getSetting("improvments_" . $user . "_floors_max", 10)) {
                    $plusTargetSteps = $newTargetSteps + round($newTargetSteps * ($this->getAppClass()->getSetting("improvments_" . $user . "_floors", 10) / 100), 0);
                } else {
                    $plusTargetSteps = $this->getAppClass()->getSetting("improvments_" . $user . "_floors_max", 10);
                }
            }
        } elseif ($string == "activeMinutes") {
            $userChallengeLength = $this->getAppClass()->getSetting("usr_challenger_" . $user . "_length", '50');
            $userChallengeStartString = $this->getAppClass()->getSetting("usr_challenger_" . $user, '03-31 last sunday'); // Default to last Sunday in March
            $userChallengeStartDate = date("Y-m-d", strtotime(date("Y") . '-' . $userChallengeStartString)); // Default to last Sunday in March
            $userChallengeEndDate = date("Y-m-d", strtotime($userChallengeStartDate . ' +' . $userChallengeLength . ' day')); // Default to last Sunday in March

            $today = strtotime(date("Y-m-d"));
            if ($today >= strtotime($userChallengeStartDate) && $today <= strtotime($userChallengeEndDate)) {
                nxr("Challenge is running");

                return $this->getAppClass()->getSetting("usr_challenger_" . $user . "_activity", '30');
            } else {
                $improvment = $this->getAppClass()->getSetting("improvments_" . $user . "_active", 10);
                if ($improvment > 0) {
                    $dbActiveMinutes = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "activity", array('veryactive', 'fairlyactive'),
                        array("AND" => array(
                            "user" => $user,
                            "date[>=]" => $oneWeek,
                            "date[<=]" => $lastMonday
                        ), "ORDER" => "date DESC", "LIMIT" => 7));

                    $totalMinutes = 0;
                    foreach ($dbActiveMinutes as $dbStep) {
                        $totalMinutes = $totalMinutes + $dbStep['veryactive'] + $dbStep['fairlyactive'];
                    }
                    if ($totalMinutes == 0) $totalMinutes = 1;

                    $newTargetActive = round($totalMinutes / count($dbActiveMinutes), 0);
                    if ($newTargetActive < $this->getAppClass()->getSetting("improvments_" . $user . "_active_max", 30)) {
                        $plusTargetSteps = $newTargetActive + round($newTargetActive * ($this->getAppClass()->getSetting("improvments_" . $user . "_active", 10) / 100), 0);
                    } else {
                        $plusTargetSteps = $this->getAppClass()->getSetting("improvments_" . $user . "_active_max", 30);
                    }
                }
            }
        }

        return $plusTargetSteps;
    }

}
