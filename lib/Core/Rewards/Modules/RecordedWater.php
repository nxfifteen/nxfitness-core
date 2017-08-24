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
class RecordedWater extends Modules {

    /**
     * @param array $eventDetails Array holding details of award to issue
     */
    public function trigger( $eventDetails ) {
        $this->setEventDetails( $eventDetails );

        $yesterday = date( "Y-m-d" );
        $rewardKey = sha1( "waterRecordingFor" . $yesterday );

        $goal = $this->getAppClass()->getUserSetting( $this->getUserID(), "goal_water", '200' );
        $dbGlassMls = $this->getAppClass()->getSetting("liquidGlassSize", 240);

        if ( ! $this->getRewardsClass()->alreadyAwarded( $rewardKey ) ) {
            $dbPrefix = $this->getAppClass()->getSetting( "db_prefix", null, false );
            $water    = $this->getAppClass()->getDatabase()->get( $dbPrefix . "water", "liquid", [ "AND" => [ "user" => $this->getUserID(), "date" => $yesterday ] ] );

            if ( $water >= $goal ) {
                $this->checkDB( "meals", "water", "drank", $rewardKey . "Bang On" );
            }
        }

        /** @noinspection PhpUndefinedFieldInspection */
        $glassesDrank = round_down($eventDetails->summary->water / $dbGlassMls, 0);
        nxr(3, "You've drank " . $glassesDrank . " classes of water");
        $rewardKey = sha1("waterRecording" . date("Y-m-d"));
        $lastRecordedDrinks = $this->getAppClass()->getUserSetting($this->getUserID(), "waterHabiticaRecorded", 0);
        if ($lastRecordedDrinks > $glassesDrank) {
            $lastRecordedDrinks = 0;
        }
        nxr(4, "Your were last rewarded for drinking " . $lastRecordedDrinks . " classes");

        $outstandingRewards = $glassesDrank - $lastRecordedDrinks;
        nxr(4, "You still need rewarded for drinking " . $outstandingRewards . " classes");

        if ($outstandingRewards > 0) {

            for ($i = 0; $i < $outstandingRewards; $i++) {
                $this->checkDB("meals", "water", "drank water", $rewardKey . $i . $glassesDrank);
            }
            $this->getAppClass()->setUserSetting($this->getUserID(), "waterHabiticaRecorded", $glassesDrank);
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