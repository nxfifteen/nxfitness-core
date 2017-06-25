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
class RecordedMeal extends Modules
{

    private $debug = false;

    /**
     * @param array $eventDetails Array holding details of award to issue
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);

        $this->checkYesterdaysCalories();
        if ($eventDetails->loggedFood->name != "Snacks Summary") {
            $this->checkMealLogged();
            $this->checkMealHealthynessLogged();
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
     *
     */
    private function checkMealLogged() {
        $meal = $this->getEventDetails();

        $mealKey = strtolower(str_ireplace(" Summary", "", $meal->loggedFood->name));
        $rewardKey = $meal->loggedFood->name . $meal->logDate;

        nxr(3, "New meal - " . $meal->loggedFood->name);
        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {
            $this->checkDB("meals", "logged", $mealKey, $rewardKey);
        }

    }

    /**
     *
     */
    private function checkMealHealthynessLogged() {
        $meal = $this->getEventDetails();

        $rewardKey = $meal->loggedFood->name . "healthy" . $meal->logDate;
        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {

            $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
            $mealKey = strtolower(str_ireplace(" Summary", "", $meal->loggedFood->name)) . "_healthy";

            if ($meal->loggedFood->name == "Breakfast Summary") {
                if ($this->getAppClass()->getDatabase()->has($db_prefix . "food", ["AND" => ["meal" => "Lunch Summary", "date" => $meal->logDate, "user" => $this->getUserID()]])) {
                    if ($this->wasMealHealthy()) {
                        $this->checkDB("meals", "logged", $mealKey, $rewardKey);
                    }
                }
            } else if ($meal->loggedFood->name == "Lunch Summary") {
                if ($this->getAppClass()->getDatabase()->has($db_prefix . "food", ["AND" => ["meal" => "Dinner Summary", "date" => $meal->logDate, "user" => $this->getUserID()]])) {
                    if ($this->wasMealHealthy()) {
                        $this->checkDB("meals", "logged", $mealKey, $rewardKey);
                    }
                }
            } else if ($meal->loggedFood->name == "Dinner Summary" && date("H") > 21) {
                if ($this->wasMealHealthy()) {
                    $this->checkDB("meals", "logged", $mealKey, $rewardKey);
                }
            }
        }
    }

    /**
     * @return bool
     */
    private function wasMealHealthy() {
        $meal = $this->getEventDetails();
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        $mealKey = strtolower(str_ireplace(" Summary", "", $meal->loggedFood->name));

        $totalCalories = $meal->nutritionalValues->calories;

        $mealQuota = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_by_$mealKey", 33);

        $calories = $this->getAppClass()->getDatabase()->sum($db_prefix . "food_goals", 'calories', ["AND" => ["date" => $meal->logDate, "user" => $this->getUserID()]]);
        $calories_per = round(($totalCalories / $calories) * 100, 0);
        $carbs = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_food_carbs", 310);
        $carbs_per = round(($meal->nutritionalValues->carbs / $carbs) * 100, 0);
        $fat = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_food_fat", 70);
        $fat_per = round(($meal->nutritionalValues->fat / $fat) * 100, 0);
        $fiber = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_food_fiber", 30);
        $fiber_per = round(($meal->nutritionalValues->fiber / $fiber) * 100, 0);
        $protein = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_food_protein", 50);
        $protein_per = round(($meal->nutritionalValues->protein / $protein) * 100, 0);
        $sodium = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_food_sodium", 2300);
        $sodium_per = round(($meal->nutritionalValues->sodium / $sodium) * 100, 0);

        //nxr(4, "New Healthy ($mealKey, quotoa $mealQuota) - " . $meal->loggedFood->name);
        //nxr(5, "goal_food_calories " . $totalCalories);
        if ($calories_per > $mealQuota) {
            return false;
        }
        //nxr(5, "goal_food_carbs    " . $carbs_per);
        if ($carbs_per > $mealQuota) {
            return false;
        }
        //nxr(5, "goal_food_fat      " . $fat_per);
        if ($fat_per > $mealQuota) {
            return false;
        }
        //nxr(5, "goal_food_fiber    " . $fiber_per);
        if ($fiber_per > $mealQuota) {
            return false;
        }
        //nxr(5, "goal_food_protein  " . $protein_per);
        if ($protein_per > $mealQuota) {
            return false;
        }
        //nxr(5, "goal_food_sodium   " . $sodium_per);
        if ($sodium_per > $mealQuota) {
            return false;
        }

        return true;
    }

    /**
     *
     */
    private function checkYesterdaysCalories() {
        $meal = $this->getEventDetails();
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        $yesterday = date("Y-m-d", strtotime($meal->logDate . " -1 days"));
        $rewardKey = sha1("mealRecordingFor" . $yesterday);

        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {
            $dbcalories = $this->getAppClass()->getDatabase()->sum($db_prefix . "food", "calories", ["AND" => ["date" => $yesterday, "user" => $this->getUserID()]]);
            $goalcalories = $this->getAppClass()->getDatabase()->sum($db_prefix . "food_goals", 'calories', ["AND" => ["date" => $yesterday, "user" => $this->getUserID()]]);

            if ($dbcalories < $goalcalories && $dbcalories >= 1200) {
                $this->checkDB("meals", "food", "calories", $rewardKey . "Bang On");
            }
        }
    }
}