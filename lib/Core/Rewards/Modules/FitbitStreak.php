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
class FitbitStreak extends Modules
{

    private $debug = false;

    /**
     * @param $eventDetails
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);
        $eventDetails = $this->getEventDetails();

        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        $rewardKey = sha1($eventDetails['goal'] . $eventDetails['streak_start'] . $eventDetails['days_between']);

        if (!$this->getRewardsClass()->alreadyAwarded(sha1($rewardKey . "db"))) {
            $this->checkDB("streak", $eventDetails['goal'], $eventDetails['days_between'], sha1($rewardKey . "db"));
        }

        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {
            $avg = round($this->getAppClass()->getDatabase()->avg($db_prefix . "streak_goal", ['length'], ["fuid" => $this->getUserID()]), 0);
            $max = round($this->getAppClass()->getDatabase()->get($db_prefix . "streak_goal", 'length', ["AND" => ["fuid" => $this->getUserID(), "goal" => "steps", "end_date[!]" => null], "ORDER" => ["length" => "DESC"]]), 0);
            $last = round($this->getAppClass()->getDatabase()->get($db_prefix . "streak_goal", 'length', ["AND" => ["fuid" => $this->getUserID(), "goal" => "steps", "end_date[!]" => null], "ORDER" => ["start_date" => "DESC"]]), 0);

            if ($eventDetails['days_between'] == $max) {
                $muliplier = $max - $avg;
            } else if ($eventDetails['days_between'] > $last) {
                $muliplier = $eventDetails['days_between'] - $last;
            } else if ($eventDetails['days_between'] > $avg) {
                $muliplier = $eventDetails['days_between'] - $avg;
            } else {
                $muliplier = 0;
            }

            $this->getRewardsClass()->issueAwards(["skill" => "rappid fire", "health" => 2 * $eventDetails['days_between'] + $muliplier, "xp" => 5 * $eventDetails['days_between'] + $muliplier], $rewardKey, "pending", "Gaming");
            $this->getRewardsClass()->notifyUser("fa fa-git", "bg-success", "Streaking!!!", "Your " . $eventDetails['goal'] . " streak ran " . $eventDetails['days_between'] . " days", 5 * $eventDetails['days_between'] . "XP", '+5 hours');
        }
    }

    /**
     * @param array $eventDetails
     */
    private function setEventDetails($eventDetails)
    {
        $this->eventDetails = [
            "goal" => $eventDetails[0],
            "days_between" => $eventDetails[1],
            "streak_start" => $eventDetails[2]
        ];
    }
}