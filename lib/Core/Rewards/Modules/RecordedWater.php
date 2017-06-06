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

        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey.rand(0,50))) {
            $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
            $water = $this->getAppClass()->getDatabase()->get($db_prefix . "water", "liquid", ["AND" => ["user" => $this->getUserID(), "date" => $yesterday]]);

            $xp = 0;
            $health = 0;
            $inbox = [];
            if ($water < $goal) {
                $xpDiff = round((($goal - $water) / 2) * -1, 0);
                $xp = $xp + $xpDiff;
                $health = -8;
                $inbox[] = ["fa fa-beer", "bg-warning", "You need water!", "Drank $water ml out of $goal ml, drink more!", round($xpDiff, 0) . "XP"];
            } else {
                $xpDiff = 50;
                $xp = $xp + $xpDiff;
                $health = 10;
                $inbox[] = ["fa fa-beer", "bg-success", "Bang On", "You hit your goal!", round($xpDiff, 0) . "XP"];
            }

            $this->getRewardsClass()->issueAwards(["skill" => "health", "health" => $health, "xp" => $xp], $rewardKey, "pending", "Gaming");
            foreach ($inbox as $inboxItem) {
                $this->getRewardsClass()->notifyUser($inboxItem[0], $inboxItem[1], $inboxItem[2], $inboxItem[3], $inboxItem[4], '+1 days');
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