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
header( 'Content-type: application/json' );

require_once( dirname( __FILE__ ) . "/lib/autoloader.php" );

nxr(0, print_r($_POST, true));
if (array_key_exists('_nx_fb_usr', $_COOKIE) && $_COOKIE['_nx_fb_usr'] != "") {
    nxr(1, "User " . $_COOKIE['_nx_fb_usr'] . " Secured");

    if (array_key_exists('token', $_POST) && array_key_exists('userid', $_POST)) {
        $apiUserid = $_POST['userid'];
        $apiKey = $_POST['token'];
    } else {
        $_POST['confirmPassword'] = $_POST['password2'];
        unset($_POST['password2']);
        $habiticaClass = new HabitRPHPG(null, null);
        $newUser = $habiticaClass->_request("post", "user/auth/local/register", $_POST);

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

    $cacheFile = 'cache' . DIRECTORY_SEPARATOR . '_' . $_GET[ 'user' ] . '_xp';

    if ( file_exists( $cacheFile ) ) {
        unlink($cacheFile);
    }


} else {
    nxr(1, "User Unsecured");
}

echo "{'result':'oky'}";
