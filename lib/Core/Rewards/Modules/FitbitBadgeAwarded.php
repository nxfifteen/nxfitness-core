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
class FitbitBadgeAwarded extends Modules
{

    private $debug = false;

    /**
     * @param $eventDetails
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);
        $badge = $this->getEventDetails();

        if ($badge->category == "Challenge" && $badge->description == "Challenge") {
            $this->triggerChallenge($badge);
        } else if ($badge->category == "Challenge" && $badge->description == "Adventure") {
            $this->triggerAdventure($badge);
        } else if (date("Y-m-d") == $badge->dateTime) {
            $rewardKey = sha1($badge->dateTime . $badge->encodedId . $badge->timesAchieved . $badge->name);

            if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {
                nxr(2, "Awarding " . $badge->name);
                $habitica = new Habitica($this->getAppClass(), $this->getUserID());
                $habitica->deliver([
                    "name" => "Earn A " . $badge->category . " Badge",
                    "system" => "habitica",
                    "source" => "nomie",
                    "description" => $badge->marketingDescription,
                    "reward" => '{"type": "habit", "tags": ["Health Wellness", "Exercise"], "priority": 1, "up": true, "down": false, "score": "up", "attribute": "per"}'
                ], 'pending', $rewardKey);
            } else {
                nxr(2, "Already rewarded " . $badge->name);
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

    /**
     * @param $badge
     */
    private function triggerChallenge($badge)
    {
        $rewardKey = sha1($badge->dateTime . $badge->encodedId . $badge->timesAchieved . $badge->name);

        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {
            nxr(2, "Awarding Challenge Badge " . $badge->name);
            $habitica = new Habitica($this->getAppClass(), $this->getUserID());
            $habitica->deliver([
                "name" => "Earn a Challenge Badge",
                "system" => "habitica",
                "source" => "nomie",
                "description" => $badge->marketingDescription,
                "reward" => '{"type": "habit", "tags": ["Health Wellness", "Exercise"], "priority": 2, "up": true, "down": false, "score": "up", "attribute": "per"}'
            ], 'pending', $rewardKey);
        } else {
            nxr(2, "Already rewarded Challenge Badge " . $badge->name);
        }
    }

    /**
     * @param $badge
     */
    private function triggerAdventure($badge)
    {
        $rewardKey = sha1($badge->dateTime . $badge->encodedId . $badge->timesAchieved . $badge->name);

        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {
            nxr(2, "Awarding Adventure Badge " . $badge->name);
            $habitica = new Habitica($this->getAppClass(), $this->getUserID());
            $habitica->deliver([
                "name" => "Earn an Adventure Badge",
                "system" => "habitica",
                "source" => "nomie",
                "description" => $badge->marketingDescription,
                "reward" => '{"type": "habit", "tags": ["Health Wellness", "Exercise"], "priority": 2, "up": true, "down": false, "score": "up", "attribute": "per"}'
            ], 'pending', $rewardKey);
        } else {
            nxr(2, "Already rewarded Adventure Badge " . $badge->name);
        }
    }
}