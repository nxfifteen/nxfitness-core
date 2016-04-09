<?php

    if($_SERVER["HTTPS"] != "on") {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }

    require_once(dirname(__FILE__) . "/inc/app.php");
    $fitbitApp = new NxFitbit();

    $inputURL = $_SERVER['REDIRECT_URL'];
    $sysPath = $fitbitApp->getSetting("path", "/", FALSE);
    if ($sysPath != "/") {
        $inputURL = str_replace($sysPath, "", $inputURL);
    }
    $inputURL = explode("/", $inputURL);
    //array_shift($inputURL);

    $fibit_id = $inputURL[0];

    if ($fitbitApp->isUser($fibit_id)) {
        $userArray = $fitbitApp->getDatabase()->get($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "users", array('name', 'token', 'secret'), array("fuid" => $fibit_id));
        if (is_array($userArray)) {
            if (count($inputURL) > 1 AND $inputURL[1] == "authorise") {
                session_destroy();
                $fitbitApp->getFitbitapi()->getLibrary()->resetSession();
                $fitbitApp->getFitbitapi()->getLibrary()->initSession($fitbitApp->getSetting("url", "http://" . $_SERVER["HTTP_HOST"], FALSE) . $sysPath . $fibit_id . "/callback");
            } else if (count($inputURL) > 1 AND $inputURL[1] == "callback") {
                $fitbitApp->getFitbitapi()->getLibrary()->initSession($fitbitApp->getSetting("url", "http://" . $_SERVER["HTTP_HOST"], FALSE) . $sysPath . $fibit_id . "/callback");
                $fitbitApp->getDatabase()->update($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "users", array(
                    'token'  => $fitbitApp->getFitbitapi()->getLibrary()->getOAuthToken(),
                    'secret' => $fitbitApp->getFitbitapi()->getLibrary()->getOAuthSecret()
                ), array("fuid" => $fibit_id));

                header('Location: ' . $fitbitApp->getSetting("url", "http://" . $_SERVER["HTTP_HOST"], FALSE) . $sysPath . $fibit_id);
            } else {
                echo "Welcome back " . $userArray['name'] . ".<br/>\n";

                if ($userArray['token'] == "" OR $userArray['secret'] == "") {
                    echo "You still need to <a href='" . $fitbitApp->getSetting("url", "http://" . $_SERVER["HTTP_HOST"], FALSE) . $sysPath . $fibit_id . "/authorise'>authorise</a> this app<br/>\n";
                } else {
                    $fitbitApp->getFitbitapi()->oAuthorise($fibit_id);

                    $profile = $fitbitApp->getFitbitapi()->pull($fibit_id, "profile", TRUE);
                    if (is_numeric($profile) AND $profile < 0) {
                        echo "Error profile: " . $fitbitApp->lookupErrorCode($profile);
                    } else {
                        echo "<pre>";
                        print_r($profile);
                        echo "</pre>";
                    }
                }
            }
        }

    } else {
        echo "Hi " . $fibit_id . ", unfortunitly this site is not open to public registration.";
    }

