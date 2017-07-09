<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 * Copyright (c) 2017. Stuart McCulloch Anderson
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Rewards
 * @subpackage  Delivery
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

namespace Core\Rewards\Delivery;

use Core\Core;
use Core\Rewards\Delivery;
use HabitRPHPG;

require_once( dirname( __FILE__ ) . "/../../../autoloader.php" );

/**
 * Modules
 *
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class Habitica extends Delivery {

    /**
     * @var HabitRPHPG
     */
    private $habitRPHPG;
    private $userId;
    private $apiKey;
    private $cache = true;
    private $apiStatus = null;

    /**
     * Delivery constructor.
     *
     * @param Core   $appClass Core API Class
     * @param string $userID   Fitbit user ID
     */
    public function __construct( $appClass, $userID ) {
        parent::__construct( $appClass, $userID );

        $this->userId = $this->getAppClass()->getUserSetting( $userID, 'habitica_user_id', null );
        $this->apiKey = $this->getAppClass()->getUserSetting( $userID, 'habitica_api_key', null );

        if ( $this->isValidUser() ) {
            $this->setHabitRPHPG( new HabitRPHPG( $this->userId, $this->apiKey ) );
        }

    }

    /**
     * @param HabitRPHPG $habitRPHPG
     */
    private function setHabitRPHPG( $habitRPHPG ) {
        $this->habitRPHPG = $habitRPHPG;
    }

    /**
     * @param string $taskSearchName Name of habit to find
     *
     * @return mixed
     * @internal param $alias
     */
    private function searchTags( $taskSearchName ) {
        $apiValues = $this->getHabitRPHPG()->getTags();
        if ( count( $apiValues ) > 0 ) {

            foreach ( $apiValues as $apiValue ) {
                if ( $apiValue[ 'name' ] == $taskSearchName ) {
                    return $apiValue[ 'id' ];
                }
            }

            $this->getHabitRPHPG()->clearTags();
            $newTag = $this->getHabitRPHPG()->_request( "post", "tags", [ 'name' => $taskSearchName ] );

            return $newTag[ 'id' ];
        } else {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function isValidUser() {
        if ( ! is_null( $this->userId ) && ! is_null( $this->apiKey ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array  $rewardProfile Array holding details of award that has been issued
     * @param string $state         State of award - Issued/Pending
     * @param string $rewardKey     Reward Key
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @return array
     */
    public function deliver( $rewardProfile, $state, $rewardKey ) {
        if ( $this->isValidUser() && $this->isAllowed() && $this->getStatus() == 'up' ) {
            nxr( 3, "Awarding Habitica Rewards" );

            if ( array_key_exists( "reward", $rewardProfile ) && $this->isJson( $rewardProfile[ "reward" ] ) ) {
                $rewardJson = json_decode( $rewardProfile[ "reward" ], true );
                if ( array_key_exists( "alias", $rewardProfile ) ) {
                    $rewardJson[ 'alias' ] = sha1( "nx" . $rewardProfile[ 'alias' ] );
                } else {
                    $rewardJson[ 'alias' ] = sha1( "nx" . $rewardProfile[ 'name' ] );
                }
            } else {
                return [ "Failed" ];
            }

            $tasks = $this->searchTasks( $rewardJson[ 'alias' ], $rewardProfile[ 'name' ], '', false, true );

            if ( is_null( $tasks ) ) {
                $tasks = $this->createNewTask( $rewardJson[ 'type' ], $rewardProfile[ 'name' ], $rewardJson );
                $tasks = $tasks[ 'id' ];
            }

            if ( ! array_key_exists( "repeat", $rewardProfile ) ) {
                $rewardProfile[ 'repeat' ] = 1;
            }

            for ( $i = 1; $i <= $rewardProfile[ 'repeat' ]; $i++ ) {
                if ( $rewardJson[ 'score' ] == "up" ) {
                    $this->getHabitRPHPG()->doTask( $tasks, 'up' );
                } else {
                    $this->getHabitRPHPG()->doTask( $tasks, 'down' );
                }
            }

            $this->recordDevlivery( $rewardProfile, "delivered", $rewardKey );

            return [ $rewardProfile[ 'description' ] ];
        }

        return [ "Failed" ];
    }

    /**
     * @param bool $quiet
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return bool|string
     */
    public function isAllowed( $quiet = false ) {
        $trigger = "habitica";

        $usrConfig = $this->getAppClass()->getUserSetting( $this->getUserID(), 'scope_' . $trigger, true );
        if ( ! is_null( $usrConfig ) AND $usrConfig != 1 ) {
            if ( ! $quiet ) {
                nxr( 3, "Aborted $trigger disabled in user config" );
            }

            return false;
        }

        $sysConfig = $this->getAppClass()->getSetting( 'scope_' . $trigger, true );
        if ( $sysConfig != 1 ) {
            if ( ! $quiet ) {
                nxr( 3, "Aborted $trigger disabled in system config" );
            }

            return false;
        }

        return true;
    }

    /**
     * @return mixed|null
     */
    public function getStatus() {
        if ( is_null( $this->apiStatus ) ) {
            $this->apiStatus = $this->getHabitRPHPG()->getStatus();
        }

        return $this->apiStatus;
    }

    /**
     * @return HabitRPHPG
     */
    public function getHabitRPHPG() {
        return $this->habitRPHPG;
    }

    /**
     * @param string $alias          Alias of habit to find
     * @param string $taskSearchName Name of habit to find
     * @param string $type           Type of habbit to find
     * @param bool   $returnObject   Return full item object
     * @param bool   $skipCache      Skip the cache and search the API
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return mixed
     */
    public function searchTasks( $alias, $taskSearchName, $type = '', $returnObject = false, $skipCache = false ) {
        $dbValue = null;
        if ( ! $skipCache && ! $returnObject ) {
            $dbValue = $this->getAppClass()->getUserSetting( $this->getUserID(), 'habitica_' . $alias, null, true );
        }
        if ( is_null( $dbValue ) || ! $this->cache ) {
            $apiValue = $this->getHabitRPHPG()->findTask( $taskSearchName, $type );
            if ( $returnObject ) {
                return $apiValue;
            } else {
                if ( count( $apiValue ) == 1 ) {
                    $this->getAppClass()->setUserSetting( $this->getUserID(), 'habitica_' . $alias, $apiValue[ 0 ][ '_id' ] );

                    return $apiValue[ 0 ][ '_id' ];
                } else {
                    return null;
                }
            }
        } else {
            return $dbValue;
        }
    }

    /**
     * @param string $type    Type of habbit
     * @param string $name    Name of habbit to create
     * @param array  $options Array of habbit options
     *
     * @return mixed
     */
    public function createNewTask( $type, $name, $options ) {
        if ( $this->isValidUser() && $this->getStatus() == 'up' ) {
            $options[ 'alias' ] = sha1( "nx" . $name );
            $searchResults      = $this->searchTasks( $options[ 'alias' ], $name, '', false, true );
            if ( is_null( $searchResults ) ) {

                if ( array_key_exists( "tags", $options ) ) {
                    foreach ( $options[ 'tags' ] as $id => $tag ) {
                        $tagId                    = $this->searchTags( $tag );
                        $options[ 'tags' ][ $id ] = $tagId;
                    }
                }

                $api = $this->getHabitRPHPG()->createTask( $type, $name, $options );
                if ( $type != "todo" ) {
                    $this->getAppClass()->setUserSetting( $this->getUserID(), 'habitica_' . $options[ 'alias' ], $api[ 'id' ] );
                }

                return $api;
            } else {
                return $searchResults;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $name Name of habit to delete
     *
     * @return mixed
     */
    public function deleteTask( $name ) {
        if ( $this->isValidUser() && $this->getStatus() == 'up' ) {
            $alias      = sha1( "nx" . $name );
            $searchTask = $this->searchTasks( $alias, $name );
            if ( is_null( $searchTask ) ) {
                return true;
            } else {
                $this->getHabitRPHPG()->_request( "delete", "tasks/" . $searchTask );
                $this->getAppClass()->delUserSetting( $this->getUserID(), 'habitica_' . $alias );

                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $guildUuid UUID of the sites guild
     *
     * @return bool|mixed
     */
    public function inviteToGuild( $guildUuid ) {
        $user = $this->getHabitRPHPG()->user();
        foreach ( $user[ 'guilds' ] as $guild ) {
            if ( $guild == $guildUuid ) {
                nxr( 0, "Use is already in the guild" );

                return false;
            }
        }

        $userIdOld = $this->userId;
        if ( $this->switchToAdmin() ) {
            $this->getHabitRPHPG()->_request( "post", "groups/$guildUuid/invite", [ 'uuids' => [ $userIdOld ] ] );
            $this->switchToUser();
        }

        return true;
    }

    /**
     *
     */
    public function switchToUser() {
        $this->userId = $this->getAppClass()->getUserSetting( $this->getUserID(), 'habitica_user_id', null, false );
        $this->apiKey = $this->getAppClass()->getUserSetting( $this->getUserID(), 'habitica_api_key', null, false );

        if ( $this->isValidUser() ) {
            $this->setHabitRPHPG( new HabitRPHPG( $this->userId, $this->apiKey ) );
        }
    }

    /**
     * @return bool
     */
    public function switchToAdmin() {
        $habiticaAdmin = $this->getAppClass()->getSetting( 'habitica_admin_user', null );
        $habiticaKey   = $this->getAppClass()->getSetting( 'habitica_admin_key', null );

        if ( is_null( $habiticaAdmin ) || is_null( $habiticaKey ) ) {
            nxr( 0, "No guild master defined" );

            return false;
        }

        if ( $habiticaAdmin == $this->userId ) {
            nxr( 0, "Current user is already the guild master" );

            return false;
        }

        $userIdOld = $this->userId;
        $apiKeyOld = $this->apiKey;

        $this->userId = $habiticaAdmin;
        $this->apiKey = $habiticaKey;

        if ( $this->isValidUser() ) {
            $this->setHabitRPHPG( new HabitRPHPG( $this->userId, $this->apiKey ) );

            return true;
        } else {
            $this->userId = $userIdOld;
            $this->apiKey = $apiKeyOld;

            return false;
        }
    }
}