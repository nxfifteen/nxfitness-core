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

        if ($this->checkDB("badge", $badge->category . " | " . $badge->shortName, "awarded", $badge->category . $badge->shortName)) {

        } else if ($this->checkDB("badge", $badge->category, "awarded", $badge->category)) {

        } else if ($this->checkDB("badge", $badge->category . " | " . $badge->shortName, $badge->timesAchieved, $badge->category . $badge->shortName . $badge->timesAchieved)) {

        } else if ($this->checkDB("badge", $badge->category, $badge->timesAchieved, $badge->category . $badge->timesAchieved)) {

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