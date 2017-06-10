<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

namespace Core\Rewards\Modules;

use Core\Rewards\Modules;

require_once(dirname(__FILE__) . "/../Modules.php");
require_once(dirname(__FILE__) . "/../../../autoloader.php");

/**
 * Nomie
 *
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class RecordedWater extends Modules
{

    private $debug = false;

    /**
     * @param $eventDetails
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);

        $yesterday = date("Y-m-d", strtotime("-1 days"));
        $rewardKey = sha1("waterRecordingFor" . $yesterday);

        $goal = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_water", '200');

        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {
            $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
            $water = $this->getAppClass()->getDatabase()->get($db_prefix . "water", "liquid", ["AND" => ["user" => $this->getUserID(), "date" => $yesterday]]);

            $inbox = [];
            if ($water < $goal) {
                $xp = -60;
                $health = -8;
                $inbox[] = ["fa fa-beer", "bg-warning", $yesterday . " - You need water!", "Drank $water ml out of $goal ml, drink more!", ""];
                $this->checkDB("meals", "water", "drank_down", $rewardKey . "Under");
            } else {
                $xp = 50;
                $health = 10;
                $inbox[] = ["fa fa-beer", "bg-success", $yesterday . " - Bang On", "You hit your goal!", ""];
                $this->checkDB("meals", "water", "drank_up", $rewardKey . "Bang On");
            }

            $this->getRewardsClass()->issueAwards(["skill" => "health", "health" => $health, "xp" => $xp], $rewardKey, "pending", "Gaming");
            foreach ($inbox as $inboxItem) {
                $this->getRewardsClass()->setRewardReason($inboxItem[2] . "|" . $inboxItem[3]);
                $this->getRewardsClass()->notifyUser($inboxItem[0], $inboxItem[1], '+1 days');
            }
        }

    }

    /**
     * @param mixed $eventDetails
     */
    private function setEventDetails($eventDetails)
    {
        $this->eventDetails = $eventDetails;
    }
}