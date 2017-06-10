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
        $rewardKey = sha1("mealRecordingFor" . $yesterday);

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

            $health = 0;
            $inbox = [];
            if ($dbcalories > $goalcalories) {
                $xp = -20;
                $health = $health + -5;
                $inbox[] = ["fa fa-cutlery", "bg-warning", $yesterday . " - Over Eating", "Your eat " . ($dbcalories - $goalcalories) . " calories over your goal", "-20XP"];
            } else if ($dbcalories <= 1200) {
                $xp = 50;
                $health = $health + -10;
                $inbox[] = ["fa fa-cutlery", "bg-danger", $yesterday . " - Under Eating", "Your eat " . ($dbcalories - $goalcalories) . " calories too few calories", "-10XP"];
            } else {
                $xp = 50;
                $health = $health + 10;
                $inbox[] = ["fa fa-cutlery", "bg-success", $yesterday . " - Bang On", "You hit your goal!", "50XP"];
                $this->checkDB("meals", "food", "calories", $rewardKey . "Bang On");
            }
            /*if ($dbcarbs > $goalcarbs) {
                $health = $health + -1;
                $inbox[] = ["fa fa-cutlery", "bg-warning", $yesterday . " - Carb Overload", "Your eatting too many carbs", ""];
            }
            if ($dbfat > $goalfat) {
                $health = $health + -2;
                $inbox[] = ["fa fa-cutlery", "bg-warning", $yesterday . " - Fatty Fat Fat", "Your eatting too much fat", ""];
            }
            if ($dbfiber > $goalfiber) {
                $health = $health + -1;
                $inbox[] = ["fa fa-cutlery", "bg-warning", $yesterday . " - Fiber is binding", "Your eatting too much fiber", ""];
            }
            if ($dbprotein > $goalprotein) {
                $health = $health + -1;
                $inbox[] = ["fa fa-cutlery", "bg-warning", $yesterday . " - Protein Overload", "Your eatting too much protein", ""];
            }
            if ($dbsodium > $goalsodium) {
                $health = $health + -5;
                $inbox[] = ["fa fa-cutlery", "bg-warning", $yesterday . " - Salt isn't a good thing!", "Your eatting too much salt", ""];
            }*/

            /*$this->getRewardsClass()->issueAwards(["skill" => "health", "health" => $health, "xp" => $xp], $rewardKey, "pending", "Gaming");
            foreach ($inbox as $inboxItem) {
                $this->getRewardsClass()->setRewardReason($inboxItem[2] . "|" . $inboxItem[3]);
                $this->getRewardsClass()->notifyUser($inboxItem[0], $inboxItem[1], '+1 days');
            }*/
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