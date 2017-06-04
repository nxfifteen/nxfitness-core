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
class RecordedMeal extends Modules
{

    private $debug = false;

    /**
     * @param $eventDetails
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);
        $meal = $this->getEventDetails();

        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        $yesterday = date("Y-m-d", strtotime($meal->logDate . " -1 days"));
        $rewardKey = sha1("mealRecordingFor" . $yesterday );

        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {
            $dbcalories = $this->getAppClass()->getDatabase()->sum($db_prefix . "food", "calories", ["AND" => ["date" => $yesterday, "user" => $this->getUserID()]]);
            $dbcarbs = $this->getAppClass()->getDatabase()->sum($db_prefix . "food", "carbs", ["AND" => ["date" => $yesterday, "user" => $this->getUserID()]]);
            $dbfat = $this->getAppClass()->getDatabase()->sum($db_prefix . "food", "fat", ["AND" => ["date" => $yesterday, "user" => $this->getUserID()]]);
            $dbfiber = $this->getAppClass()->getDatabase()->sum($db_prefix . "food", "fiber", ["AND" => ["date" => $yesterday, "user" => $this->getUserID()]]);
            $dbprotein = $this->getAppClass()->getDatabase()->sum($db_prefix . "food", "protein", ["AND" => ["date" => $yesterday, "user" => $this->getUserID()]]);
            $dbsodium = $this->getAppClass()->getDatabase()->sum($db_prefix . "food", "sodium", ["AND" => ["date" => $yesterday, "user" => $this->getUserID()]]);

            $goalcalories = $this->getAppClass()->getDatabase()->sum($db_prefix . "food_goals", 'calories', ["AND" => ["date" => $yesterday, "user" => $this->getUserID()]]);
            $goalcarbs = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_food_carbs", 310);
            $goalfat = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_food_fat", 70);
            $goalfiber = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_food_fiber", 30);
            $goalprotein = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_food_protein", 50);
            $goalsodium = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_food_sodium", 2300);

            $xp = 0;
            $inbox = [];
            if ($dbcalories > $goalcalories) {
                $xpDiff = (($dbcalories - $goalcalories) + 10) * -1;
                $xp = $xp + $xpDiff;
                $inbox[] = ["fa fa-cutlery", "bg-warning", "Over Eating", "Your eat " . ($dbcalories - $goalcalories) . " calories over your goal", round($xpDiff, 0) . "XP"];
            } else if ($dbcalories <= 1200) {
                $xpDiff = (($goalcalories - $dbcalories) + 20) * -1;
                $xp = $xp + $xpDiff;
                $inbox[] = ["fa fa-cutlery", "bg-danger", "Under Eating", "Your eat " . ($dbcalories - $goalcalories) . " calories too few calories", round($xpDiff, 0) . "XP"];
            } else {
                $xpDiff = 50;
                $xp = $xp + $xpDiff;
                $inbox[] = ["fa fa-cutlery", "bg-success", "Bang On", "You hit your goal!", round($xpDiff, 0) . "XP"];
            }
            if ($dbcarbs > $goalcarbs) {
                $xpDiff = (($dbcarbs - $goalcarbs) + 5) * -1;
                $xp = $xp + $xpDiff;
                $inbox[] = ["fa fa-cutlery", "bg-warning", "Carb Overload", "Your eatting too many carbs", round($xpDiff, 0) . "XP"];
            }
            if ($dbfat > $goalfat) {
                $xpDiff = (($dbfat - $goalfat) + 15) * -1;
                $xp = $xp + $xpDiff;
                $inbox[] = ["fa fa-cutlery", "bg-warning", "Fatty Fat Fat", "Your eatting too much fat", round($xpDiff, 0) . "XP"];
            }
            if ($dbfiber > $goalfiber) {
                $xpDiff = (($dbfiber - $goalfiber) + 1) * -1;
                $xp = $xp + $xpDiff;
                $inbox[] = ["fa fa-cutlery", "bg-warning", "Fiber is binding", "Your eatting too much fiber", round($xpDiff, 0) . "XP"];
            }
            if ($dbprotein > $goalprotein) {
                $xpDiff = (($dbprotein - $goalprotein) + 1) * -1;
                $xp = $xp + $xpDiff;
                $inbox[] = ["fa fa-cutlery", "bg-warning", "Protein Overload", "Your eatting too much protein", round($xpDiff, 0) . "XP"];
            }
            if ($dbsodium > $goalsodium) {
                $xpDiff = (($dbsodium - $goalsodium) + 8) * -1;
                $xp = $xp + $xpDiff;
                $inbox[] = ["fa fa-cutlery", "bg-warning", "Salt isn't a good thing!", "Your eatting too much salt", round($xpDiff, 0) . "XP"];
            }

            $this->getRewardsClass()->giveUserXp(round($xp, 0), $rewardKey);
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