<?php
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');

    require_once(dirname(__FILE__) . "/../../inc/app.php");
    $fitbitApp = new NxFitbit();

    if (array_key_exists("fuid", $_POST)) {
        $valid = $fitbitApp->isUserValid($_POST['fuid'], hash("sha256", $fitbitApp->getSetting("salt") . $_POST['password']));
        if ($valid == -1 and array_key_exists("password", $_POST) and array_key_exists("password2", $_POST) and $_POST['password'] == $_POST['password2']) {
            $newUserArray = array('password' => hash("sha256", $fitbitApp->getSetting("salt") . $_POST['password']));
            if (array_key_exists("email", $_POST) and $_POST['email'] != "")  $newUserArray['eml'] = $_POST['email'];
            $fitbitApp->getDatabase()->update($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "users", $newUserArray, array('fuid' => $_POST['fuid']));

            setcookie('_nx_fb_usr', $_POST['fuid'], false, '/', $_SERVER['SERVER_NAME']);
            setcookie('_nx_fb_key', gen_cookie_hash($fitbitApp, $_POST['fuid']), false, '/', $_SERVER['SERVER_NAME']);

            header("Location: " . $_SESSION['admin_config']['http/admin'] . "/");
        } else if ($valid == -1 and array_key_exists("password", $_POST) and array_key_exists("password2", $_POST) and $_POST['password'] != $_POST['password2']) {
            header("Location: " . $_SESSION['admin_config']['http/admin'] . "/register?usr=".$_POST['fuid']."&eml=".$_POST['eml']."&err=Passwords Dont Match");
        } else {
            if ($valid != -1 and is_string($valid)) {
                if (isset($_POST['remember'])) {
                    /* Set cookie to last 1 year */
                    setcookie('_nx_fb_usr', $valid, time()+60*60*24*365, '/', $_SERVER['SERVER_NAME']);
                    setcookie('_nx_fb_key', gen_cookie_hash($fitbitApp, $valid), time()+60*60*24*365, '/', $_SERVER['SERVER_NAME']);
                } else {
                    /* Cookie expires when browser closes */
                    setcookie('_nx_fb_usr', $valid, false, '/', $_SERVER['SERVER_NAME']);
                    setcookie('_nx_fb_key', gen_cookie_hash($fitbitApp, $valid), false, '/', $_SERVER['SERVER_NAME']);
                }
                header("Location: " . $_SESSION['admin_config']['http/admin'] . "/");
            } else if ($valid == -1) {
                header("Location: " . $_SESSION['admin_config']['http/admin'] . "/register?usr=".$_POST['fuid']);
            } else {
                header("Location: " . $_SESSION['admin_config']['http/admin'] . "/login?usr=".$valid."&err=Username/Password Invalid");
            }
        }
    } else {
        header("Location: " . $_SESSION['admin_config']['http/admin'] . "/login");
    }

    /**
     * @param NxFitbit $fitbitApp
     * @param array $_POST
     *
     * @return string
     */
    function gen_cookie_hash($fitbitApp, $fuid) {
        return hash("sha256", $fitbitApp->getSetting("salt") . $fuid . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR']);
    }

