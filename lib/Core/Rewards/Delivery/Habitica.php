<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

namespace Core\Rewards\Delivery;

use Core\Rewards\Delivery;
use HabitRPHPG;

require_once(dirname(__FILE__) . "/../../../autoloader.php");

/**
 * Modules
 *
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class Habitica extends Delivery
{
    /**
     * @var HabitRPHPG
     */
    private $HabitRPHPG;
    private $user_id;
    private $api_key;
    private $cache = true;
    private $apiStatus = null;

    /**
     * Delivery constructor.
     * @param $AppClass
     * @param $UserID
     */
    public function __construct($AppClass, $UserID)
    {
        parent::__construct($AppClass, $UserID);

        $this->user_id = $this->getAppClass()->getUserSetting($_GET[ 'user' ], 'user_id', NULL, false);
        $this->api_key = $this->getAppClass()->getUserSetting($_GET[ 'user' ], 'api_key', NULL, false);

        if ($this->isValidUser()) {
            $this->setHabitRPHPG(new HabitRPHPG($this->user_id, $this->api_key));
        }

    }

    /**
     * @param array $rewardProfile
     * @param string $state
     * @param string $rewardKey
     * @return array
     */
    public function deliver($rewardProfile, $state, $rewardKey)
    {
        if ($this->isValidUser() && $this->getStatus() == 'up') {
            nxr(4, "Awarding Habitica Rewards");

            if (array_key_exists("reward", $rewardProfile) && $this->isJson($rewardProfile["reward"])) {
                $rewardJson = json_decode($rewardProfile["reward"], true);
                $rewardJson['alias']  = sha1("nx" . $rewardProfile['name']);
            } else {
                return ["Failed"];
            }

            $tasks = $this->_search($rewardJson['alias'], $rewardProfile['name']);

            if(is_null($tasks)) {
                $tasks = $this->_create($rewardJson['type'], $rewardProfile['name'], $rewardJson);
                $tasks = $tasks['id'];
            }

            if ($rewardJson['score'] == "up") {
                $apiReturn = $this->getHabitRPHPG()->doTask($tasks, 'up');
            } else {
                $apiReturn = $this->getHabitRPHPG()->doTask($tasks, 'down');
            }

            $updatedValues = [
                "class" => $apiReturn['class'],
                "xp" => round($apiReturn['exp'], 0, PHP_ROUND_HALF_DOWN),
                "level" => $apiReturn['lvl'],
                "percent" => 0,
                "mana" => $apiReturn['mp'],
                "health" => $apiReturn['hp']
            ];
            if (!$this->getAppClass()->getDatabase()->has($this->dbPrefix . "users_xp", ['fuid' => $this->getUserID()])) {
                $this->getAppClass()->getDatabase()->insert($this->dbPrefix . "users_xp", array_merge($updatedValues, ["fuid" => $this->getUserID()]));
            } else {
                $this->getAppClass()->getDatabase()->update($this->dbPrefix . "users_xp", $updatedValues, ["fuid" => $this->getUserID()]);
            }
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

            return [$rewardProfile['description']];
        }

        return ["Failed"];
    }

    /**
     * @param $type
     * @param $name
     * @param $options
     * @return mixed
     */
    public function _create($type, $name, $options) {
        if ($this->isValidUser() && $this->getStatus() == 'up') {
            $options['alias']  = sha1("nx" . $name);
            if (is_null($this->_search($options['alias'], $name))) {
                $api = $this->getHabitRPHPG()->createTask($type, $name, $options);
                $this->getAppClass()->setUserSetting($_GET[ 'user' ], 'habitica_' . $options['alias'], $api['id']);
                return $api;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function _deleteIfIncomplete($name) {
        if ($this->isValidUser() && $this->getStatus() == 'up') {
            $apiValue = $this->getHabitRPHPG()->findTask($name);
            if(count($apiValue) == 1) {
                print_r($apiValue);
            }
        }
        return true;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function _delete($name) {
        if ($this->isValidUser() && $this->getStatus() == 'up') {
            $alias  = sha1("nx" . $name);
            $searchTask = $this->_search($alias, $name);
            if (is_null($searchTask)) {
                return true;
            } else {
                var_dump($this->getHabitRPHPG()->_request("delete", "tasks/" . $searchTask));
                $this->getAppClass()->delUserSetting($_GET[ 'user' ], 'habitica_' . $alias);
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $alias
     * @param $task_string
     * @return mixed
     */
    private function _search($alias, $task_string) {

        $dbValue = $this->getAppClass()->getUserSetting($_GET[ 'user' ], 'habitica_' . $alias, NULL, true);
        if (is_null($dbValue) || !$this->cache) {
            $apiValue = $this->getHabitRPHPG()->findTask($task_string);
            if(count($apiValue) == 1) {
                $this->getAppClass()->setUserSetting($_GET[ 'user' ], 'habitica_' . $alias, $apiValue[0]['_id']);
                return $apiValue[0]['_id'];
            } else {
                return NULL;
            }
        } else {
            return $dbValue;
        }
    }

    /**
     * @return HabitRPHPG
     */
    private function getHabitRPHPG()
    {
        return $this->HabitRPHPG;
    }

    /**
     * @param HabitRPHPG $HabitRPHPG
     */
    private function setHabitRPHPG($HabitRPHPG)
    {
        $this->HabitRPHPG = $HabitRPHPG;
    }

    /**
     * @return bool
     */
    public function isValidUser()
    {
        if (!is_null($this->user_id) && !is_null($this->api_key)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed|null
     */
    public function getStatus()
    {
        if (is_null($this->apiStatus)) {
            $this->apiStatus = $this->getHabitRPHPG()->getStatus();
        }
        return $this->apiStatus;
    }

}