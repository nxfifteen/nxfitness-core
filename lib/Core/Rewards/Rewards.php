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

use Core\Core;

require_once(dirname(__FILE__) . "/../../autoloader.php");

/**
 * Rewards
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-RewardsMinecraft
 *            phpDocumentor wiki for RewardsSystem.
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class Rewards
{

    /**
     * @var String
     */
    private $UserID;

    /**
     * @var Core
     */
    private $AppClass;

    /**
     * Modules constructor.
     * @param $AppClass
     * @param $UserID
     */
    public function __construct($AppClass, $UserID) {
        $this->setAppClass($AppClass);
        $this->setUserID($UserID);
    }

    /**
     * @return String
     */
    private function getUserID()
    {
        return $this->UserID;
    }

    /**
     * @param String $UserID
     */
    private function setUserID($UserID)
    {
        $this->UserID = $UserID;
    }

    /**
     * @return Core
     */
    private function getAppClass()
    {
        return $this->AppClass;
    }

    /**
     * @param Core $AppClass
     */
    private function setAppClass($AppClass)
    {
        $this->AppClass = $AppClass;
    }

    /**
     * @param $rewardKey
     * @return bool
     */
    public function alreadyAwarded($rewardKey)
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
        return $this->getAppClass()->getDatabase()->has($db_prefix . "reward_queue", ["AND" => ['fuid' => $this->getUserID(), 'rkey[~]' => $rewardKey]]);
    }

    /**
     * @param $cat
     * @param $event
     * @param $score
     * @return array|\PDOStatement
     */
    public function getDBAwards($cat, $event, $score)
    {
        $returnRewards = [];
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
        $rewards = $this->getAppClass()->getDatabase()->select($db_prefix . "reward_map", [
            "rmid",
            "xp",
            "reward(rid)",
            "name",
        ], [
            "AND" => [
                "cat" => $cat,
                "event" => $event,
                "rule" => $score
            ]
        ]);

        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);
        if (is_array($rewards) && count($rewards) > 0) {

            foreach ($rewards as $reward) {
                if ((is_numeric($reward['xp']) && $reward['xp'] <> 0) || $reward['rid'] != "") {
                    array_push($returnRewards, $reward);
                }
            }

        }

        if (is_array($returnRewards) && count($returnRewards) > 0) {
            return $returnRewards;
        } else {
            return [];
        }
    }

    /**
     * @param $awardWhere
     * @return bool
     */
    public function hasDBAwards($awardWhere)
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
        return $this->getAppClass()->getDatabase()->has($db_prefix . "reward_map", $awardWhere);
    }

    /**
     * @param $xp
     */
    public function giveUserXp($xp)
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
        if (!$this->getAppClass()->getDatabase()->has($db_prefix . "users_xp", ['fuid' => $this->getUserID()])) {
            $this->getAppClass()->getDatabase()->insert($db_prefix . "users_xp", ["xp" => 0, "fuid" => $this->getUserID()]);
            $dbCurrentXp = 0;
        } else {
            $dbCurrentXp = $this->getAppClass()->getDatabase()->get($db_prefix . "users_xp", 'xp', ["fuid" => $this->getUserID()]);
        }

        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

        $this->getAppClass()->getDatabase()->update($db_prefix . "users_xp", ["xp" => $dbCurrentXp + $xp], ["fuid" => $this->getUserID()]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
    }

    /**
     * @param $rid
     */
    public function nukeConflictingAwards($rid)
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
        $nukeOne = $this->getAppClass()->getDatabase()->select($db_prefix . "reward_nuke", 'rid', ["AND" => ["nukeid" => $rid, "directional" => "true"]]);
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

        $nukeTwo = $this->getAppClass()->getDatabase()->select($db_prefix . "reward_nuke", 'nukeid', ["AND" => ["rid" => $rid, "directional" => "false"]]);
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
    }

    /**
     * @param $cat
     * @param $event
     * @param $score
     */
    public function createDBAwards($cat, $event, $score)
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
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

    /**
     * @param $recordReward
     * @param $state
     * @param $rewardKey
     */
    public function issueAwards($recordReward, $state, $rewardKey)
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
        $this->getAppClass()->getDatabase()->insert($db_prefix . "reward_queue", ["fuid" => $this->getUserID(), "state" => $state, "rmid" => $recordReward['rmid'], "reward" => $recordReward['rid'], "rkey" => $rewardKey]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
    }

    /**
     * @param $ico
     * @param $icoColour
     * @param $subject
     * @param $body
     * @param $bold
     * @param $expire
     */
    public function notifyUser($ico, $icoColour, $subject, $body, $bold, $expire)
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
        $this->getAppClass()->getDatabase()->insert($db_prefix . "inbox",
            [
                "fuid" => $this->getUserID(),
                "expires" => date("Y-m-d H:j:s", strtotime($expire)),
                "ico" => $ico,
                "icoColour" => $icoColour,
                "subject" => $subject,
                "body" => $body,
                "bold" => $bold
            ]
        );

        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
    }
}