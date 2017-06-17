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

} else {
    nxr(1, "User Unsecured");
    http_response_code(403);
    echo "Unauthorised Access";
}
