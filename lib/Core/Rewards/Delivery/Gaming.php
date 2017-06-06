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
class Gaming extends Delivery
{
    private $blancing = [];
    
    /**
     * @param array $rewardProfile
     * @param string $state
     * @param string $rewardKey
     */
    public function deliver($rewardProfile, $state, $rewardKey)
    {
        if (array_key_exists("reward", $rewardProfile) && $this->isJson($rewardProfile["reward"])) {
            $rewardProfile = json_decode($rewardProfile["reward"], true);
        }

        if (!$this->getAppClass()->getDatabase()->has($this->dbPrefix . "users_xp", ['fuid' => $this->getUserID()])) {
            $this->getAppClass()->getDatabase()->insert($this->dbPrefix . "users_xp", ["class" => "Rebel", "xp" => 0, "mana" => 0, "health" => 100, "fuid" => $this->getUserID()]);
            $dbCurrent = ["class" => "Rebel", "xp" => 0, "mana" => 0, "health" => 100];
        } else {
            $dbCurrent = $this->getAppClass()->getDatabase()->get($this->dbPrefix . "users_xp", ['class','xp','mana','health'], ["fuid" => $this->getUserID()]);
        }

        $balancingRules = $this->get(["class" => $dbCurrent['class'], "skill" => $rewardProfile['skill']], ["xp" => 1, "mana" => 1, "health" => 1]);
        $healthMulipiler = $dbCurrent['health'] / 100;

        $updatedValues = [];
        if (array_key_exists("xp", $rewardProfile)) {
            $updatedValues['xp'] = round($dbCurrent['xp'] + ( ( $rewardProfile['xp'] * $healthMulipiler ) * $balancingRules['xp']), 0, PHP_ROUND_HALF_DOWN);
            if ($updatedValues['xp'] < 0) $updatedValues['xp'] = 0;
        }
        $xpLevel = $this->calculateXP($updatedValues['xp']);
        if (array_key_exists("mana", $rewardProfile)) {
            $updatedValues['mana'] = round($dbCurrent['mana'] + ( ( $rewardProfile['mana'] * $healthMulipiler ) * $balancingRules['mana']), 0, PHP_ROUND_HALF_DOWN);
            if ($updatedValues['mana'] < 0) $updatedValues['mana'] = 0;
        }
        if (array_key_exists("health", $rewardProfile)) {
            $updatedValues['health'] = $this->maxHealth(round($dbCurrent['health'] + ( ( $rewardProfile['health'] * $healthMulipiler ) * $balancingRules['health']), 0, PHP_ROUND_HALF_DOWN), $xpLevel['level']);
            if ($updatedValues['health'] < 0) $updatedValues['health'] = 0;
        }

        $updatedValues['percent'] = $xpLevel['percent'];
        $updatedValues['level'] = $xpLevel['level'];

        $this->getAppClass()->getDatabase()->update($this->dbPrefix . "users_xp", $updatedValues, ["fuid" => $this->getUserID()]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

        $this->recordDevlivery([], "delivered", $rewardKey);
    }

    /**
     * @param $health
     * @param $level
     */
    private function maxHealth($health, $level) {
        if ($level >= 0 && $level <= 9) {
            if ($health > 100) return 100;
        } else if ($level >= 10 && $level <= 19) {
            if ($health > 110) return 110;
        } else if ($level >= 20 && $level <= 29) {
            if ($health > 120) return 120;
        } else if ($level >= 30 && $level <= 39) {
            if ($health > 130) return 130;
        } else if ($level >= 40 && $level <= 49) {
            if ($health > 140) return 140;
        } else if ($level >= 50 && $level <= 59) {
            if ($health > 150) return 150;
        } else if ($level >= 60 && $level <= 69) {
            if ($health > 160) return 160;
        } else if ($level >= 70 && $level <= 79) {
            if ($health > 170) return 170;
        } else if ($level >= 80 && $level <= 89) {
            if ($health > 180) return 180;
        } else if ($level >= 90 && $level <= 99) {
            if ($health > 190) return 190;
        } else if ($level >= 100) {
            if ($health > 200) return 200;
        }

        return $health;
    }

    /**
     * @param $myPoints
     * @return array
     */
    private function calculateXP($myPoints)
    {

        if ($myPoints <= 0)
            return array('percent' => 0, 'level' => 0);

        $calcStart = 0; // Level 1 Start XP
        $calcEnd = 100;  // Level 1 End XP
        $calcInc = 100;   // Increase by extra how many per level?
        $calcLevel = 20; // Multiply by how many per level? (1- easy / 20- hard)

        /* Calculate Level */
        $myStart = 0;
        $myEnd = 0;
        $myLevel = 0;
        $calcCount = 0;
        do {
            $calcCount = $calcCount + 1;
            if ($calcCount % 2 == 0) {
                $calcInc = $calcInc + $calcLevel;
            }
            if (($myPoints < $calcEnd) && ($myPoints >= $calcStart)) {
                $myLevel = $calcCount;
                $myStart = $calcStart;
                $myEnd = $calcEnd;
            }
            $calcStart = $calcEnd;
            $calcEnd = $calcEnd + $calcInc;
        } while ($myLevel == 0);
        $myLevel--;

        /* Calculate Percentage to Next Level */
        $myPercent = (($myPoints - $myStart) / ($myEnd - $myStart)) * 100;
        $myPercent = round($myPercent);
        if ($myPercent == 0) {
            $myPercent = 1;
        }

        return array('percent' => $myPercent, 'level' => $myLevel);
    }

    /**
     * Return setting value
     * Main function called to query settings for value. Default value can be provided, if not NULL is returned.
     * Values can be queried in the database or limited to config file and 'live' values
     *
     * @param array $key Setting to query
     * @param array $default Default value to return
     * @param bool $query_db Boolean to search database or not
     *
     * @return array Setting value, or default as per defined
     */
    private function get($key, $default = null, $query_db = true)
    {
        if ($query_db && $this->getAppClass()->getDatabase()->has($this->dbPrefix . "blancing", $key)) {
            $dbResults = $this->getAppClass()->getDatabase()->get($this->dbPrefix . "blancing", ['xp','mana','health'], $key);
            return $dbResults;
        } else {
            if ($query_db && !is_null($default)) {
                $this->set($key, $default);
            }

            return $default;
        }
    }

    /**
     * Set setting value
     * Function to store/change setting values. Values can be stored in the database or held in memory.
     *
     * @param array $key Setting to query
     * @param array $value Value to store
     * @param bool $query_db Boolean to store in database or not
     *
     * @return bool was data stored correctly
     */
    private function set($key, $value)
    {
        if ($this->getAppClass()->getDatabase()->has($this->dbPrefix . "blancing", $key)) {
            $this->getAppClass()->getDatabase()->update($this->dbPrefix . "blancing", $value, $key);
        } else {
            $this->getAppClass()->getDatabase()->insert($this->dbPrefix . "blancing", array_merge($value, $key));
        }
    }
}