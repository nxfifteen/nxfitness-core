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
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */

namespace Core\Rewards\Modules;

use Core\Rewards\Delivery\Habitica;
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

        if (count($eventDetails) == 4) {
            $this->eventDetails["streak_ended"] = $eventDetails[3];
        } else {
            $this->eventDetails["streak_ended"] = false;
        }
    }

    /**
     * @param array $eventDetails Array holding details of award to issue
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
            $max = round($this->getAppClass()->getDatabase()->get($db_prefix . "streak_goal", 'length', ["AND" => ["fuid" => $this->getUserID(), "goal" => $eventDetails['goal'], "end_date[!]" => null], "ORDER" => ["length" => "DESC"]]), 0);
            $last = round($this->getAppClass()->getDatabase()->get($db_prefix . "streak_goal", 'length', ["AND" => ["fuid" => $this->getUserID(), "goal" => $eventDetails['goal'], "end_date[!]" => null], "ORDER" => ["start_date" => "DESC"]]), 0);

            $habitica = new Habitica($this->getAppClass(), $this->getUserID());
            if ($habitica->isValidUser() && $habitica->getStatus() == 'up') {
                if ($this->eventDetails["streak_ended"]) {
                    $habitica->_delete(":mans_shoe: Beat Your Last Streak");
                    $habitica->_delete(":mans_shoe: Beat Your Average Streak");
                    $habitica->_delete(":mans_shoe: Beat Your Longest Streak");
                } else if ($eventDetails['days_between'] == $max) {
                    $habitica->deliver([
                        "name" => ":mans_shoe: Beat Your Longest Streak",
                        "system" => "habitica",
                        "description" => "Your Beat Your Longest Steak",
                        "reward" => '{"type": "todo", "tags": ["Exercise", "Personal Goals"], "priority": 2, "up": true, "down": false, "score": "up"}'
                    ], "pending", $rewardKey);
                } else if ($eventDetails['days_between'] > $last) {
                    $habitica->deliver([
                        "name" => ":mans_shoe: Beat Your Last Streak",
                        "system" => "habitica",
                        "description" => "Your Beat Your Last Steak",
                        "reward" => '{"type": "todo", "tags": ["Exercise", "Personal Goals"], "priority": 1, "up": true, "down": false, "score": "up"}'
                    ], "pending", $rewardKey);
                } else if ($eventDetails['days_between'] > $avg) {
                    $habitica->deliver([
                        "name" => ":mans_shoe: Beat Your Average Streak",
                        "system" => "habitica",
                        "description" => "Your Beat Your Average",
                        "reward" => '{"type": "todo", "tags": ["Exercise", "Personal Goals"], "priority": 1.5, "up": true, "down": false, "score": "up"}'
                    ], "pending", $rewardKey);
                } else if ($eventDetails['days_between'] > 0 && !$this->eventDetails["streak_ended"]) {
                    $tasks = $habitica->_create("todo", ":mans_shoe: Beat Your Last Streak", json_decode('{"type": "todo", "tags": ["Exercise", "Personal Goals"], "priority": 1, "notes": "Your last streak was ' . $last . ' days, beat that!", "up": true, "down": false, "score": "up", "date": "' . date("m/d/Y 23:59:59", strtotime($eventDetails['streak_start'] . ' +' . $last . ' days')) . '"}', true));
                    if (is_array($tasks)) {
                        $tasks = $tasks['_id'];
                        for ($i = 1; $i < $last; $i++) {
                            $habitica->getHabitRPHPG()->_request("post", "tasks/" . $tasks . "/checklist", ["text" => "Beat Your Steps on " . date("M jS", strtotime($eventDetails['streak_start'] . ' +' . $i . ' days'))]);
                        }
                    }

                    $tasks = $habitica->_create("todo", ":mans_shoe: Beat Your Average Streak", json_decode('{"type": "todo", "tags": ["Exercise", "Personal Goals"], "priority": 1.5, "notes": "Your average streak is ' . $avg . ' days, beat that!", "up": true, "down": false, "score": "up", "date": "' . date("m/d/Y 23:59:59", strtotime($eventDetails['streak_start'] . ' +' . $avg . ' days')) . '"}', true));
                    if (is_array($tasks)) {
                        $tasks = $tasks['_id'];
                        for ($i = 1; $i < $avg; $i++) {
                            $habitica->getHabitRPHPG()->_request("post", "tasks/" . $tasks . "/checklist", ["text" => "Beat Your Steps on " . date("M jS", strtotime($eventDetails['streak_start'] . ' +' . $i . ' days'))]);
                        }
                    }

                    $tasks = $habitica->_create("todo", ":mans_shoe: Beat Your Longest Streak", json_decode('{"type": "todo", "tags": ["Exercise", "Personal Goals"], "priority": 2, "notes": "Your longest streak was ' . $max . ' days, beat that!", "up": true, "down": false, "score": "up", "date": "' . date("m/d/Y 23:59:59", strtotime($eventDetails['streak_start'] . ' +' . $max . ' days')) . '"}', true));
                    if (is_array($tasks)) {
                        $tasks = $tasks['_id'];
                        for ($i = 1; $i < $max; $i++) {
                            $habitica->getHabitRPHPG()->_request("post", "tasks/" . $tasks . "/checklist", ["text" => "Beat Your Steps on " . date("M jS", strtotime($eventDetails['streak_start'] . ' +' . $i . ' days'))]);
                        }
                    }
                }
            }
        }
    }
}