<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 * Copyright (c) 2017. Stuart McCulloch Anderson
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
use DateTime;

require_once( dirname( __FILE__ ) . "/../Modules.php" );
require_once( dirname( __FILE__ ) . "/../../../autoloader.php" );

/**
 * Nomie
 *
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class BodyWeight extends Modules {

    /**
     * @param mixed $eventDetails
     */
    private function setEventDetails( $eventDetails ) {
        $this->eventDetails = [
            "current" => $eventDetails[ 0 ],
            "goal"    => $eventDetails[ 1 ],
            "last"    => $eventDetails[ 2 ]
        ];
    }

    /**
     * @param array $eventDetails Array holding details of award to issue
     */
    public function trigger( $eventDetails ) {
        $this->setEventDetails( $eventDetails );
        $body = $this->getEventDetails();

        $currentDate = new DateTime ( 'now' );
        $currentDate = $currentDate->format( "Y-m-d" );
        if ( $body[ 'current' ] <= $body[ 'goal' ] ) {
            $this->checkDB( "body", "weight", "goal", $currentDate . "weightgoal" );
        } else if ( $body[ 'current' ] < $body[ 'last' ] ) {
            $this->checkDB( "body", "weight", "decreased", $currentDate . "weightdecreased" );
        } else if ( $body[ 'current' ] > $body[ 'last' ] ) {
            $this->checkDB( "body", "weight", "increased", $currentDate . "weightincreased" );
        }
        $this->checkDB( "body", "scales", "report", $currentDate . "scalesreported" );
    }
}