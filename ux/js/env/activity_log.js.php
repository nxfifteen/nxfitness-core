<?php
    session_start();
    header('Content-type: text/javascript');

    define('PROJECT_ROOT', $_SESSION['PROJECT_ROOT']);
    define("PATH_ADMIN", $_SESSION['PATH_ADMIN']);
    define("PATH_ROOT", $_SESSION['PATH_ROOT']);

    require_once( dirname(__FILE__) . "/../../_class/NxFitAdmin.php" );
    $App         = new NxFitAdmin($_COOKIE['_nx_fb_usr']);
    $userProfile = $App->getUserProfile();

    echo "var userProfileName = '" . $userProfile['name'] . "';";
    echo "var userProfileAvatar = '" . $userProfile['avatar'] . "';";

    unset($App);