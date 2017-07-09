<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @subpackage  Rewards
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

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
    protected $appClass;
    /**
     * @var String
     */
    protected $userID;
    /**
     * @var string
     */
    protected $userMinecraftID;
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
     * @return Core
     */
    private function getAppClass()
    {
        return $this->AppClass;
    }

    /**
     * @param string $cat
     * @param string $event
     * @param string $score
     * @param null|string $rewardKey
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * @return array|bool
     */
    private function checkForAward($cat, $event, $score, $rewardKey = null)
    {
        // @todo: Find all debug unused classes
        nxr(0, "**** DEBUG FUNCTION CALLED: " . __FUNCTION__);

        $reward = [];
        $dbPrefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        if (is_null($rewardKey)) {
            $currentDate = new DateTime ('now');
            $rewardKey = sha1($currentDate->format("Y-m-d"));
        } else {
            $rewardKey = sha1($rewardKey);
        }

        if ($this->getAppClass()->getDatabase()->has($dbPrefix . "reward_map", [
            "AND" => [
                'cat' => $cat,
                'event' => $event,
                'rule' => $score
            ]
        ])
        ) {
            $rewards = $this->getAppClass()->getDatabase()->query(
                "SELECT `" . $dbPrefix . "reward_map`.`rmid` AS `rmid`,`" . $dbPrefix . "reward_map`.`xp` AS `xp`,`" . $dbPrefix . "reward_map`.`reward` AS `rid`"
                . " FROM `" . $dbPrefix . "reward_map`"
                . " WHERE `" . $dbPrefix . "reward_map`.`cat` = '" . $cat . "' AND `" . $dbPrefix . "reward_map`.`event` = '" . $event . "' AND `" . $dbPrefix . "reward_map`.`rule` = '" . $score . "' ");
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
            $this->getAppClass()->getDatabase()->insert($dbPrefix . "reward_map", [
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
                    if (!$this->getAppClass()->getDatabase()->has($dbPrefix . "reward_queue", [
                        "AND" => [
                            'fuid' => $this->getUserID(),
                            'rkey[~]' => $rewardKey,
                            'rmid' => $recordReward['rmid']
                        ]
                    ])
                    ) {
                        if ($recordReward['xp'] > 0) {
                            if (!$this->getAppClass()->getDatabase()->has($dbPrefix . "users_xp", ['fuid' => $this->getUserID()])) {
                                $this->getAppClass()->getDatabase()->insert($dbPrefix . "users_xp", ["xp" => 0, "fuid" => $this->getUserID()]);
                                $dbCurrentXp = 0;
                            } else {
                                $dbCurrentXp = $this->getAppClass()->getDatabase()->get($dbPrefix . "users_xp", 'xp', ["fuid" => $this->getUserID()]);
                            }

                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

                            $this->getAppClass()->getDatabase()->update($dbPrefix . "users_xp", ["xp" => $dbCurrentXp + $recordReward['xp']], ["fuid" => $this->getUserID()]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
                            nxr(4, "Awarding $cat / $event ($score) = " . $recordReward['xp'] . " xp");
                            $state = 'delivered';
                        }

                        if ($recordReward['rid'] != "") {
                            $recordReward['description'] = $this->getAppClass()->getDatabase()->get($dbPrefix . "rewards", "description", ["rid" => $recordReward['rid']]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

                            $nukeOne = $this->getAppClass()->getDatabase()->select($dbPrefix . "reward_nuke", 'rid', ["AND" => ["nukeid" => $recordReward['rid'], "directional" => "true"]]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
                            if (count($nukeOne) > 0) {
                                foreach ($nukeOne as $nukeId) {
                                    if ($this->getAppClass()->getDatabase()->has($dbPrefix . "reward_queue", [
                                        "AND" => [
                                            'fuid' => $this->getUserID(),
                                            'reward' => $nukeId
                                        ]
                                    ])
                                    ) {
                                        $this->getAppClass()->getDatabase()->delete($dbPrefix . "reward_queue", [
                                            "AND" => [
                                                'fuid' => $this->getUserID(),
                                                'reward' => $nukeId
                                            ]
                                        ]);
                                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
                                    }
                                }
                            }

                            $nukeTwo = $this->getAppClass()->getDatabase()->select($dbPrefix . "reward_nuke", 'nukeid', ["AND" => ["rid" => $recordReward['rid'], "directional" => "false"]]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
                            if (count($nukeTwo) > 0) {
                                foreach ($nukeTwo as $nukeId) {
                                    if ($this->getAppClass()->getDatabase()->has($dbPrefix . "reward_queue", [
                                        "AND" => [
                                            'fuid' => $this->getUserID(),
                                            'reward' => $nukeId
                                        ]
                                    ])
                                    ) {
                                        $this->getAppClass()->getDatabase()->delete($dbPrefix . "reward_queue", [
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

                        $this->getAppClass()->getDatabase()->insert($dbPrefix . "reward_queue", [
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

    /**
     * @return String
     */
    public function getuserMinecraftID()
    {
        return $this->userMinecraftID;
    }

    /**
     * @param string $userMinecraftID
     *
     * @todo     Consider test case
     * @internal param String $userID
     */
    public function setuserMinecraftID($userMinecraftID)
    {
        $this->userMinecraftID = $userMinecraftID;
    }

    /**
     * @return array
     */
    public function queryMinecraftRewards()
    {
        $wmcKeyProvided = filter_input(INPUT_GET, 'wmc_key', FILTER_SANITIZE_STRING);
        $wmcKeyCorrect = $this->getAppClass()->getSetting("wmc_key", null, true);
        nxr(0, "Minecraft rewards Check");

        if ($wmcKeyProvided != $wmcKeyCorrect) {
            nxr(1, "Key doesnt match");

            return ["success" => false, "data" => ["msg" => "Incorrect key"]];
        }

        $databaseTable = $this->getAppClass()->getSetting("db_prefix", null, false);

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) == "GET") {
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

        } else if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) == "POST" && filter_input(INPUT_POST, 'processedOrders', FILTER_SANITIZE_STRING)) {

            $processedOrders = json_decode(filter_input(INPUT_POST, 'processedOrders', FILTER_SANITIZE_STRING));

            if (is_array($processedOrders)) {
                foreach ($processedOrders as $processedOrder) {
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
     * @return String
     */
    public function getUserID()
    {
        return $this->UserID;
    }

    /**
     *
     * @param String $userID
     */
    public function setUserID($userID)
    {
        $this->UserID = $userID;
    }
}
