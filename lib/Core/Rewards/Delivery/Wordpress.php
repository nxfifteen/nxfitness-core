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
class Wordpress extends Delivery
{
    /**
     * @param array $recordReward
     * @param string $state
     * @param string $rewardKey
     */
    public function deliver($recordReward, $state, $rewardKey)
    {
        $user_wp_id = $this->getAppClass()->getUserSetting($this->getUserID(), "wp_user_id");
        if (is_null($user_wp_id)) {
            nxr(0, "User doesnt have a WP ID");
        } else {
            if ($this->getAppClass()->getDatabase()->has("wp_usermeta", ['AND' => ['user_id' => $user_wp_id, 'meta_key' => '_uw_balance']])) {
                $dbCurrentBalance = $this->getAppClass()->getDatabase()->get("wp_usermeta", 'meta_value', ['AND' => ['user_id' => $user_wp_id, 'meta_key' => '_uw_balance']]);
            } else {
                nxr(0, print_r($this->getAppClass()->getDatabase()->error(), true));
                $this->getAppClass()->getDatabase()->insert("wp_usermeta", ["meta_value" => 0, "user_id" => $user_wp_id, "meta_key" => "_uw_balance"]);
                $dbCurrentBalance = 0;
            }

            $newBalance = $dbCurrentBalance + $recordReward['reward'];

            $this->getAppClass()->getDatabase()->update("wp_usermeta", ["meta_value" => $newBalance], ['AND' => ['user_id' => $user_wp_id, 'meta_key' => '_uw_balance']]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);
        }

        $this->recordDevlivery($recordReward, "delivered", $rewardKey);
    }
}