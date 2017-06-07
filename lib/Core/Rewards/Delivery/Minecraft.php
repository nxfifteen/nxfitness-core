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
class Minecraft extends Delivery
{

    /**
     * @param array $recordReward
     * @param string $state
     * @param string $rewardKey
     * @return array
     */
    public function deliver($recordReward, $state, $rewardKey)
    {
        $minecraftUsername = $this->getAppClass()->getUserSetting($this->getUserID(), "minecraft_username", null);

        if (!is_null($minecraftUsername) && !is_numeric($minecraftUsername)) {
            $recordReward['reward'] = str_replace("%s", $minecraftUsername, $recordReward['reward']);

            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null, false) . "minecraft", ["username" => $minecraftUsername, "command" => $recordReward['reward'], "delivery" => "pending"]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

            $this->recordDevlivery($recordReward, "delivered", $rewardKey);
            return ["WooCraft: " . $recordReward['reward']];
        } else {
            nxr(0, "User doesnt have a Minecraft Username");
        }

        return [];
    }
}