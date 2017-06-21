<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

$config = array();
require_once("../config/config.inc.php");

$mysqli = new mysqli($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$files = glob('../tcx/Runtastic/*.tcx', GLOB_BRACE);
foreach ($files as $file) {
    $xml = simplexml_load_file($file);
    $xml = json_decode(json_encode($xml), true);

    $sport = $xml['Activities']['Activity']['@attributes']['Sport'];
    $dateTime = $xml['Activities']['Activity']['Id'];
    $date = substr($dateTime, 0, 10);
    $time = substr($dateTime, 11, 5);

    echo $sport . "\t" . $date . " @" . $time;
    $res = $mysqli->query("SELECT * FROM `" . $config['db_prefix'] . "activity_log` WHERE `user` LIKE '" . $config['ownerFuid'] . "' AND `activityName` LIKE '%" . $sport . "%' AND `startDate` LIKE '" . $date . "' AND `startTime` LIKE '" . $time . "%';");

    if ($res->num_rows == 1) {
        while ($row = $res->fetch_assoc()) {
            echo " -> " . $row['logId'];
            if (!file_exists('../tcx/' . $row['logId'] . '.tcx')) {
                copy($file, '../tcx/' . $row['logId'] . '.tcx');
                echo " [DONE]\n";
            } else {
                echo " [SKIPPED]\n";
            }
        }
    } else {
        echo " -> " . $res->num_rows . "\n";
    }
}