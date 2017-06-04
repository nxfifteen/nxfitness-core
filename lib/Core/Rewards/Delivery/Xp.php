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
class Xp extends Delivery
{
    /**
     * @param int $xp
     * @param string $state
     * @param string $rewardKey
     */
    public function deliver($xp, $state, $rewardKey) {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
        if (!$this->getAppClass()->getDatabase()->has($db_prefix . "users_xp", ['fuid' => $this->getUserID()])) {
            $this->getAppClass()->getDatabase()->insert($db_prefix . "users_xp", ["xp" => 0, "fuid" => $this->getUserID()]);
            $dbCurrentXp = 0;
        } else {
            $dbCurrentXp = $this->getAppClass()->getDatabase()->get($db_prefix . "users_xp", 'xp', ["fuid" => $this->getUserID()]);
        }

        $this->getAppClass()->getDatabase()->update($db_prefix . "users_xp", ["xp" => $dbCurrentXp + $xp], ["fuid" => $this->getUserID()]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

        $this->recordDevlivery([], "delivered", $rewardKey);
    }
}