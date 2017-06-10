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
            $goalcalories = $this->getAppClass()->getDatabase()->sum($db_prefix . "food_goals", 'calories', ["AND" => ["date" => $yesterday, "user" => $this->getUserID()]]);

            if ($dbcalories < $goalcalories && $dbcalories >= 1200) {
                $this->checkDB("meals", "food", "calories", $rewardKey . "Bang On");
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