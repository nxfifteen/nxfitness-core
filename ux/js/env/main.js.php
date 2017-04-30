<?php
    use UX\NxFitAdmin;

    session_start();
    header('Content-type: text/javascript');

    define('CORE_PROJECT_ROOT', $_SESSION['CORE_PROJECT_ROOT']);
    define("CORE_UX", $_SESSION['CORE_UX']);
    define("CORE_ROOT", $_SESSION['CORE_ROOT']);

    $App = new NxFitAdmin($_COOKIE['_nx_fb_usr']);

    echo "var localWeatherImage = '" . $App->getLocalWeatherImage() . "';";

    unset($App);
