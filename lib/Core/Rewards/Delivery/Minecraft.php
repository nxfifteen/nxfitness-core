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

require_once( dirname( __FILE__ ) . "/../../../autoloader.php" );

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
     */
    public function deliver($recordReward, $state, $rewardKey) {

        nxr(0, print_r($recordReward, true));
        nxr(0, $state);

        $this->recordDevlivery($recordReward, "delivered", $rewardKey);
    }
}