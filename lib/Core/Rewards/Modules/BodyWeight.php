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
class BodyWeight extends Modules
{

    private $debug = false;

    /**
     * @param $eventDetails
     */
    public function trigger($eventDetails)
    {
        $this->setEventDetails($eventDetails);
        $body = $this->getEventDetails();

        $currentDate = new DateTime ('now');
        $currentDate = $currentDate->format("Y-m-d");
        if ($body['current'] <= $body['goal']) {
            $this->checkDB("body", "weight", "goal", $currentDate . "weightgoal");
        } else if ($body['current'] < $body['last']) {
            $this->checkDB("body", "weight", "decreased", $currentDate . "weightdecreased");
        } else if ($body['current'] > $body['last']) {
            $this->checkDB("body", "weight", "increased", $currentDate . "weightincreased");
        }
    }

    /**
     * @param mixed $eventDetails
     */
    private function setEventDetails($eventDetails)
    {
        $this->eventDetails = [
            "current" => $eventDetails[0],
            "goal" => $eventDetails[1],
            "last" => $eventDetails[2]
        ];
    }
}