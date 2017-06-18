<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

use Core\Core;

header( 'Access-Control-Allow-Origin: https://wp.dev.psi.nxfifteen.me.uk' );
header( 'Cache-Control: no-cache, must-revalidate' );
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );

require_once( dirname( __FILE__ ) . "/lib/autoloader.php" );

if (array_key_exists('_nx_fb_usr', $_COOKIE) && $_COOKIE['_nx_fb_usr'] != "") {
    nxr(1, "User " . $_COOKIE['_nx_fb_usr'] . " Secured");

    if (array_key_exists('formId', $_POST)) {
        if ($_POST['formId'] == "habiticaRegister" || $_POST['formId'] == "habiticaConnect") {
            if (array_key_exists('token', $_POST) && array_key_exists('userid', $_POST)) {
                $apiUserid = $_POST['userid'];
                $apiKey = $_POST['token'];

                $habiticaClass = new HabitRPHPG($apiUserid, $apiKey);
                $newUser = $habiticaClass->_request("get", "user", '', true);
                if (!$newUser['_id']) {
                    http_response_code(500);
                    if ($newUser['message'] == "There is no account that uses those credentials.") {
                        echo "There is a problem with your credentials, please check the user id and api key before trying again. Or create an account bellow.";
                    } else {
                        nxr(1, print_r($newUser, true));
                        echo "Unknown error: " . $newUser['error'] . " :: " . $newUser['message'];
                    }

                    die();
                }

            } else {
                $_POST['confirmPassword'] = $_POST['password2'];
                unset($_POST['password2']);
                $habiticaClass = new HabitRPHPG(null, null);
                $newUser = $habiticaClass->_request("post", "user/auth/local/register", $_POST, true);

                if (!$newUser['_id']) {
                    http_response_code(500);
                    if ($newUser['message'] == "Username already taken.") {
                        echo "Username already taken. If this is your account please use the form above to connect to it.";
                    } else if ($newUser['message'] == "Email address is already used in an account.") {
                        echo "Email address is already used in an account. If you already have an account please use the form above to connect to it.";
                    } else if ($newUser['message'] == "Invalid request parameters.") {
                        foreach ($newUser['errors'] as $error) {
                            echo $error['message'] . "<br />";
                        }
                    } else {
                        nxr(1, print_r($newUser, true));
                        echo "Unknown error: " . $newUser['error'] . " :: " . $newUser['message'];
                    }

                    die();
                }

                $apiUserid = $newUser['id'];
                $apiKey = $newUser['apiToken'];
            }

            $core = new Core();
            if (isset($apiUserid) && isset($apiKey)) {
                $core->setUserSetting($_COOKIE['_nx_fb_usr'], "user_id", $apiUserid);
                $core->setUserSetting($_COOKIE['_nx_fb_usr'], "api_key", $apiKey);
            }
            $core->getFitbitAPI( $_COOKIE['_nx_fb_usr'] )->setForceSync( true );
            $core->getFitbitAPI( $_COOKIE['_nx_fb_usr'] )->pull( $_COOKIE['_nx_fb_usr'], 'habitica' );

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_xp';
            if ( file_exists( $cacheFile ) ) {
                unlink($cacheFile);
            }

            http_response_code(200);
        } else if ($_POST['formId'] == "accDeletion") {
            nxr(1, "Users requested an account deletion");
            $core = new Core();
            if (isset($apiUserid) && isset($apiKey)) {
                $core->setUserSetting($_COOKIE['_nx_fb_usr'], "user_id", $apiUserid);
                $core->setUserSetting($_COOKIE['_nx_fb_usr'], "api_key", $apiKey);
            }

            $data = $core->getDatabase()->query("SHOW TABLES FROM " . $core->getSetting("db_name", null, false))->fetchAll();
            if (count($data) > 0) {
                $prefix = $core->getSetting("db_prefix", null, false);
                foreach ($data as $dbTable) {
                    if (substr($dbTable[0], 0, strlen($prefix)) === $prefix) {
                        $userColumn = whatIsUserColumn($prefix, $dbTable[0]);
                        if (!is_null($userColumn)) {
                            $tableFunction = "delUserFrom_" . str_ireplace($prefix, "", $dbTable[0]);
                            if (function_exists($tableFunction)){
                                $tableFunction($core, $prefix);
                            } else {
                                nxr(2, "Removing user from " . $dbTable[0]);
                                $core->getDatabase()->delete($dbTable[0], [$userColumn => $_COOKIE['_nx_fb_usr']]);
                            }
                        }
                    }
                }
            }

            setcookie('_nx_fb_key', '', time() - 60 * 60 * 24 * 365, '/', $_SERVER['SERVER_NAME']);
            setcookie('_nx_fb_usr', '', time() - 60 * 60 * 24 * 365, '/', $_SERVER['SERVER_NAME']);

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_';
            foreach(glob($cacheFile . "*") as $f) {
                nxr(2, "Deleteing $f");
                if ( file_exists( $f ) ) {
                    unlink($f);
                }
            }

            http_response_code(200);
        } else {
            nxr(1, "Unknown Form");
            http_response_code(403);
            echo "Unknown Form";
        }

    } else {
        nxr(1, "Invalid Form");
        http_response_code(403);
        echo "Invalid Form";
    }

} else {
    nxr(1, "User Unsecured");
    http_response_code(403);
    echo "Unauthorised Access";
}

/**
 * @param Core $core
 * @param string $prefix
 */
function delUserFrom_devices_user($core, $prefix) {
    nxr(2, "Deleting users devices");
    $deviceIds = $core->getDatabase()->select($prefix . "devices_user", "device", ["user" => $_COOKIE['_nx_fb_usr']]);
    foreach ($deviceIds as $deviceId) {
        $core->getDatabase()->delete($prefix . "devices", ["id" => $deviceId]);
        $core->getDatabase()->delete($prefix . "devices_user", ["id" => $deviceId]);
    }
    $core->getDatabase()->delete($prefix . "devices_user", ["user" => $_COOKIE['_nx_fb_usr']]);
}

/**
 * @param Core $core
 * @param string $prefix
 */
function delUserFrom_sleep($core, $prefix) {
    nxr(2, "Deleting users Sleep logs");
    $sleepIds = $core->getDatabase()->select($prefix . "sleep_user", "sleeplog", ["user" => $_COOKIE['_nx_fb_usr']]);
    foreach ($sleepIds as $sleepId) {
        $core->getDatabase()->delete($prefix . "sleep", ["logId" => $sleepId]);
    }
    $core->getDatabase()->delete($prefix . "sleep_user", ["user" => $_COOKIE['_nx_fb_usr']]);
}

/**
 * @param Core $core
 * @param string $prefix
 */
function delUserFrom_activity_log($core, $prefix) {
    nxr(2, "Deleting users TCX files");
    $tcxIds = $core->getDatabase()->select($prefix . "activity_log", "logId", ["user" => $_COOKIE['_nx_fb_usr']]);
    foreach ($tcxIds as $tcxFile) {
        nxr(2, "Deleteing " . $tcxFile);

        $tcxFile_tcx = "tcx" . DIRECTORY_SEPARATOR . $tcxFile . ".tcx";
        if ( file_exists( $tcxFile_tcx ) ) {
            nxr(3, "TCX Deleted");
            unlink($tcxFile_tcx);
        } else {
            nxr(3, "TCX Missing " . $tcxFile_tcx);
        }

        $tcxFile_gpx = "cache" . DIRECTORY_SEPARATOR . $tcxFile . ".gpx";
        if ( file_exists( $tcxFile_gpx ) ) {
            nxr(3, "GPX Cache Deleted");
            unlink($tcxFile_gpx);
        } else {
            nxr(3, "GPX Missing " . $tcxFile_gpx);
        }

        $tcxFile_laps = "cache" . DIRECTORY_SEPARATOR . $tcxFile . "_laps.json";
        if ( file_exists( $tcxFile_laps ) ) {
            nxr(3, "Laps Cache Deleted");
            unlink($tcxFile_laps);
        } else {
            nxr(3, "Laps Missing " . $tcxFile_laps);
        }
    }
    $core->getDatabase()->delete($prefix . "activity_log", ["user" => $_COOKIE['_nx_fb_usr']]);
}

/**
 * @param $prefix
 * @param $tableName
 * @return string
 */
function whatIsUserColumn($prefix, $tableName) {
    switch ($tableName) {
        case $prefix . "activity":
        case $prefix . "activity_log":
        case $prefix . "body":
        case $prefix . "devices_user":
        case $prefix . "food":
        case $prefix . "food_goals":
        case $prefix . "heartAverage":
        case $prefix . "heart_activity":
        case $prefix . "push":
        case $prefix . "queue":
        case $prefix . "runlog":
        case $prefix . "sleep":
        case $prefix . "steps":
        case $prefix . "steps_goals":
        case $prefix . "units":
        case $prefix . "water":
            return "user";

        case $prefix . "bages_user":
        case $prefix . "inbox":
        case $prefix . "journeys_travellers":
        case $prefix . "nomie_events":
        case $prefix . "nomie_trackers":
        case $prefix . "reward_queue":
        case $prefix . "settings_users":
        case $prefix . "streak_goal":
        case $prefix . "users":
        case $prefix . "users_xp":
            return "fuid";

        default:
            return null;

    }
}