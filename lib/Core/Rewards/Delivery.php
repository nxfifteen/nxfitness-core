<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 * Copyright (c) 2017. Stuart McCulloch Anderson
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @subpackage  Rewards
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

namespace Core\Rewards;

use Core\Core;

require_once( dirname( __FILE__ ) . "/../../autoloader.php" );

/**
 * Modules
 *
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class Delivery {

    protected $dbPrefix;
    /**
     * @var Core
     */
    private $appClass;
    /**
     * @var String
     */
    private $userID;

    /**
     * Delivery constructor.
     *
     * @param Core   $appClass Core API Class
     * @param string $userID   Fitbit user ID
     */
    public function __construct( $appClass, $userID ) {
        $this->setAppClass( $appClass );
        $this->setUserID( $userID );
        $this->dbPrefix = $this->getAppClass()->getSetting( "db_prefix", null, false );
    }

    /**
     * @return Core
     */
    protected function getAppClass() {
        return $this->appClass;
    }

    /**
     * @param Core $appClass Core API Class
     */
    protected function setAppClass( $appClass ) {
        $this->appClass = $appClass;
    }

    /**
     * @param array  $recordReward Array holding details of award that has been issued
     * @param string $state        State of award - Issued/Pending
     * @param string $rewardKey    Reward Key
     */
    protected function recordDevlivery( $recordReward, $state, $rewardKey ) {
        if ( ! is_array( $recordReward ) ) {
            $recordReward = [];
        }

        if ( ! array_key_exists( "rmid", $recordReward ) ) {
            $recordReward[ 'rmid' ] = null;
        }
        if ( ! array_key_exists( "rid", $recordReward ) ) {
            $recordReward[ 'rid' ] = null;
        }

        $this->getAppClass()->getDatabase()->insert( $this->dbPrefix . "reward_queue", [ "fuid" => $this->getUserID(), "state" => $state, "rmid" => $recordReward[ 'rmid' ], "reward" => $recordReward[ 'rid' ], "rkey" => sha1( $rewardKey ) ] );
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), [ "METHOD" => __METHOD__, "LINE" => __LINE__ ] );
    }

    /**
     * @return String
     */
    protected function getUserID() {
        return $this->userID;
    }

    /**
     * @param String $userID
     */
    protected function setUserID( $userID ) {
        $this->userID = $userID;
    }

    /**
     * @param string $string Value to test for JSON
     *
     * @return bool
     */
    protected function isJson( $string ) {
        return ( ( is_string( $string ) &&
                   ( is_object( json_decode( $string ) ) ||
                     is_array( json_decode( $string ) ) ) ) ) ? true : false;
    }

    /**
     * @param array  $recordReward Array holding details of award that has been issued
     * @param string $state        State of award - Issued/Pending
     * @param string $rewardKey    Reward Key
     *
     * @return array
     */
    public function deliver( $recordReward, $state, $rewardKey ) {
        $this->recordDevlivery( $recordReward, $state, $rewardKey );

        return [];
    }

}