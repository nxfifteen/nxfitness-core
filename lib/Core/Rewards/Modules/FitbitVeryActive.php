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
class FitbitVeryActive extends Modules
{

    private $debug = false;

    /**
     * @param $eventDetails
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);
        $eventDetails = $this->getEventDetails();
        if ($eventDetails >= 1) {

            $currentDate = new DateTime ('now');
            $currentDate = $currentDate->format("Y-m-d");

            $rewardKey = $currentDate . "veryactive" . "reached";

            if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {

                $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
                $recordedValue = $eventDetails;
                $recordedTarget = $this->getAppClass()->getDatabase()->get($db_prefix . "steps_goals", "activeMinutes", ["AND" => ["user" => $this->getUserID(), "date" => $currentDate]]);
                if (!is_numeric($recordedTarget) || $recordedTarget <= 0) {
                    $recordedTarget = round($this->getAppClass()->getUserSetting($this->getUserID(), "goal_activity"), 30);
                }

                if ($recordedValue >= $recordedTarget) {
                    $this->checkDB("goal", "veryactive", "reached", $rewardKey);
                }
            }
        }
    }

    /**
     * @param string $eventDetails
     */
    private function setEventDetails($eventDetails)
    {
        $this->eventDetails = $eventDetails;
    }
}