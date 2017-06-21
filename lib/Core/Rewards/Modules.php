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
use DateTime;

require_once(dirname(__FILE__) . "/../../autoloader.php");

/**
 * Modules
 *
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class Modules
{

    /**
     * @var array
     */
    protected $eventDetails;
    /**
     * @var String
     */
    protected $UserID;
    /**
     * @var Rewards
     */
    protected $RewardsClass;
    private $createNewAwards = false;
    /**
     * @var Core
     */
    private $AppClass;

    /**
     * Modules constructor.
     * @param Core $AppClass Core API Class
     * @param string $UserID Fitbit user ID
     */
    public function __construct($AppClass, $UserID)
    {
        $this->setAppClass($AppClass);
        $this->setUserID($UserID);
        $this->setRewardsClass(new Rewards($AppClass, $UserID));
    }

    /**
     * @return array|string|object
     */
    protected function getEventDetails()
    {
        return $this->eventDetails;
    }

    /**
     * @param string $cat Reward Category
     * @param string $event Reward Event
     * @param string $score Reward Score
     * @param null|string $rewardKey Reward Key
     * @return bool
     */
    protected function checkDB($cat, $event, $score, $rewardKey = null)
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        if (is_null($rewardKey)) {
            $currentDate = new DateTime ('now');
            $rewardKey = $cat . $event . $score . $currentDate->format("Y-m-d");
        }

        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {
            if ($this->getRewardsClass()->hasDBAwards(["AND" => ['cat' => $cat, 'event' => $event, 'rule' => $score]])) {
                $rewards = $this->getRewardsClass()->getDBAwards($cat, $event, $score);

                if (count($rewards) > 0) {
                    foreach ($rewards as $recordReward) {

                        if (array_key_exists("system", $recordReward)) {

                            $this->getRewardsClass()->issueAwards($recordReward, $rewardKey, "pending", $recordReward['system']);
                            $this->getRewardsClass()->setRewardReason($recordReward['name'] . "|" . $recordReward['description']);

                            nxr(3, "File Award Processed '" . $recordReward['name'] . "', " . $recordReward['description']);
                        } else {
                            $state = 'noaward';
                            $delivery = "Default";

                            if (array_key_exists("rid", $recordReward) && $recordReward['rid'] != "") {
                                $dbReward = $this->getAppClass()->getDatabase()->get($db_prefix . "rewards", ["description", "system", "reward"], ["rid" => $recordReward['rid']]);

                                $recordReward['descriptionRid'] = $dbReward['description'];
                                $recordReward['reward'] = $dbReward['reward'];
                                $delivery = $dbReward['system'];

                                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

                                $this->getRewardsClass()->nukeConflictingAwards($recordReward['rid']);

                                $state = 'pending';
                            } else {
                                $recordReward['rid'] = null;
                                $recordReward['descriptionRid'] = "";
                            }

                            if ($recordReward['descriptionRid'] != "") {
                                $recordReward['description'] = $recordReward['descriptionRid'];
                            } else {
                                $recordReward['description'] = "";
                            }

                            if ($recordReward['name'] == "") {
                                $recordReward['name'] = "$cat, $event - $score";
                            }

                            if ($state != "noaward") {
                                $this->getRewardsClass()->issueAwards($recordReward, $rewardKey, $state, $delivery);
                                $this->getRewardsClass()->setRewardReason($recordReward['name'] . "|" . $recordReward['description']);

                                nxr(3, "DB Award Processed '" . $recordReward['name'] . "', " . $recordReward['description']);
                            }
                        }
                    }

                    return true;
                }

            } else if ($this->createNewAwards) {
                $this->getRewardsClass()->createDBAwards($cat, $event, $score);
                nxr(3, "Award Created for $cat, $event - $score");
            }
        } else {
            return true;
        }

        return false;
    }

    /**
     * @return Core
     */
    protected function getAppClass()
    {
        return $this->AppClass;
    }

    /**
     * @param Core $AppClass
     */
    protected function setAppClass($AppClass)
    {
        $this->AppClass = $AppClass;
    }

    /**
     * @return Rewards
     */
    protected function getRewardsClass()
    {
        return $this->RewardsClass;
    }

    /**
     * @param Rewards $RewardsClass
     */
    protected function setRewardsClass($RewardsClass)
    {
        $this->RewardsClass = $RewardsClass;
    }

    /**
     *
     */
    protected function cleanupQueue()
    {
        $prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
        $this->getAppClass()->getDatabase()->delete($prefix . "reward_queue",
            ["AND" => ["fuid" => $this->getUserID(), "state" => "delivered", "date[<]" => date('Y-m-d', strtotime(' -14 days'))]]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);
    }

    /**
     * @return String
     */
    protected function getUserID()
    {
        return $this->UserID;
    }

    /**
     *
     * @param String $UserID
     */
    protected function setUserID($UserID)
    {
        $this->UserID = $UserID;
    }

}