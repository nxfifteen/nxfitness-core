<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Rewards
 * @subpackage  Modules
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

namespace Core\Rewards\Modules;

use Core\Rewards\Modules;
use DateTime;

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
class FitbitTracker extends Modules
{

    private $debug = false;

    /**
     * @param array $eventDetails Array holding details of award to issue
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);
        $eventDetails = $this->getEventDetails();

        $goalsToCheck = ["steps", "floors", "distance"];

        if (in_array($eventDetails['trigger'], $goalsToCheck)) {
            if (date('Y-m-d') == $eventDetails['date']) {
                // Crushed Step Goal
                if (!$this->crushedGoal($eventDetails['trigger'], $eventDetails['value'])) {
                    // Smashed Step Goal
                    if (!$this->smashedGoal($eventDetails['trigger'], $eventDetails['value'])) {
                        // Reached Step Goal
                        if ($this->reachedGoal($eventDetails['trigger'], $eventDetails['value'])) {
                            $this->checkDB("goal", $eventDetails['trigger'], "reached", date('Y-m-d') . $eventDetails['trigger'] . "reached");
                        }
                    } else {
                        $this->checkDB("goal", $eventDetails['trigger'], "smashed", date('Y-m-d') . $eventDetails['trigger'] . "smashed");
                    }
                } else {
                    $this->checkDB("goal", $eventDetails['trigger'], "crushed", date('Y-m-d') . $eventDetails['trigger'] . "crushed");
                }

                if ($eventDetails['trigger'] == "steps") {
                    $this->triggerSteps();
                } else {

                    $yesterday = date('Y-m-d', strtotime('-1 days'));
                    $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
                    $eventDetails['value'] = $this->getAppClass()->getDatabase()->get($db_prefix . "steps", $eventDetails['trigger'], ["AND" => ["user" => $this->getUserID(), "date" => $yesterday]]);

                    if ($eventDetails['trigger'] == "distance") {
                        $divider = 10;
                        $cutOff = 10;
                    } else if ($eventDetails['trigger'] == "floors") {
                        $divider = 10;
                        $cutOff = 20;
                    } else {
                        $divider = 10;
                        $cutOff = 0;
                    }

                    if ($eventDetails['value'] > $cutOff) {
                        $yesterdaySteps = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null, false) . "steps_goals",
                            $eventDetails['trigger'], [
                                "AND" => [
                                    "user" => $this->getUserID(),
                                    "date" => $yesterday
                                ]
                            ]);

                        $recordedValue = round($yesterdaySteps, 3);
                        $hundredth = round($recordedValue / $divider, 0);

                        $rewardKey = sha1($yesterday . $eventDetails['trigger'] . $hundredth);

                        if (!$this->getRewardsClass()->alreadyAwarded(sha1($rewardKey . "db"))) {
                            $this->checkDB("hundredth", $eventDetails['trigger'], $hundredth, sha1($rewardKey . "db"));
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $eventDetails
     */
    private function setEventDetails($eventDetails)
    {
        $this->eventDetails = [
            "date" => $eventDetails[0],
            "trigger" => $eventDetails[1],
            "value" => $eventDetails[2]
        ];
    }

    /**
     * @param string $goal
     * @param string $value
     *
     * @return bool
     */
    private function crushedGoal($goal, $value)
    {
        return $this->reachedGoal($goal, $value, 2);
    }

    /**
     * @param string $goal
     * @param int $value
     * @param int $multiplyer
     *
     * @return bool
     */
    private function reachedGoal($goal, $value, $multiplyer = 1)
    {
        $currentDate = new DateTime ('now');
        $currentDate = $currentDate->format("Y-m-d");
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
        if ($value >= 1) {
            $recordedValue = $value;
            $recordedTarget = round($this->getAppClass()->getDatabase()->get($db_prefix . "steps_goals", $goal,
                [
                    "AND" => [
                        "user" => $this->getUserID(),
                        "date" => $currentDate
                    ]
                ]), 3);
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
     * @return bool
     */
    private function smashedGoal($goal, $value)
    {
        return $this->reachedGoal($goal, $value, 1.5);
    }

    /**
     *
     */
    private function triggerSteps()
    {
        $eventDetails = $this->getEventDetails();

        $sysRewards = $this->getRewardsClass()->getCatRewards('fitbit_tracker');

        foreach ($sysRewards['steps'] as $score => $sysReward) {
            $scoreRange = explode(":", $score);
            if ($eventDetails['value'] >= $scoreRange[0]) {
                if (!$this->getRewardsClass()->alreadyAwarded($eventDetails['trigger'] . $eventDetails['date'] . $score)) {
                    nxr(3, "Awarding " . $eventDetails['trigger'] . " " . $eventDetails['date'] . " " . $score);
                    $this->checkDB("fitbit_tracker", "steps", $score, $eventDetails['trigger'] . $eventDetails['date'] . $score);
                } else {
                    nxr(3, "Already rewarded " . $eventDetails['trigger'] . " " . $eventDetails['date'] . " " . $score);
                }
            }
        }
    }
}