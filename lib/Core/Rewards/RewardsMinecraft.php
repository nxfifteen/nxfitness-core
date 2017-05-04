<?php

    namespace Core\Rewards;

    require_once(dirname(__FILE__) . "/../../autoloader.php");

    use Core\Core;
    use DateTime;

    /**
     * RewardsMinecraft
     *
     * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-RewardsMinecraft
     *            phpDocumentor wiki for RewardsMinecraft.
     * @version   0.0.1
     * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link      https://nxfifteen.me.uk NxFIFTEEN
     * @copyright 2017 Stuart McCulloch Anderson
     * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
     */
    class RewardsMinecraft
    {

        /**
         * @var Core
         */
        protected $AppClass;

        /**
         * @var String
         */
        protected $UserID;
        /**
         * @var string
         */
        protected $UserMinecraftID;

        /**
         * @var boolean
         */
        protected $createRewards;

        /**
         * @var array
         */
        protected $AwardsGiven;
        /**
         * @var null
         */
        private $user;

        /**
         * @param null $user
         *
         * @internal param $userFid
         */
        public function __construct($user = null)
        {
            $this->setAppClass(new Core());
            $this->AwardsGiven   = array();
            $this->createRewards = true;
            $this->setUserID($user);
            $this->user = $user;
        }

        /**
         * @param Core $paramClass
         *
         * @todo Consider test case
         */
        private function setAppClass($paramClass)
        {
            $this->AppClass = $paramClass;
        }

        /**
         * @todo Consider test case
         * @return Core
         */
        private function getAppClass()
        {
            return $this->AppClass;
        }

        /**
         * @todo Consider test case
         *
         * @param string $cat
         * @param string $event
         * @param string $score
         *
         * @return array|bool
         */
        private function checkForAward($cat, $event, $score)
        {
            $reward    = array();
            $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

            if ($this->getAppClass()->getDatabase()->has($db_prefix . "reward_map", array(
                "AND" => array(
                    'cat'   => $cat,
                    'event' => $event,
                    'rule'  => $score
                )
            ))
            ) {
                $rewards = $this->getAppClass()->getDatabase()->query(
                    "SELECT `" . $db_prefix . "reward_map`.`rmid` AS `rmid`,`" . $db_prefix . "reward_map`.`reward` AS `rid`,`" . $db_prefix . "rewards`.`reward` AS `reward`,`" . $db_prefix . "rewards`.`description` AS `description`"
                    . " FROM `" . $db_prefix . "reward_map`"
                    . " JOIN `" . $db_prefix . "rewards` ON (`" . $db_prefix . "reward_map`.`reward` = `" . $db_prefix . "rewards`.`rid`)"
                    . " WHERE `" . $db_prefix . "reward_map`.`cat` = '" . $cat . "' AND `" . $db_prefix . "reward_map`.`event` = '" . $event . "' AND `" . $db_prefix . "reward_map`.`rule` = '" . $score . "' ");
                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), array(
                    "METHOD" => __METHOD__,
                    "LINE"   => __LINE__
                ));
                foreach ($rewards as $dbReward) {
                    array_push($reward, array(
                        "rid"         => $dbReward['rid'],
                        "rmid"        => $dbReward['rmid'],
                        "reward"      => $dbReward['reward'],
                        "description" => $dbReward['description']
                    ));
                }
            } else if ($this->createRewards) {
                $this->getAppClass()->getDatabase()->insert($db_prefix . "reward_map", array(
                    "cat"   => $cat,
                    "event" => $event,
                    "rule"  => $score
                ));
                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), array(
                    "METHOD" => __METHOD__,
                    "LINE"   => __LINE__
                ));
            }

            if (count($reward) == 0) {
                return false;
            } else {

                foreach ($reward as $recordReward) {

                    $currentDate = new DateTime ('now');
                    $currentDate = $currentDate->format("Y-m-d");
                    if (!$this->getAppClass()->getDatabase()->has($db_prefix . "reward_queue", array(
                        "AND" => array(
                            'fuid'    => $this->getUserID(),
                            'date[~]' => $currentDate,
                            'rmid'    => $recordReward['rmid']
                        )
                    ))
                    ) {
                        $nukeOne = $this->getAppClass()->getDatabase()->select($db_prefix . "reward_nuke", 'rid', array(
                            "AND" => array(
                                "nukeid"      => $recordReward['rid'],
                                "directional" => "true"
                            )
                        ));
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            array(
                                "METHOD" => __METHOD__,
                                "LINE"   => __LINE__
                            ));
                        if (count($nukeOne) > 0) {
                            foreach ($nukeOne as $nukeId) {
                                if ($this->getAppClass()->getDatabase()->has($db_prefix . "reward_queue", array(
                                    "AND" => array(
                                        'fuid'   => $this->getUserID(),
                                        'reward' => $nukeId
                                    )
                                ))
                                ) {
                                    $this->getAppClass()->getDatabase()->delete($db_prefix . "reward_queue", array(
                                        "AND" => array(
                                            'fuid'   => $this->getUserID(),
                                            'reward' => $nukeId
                                        )
                                    ));
                                }
                            }
                        }

                        $nukeTwo = $this->getAppClass()->getDatabase()->select($db_prefix . "reward_nuke", 'nukeid',
                            array(
                                "AND" => array(
                                    "rid"         => $recordReward['rid'],
                                    "directional" => "false"
                                )
                            ));
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            array(
                                "METHOD" => __METHOD__,
                                "LINE"   => __LINE__
                            ));
                        if (count($nukeTwo) > 0) {
                            foreach ($nukeTwo as $nukeId) {
                                if ($this->getAppClass()->getDatabase()->has($db_prefix . "reward_queue", array(
                                    "AND" => array(
                                        'fuid'   => $this->getUserID(),
                                        'reward' => $nukeId
                                    )
                                ))
                                ) {
                                    $this->getAppClass()->getDatabase()->delete($db_prefix . "reward_queue", array(
                                        "AND" => array(
                                            'fuid'   => $this->getUserID(),
                                            'reward' => $nukeId
                                        )
                                    ));
                                }
                            }
                        }

                        $this->getAppClass()->getDatabase()->insert($db_prefix . "reward_queue", array(
                            "fuid"   => $this->getUserID(),
                            "state"  => 'pending',
                            "rmid"   => $recordReward['rmid'],
                            "reward" => $recordReward['rid']
                        ));
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            array(
                                "METHOD" => __METHOD__,
                                "LINE"   => __LINE__
                            ));
                        nxr(4, "Awarding $cat / $event ($score) = " . print_r($recordReward['description'], true));
                    } else {
                        nxr(4, "Already awarded $cat / $event ($score) = " . print_r($recordReward['description'],
                                true));
                    }

                }

                return $reward;
            }

        }

        /**
         * @param string $goal
         * @param int    $value
         * @param int    $multiplyer
         *
         * @todo Consider test case
         * @return bool
         */
        private function reachedGoal($goal, $value, $multiplyer = 1)
        {
            $currentDate = new DateTime ('now');
            $currentDate = $currentDate->format("Y-m-d");
            $db_prefix   = $this->getAppClass()->getSetting("db_prefix", null, false);
            if ($value >= 1) {
                $recordedValue  = $value;
                $recordedTarget = round($this->getAppClass()->getDatabase()->get($db_prefix . "steps_goals", $goal,
                    array(
                        "AND" => array(
                            "user" => $this->getUserID(),
                            "date" => $currentDate
                        )
                    )), 3);
                if (!is_numeric($recordedTarget) || $recordedTarget <= 0) {
                    $recordedTarget = round($this->getAppClass()->getUserSetting($this->getUserID(), "goal_" . $goal),
                        3);
                }
                $requiredTarget = $recordedTarget * $multiplyer;
                if ($recordedValue >= $requiredTarget) {
                    return true;
                }
            } else {
                nxr(4, "No $goal data recorded for $currentDate");
            }

            return false;
        }

        /**
         * @param string $goal
         * @param string $value
         *
         * @todo Consider test case
         * @return bool
         */
        private function smashedGoal($goal, $value) { return $this->reachedGoal($goal, $value, 1.5); }

        /**
         * @param string $goal
         * @param string $value
         *
         * @todo Consider test case
         * @return bool
         */
        private function crushedGoal($goal, $value) { return $this->reachedGoal($goal, $value, 2); }

        /**
         * @todo Consider test case
         * @return String
         */
        public function getUserID()
        {
            return $this->UserID;
        }

        /**
         * @todo Consider test case
         *
         * @param String $UserID
         */
        public function setUserID($UserID)
        {
            $this->UserID = $UserID;
        }

        /**
         * @todo Consider test case
         * @return String
         */
        public function getUserMinecraftID()
        {
            return $this->UserMinecraftID;
        }

        /**
         * @param string $UserMinecraftID
         *
         * @todo     Consider test case
         * @internal param String $UserID
         */
        public function setUserMinecraftID($UserMinecraftID)
        {
            $this->UserMinecraftID = $UserMinecraftID;
        }

        /**
         * @todo Consider test case
         * @return array
         */
        public function queryRewards()
        {
            $wmc_key_provided = $_GET['wmc_key'];
            $wmc_key_correct  = $this->getAppClass()->getSetting("wmc_key", null, true);
            nxr(0, "Minecraft rewards Check");

            if ($wmc_key_provided != $wmc_key_correct) {
                nxr(1, "Key doesnt match");

                return array("success" => false, "data" => array("msg" => "Incorrect key"));
            }

            $databaseTable = $this->getAppClass()->getSetting("db_prefix", null, false);

            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                $rewards = $this->getAppClass()->getDatabase()->query(
                    "SELECT `" . $databaseTable . "rewards`.`reward` AS `reward`,"
                    . " `" . $databaseTable . "reward_queue`.`fuid` AS `fuid`,"
                    . " `" . $databaseTable . "reward_queue`.`rqid` AS `rqid`"
                    . " FROM `" . $databaseTable . "rewards`"
                    . " JOIN `" . $databaseTable . "reward_queue` ON (`" . $databaseTable . "reward_queue`.`reward` = `" . $databaseTable . "rewards`.`rid`)"
                    . " WHERE `" . $databaseTable . "reward_queue`.`state` = 'pending' LIMIT 50");

                $data = array();
                foreach ($rewards as $dbReward) {
                    $minecraftUsername = $this->getAppClass()->getUserSetting($dbReward['fuid'], "minecraft_username",
                        false);

                    if (!array_key_exists($minecraftUsername, $data)) {
                        $data[$minecraftUsername] = array();
                    }
                    if (!array_key_exists($dbReward['rqid'], $data[$minecraftUsername])) {
                        $data[$minecraftUsername][$dbReward['rqid']] = array();
                    }
                    $dbReward['reward'] = str_replace("%s", $minecraftUsername, $dbReward['reward']);
                    array_push($data[$minecraftUsername][$dbReward['rqid']], $dbReward['reward']);
                }

                return array("success" => true, "data" => $data);

            } else if ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists("processedOrders", $_POST)) {

                $_POST['processedOrders'] = json_decode($_POST['processedOrders']);

                if (is_array($_POST['processedOrders'])) {
                    foreach ($_POST['processedOrders'] as $processedOrder) {
                        if ($this->getAppClass()->getDatabase()->has($databaseTable . "reward_queue",
                            array("rqid" => $processedOrder))
                        ) {

                            $this->getAppClass()->getDatabase()->update($databaseTable . "reward_queue",
                                array("state" => "delivered"), array("rqid" => $processedOrder));
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                array(
                                    "METHOD" => __METHOD__,
                                    "LINE"   => __LINE__
                                ));

                            nxr(1, "Reward " . $processedOrder . " processed");
                        } else {
                            nxr(1, "Reward " . $processedOrder . " is invalid ID");
                        }
                    }
                } else {
                    nxr(1, "No processed rewards recived");
                }

                return array("success" => true);

            }

            return array("success" => false, "data" => array("msg" => "Unknown Error"));

        }

        /**
         * @param object $activity
         *
         * @todo Consider test case
         */
        public function eventTriggerActivity($activity)
        {
            $currentDate   = new DateTime ('now');
            $currentDate   = $currentDate->format("Y-m-d");
            $db_prefix     = $this->getAppClass()->getSetting("db_prefix", null, false);
            $checkForThese = array(
                "Aerobic",
                "Bicycling",
                "Bodyweight",
                "Calisthenics",
                "Circuit Training",
                "Elliptical Trainer",
                "Hike",
                "Meditating",
                "Outdoor Bike",
                "Push-ups",
                "Run",
                "Sit-ups",
                "Skiing",
                "Stationary bike",
                "Strength training",
                "Swimming",
                "Tai chi",
                "Treadmill",
                "Walk",
                "Workout",
                "Yoga"
            );

            $supportActivity = false;
            if ($activity->activityName != "auto_detected") {
                foreach ($checkForThese as $tracker) {
                    if (!$supportActivity && strpos($activity->activityName, $tracker) !== false) {
                        $supportActivity = true;
                    }
                }
            }

            if ($supportActivity) {
                $sql_search       = array(
                    "user"            => $this->getUserID(),
                    "activityName[~]" => $activity->activityName,
                    "startDate"       => $currentDate,
                    "logType[!]"      => 'auto_detected'
                );
                $minMaxAvg        = array();
                $minMaxAvg['min'] = ($this->getAppClass()->getDatabase()->min($db_prefix . "activity_log",
                            "activeDuration", array("AND" => $sql_search)) / 1000) / 60;
                $minMaxAvg['avg'] = ($this->getAppClass()->getDatabase()->avg($db_prefix . "activity_log",
                            "activeDuration", array("AND" => $sql_search)) / 1000) / 60;
                $minMaxAvg['max'] = ($this->getAppClass()->getDatabase()->max($db_prefix . "activity_log",
                            "activeDuration", array("AND" => $sql_search)) / 1000) / 60;

                $minMaxAvg['min2avg'] = (($minMaxAvg['avg'] - $minMaxAvg['min']) / 2) + $minMaxAvg['min'];
                $minMaxAvg['avg2max'] = (($minMaxAvg['max'] - $minMaxAvg['avg']) / 2) + $minMaxAvg['avg'];

                $activeDuration = $activity->duration / 1000 / 60;

                if ($activeDuration == $minMaxAvg['max']) {
                    $this->checkForAward("activity", $activity->activityName, "max");
                } else if ($activeDuration >= $minMaxAvg['avg2max']) {
                    $this->checkForAward("activity", $activity->activityName, "avg2max");
                } else if ($activeDuration >= $minMaxAvg['avg']) {
                    $this->checkForAward("activity", $activity->activityName, "avg");
                } else if ($activeDuration >= $minMaxAvg['min2avg']) {
                    $this->checkForAward("activity", $activity->activityName, "min2avg");
                } else {
                    $this->checkForAward("activity", $activity->activityName, "other");
                }
            }

        }

        /**
         * @param object $badge
         *
         * @todo Consider test case
         */
        public function eventTriggerBadgeAwarded($badge)
        {
            nxr(1, "** API Event Trigger Badge");

            //if (date('Y-m-d') == $badge->dateTime) {
            nxr(4,
                $badge->shortName . " (" . $badge->category . ") awarded " . $badge->timesAchieved . " on " . $badge->dateTime);

            if ($this->checkForAward("badge", $badge->category . " | " . $badge->shortName, "awarded")) {

            } else if ($this->checkForAward("badge", $badge->category, "awarded")) {

            } else if ($this->checkForAward("badge", $badge->category . " | " . $badge->shortName,
                $badge->timesAchieved)
            ) {

            } else if ($this->checkForAward("badge", $badge->category, $badge->timesAchieved)) {

            }
            //}
        }

        /**
         * @param double $current
         * @param double $goal
         * @param double $last
         *
         * @todo Consider test case
         */
        public function eventTriggerWeightChange($current, $goal, $last)
        {
            if ($current <= $goal) {
                $this->checkForAward("body", "weight", "goal");
            } else if ($current < $last) {
                $this->checkForAward("body", "weight", "decreased");
            } else if ($current > $last) {
                $this->checkForAward("body", "weight", "increased");
            }
        }

        /**
         * @param double $current
         * @param double $goal
         * @param double $last
         *
         * @todo Consider test case
         */
        public function eventTriggerFatChange($current, $goal, $last)
        {
            if ($current <= $goal) {
                $this->checkForAward("body", "fat", "goal");
            } else if ($current < $last) {
                $this->checkForAward("body", "fat", "decreased");
            } else if ($current > $last) {
                $this->checkForAward("body", "fat", "increased");
            }
        }

        /**
         * @param object $meal
         *
         * @todo Consider test case
         */
        public function eventTriggerNewMeal($meal)
        {
            nxr(1, "** API Event Meal Logged");
            nxr(6, $meal->loggedFood->name . " recorded");
        }

        /**
         * @param string $veryActive
         *
         * @todo Consider test case
         */
        public function eventTriggerVeryActive($veryActive)
        {
            $currentDate = new DateTime ('now');
            $currentDate = $currentDate->format("Y-m-d");
            $db_prefix   = $this->getAppClass()->getSetting("db_prefix", null, false);
            if ($veryActive >= 1) {
                $recordedValue  = $veryActive;
                $recordedTarget = $this->getAppClass()->getDatabase()->get($db_prefix . "steps_goals", "activeMinutes",
                    array(
                        "AND" => array(
                            "user" => $this->getUserID(),
                            "date" => $currentDate
                        )
                    ));
                if (!is_numeric($recordedTarget) || $recordedTarget <= 0) {
                    $recordedTarget = round($this->getAppClass()->getUserSetting($this->getUserID(), "goal_activity"),
                        30);
                }

                if ($recordedValue >= $recordedTarget) {
                    $this->checkForAward("goal", "veryactive", "reached");
                }
            }
        }

        /**
         * @param string $date
         * @param string $trigger
         * @param string $value
         *
         * @todo Consider test case
         */
        public function eventTriggerTracker($date, $trigger, $value)
        {
            $goalsToCheck = array("steps", "floors", "distance");

            if (in_array($trigger, $goalsToCheck) && date('Y-m-d') == $date) {
                // Crushed Step Goal
                if (!$this->crushedGoal($trigger, $value)) {
                    // Smashed Step Goal
                    if (!$this->smashedGoal($trigger, $value)) {
                        // Reached Step Goal
                        if ($this->reachedGoal($trigger, $value)) {
                            $this->checkForAward("goal", $trigger, "reached");
                        }
                    } else {
                        $this->checkForAward("goal", $trigger, "smashed");
                    }
                } else {
                    $this->checkForAward("goal", $trigger, "crushed");
                }

                if ($trigger == "steps") {
                    $divider = 100;
                } else {
                    $divider = 10;
                }

                if ($value >= 1) {
                    $recordedValue = round($value, 3);
                    $hundredth     = round($recordedValue / $divider, 0);
                    nxr(1, "checking awards for $trigger $hundredth");
                    $this->checkForAward("hundredth", $trigger, $hundredth);

                }
            }
        }

        /**
         * @param array $inputArray
         *
         * @todo Consider test case
         */
        public function eventTriggerNomie($inputArray)
        {
            $event = $inputArray[2];
            $date  = $inputArray[5];
            $score = $inputArray[4];

            nxr(2, "** API Event Nomie - " . $event . " logged on " . $date . " and scored " . $score);

            if (!$this->checkForAward("nomie", "logged", $event)) {
                $this->checkForAward("nomie", "score", $score);
            }
        }

        /**
         * @param string $goal
         * @param string $length
         *
         * @todo Consider test case
         */
        public function eventTriggerStreak($goal, $length)
        {
            $this->checkForAward("streak", $goal, $length);
        }
    }
