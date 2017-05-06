<?php

    define('TEST_SUITE', true);

    if (!function_exists('autoloader')) {
        require_once(dirname(__FILE__) . "/../lib/autoloader.php");
        require_once(dirname(__FILE__) . "/../vendor/autoload.php");
    }

    require_once(dirname(__FILE__) . "/../lib/functions.php");

