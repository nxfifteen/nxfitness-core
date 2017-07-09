<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
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
class Wordpress extends Delivery
{
    /**
     * @param array $recordReward Array holding details of award that has been issued
     * @param string $state State of award - Issued/Pending
     * @param string $rewardKey Reward Key
     * @return array
     */
    public function deliver($recordReward, $state, $rewardKey)
    {
        nxr(4, "Awarding Wordpress Rewards");

        $userWpId = $this->getAppClass()->getUserSetting($this->getUserID(), "wp_user_id");
        if (is_null($userWpId)) {
            nxr(0, "User doesnt have a WP ID");
        } else {
            $dbWpPrefix = $this->getAppClass()->getSetting("wp_db_prefix", "wp_");

            if ($this->getAppClass()->getDatabase()->has($dbWpPrefix . "usermeta", ['AND' => ['user_id' => $userWpId, 'meta_key' => '_uw_balance']])) {
                $dbCurrentBalance = $this->getAppClass()->getDatabase()->get($dbWpPrefix . "usermeta", 'meta_value', ['AND' => ['user_id' => $userWpId, 'meta_key' => '_uw_balance']]);
            } else {
                $this->getAppClass()->getDatabase()->insert($dbWpPrefix . "usermeta", ["meta_value" => 0, "user_id" => $userWpId, "meta_key" => "_uw_balance"]);
                $dbCurrentBalance = 0;
            }

            $newBalance = round($dbCurrentBalance + ($recordReward['reward']) / 100, 2);
            if ($newBalance < 0) $newBalance = 0;

            $this->getAppClass()->getDatabase()->update($dbWpPrefix . "usermeta", ["meta_value" => $newBalance], ['AND' => ['user_id' => $userWpId, 'meta_key' => '_uw_balance']]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
        }

        $this->recordDevlivery($recordReward, "delivered", $rewardKey);
        return ["Awarded " . ($recordReward['reward']) / 100 . " WP Gold"];
    }
}