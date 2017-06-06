<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

$config = [];
if ( file_exists( dirname( __FILE__ ) . "/config.inc.php" ) ) {
    require_once(dirname(__FILE__) . "/config.inc.php");
}
