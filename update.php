<?php
    $test = array("user" => "269VLG", "data" => "Weight");

    require_once(dirname(__FILE__) . "/inc/upgrade.php");
    $dataReturnClass = new Upgrade($test['user']);
    /** @noinspection PhpUndefinedMethodInspection */
    $dataReturnClass->subscribeUser();
