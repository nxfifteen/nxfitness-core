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

require_once( dirname( __FILE__ ) . "/../../autoloader.php" );

use Core\Core;

/**
 * RewardsSystem
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-RewardsMinecraft
 *            phpDocumentor wiki for RewardsSystem.
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class RewardsSystem {

    /**
     * @var Core
     */
    protected $appClass;
    /**
     * @var String
     */
    protected $userID;
    /**
     * @var string
     */
    protected $userMinecraftID;
    /**
     * @var array
     */
    protected $AwardsGiven;
    private $debug = false;
    /**
     * @var null
     */
    private $user;

    /**
     * @param null $user
     *
     * @internal param $userFid
     */
    public function __construct( $user = null ) {
        $this->setAppClass( new Core() );
        $this->AwardsGiven = [];
        $this->setUserID( $user );
        $this->user = $user;
    }

    /**
     * @param Core $paramClass
     */
    private function setAppClass( $paramClass ) {
        $this->appClass = $paramClass;
    }

    /**
     * @return Core
     */
    private function getAppClass() {
        return $this->appClass;
    }

    /**
     * @return String
     */
    public function getuserMinecraftID() {
        return $this->userMinecraftID;
    }

    /**
     * @param string $userMinecraftID
     *
     * @todo     Consider test case
     * @internal param String $userID
     */
    public function setuserMinecraftID( $userMinecraftID ) {
        $this->userMinecraftID = $userMinecraftID;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return array
     */
    public function queryMinecraftRewards() {
        $wmcKeyProvided = filter_input( INPUT_GET, 'wmc_key', FILTER_SANITIZE_STRING );
        $wmcKeyCorrect  = $this->getAppClass()->getSetting( "wmc_key", null, true );
        nxr( 0, "Minecraft rewards Check" );

        if ( $wmcKeyProvided != $wmcKeyCorrect ) {
            nxr( 1, "Key doesnt match" );

            return [ "success" => false, "data" => [ "msg" => "Incorrect key" ] ];
        }

        $databaseTable = $this->getAppClass()->getSetting( "db_prefix", null, false );

        if ( filter_input( INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING ) == "GET" ) {
            $rewards = $this->getAppClass()->getDatabase()->select( $databaseTable . "minecraft",
                [
                    'mcrid',
                    'username',
                    'command'
                ], [
                    "delivery" => "pending",
                    "ORDER"    => [ 'mcrid' => "ASC" ]
                ] );
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE"   => __LINE__
            ] );

            $data = [];
            foreach ( $rewards as $dbReward ) {
                if ( ! array_key_exists( $dbReward[ 'username' ], $data ) ) {
                    $data[ $dbReward[ 'username' ] ] = [];
                }
                if ( ! array_key_exists( $dbReward[ 'mcrid' ], $data[ $dbReward[ 'username' ] ] ) ) {
                    $data[ $dbReward[ 'username' ] ][ $dbReward[ 'mcrid' ] ] = [];
                }
                array_push( $data[ $dbReward[ 'username' ] ][ $dbReward[ 'mcrid' ] ], $dbReward[ 'command' ] );
            }

            return [ "success" => true, "data" => $data ];

        } else if ( filter_input( INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING ) == "POST" && filter_input( INPUT_POST, 'processedOrders', FILTER_SANITIZE_STRING ) ) {

            $processedOrders = json_decode( filter_input( INPUT_POST, 'processedOrders', FILTER_SANITIZE_STRING ) );

            if ( is_array( $processedOrders ) ) {
                foreach ( $processedOrders as $processedOrder ) {
                    if ( $this->getAppClass()->getDatabase()->has( $databaseTable . "minecraft", [ "mcrid" => $processedOrder ] ) ) {
                        $this->getAppClass()->getDatabase()->update( $databaseTable . "minecraft", [ "delivery" => "delivered" ], [ "mcrid" => $processedOrder ] );
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), [ "METHOD" => __METHOD__, "LINE" => __LINE__ ] );

                        nxr( 1, "Reward " . $processedOrder . " processed" );
                    } else {
                        nxr( 1, "Reward " . $processedOrder . " is invalid ID" );
                    }
                }
            } else {
                nxr( 1, "No processed rewards recived" );
            }

            return [ "success" => true ];

        }

        return [ "success" => false, "data" => [ "msg" => "Unknown Error" ] ];

    }

    /**
     * @param string $system       Name of system used to issue reward, also Class name
     * @param array  $eventDetails Array holding details of award to issue
     */
    public function eventTrigger( $system, $eventDetails ) {
        $className   = "Core\\Rewards\\Modules\\" . $system;
        $includePath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "Modules";

        if ( file_exists( $includePath . DIRECTORY_SEPARATOR . "Private" . DIRECTORY_SEPARATOR . $system . ".php" ) ) {
            $includePath = $includePath . DIRECTORY_SEPARATOR . "Private" . DIRECTORY_SEPARATOR . $system . ".php";
        } else if ( file_exists( $includePath . DIRECTORY_SEPARATOR . $system . ".php" ) ) {
            $includePath = $includePath . DIRECTORY_SEPARATOR . $system . ".php";
        } else {
            $includePath = null;
        }

        if ( ! is_null( $includePath ) ) {
            if ( $this->debug ) {
                nxr( 2, "includePath: " . $includePath );
            }
            if ( $this->debug ) {
                nxr( 2, "className: " . $className );
            }

            /** @noinspection PhpIncludeInspection */
            require_once( $includePath );
            $rewardSystem = new $className( $this->getAppClass(), $this->getUserID() );
            /** @noinspection PhpUndefinedMethodInspection */
            $rewardSystem->trigger( $eventDetails );

        } else {
            nxr( 2, "Create a new class '$className' in " . $includePath . DIRECTORY_SEPARATOR . "Private" . DIRECTORY_SEPARATOR . $system . ".php" );
        }
    }

    /**
     * @return String
     */
    public function getUserID() {
        return $this->userID;
    }

    /**
     * @param String $userID
     */
    public function setUserID( $userID ) {
        $this->userID = $userID;
    }
}
