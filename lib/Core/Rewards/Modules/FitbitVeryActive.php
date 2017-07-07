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
class FitbitVeryActive extends Modules
{

    private $debug = false;

    /**
     * @param array $eventDetails Array holding details of award to issue
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);
        $eventDetails = $this->getEventDetails();
        if ($eventDetails >= 1) {
            $rewardKey = strtotime(' -1 days') . "veryactive" . "reached";

            if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {

                $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
                $recordedValue = $eventDetails;
                $recordedTarget = $this->getAppClass()->getDatabase()->get($db_prefix . "steps_goals", "activeMinutes", ["AND" => ["user" => $this->getUserID(), "date" => date("Y-m-d", strtotime(' -1 days'))]]);
                if (!is_numeric($recordedTarget) || $recordedTarget <= 0) {
                    $recordedTarget = round($this->getAppClass()->getUserSetting($this->getUserID(), "goal_activity"), 30);
                }

                /*if ($recordedValue >= $recordedTarget * 2) {
                    $this->checkDB("goal", "veryactive", "crushed", $rewardKey);
                } else if ($recordedValue >= $recordedTarget * 1.5) {
                    $this->checkDB("goal", "veryactive", "smashed", $rewardKey);
                } else */

                if ($recordedValue >= $recordedTarget) {
                    $this->checkDB("goal", "veryactive", "reached", $rewardKey);
                }
            }
        }
    }

    /**
     * @param array $eventDetails
     */
    private function setEventDetails($eventDetails)
    {
        $this->eventDetails = $eventDetails;
    }
}