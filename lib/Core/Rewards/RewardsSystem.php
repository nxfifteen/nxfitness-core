<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

namespace Core\Rewards;

require_once(dirname(__FILE__) . "/../../autoloader.php");

use Core\Core;
use DateTime;

/**
 * RewardsSystem
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-RewardsMinecraft
 *            phpDocumentor wiki for RewardsSystem.
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class RewardsSystem
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
     * @var array
     */
    protected $AwardsGiven;
    private $debug = false;
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
        $this->AwardsGiven = [];
        $this->setUserID($user);
        $this->user = $user;
    }

    /**
     * @param Core $paramClass
     */
    private function setAppClass($paramClass)
    {
        $this->AppClass = $paramClass;
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
    public function queryMinecraftRewards()
    {
        $wmc_key_provided = $_GET['wmc_key'];
        $wmc_key_correct = $this->getAppClass()->getSetting("wmc_key", null, true);
        nxr(0, "Minecraft rewards Check");

        if ($wmc_key_provided != $wmc_key_correct) {
            nxr(1, "Key doesnt match");

            return ["success" => false, "data" => ["msg" => "Incorrect key"]];
        }

        $databaseTable = $this->getAppClass()->getSetting("db_prefix", null, false);

        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $rewards = $this->getAppClass()->getDatabase()->select($databaseTable . "minecraft",
                [
                    'mcrid',
                    'username',
                    'command'
                ], [
                    "delivery" => "pending",
                    "ORDER" => ['mcrid' => "ASC"]
                ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);

            $data = [];
            foreach ($rewards as $dbReward) {
                if (!array_key_exists($dbReward['username'], $data)) {
                    $data[$dbReward['username']] = [];
                }
                if (!array_key_exists($dbReward['mcrid'], $data[$dbReward['username']])) {
                    $data[$dbReward['username']][$dbReward['mcrid']] = [];
                }
                array_push($data[$dbReward['username']][$dbReward['mcrid']], $dbReward['command']);
            }

            return ["success" => true, "data" => $data];

        } else if ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists("processedOrders", $_POST)) {

            $_POST['processedOrders'] = json_decode($_POST['processedOrders']);

            if (is_array($_POST['processedOrders'])) {
                foreach ($_POST['processedOrders'] as $processedOrder) {
                    if ($this->getAppClass()->getDatabase()->has($databaseTable . "minecraft", ["mcrid" => $processedOrder])) {
                        $this->getAppClass()->getDatabase()->update($databaseTable . "minecraft", ["delivery" => "delivered"], ["mcrid" => $processedOrder]);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

                        nxr(1, "Reward " . $processedOrder . " processed");
                    } else {
                        nxr(1, "Reward " . $processedOrder . " is invalid ID");
                    }
                }
            } else {
                nxr(1, "No processed rewards recived");
            }

            return ["success" => true];

        }

        return ["success" => false, "data" => ["msg" => "Unknown Error"]];

    }

    /**
     * @return Core
     */
    private function getAppClass()
    {
        return $this->AppClass;
    }

    /**
     * @param string $system Name of system used to issue reward, also Class name
     * @param array $eventDetails Array holding details of award to issue
     */
    public function eventTrigger($system, $eventDetails)
    {
        $className = "Core\\Rewards\\Modules\\" . $system;
        $includePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "Modules";

        if (file_exists($includePath . DIRECTORY_SEPARATOR . "Private" . DIRECTORY_SEPARATOR . $system . ".php")) {
            $includePath = $includePath . DIRECTORY_SEPARATOR . "Private" . DIRECTORY_SEPARATOR . $system . ".php";
        } else if (file_exists($includePath . DIRECTORY_SEPARATOR . $system . ".php")) {
            $includePath = $includePath . DIRECTORY_SEPARATOR . $system . ".php";
        } else {
            $includePath = null;
        }

        if (!is_null($includePath)) {
            if ($this->debug) nxr(2, "includePath: " . $includePath);
            if ($this->debug) nxr(2, "className: " . $className);

            /** @noinspection PhpIncludeInspection */
            require_once($includePath);
            $rewardSystem = new $className($this->getAppClass(), $this->getUserID());
            /** @noinspection PhpUndefinedMethodInspection */
            $rewardSystem->trigger($eventDetails);

        } else {
            nxr(2, "Create a new class '$className' in " . $includePath . DIRECTORY_SEPARATOR . "Private" . DIRECTORY_SEPARATOR . $system . ".php");
        }
    }

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
     * @param string $cat
     * @param string $event
     * @param string $score
     * @param null|string $rewardKey
     *
     * @return array|bool
     */
    private function checkForAward($cat, $event, $score, $rewardKey = null)
    {
        $reward = [];
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        if (is_null($rewardKey)) {
            $currentDate = new DateTime ('now');
            $rewardKey = sha1($currentDate->format("Y-m-d"));
        } else {
            $rewardKey = sha1($rewardKey);
        }

        if ($this->getAppClass()->getDatabase()->has($db_prefix . "reward_map", [
            "AND" => [
                'cat' => $cat,
                'event' => $event,
                'rule' => $score
            ]
        ])
        ) {
            $rewards = $this->getAppClass()->getDatabase()->query(
                "SELECT `" . $db_prefix . "reward_map`.`rmid` AS `rmid`,`" . $db_prefix . "reward_map`.`xp` AS `xp`,`" . $db_prefix . "reward_map`.`reward` AS `rid`"
                . " FROM `" . $db_prefix . "reward_map`"
                . " WHERE `" . $db_prefix . "reward_map`.`cat` = '" . $cat . "' AND `" . $db_prefix . "reward_map`.`event` = '" . $event . "' AND `" . $db_prefix . "reward_map`.`rule` = '" . $score . "' ");
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);
            foreach ($rewards as $dbReward) {
                array_push($reward, [
                    "rmid" => $dbReward['rmid'],
                    "rid" => $dbReward['rid'],
                    "xp" => $dbReward['xp']
                ]);
            }
        } else if ($this->createRewards) {
            $this->getAppClass()->getDatabase()->insert($db_prefix . "reward_map", [
                "cat" => $cat,
                "event" => $event,
                "rule" => $score
            ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);
        }

        if (count($reward) == 0) {
            return false;
        } else {
            foreach ($reward as $recordReward) {
                if ($recordReward['rid'] != "" || $recordReward['xp'] > 0) {
                    if (!$this->getAppClass()->getDatabase()->has($db_prefix . "reward_queue", [
                        "AND" => [
                            'fuid' => $this->getUserID(),
                            'rkey[~]' => $rewardKey,
                            'rmid' => $recordReward['rmid']
                        ]
                    ])
                    ) {
                        if ($recordReward['xp'] > 0) {
                            if (!$this->getAppClass()->getDatabase()->has($db_prefix . "users_xp", ['fuid' => $this->getUserID()])) {
                                $this->getAppClass()->getDatabase()->insert($db_prefix . "users_xp", ["xp" => 0, "fuid" => $this->getUserID()]);
                                $dbCurrentXp = 0;
                            } else {
                                $dbCurrentXp = $this->getAppClass()->getDatabase()->get($db_prefix . "users_xp", 'xp', ["fuid" => $this->getUserID()]);
                            }

                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

                            $this->getAppClass()->getDatabase()->update($db_prefix . "users_xp", ["xp" => $dbCurrentXp + $recordReward['xp']], ["fuid" => $this->getUserID()]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
                            nxr(4, "Awarding $cat / $event ($score) = " . $recordReward['xp'] . " xp");
                            $state = 'delivered';
                        }

                        if ($recordReward['rid'] != "") {
                            $recordReward['description'] = $this->getAppClass()->getDatabase()->get($db_prefix . "rewards", "description", ["rid" => $recordReward['rid']]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

                            $nukeOne = $this->getAppClass()->getDatabase()->select($db_prefix . "reward_nuke", 'rid', ["AND" => ["nukeid" => $recordReward['rid'], "directional" => "true"]]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
                            if (count($nukeOne) > 0) {
                                foreach ($nukeOne as $nukeId) {
                                    if ($this->getAppClass()->getDatabase()->has($db_prefix . "reward_queue", [
                                        "AND" => [
                                            'fuid' => $this->getUserID(),
                                            'reward' => $nukeId
                                        ]
                                    ])
                                    ) {
                                        $this->getAppClass()->getDatabase()->delete($db_prefix . "reward_queue", [
                                            "AND" => [
                                                'fuid' => $this->getUserID(),
                                                'reward' => $nukeId
                                            ]
                                        ]);
                                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
                                    }
                                }
                            }

                            $nukeTwo = $this->getAppClass()->getDatabase()->select($db_prefix . "reward_nuke", 'nukeid', ["AND" => ["rid" => $recordReward['rid'], "directional" => "false"]]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
                            if (count($nukeTwo) > 0) {
                                foreach ($nukeTwo as $nukeId) {
                                    if ($this->getAppClass()->getDatabase()->has($db_prefix . "reward_queue", [
                                        "AND" => [
                                            'fuid' => $this->getUserID(),
                                            'reward' => $nukeId
                                        ]
                                    ])
                                    ) {
                                        $this->getAppClass()->getDatabase()->delete($db_prefix . "reward_queue", [
                                            "AND" => [
                                                'fuid' => $this->getUserID(),
                                                'reward' => $nukeId
                                            ]
                                        ]);
                                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
                                    }
                                }
                            }

                            nxr(4, "Awarding $cat / $event ($score) = " . print_r($recordReward['description'], true));
                            $state = 'pending';
                        } else {
                            $recordReward['rid'] = null;
                        }

                        $this->getAppClass()->getDatabase()->insert($db_prefix . "reward_queue", [
                            "fuid" => $this->getUserID(),
                            "state" => $state,
                            "rmid" => $recordReward['rmid'],
                            "reward" => $recordReward['rid'],
                            "rkey" => $rewardKey
                        ]);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

                    } else {
                        nxr(4, "Already awarded $cat / $event ($score)");
                    }
                }
            }

            return $reward;
        }

    }
}
