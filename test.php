<?php
    $test = array("user" => "269VLG", "data" => "StepGoal");

    require_once(dirname(__FILE__) . "/inc/dataReturn.php");
    $dataReturnClass = new dataReturn($test['user']);
    print_r($dataReturnClass->returnUserRecords($test));
