<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

$rules = [];
$rewards = [];
if ( file_exists( dirname( __FILE__ ) . "/rewards.inc.php" ) ) {
    require(dirname(__FILE__) . "/rewards.inc.php");
}
