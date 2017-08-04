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
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

use Core\Core;

header( 'Access-Control-Allow-Origin: http://nxdev.dev.itsabeta.nx' );
header( 'Cache-Control: no-cache, must-revalidate' );
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );

require_once( dirname( __FILE__ ) . "/lib/autoloader.php" );

if (array_key_exists('_nx_fb_usr', $_COOKIE) && $_COOKIE['_nx_fb_usr'] != "") {
    //nxr(1, "User " . $_COOKIE['_nx_fb_usr'] . " Secured");

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
                $core->setUserSetting($_COOKIE['_nx_fb_usr'], "habitica_user_id", $apiUserid);
                $core->setUserSetting($_COOKIE['_nx_fb_usr'], "habitica_api_key", $apiKey);
            }
            $core->getFitbitAPI( $_COOKIE['_nx_fb_usr'] )->setForceSync( true );
            $core->getFitbitAPI( $_COOKIE['_nx_fb_usr'] )->pull( $_COOKIE['_nx_fb_usr'], 'habitica' );

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_xp';
            if ( file_exists( $cacheFile ) ) {
                unlink($cacheFile);
            }

            http_response_code(200);
        } else if ($_POST['formId'] == "habiticaBuyGems") {
            $core = new Core();

            if (array_key_exists("habitica_max_gems", $_POST)) {
                $core->setUserSetting( $_COOKIE[ '_nx_fb_usr' ], 'habitica_max_gems', $_POST[ 'habitica_max_gems' ] );
                $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], 'habitica_min_gold', $_POST['habitica_min_gold']);
                echo "Updated Minimum Gold To Keep & Maximum Gems You Want";
            }

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_Account';
            if ( file_exists( $cacheFile ) ) {
                unlink($cacheFile);
            }


            http_response_code(200);
        } else if ($_POST['formId'] == "habiticaMaxItems") {
            $core = new Core();
            $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], 'habitica_max_eggs', $_POST['habitica_max_eggs']);
            $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], 'habitica_max_potions', $_POST['habitica_max_potions']);

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_Account';
            if ( file_exists( $cacheFile ) ) {
                unlink($cacheFile);
            }

            echo "Updated Maximum Eggs/Potions You Will Hold";

            http_response_code(200);
        } else if ($_POST['formId'] == "habiticaSwitches") {
            $core = new Core();
            if ($_POST['value'] == "true") {
                $core->setUserSetting( $_COOKIE[ '_nx_fb_usr' ], $_POST[ 'switch' ], 1 );
            } else {
                $core->setUserSetting( $_COOKIE[ '_nx_fb_usr' ], $_POST[ 'switch' ], 0 );
            }

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_Account';
            if ( file_exists( $cacheFile ) ) {
                unlink($cacheFile);
            }

            echo "okay";

            http_response_code(200);
        } else if ($_POST['formId'] == "habiticaKeyUpdate") {
            $core = new Core();
            $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], 'habitica_user_id', $_POST['habitica_user_id']);
            $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], 'habitica_api_key', $_POST['habitica_api_key']);

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_Account';
            if ( file_exists( $cacheFile ) ) {
                unlink($cacheFile);
            }

            echo "Habitica Credentials Updated";

            http_response_code(200);
        } else if ($_POST['formId'] == "accDeletion") {
            nxr(1, "Users requested an account deletion");
            $core = new Core();
            if (isset($apiUserid) && isset($apiKey)) {
                $core->setUserSetting($_COOKIE['_nx_fb_usr'], "habitica_user_id", $apiUserid);
                $core->setUserSetting($_COOKIE['_nx_fb_usr'], "habitica_api_key", $apiKey);
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
        } else if ($_POST['formId'] == "intentSwitch") {
            $core = new Core();
            nxr(0, "Updated " . $_POST['switch'] . " to " . $_POST['value'] . " for " . $_COOKIE[ '_nx_fb_usr' ]);
            if ($_POST['value'] == "true") {
                $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], 'scope_' . $_POST['switch'], 1);
            } else {
                $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], 'scope_' . $_POST['switch'], 0);
            }

            $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_Account";
            if ( file_exists( $babelCacheFile ) ) {
                unlink($babelCacheFile);
            }

            http_response_code(200);
        } else if ($_POST['formId'] == "basicProfile") {
            $core = new Core();
            $db_prefix = $core->getSetting("db_prefix", null, false);
            $core->getDatabase()->update($db_prefix . "users", ['eml' => $_POST['eml']], ["fuid" => $_COOKIE[ '_nx_fb_usr' ]]);

            $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_Account";
            if ( file_exists( $babelCacheFile ) ) {
                unlink($babelCacheFile);
            }

            http_response_code(200);
        } else if ($_POST['formId'] == "passwordChange") {
            $core = new Core();
            $db_prefix = $core->getSetting("db_prefix", null, false);

            nxr(0, "New password request");
            nxr(1, "Current supplied password " . $_POST['passwordCurrent']);
            nxr(2, "New supplied password " . $_POST['passwordNew']);
            nxr(2, "Confirmation password " . $_POST['passwordNew2']);

            $valid = $core->isUserValid($_COOKIE[ '_nx_fb_usr' ], hash("sha256", $core->getSetting("salt") . $_POST['passwordCurrent']));
            nxr(1, "Validity " . print_r($valid, true));
            if ($valid == $_COOKIE[ '_nx_fb_usr' ] && array_key_exists("passwordNew", $_POST) and array_key_exists("passwordNew2", $_POST) and $_POST['passwordNew'] != "" && $_POST['passwordNew'] == $_POST['passwordNew2']) {
                $newUserArray = ['password' => hash("sha256", $core->getSetting("salt") . $_POST['passwordNew'])];
                $core->getDatabase()->update($db_prefix . "users", $newUserArray, ['fuid' => $_COOKIE[ '_nx_fb_usr' ]]);

                setcookie('_nx_fb_usr', $_COOKIE[ '_nx_fb_usr' ], false, '/', $_SERVER['SERVER_NAME']);
                setcookie('_nx_fb_key', gen_cookie_hash($core, $_COOKIE[ '_nx_fb_usr' ]), false, '/', $_SERVER['SERVER_NAME']);
                $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_Account";
                if ( file_exists( $babelCacheFile ) ) {
                    unlink($babelCacheFile);
                }

                nxr(0, "Password changed for " . $_COOKIE[ '_nx_fb_usr' ]);

                echo "Password changed to " . $_POST['passwordNew'];
                http_response_code(200);
            } else if ($valid != $_COOKIE[ '_nx_fb_usr' ]) {
                nxr(0, "Incorrect Password");
                echo "Incorrect Password";
                http_response_code(403);
            } else if (!array_key_exists("passwordNew", $_POST) || $_POST['passwordNew'] == "") {
                nxr(0, "New Password not supplied");
                echo "New Password not supplied";
                http_response_code(500);
            } else if (!array_key_exists("passwordNew2", $_POST)) {
                nxr(0, "New Password not confirmed");
                echo "New Password not confirmed";
                http_response_code(500);
            } else if ($_POST['passwordNew'] != $_POST['passwordNew2']) {
                nxr(0, "Password doesnt match confirmaton");
                echo "Password doesnt match confirmaton";
                http_response_code(500);
            }

        } else if ($_POST['formId'] == "apiKeyRefresh") {

            $core = new Core();
            $db_prefix = $core->getSetting("db_prefix", null, false);
            $newKeySalt = $core->getDatabase()->get($db_prefix . "users", ['eml', 'password', 'rank', 'tkn_refresh', 'tkn_access'], ["fuid" => $_COOKIE[ '_nx_fb_usr' ]]);
            $newKey = random_bytes(10);
            foreach ($newKeySalt as $key => $value) {
                $newKey .= $value.$key.random_bytes(5);
            }

            $newKey = sha1($newKey);
            $newKey = preg_replace('/^([\w]{4})([\w]{7})([\w]{6})([\w]+)/m', '$1-$2-$3', $newKey);
            $core->getDatabase()->update($db_prefix . "users", ['api' => $newKey], ["fuid" => $_COOKIE[ '_nx_fb_usr' ]]);

            $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_Account";
            if ( file_exists( $babelCacheFile ) ) {
                unlink($babelCacheFile);
            }

            echo $newKey;

            http_response_code(200);

        } else if ($_POST['formId'] == "privacyPoint") {
            $core = new Core();
            $currentPoints = json_decode($core->getUserSetting($_COOKIE[ '_nx_fb_usr' ], "geo_private", array()), true);
            if (!is_array($currentPoints)) {
                $currentPoints = [];
            }
            $currentPoints[] = [
                "display_name" => $_POST['display_name'],
                "lat" => $_POST['lat'],
                "lon" => $_POST['lon'],
                "radious" => $_POST['radious']
            ];

            $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], "geo_private", json_encode($currentPoints));

            $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_GeoSecure";
            if ( file_exists( $babelCacheFile ) ) {
                unlink($babelCacheFile);
            }

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_ActivityHistory_';
            foreach(glob($cacheFile . "*") as $f) {
                if ( file_exists( $f ) ) {
                    unlink($f);
                }
            }

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_';
            foreach(glob($cacheFile . "*.gpx") as $f) {
                if ( file_exists( $f ) ) {
                    unlink($f);
                }
            }

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_';
            foreach(glob($cacheFile . "*_laps.json") as $f) {
                if ( file_exists( $f ) ) {
                    unlink($f);
                }
            }

            echo "Okay";

            http_response_code(200);

        } else if ($_POST['formId'] == "privacyPointDel") {
            $core = new Core();
            $currentPoints = json_decode($core->getUserSetting($_COOKIE[ '_nx_fb_usr' ], "geo_private", array()), true);
            if (!is_array($currentPoints)) {
                $currentPoints = [];
            }

            $newPoints = [];
            if (count($currentPoints) > 0) {
                nxr(0, $_POST['point']);
                foreach ($currentPoints as $currentPoint) {
                    nxr(1, $currentPoint['display_name']);
                    if ($currentPoint['display_name'] != $_POST['point']) {
                        $newPoints[] = $currentPoint;
                    }
                }
            }

            $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], "geo_private", json_encode($newPoints));

            $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_GeoSecure";
            if ( file_exists( $babelCacheFile ) ) {
                unlink($babelCacheFile);
            }

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_ActivityHistory_';
            foreach(glob($cacheFile . "*") as $f) {
                if ( file_exists( $f ) ) {
                    unlink($f);
                }
            }

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_';
            foreach(glob($cacheFile . "*.gpx") as $f) {
                if ( file_exists( $f ) ) {
                    unlink($f);
                }
            }

            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_COOKIE[ '_nx_fb_usr' ] . '_';
            foreach(glob($cacheFile . "*_laps.json") as $f) {
                if ( file_exists( $f ) ) {
                    unlink($f);
                }
            }

            echo "Okay";

            http_response_code(200);

        } else if ($_POST['formId'] == "desireStep") {

            if ($_POST['maximumSteps'] < $_POST['minimumSteps']) {
                echo "Your final goal can't really be less than your minimum";
                http_response_code(403);
            } else {
                $core = new Core();
                $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], "desire_steps", $_POST['desireSteps']);
                $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], "desire_steps_min", $_POST['minimumSteps']);
                $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], "desire_steps_max", $_POST['maximumSteps']);

                $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_Account";
                if ( file_exists( $babelCacheFile ) ) {
                    unlink($babelCacheFile);
                }

                echo "Step goal increase set to " . $_POST['desireSteps'] . "%, aiming for " . $_POST['maximumSteps'] . " with a lower limit of " . $_POST['minimumSteps'];

                http_response_code(200);
            }

        } else if ($_POST['formId'] == "journeySelector") {

            $core = new Core();
            $db_prefix = $core->getSetting("db_prefix", null, false);

            $dbJourneyId = $core->getDatabase()->get($db_prefix . "journeys", 'jid', ['name' => $_POST['selectedJourney']]);
            $core->getErrorRecording()->postDatabaseQuery($core->getDatabase(), [ "METHOD" => __METHOD__, "LINE" => __LINE__ ]);
            if ($_POST['selectedJourney'] == "Select a new Journey") {
                $dbJourney = $core->getDatabase()->delete($db_prefix . "journeys_travellers", ["fuid" => $_COOKIE[ '_nx_fb_usr' ]]);
                $core->getErrorRecording()->postDatabaseQuery($core->getDatabase(), [ "METHOD" => __METHOD__, "LINE" => __LINE__ ]);

                echo "Time to end your journey";
            } else {
                $dbJourney = $core->getDatabase()->insert($db_prefix . "journeys_travellers", ["jid" => $dbJourneyId, "fuid" => $_COOKIE[ '_nx_fb_usr' ], "start_date" => date('Y-m-d')]);
                echo "Get ready to start " . $_POST['selectedJourney'] . " journey";
            }

            $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_Account";
            if ( file_exists( $babelCacheFile ) ) {
                unlink($babelCacheFile);
            }

            $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_Journeys";
            if ( file_exists( $babelCacheFile ) ) {
                unlink($babelCacheFile);
            }

            $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_JourneysState";
            if ( file_exists( $babelCacheFile ) ) {
                unlink($babelCacheFile);
            }

            http_response_code(200);
        } else if ($_POST['formId'] == "pushSelector") {

            $core = new Core();
            $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], "push", $_POST['push']);
            $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], "push_steps", $_POST['push_steps']);
            $core->setUserSetting($_COOKIE[ '_nx_fb_usr' ], "push_length", $_POST['push_length']);

            $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_Account";
            if ( file_exists( $babelCacheFile ) ) {
                unlink($babelCacheFile);
            }

            $babelCacheFile = "cache" . DIRECTORY_SEPARATOR . "_" . $_COOKIE[ '_nx_fb_usr' ] . "_Push";
            if ( file_exists( $babelCacheFile ) ) {
                unlink($babelCacheFile);
            }

            echo "Okay";

            http_response_code(200);
        } else {
            nxr(1, "Unknown Form");
            nxr(0, print_r($_POST, true));
            echo "Unknown Form";
            http_response_code(403);
        }

    } else {
        nxr(1, "Invalid Form");
        echo "Invalid Form";
        http_response_code(403);
    }

} else {
    nxr(1, "User Unsecured");
    echo "Unauthorised Access";
    http_response_code(403);
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

/**
 * @param Core $fitbitApp
 * @param          $fuid
 *
 * @return string
 * @internal param array $_POST
 */
function gen_cookie_hash($fitbitApp, $fuid)
{
    return hash("sha256",
        $fitbitApp->getSetting("salt") . $fuid . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR']);
}