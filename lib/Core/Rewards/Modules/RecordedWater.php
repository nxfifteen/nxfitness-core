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
class RecordedWater extends Modules
{

    private $debug = false;

    /**
     * @param array $eventDetails Array holding details of award to issue
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);

        $yesterday = date("Y-m-d");
        $rewardKey = sha1("waterRecordingFor" . $yesterday);

        $goal = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_water", '200');

        if (!$this->getRewardsClass()->alreadyAwarded($rewardKey)) {
            $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
            $water = $this->getAppClass()->getDatabase()->get($db_prefix . "water", "liquid", ["AND" => ["user" => $this->getUserID(), "date" => $yesterday]]);

            if ($water >= $goal) {
                $this->checkDB("meals", "water", "drank", $rewardKey . "Bang On");
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