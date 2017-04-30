<?php
    use UX\NxFitAdmin;

    session_start();
    header('Content-type: text/javascript');

    define('CORE_PROJECT_ROOT', $_SESSION['CORE_PROJECT_ROOT']);
    define("CORE_UX", $_SESSION['CORE_UX']);
    define("CORE_ROOT", $_SESSION['CORE_ROOT']);

    require_once(dirname(__FILE__) . "/../../_class/NxFitAdmin.php");
    $App         = new NxFitAdmin($_COOKIE['_nx_fb_usr']);
    $userProfile = $App->getUserProfile();

    echo "var userProfileName = '" . $userProfile['name'] . "';";
    echo "var userProfileAvatar = '" . $userProfile['avatar'] . "';";

    unset($App);
