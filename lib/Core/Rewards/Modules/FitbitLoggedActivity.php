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
class FitbitLoggedActivity extends Modules
{

    private $debug = false;

    /**
     * @param $eventDetails
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);
        $activity = $this->getEventDetails();

        $checkForThese = [
            "Aerobic",
            "Bicycling",
            "Bodyweight",
            "Calisthenics",
            "Circuit Training",
            "Elliptical Trainer",
            "Hike",
            "Meditating",
            "Outdoor Bike",
            "Push-ups",
            "Run",
            "Sit-ups",
            "Skiing",
            "Stationary bike",
            "Strength training",
            "Swimming",
            "Tai chi",
            "Treadmill",
            "Walk",
            "Workout",
            "Yoga"
        ];
        if ($activity->activityName != "auto_detected" && in_array($activity->activityName, $checkForThese) && !$this->getRewardsClass()->alreadyAwarded($activity->logId)) {
            $currentDate = new DateTime ('now');
            $currentDate = $currentDate->format("Y-m-d");
            $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

            $sql_search = [
                "user" => $this->getUserID(),
                "activityName[~]" => $activity->activityName,
                "startDate" => $currentDate,
                "logType[!]" => 'auto_detected'
            ];
            $minMaxAvg = [];
            $minMaxAvg['min'] = ($this->getAppClass()->getDatabase()->min($db_prefix . "activity_log", "activeDuration", ["AND" => $sql_search]) / 1000) / 60;
            $minMaxAvg['avg'] = ($this->getAppClass()->getDatabase()->avg($db_prefix . "activity_log", "activeDuration", ["AND" => $sql_search]) / 1000) / 60;
            $minMaxAvg['max'] = ($this->getAppClass()->getDatabase()->max($db_prefix . "activity_log", "activeDuration", ["AND" => $sql_search]) / 1000) / 60;

            $minMaxAvg['min2avg'] = (($minMaxAvg['avg'] - $minMaxAvg['min']) / 2) + $minMaxAvg['min'];
            $minMaxAvg['avg2max'] = (($minMaxAvg['max'] - $minMaxAvg['avg']) / 2) + $minMaxAvg['avg'];

            $activeDuration = $activity->duration / 1000 / 60;

            if ($activeDuration == $minMaxAvg['max']) {
                $awardMade = $this->checkDB("activity", strtolower($activity->activityName), "max", $activity->logId);
                if (!$awardMade) $this->checkDB("activity", 'other', "max", $activity->logId);
            } else if ($activeDuration >= $minMaxAvg['avg2max']) {
                $awardMade = $this->checkDB("activity", strtolower($activity->activityName), "avg2max", $activity->logId);
                if (!$awardMade) $this->checkDB("activity", 'other', "avg2max", $activity->logId);
            } else if ($activeDuration >= $minMaxAvg['avg']) {
                $awardMade = $this->checkDB("activity", strtolower($activity->activityName), "avg", $activity->logId);
                if (!$awardMade) $this->checkDB("activity", 'other', "avg", $activity->logId);
            } else if ($activeDuration >= $minMaxAvg['min2avg']) {
                $awardMade = $this->checkDB("activity", strtolower($activity->activityName), "min2avg", $activity->logId);
                if (!$awardMade) $this->checkDB("activity", 'other', "min2avg", $activity->logId);
            } else {
                $awardMade = $this->checkDB("activity", strtolower($activity->activityName), "other", $activity->logId);
                if (!$awardMade) $this->checkDB("activity", 'other', "other", $activity->logId);
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