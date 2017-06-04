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

require_once( dirname( __FILE__ ) . "/../../autoloader.php" );

/**
 * Modules
 *
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class Delivery
{

    /**
     * @var Core
     */
    private $AppClass;

    /**
     * @var String
     */
    private $UserID;

    /**
     * Delivery constructor.
     * @param $AppClass
     * @param $UserID
     */
    public function __construct($AppClass, $UserID) {
        $this->setAppClass($AppClass);
        $this->setUserID($UserID);
    }

    /**
     * @param array $recordReward
     * @param string $state
     * @param string $rewardKey
     */
    public function deliver($recordReward, $state, $rewardKey) {
        $this->recordDevlivery($recordReward, $state, $rewardKey);
    }

    /**
     * @param $recordReward
     * @param $state
     * @param $rewardKey
     */
    protected function recordDevlivery($recordReward, $state, $rewardKey) {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        if (!array_key_exists("rmid", $recordReward)) {
            $recordReward['rmid'] = null;
        }
        if (!array_key_exists("rid", $recordReward)) {
            $recordReward['rid'] = null;
        }

        $this->getAppClass()->getDatabase()->insert($db_prefix . "reward_queue", ["fuid" => $this->getUserID(), "state" => $state, "rmid" => $recordReward['rmid'], "reward" => $recordReward['rid'], "rkey" => $rewardKey]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
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
     * @return String
     */
    protected function getUserID()
    {
        return $this->UserID;
    }

    /**
     * @param String $UserID
     */
    protected function setUserID($UserID)
    {
        $this->UserID = $UserID;
    }

}