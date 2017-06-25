<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @subpackage  Deploy
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

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