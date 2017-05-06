<?php
    /*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 * https://nxfifteen.me.uk
 *
 * Copyright (c) 2017, Stuart McCulloch Anderson
 *
 * Released under the MIT license
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

    define('TEST_SUITE', true);

    if (!function_exists('autoloader')) {
        require_once(dirname(__FILE__) . "/../lib/autoloader.php");
        require_once(dirname(__FILE__) . "/../vendor/autoload.php");
    }

    require_once(dirname(__FILE__) . "/../lib/functions.php");

