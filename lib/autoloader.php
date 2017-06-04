<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "functions.php");

spl_autoload_register(function ($className) {
    $namespace = str_replace("\\", DIRECTORY_SEPARATOR, __NAMESPACE__);
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    $class = dirname(__FILE__) . DIRECTORY_SEPARATOR . (empty($namespace) ? "" : $namespace . DIRECTORY_SEPARATOR) . "{$className}.php";

    if (file_exists($class)) {
        /** @noinspection PhpIncludeInspection */
        require_once($class);
    }

});

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "bundle" . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php");
