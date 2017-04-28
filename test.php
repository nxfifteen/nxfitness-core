<?php
    require_once(dirname(__FILE__) . "/inc/DataReturn.php");

    $test            = array("user" => "269VLG", "data" => "Badges");
    $dataReturnClass = new DataReturn($test['user']);
    print_r($dataReturnClass->returnUserRecords($test));

    //$test = array("user" => "269VLG", "data" => "StepsGoal");
    //print_r($dataReturnClass->returnUserRecords($test));
