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
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */

namespace Core\Rewards;

use Core\Core;
use Core\Rewards\Delivery\Wordpress;

require_once( dirname( __FILE__ ) . "/../../autoloader.php" );

/**
 * Rewards
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
class Rewards {

    /**
     * @var String
     */
    private $userID;

    /**
     * @var Core
     */
    private $appClass;

    /**
     * @var Core
     */
    private $FileRewards;

    /**
     * @var array
     */
    private $RewardsIssued = [];

    /**
     * @var string
     */
    private $rewardReason;

    /**
     * Modules constructor.
     *
     * @param Core   $appClass Core API Class
     * @param string $userID   Fitbit user ID
     */
    public function __construct( $appClass, $userID ) {
        $this->setAppClass( $appClass );
        $this->setUserID( $userID );
        if ( file_exists( dirname( __FILE__ ) . "/../../../config/rewards.dist.php" ) ) {
            $rules = [];
            require( dirname( __FILE__ ) . "/../../../config/rewards.dist.php" );
            $this->FileRewards = $rules;
        }
    }

    /**
     * @param Core $appClass
     */
    private function setAppClass( $appClass ) {
        $this->appClass = $appClass;
    }

    /**
     * @param String $userID
     */
    private function setUserID( $userID ) {
        $this->userID = $userID;
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
    private function getUserID() {
        return $this->userID;
    }

    /**
     * @param string $rewardKey Unique RewardKey
     *
     * @return bool
     */
    public function alreadyAwarded( $rewardKey ) {
        $dbPrefix = $this->getAppClass()->getSetting( "db_prefix", null, false );

        return $this->getAppClass()->getDatabase()->has( $dbPrefix . "reward_queue", [ "AND" => [ 'fuid' => $this->getUserID(), 'rkey[~]' => sha1( $rewardKey ) ] ] );
    }

    /**
     * @param string $cat   Reward Category
     * @param string $event Reward Event
     * @param string $score Reward Score
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @return array|\PDOStatement
     */
    public function getDBAwards( $cat, $event, $score ) {
        $returnRewards = [];
        $dbPrefix      = $this->getAppClass()->getSetting( "db_prefix", null, false );
        $rewards       = $this->getAppClass()->getDatabase()->select( $dbPrefix . "reward_map", [
            "rmid",
            "xp",
            "reward(rid)",
            "name",
        ], [
            "AND" => [
                "cat"   => $cat,
                "event" => $event,
                "rule"  => $score
            ]
        ] );

        $this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE"   => __LINE__
        ] );
        if ( is_array( $rewards ) && count( $rewards ) > 0 ) {

            foreach ( $rewards as $reward ) {
                if ( ( is_numeric( $reward[ 'xp' ] ) && $reward[ 'xp' ] <> 0 ) || $reward[ 'rid' ] != "" ) {
                    array_push( $returnRewards, $reward );
                }
            }

        }

        $cat   = strtolower( $cat );
        $event = strtolower( $event );
        $rule  = strtolower( $score );

        if (
            array_key_exists( $cat, $this->FileRewards ) &&
            array_key_exists( $event, $this->FileRewards[ $cat ] ) &&
            array_key_exists( $rule, $this->FileRewards[ $cat ][ $event ] )
        ) {
            foreach ( $this->FileRewards[ $cat ][ $event ][ $rule ] as $fileReward ) {
                array_push( $returnRewards, $fileReward );
            }
        }

        if ( is_array( $returnRewards ) && count( $returnRewards ) > 0 ) {
            return $returnRewards;
        } else {
            return [];
        }
    }

    /**
     * @param array $awardWhere Medoo style where search clause
     *
     * @return bool
     */
    public function hasDBAwards( $awardWhere ) {
        $dbPrefix = $this->getAppClass()->getSetting( "db_prefix", null, false );
        if ( $this->getAppClass()->getDatabase()->has( $dbPrefix . "reward_map", $awardWhere ) ) {
            return true;
        } else {
            $cat   = strtolower( $awardWhere[ 'AND' ][ 'cat' ] );
            $event = strtolower( $awardWhere[ 'AND' ][ 'event' ] );
            $rule  = strtolower( $awardWhere[ 'AND' ][ 'rule' ] );

            if (
                array_key_exists( $cat, $this->FileRewards ) &&
                array_key_exists( $event, $this->FileRewards[ $cat ] ) &&
                array_key_exists( $rule, $this->FileRewards[ $cat ][ $event ] )
            ) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @param integer $userXp    XP to award
     * @param string  $rewardKey Reward Key
     */
    public function giveUserXp( $userXp, $rewardKey ) {
        $wordpress = new Wordpress( $this->getAppClass(), $this->getUserID() );
        $wordpress->deliver( [ 'reward' => $userXp ], $rewardKey, "pending" );
    }

    /**
     * @param array  $recordReward
     * @param string $rewardKey
     * @param string $state
     * @param string $delivery
     */
    public function issueAwards( $recordReward, $rewardKey, $state = "pending", $delivery = "Default" ) {
        $className   = "Core\\Rewards\\Delivery";
        $includePath = dirname( __FILE__ );
        $includeFile = $includePath . DIRECTORY_SEPARATOR . "Delivery.php";

        if ( $delivery != "Default" ) {
            $delivery = ucwords( $delivery );

            if ( file_exists( $includePath . DIRECTORY_SEPARATOR . "Delivery" . DIRECTORY_SEPARATOR . $delivery . ".php" ) ) {
                $includeFile = $includePath . DIRECTORY_SEPARATOR . "Delivery" . DIRECTORY_SEPARATOR . $delivery . ".php";
                $className   = "Core\\Rewards\\Delivery\\" . $delivery;
            } else {
                nxr( 2, "Create a new class 'Core\\Rewards\\Delivery\\$delivery' in " . $includePath . DIRECTORY_SEPARATOR . "Delivery" . DIRECTORY_SEPARATOR );
            }
        }

        /** @noinspection PhpIncludeInspection */
        require_once( $includeFile );
        $rewardSystem = new $className( $this->getAppClass(), $this->getUserID() );
        /** @noinspection PhpUndefinedMethodInspection */
        $deliveryReturn      = $rewardSystem->deliver( $recordReward, $state, $rewardKey );
        $this->RewardsIssued = array_merge( $this->RewardsIssued, $deliveryReturn );
    }

    /**
     * @param integer $rid Reward ID
     */
    public function nukeConflictingAwards( $rid ) {
        $dbPrefix = $this->getAppClass()->getSetting( "db_prefix", null, false );
        $nukeOne  = $this->getAppClass()->getDatabase()->select( $dbPrefix . "reward_nuke", 'rid', [ "AND" => [ "nukeid" => $rid, "directional" => "true" ] ] );
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), [ "METHOD" => __METHOD__, "LINE" => __LINE__ ] );
        if ( count( $nukeOne ) > 0 ) {
            foreach ( $nukeOne as $nukeId ) {
                if ( $this->getAppClass()->getDatabase()->has( $dbPrefix . "reward_queue", [
                    "AND" => [
                        'fuid'   => $this->getUserID(),
                        'reward' => $nukeId
                    ]
                ] )
                ) {
                    $this->getAppClass()->getDatabase()->delete( $dbPrefix . "reward_queue", [
                        "AND" => [
                            'fuid'   => $this->getUserID(),
                            'reward' => $nukeId
                        ]
                    ] );
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), [ "METHOD" => __METHOD__, "LINE" => __LINE__ ] );
                }
            }
        }

        $nukeTwo = $this->getAppClass()->getDatabase()->select( $dbPrefix . "reward_nuke", 'nukeid', [ "AND" => [ "rid" => $rid, "directional" => "false" ] ] );
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), [ "METHOD" => __METHOD__, "LINE" => __LINE__ ] );
        if ( count( $nukeTwo ) > 0 ) {
            foreach ( $nukeTwo as $nukeId ) {
                if ( $this->getAppClass()->getDatabase()->has( $dbPrefix . "reward_queue", [
                    "AND" => [
                        'fuid'   => $this->getUserID(),
                        'reward' => $nukeId
                    ]
                ] )
                ) {
                    $this->getAppClass()->getDatabase()->delete( $dbPrefix . "reward_queue", [
                        "AND" => [
                            'fuid'   => $this->getUserID(),
                            'reward' => $nukeId
                        ]
                    ] );
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), [ "METHOD" => __METHOD__, "LINE" => __LINE__ ] );
                }
            }
        }
    }

    /**
     * @param string $cat   Reward Category
     * @param string $event Reward Event
     * @param string $score Reward Score
     */
    public function createDBAwards( $cat, $event, $score ) {
        $dbPrefix = $this->getAppClass()->getSetting( "db_prefix", null, false );
        $this->getAppClass()->getDatabase()->insert( $dbPrefix . "reward_map", [
            "cat"   => $cat,
            "event" => $event,
            "rule"  => $score
        ] );
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE"   => __LINE__
        ] );
    }

    /**
     * @return array
     */
    public function getRewardReason() {
        return explode( "|", $this->rewardReason );
    }

    /**
     * @param string $rewardReason
     */
    public function setRewardReason( $rewardReason ) {
        $this->rewardReason = $rewardReason;
    }

    /**
     * @param string $string Name of reward system to return
     *
     * @return array
     */
    public function getSystemRewards( $string ) {
        $returnRewards = [];
        foreach ( $this->FileRewards as $fileRewardCat ) {
            foreach ( $fileRewardCat as $fileRewardEvent ) {
                foreach ( $fileRewardEvent as $fileRewardScore ) {
                    foreach ( $fileRewardScore as $fileReward ) {
                        if ( strtolower( $fileReward[ 'system' ] ) == strtolower( $string ) ) {
                            $returnRewards[] = $fileReward;
                        }
                    }
                }
            }
        }

        return $returnRewards;
    }

    /**
     * @param string $string Reward category
     *
     * @return array
     */
    public function getCatRewards( $string ) {
        if ( array_key_exists( $string, $this->FileRewards ) ) {
            return $this->FileRewards[ $string ];
        }

        return [];
    }

    /**
     * @param string $rewardKey Reward Key
     */
    public function recordAwarded( $rewardKey ) {
        $this->getAppClass()->getDatabase()->insert( $this->getAppClass()->getSetting( "db_prefix", null, false ) . "reward_queue", [
            "fuid"   => $this->getUserID(),
            "state"  => 'recorded',
            "rmid"   => null,
            "reward" => null,
            "rkey"   => sha1( $rewardKey )
        ] );
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), [ "METHOD" => __METHOD__, "LINE" => __LINE__ ] );
    }
}