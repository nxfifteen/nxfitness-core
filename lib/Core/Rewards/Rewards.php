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
    public function __construct($AppClass, $UserID)
    {
        $this->setAppClass($AppClass);
        $this->setUserID($UserID);
    }

    /**
     * @param Core $AppClass
     */
    private function setAppClass($AppClass)
    {
        $this->AppClass = $AppClass;
    }

    /**
     * @param String $UserID
     */
    private function setUserID($UserID)
    {
        $this->UserID = $UserID;
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
     * @return Core
     */
    private function getAppClass()
    {
        return $this->AppClass;
    }

    /**
     * @return String
     */
    private function getUserID()
    {
        return $this->UserID;
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
     * @param $rewardKey
     */
    public function giveUserXp($xp, $rewardKey)
    {
        $this->issueAwards($xp, $rewardKey, "pending", "Xp");
    }

    /**
     * @param array $recordReward
     * @param string $state
     * @param string $rewardKey
     * @param string $delivery
     */
    public function issueAwards($recordReward, $rewardKey, $state = "pending", $delivery = "Default")
    {

        $className = "Core\\Rewards\\Delivery";
        $includePath = dirname(__FILE__);
        $includeFile = $includePath . DIRECTORY_SEPARATOR . "Delivery.php";

        if ($delivery != "Default") {
            $delivery = ucwords($delivery);

            if (file_exists($includePath . DIRECTORY_SEPARATOR . "Delivery" . DIRECTORY_SEPARATOR . $delivery . ".php")) {
                $includeFile = $includePath . DIRECTORY_SEPARATOR . "Delivery" . DIRECTORY_SEPARATOR . $delivery . ".php";
                $className = "Core\\Rewards\\Delivery\\" . $delivery;
            } else {
                nxr(2, "Create a new class 'Core\\Rewards\\Delivery\\$delivery' in " . $includePath . DIRECTORY_SEPARATOR . "Delivery" . DIRECTORY_SEPARATOR);
            }
        }

        /** @noinspection PhpIncludeInspection */
        require_once($includeFile);
        $rewardSystem = new $className($this->getAppClass(), $this->getUserID());
        /** @noinspection PhpUndefinedMethodInspection */
        $rewardSystem->deliver($recordReward, $state, $rewardKey);
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
                "expires" => date("Y-m-d H:i:s", strtotime($expire)),
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