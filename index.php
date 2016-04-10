<?php
if (!defined('DEBUG_MY_PROJECT')) define('DEBUG_MY_PROJECT', FALSE);

// Force HTTPS
if ($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

if (!function_exists("nxr")) {
    function nxr($msg)
    {
        if (is_writable(dirname(__FILE__) . "/fitbit.log")) {
            $fh = fopen(dirname(__FILE__) . "/fitbit.log", "a");
            fwrite($fh, date("Y-m-d H:i:s") . ": " . $msg . "\n");
            fclose($fh);
        }

        if (php_sapi_name() == "cli") {
            echo date("Y-m-d H:i:s") . ": " . $msg . "\n";
        }
    }
}

// Split-up the input URL to workout whats required
$inputURL = $_SERVER['REDIRECT_URL'];

// TODO: GitLab Issue #7 - Removed include as it breaks the config class when building up the full app
/*require_once(dirname(__FILE__) . "/config.inc.php");
if (array_key_exists("path", $config) && $config["path"] != "") {
    $sysPath = $config["path"];
} else {
    $sysPath = "/";
}*/
$sysPath = "/api/fitbit/";
if ($sysPath != "/") {
    $inputURL = str_replace($sysPath, "", $inputURL);
}
$inputURL = explode("/", $inputURL);
$url_namespace = $inputURL[0];

// start the session
session_start();

if ($url_namespace == "authorise" && array_key_exists("_nx_fb_usr", $_COOKIE) && $_COOKIE["_nx_fb_usr"] != "") {
    // Authorise a user against Fitbit's OAuth AIP
    if (DEBUG_MY_PROJECT) {
        echo __FILE__ . " @" . __LINE__ . " ## authorise - " . $_COOKIE['_nx_fb_usr'] . "<br />\n";
    }

    // Setup the App
    require_once(dirname(__FILE__) . "/inc/app.php");
    $NxFitbit = new NxFitbit();

    // We're even talking about a valid user right?
    if ($NxFitbit->isUser($_COOKIE['_nx_fb_usr'])) {

        // And lets double check we still need to register
        if ($NxFitbit->valdidateOAuth($NxFitbit->getUserOAuthTokens($_COOKIE['_nx_fb_usr'], FALSE))) {
            if (DEBUG_MY_PROJECT) {
                echo __FILE__ . " @" . __LINE__ . " ## " . $_COOKIE['_nx_fb_usr'] . " is already authorised with Fitbit<br />\n";
            } else {
                header("Location: https://" . $_SERVER["HTTP_HOST"] . $NxFitbit->getSetting("path", NULL, FALSE) . "admin/");
                exit();
            }
        } else {
            // Sent the user off too Fitbit to authenticate

            $helper = new djchen\OAuth2\Client\Provider\Fitbit([
                'clientId' => $NxFitbit->getSetting("fitbit_clientId", NULL, FALSE),
                'clientSecret' => $NxFitbit->getSetting("fitbit_clientSecret", NULL, FALSE),
                'redirectUri' => $NxFitbit->getSetting("fitbit_redirectUri", NULL, FALSE)
            ]);

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $helper->getAuthorizationUrl(array('scope' => array('activity', 'heartrate', 'location', 'nutrition', 'profile', 'settings', 'sleep', 'social', 'weight')));

            // Get the state generated for you and store it to the session.
            $_SESSION['oauth2state'] = $helper->getState();

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;

        }

    } else if (DEBUG_MY_PROJECT) {
        echo __FILE__ . " @" . __LINE__ . " ## This is not a valid user<br />\n";
    } else {
        // When we dont know what to do put the user over to the user interface screens
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $NxFitbit->getSetting("path", NULL, FALSE) . "admin/");
        exit();
    }

} else if ($url_namespace == "callback") {
    // Process the return information from a Fitbit authentication flow
    if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
        exit('Invalid state');
    } else {
        try {
            // Setup the App
            require_once(dirname(__FILE__) . "/inc/app.php");
            $NxFitbit = new NxFitbit();

            $helper = new djchen\OAuth2\Client\Provider\Fitbit([
                'clientId' => $NxFitbit->getSetting("fitbit_clientId", NULL, FALSE),
                'clientSecret' => $NxFitbit->getSetting("fitbit_clientSecret", NULL, FALSE),
                'redirectUri' => $NxFitbit->getSetting("fitbit_redirectUri", NULL, FALSE)
            ]);

            // Try to get an access token using the authorization code grant.
            $accessToken = $helper->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            // Findout who the new OAuth keys belong too
            $resourceOwner = $helper->getResourceOwner($accessToken);

            // Check again that this really is one of our users
            if ($NxFitbit->isUser($resourceOwner->getId())) {
                // Update the users new keys
                $NxFitbit->setUserOAuthTokens($resourceOwner->getId(), $accessToken);

                // Since we're done pass them back to the Admin UI
                header("Location: https://" . $_SERVER["HTTP_HOST"] . $NxFitbit->getSetting("path", NULL, FALSE) . "admin/");
                exit();

            } else if (DEBUG_MY_PROJECT) {
                echo __FILE__ . " @" . __LINE__ . " ## The returned OAuth '" . $resourceOwner->getId() . "' is not for one of our users<br />\n";

            } else {
                // When we don't know what to do put the user over to the user interface screens
                header("Location: https://" . $_SERVER["HTTP_HOST"] . $NxFitbit->getSetting("path", NULL, FALSE) . "admin/");
                exit();
            }

        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            exit($e->getMessage());
        }

    }

} else if ($url_namespace == "service") {
    if (is_array($_GET) && array_key_exists("verify", $_GET)) {
        require_once(dirname(__FILE__) . "/config.inc.php");
        if ($_GET['verify'] == $config['fitbit_subscriber_id']) {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: text/plain');
            header('HTTP/1.0 204 No Content');

            nxr("Valid subscriber request - " . $url_namespace);
        } else {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('HTTP/1.0 404 Not Found');

            nxr("Invalid subscriber request - " . $_GET['verify'] . " - " . $url_namespace);
        }
    } else {
        // Deal with Fitbit subsciptions
        require_once(dirname(__FILE__) . "/service.php");
    }

} else if ($url_namespace == "webhook") {
    if (is_array($_GET) && array_key_exists("verify", $_GET)) {
        require_once(dirname(__FILE__) . "/config.inc.php");
        if ($_GET['verify'] == $config['fitbit_subscriber_id']) {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: text/plain');
            header('HTTP/1.0 204 No Content');

            nxr("Valid subscriber request - " . $url_namespace);
        } else {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('HTTP/1.0 404 Not Found');

            nxr("Invalid subscriber request - " . $_GET['verify'] . " - " . $url_namespace);
        }
    } else {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('HTTP/1.0 404 Not Found');

        // If we're debugging things print out the unknown namespace
        nxr("Namespace Called: " . $url_namespace);
    }
} else if ($url_namespace != "" && DEBUG_MY_PROJECT) {
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('HTTP/1.0 404 Not Found');

    // If we're debugging things print out the unknown namespace
    nxr("Namespace Called: " . $url_namespace);

} else {
    // When we dont know what to do put the user over to the user interface screens
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "admin/");
    exit();
}
