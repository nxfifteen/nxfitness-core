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
            nxr(0, print_r($_POST, true));
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
                        $userColumn = whatIsUserColumn($dbTable[0]);
                        if (!is_null($userColumn)) {
                            nxr(0, $userColumn . " in " . $dbTable[0]);
                        }
                    }
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
 * @param $tableName
 * @return string
 */
function whatIsUserColumn($tableName) {
    switch ($tableName) {
        case "nx_fitbit_activity":
        case "nx_fitbit_activity_log":
        case "nx_fitbit_body":
        case "nx_fitbit_devices_user":
        case "nx_fitbit_food":
        case "nx_fitbit_food_goals":
        case "nx_fitbit_heartAverage":
        case "nx_fitbit_heart_activity":
        case "nx_fitbit_push":
        case "nx_fitbit_queue":
        case "nx_fitbit_runlog":
        case "nx_fitbit_sleep":
        case "nx_fitbit_steps":
        case "nx_fitbit_steps_goals":
        case "nx_fitbit_streak_goal":
        case "nx_fitbit_units":
        case "nx_fitbit_water":
            return "user";

        case "nx_fitbit_bages_user":
        case "nx_fitbit_inbox":
        case "nx_fitbit_journeys_travellers":
        case "nx_fitbit_nomie_events":
        case "nx_fitbit_nomie_trackers":
        case "nx_fitbit_reward_queue":
        case "nx_fitbit_settings_users":
        case "nx_fitbit_users":
        case "nx_fitbit_users_xp":
            return "fuid";

        default:
            return null;

    }
}