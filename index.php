<?php
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');

    // Force HTTPS
    if ($_SERVER['SERVER_ADDR'] != "10.1.1.1" && $_SERVER["HTTPS"] != "on") {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }

    $http = $_SERVER["HTTPS"] != "on" ? "http" : "https";
    if ($_SERVER['SERVER_ADDR'] == "10.1.1.1" && $_SERVER['REDIRECT_URL'] == "/api/fitbit/json.php") {
        header("Location: " . $http . "://" . $_SERVER["HTTP_HOST"] . $sysPath . "/json.php?" . http_build_query($_GET));
        die();
    }

    if (!function_exists("nxr")) {
        /**
         * NXR is a helper function. Past strings are recorded in a text file
         * and when run from a command line output is displayed on screen as
         * well
         *
         * @param string $msg String input to be displayed in logs files
         * @param bool   $includeDate
         * @param bool   $newline
         */
        function nxr($msg, $includeDate = TRUE, $newline = TRUE) {
            if ($includeDate) $msg = date("Y-m-d H:i:s") . ": " . $msg;
            if ($newline) $msg = $msg . "\n";

            if (is_writable(dirname(__FILE__) . "/fitbit.log")) {
                $fh = fopen(dirname(__FILE__) . "/fitbit.log", "a");
                fwrite($fh, $msg);
                fclose($fh);
            }
        }
    }

    if (!defined('DEBUG_MY_PROJECT')) define('DEBUG_MY_PROJECT', FALSE);

    // Split-up the input URL to workout whats required
    $inputURL = $_SERVER['REDIRECT_URL'];

    // TODO: GitLab Issue #7 - Removed include as it breaks the config class when building up the full app
    $sysPath = $_SERVER['SERVER_ADDR'] != "10.1.1.1" ? "/api/fitbit/" : "/";
    if ($sysPath != "/") {
        $inputURL = str_replace($sysPath, "", $inputURL);
    }
    $inputURL = explode("/", $inputURL);
    $url_namespace = $inputURL[0];

    // start the session
    session_start();

    if ($url_namespace == "authorise" && !array_key_exists("_nx_fb_usr", $_COOKIE)) {
        // Authorise a user against Fitbit's OAuth AIP
        nxr("New user registration started");

        // Setup the App
        require_once(dirname(__FILE__) . "/inc/app.php");
        $NxFitbit = new NxFitbit();

        // Sent the user off too Fitbit to authenticate
        $helper = new djchen\OAuth2\Client\Provider\Fitbit([
            'clientId'     => $NxFitbit->getSetting("fitbit_clientId", NULL, FALSE),
            'clientSecret' => $NxFitbit->getSetting("fitbit_clientSecret", NULL, FALSE),
            'redirectUri'  => $NxFitbit->getSetting("fitbit_redirectUri", NULL, FALSE)
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

    } else if ($url_namespace == "authorise" && array_key_exists("_nx_fb_usr", $_COOKIE) && $_COOKIE["_nx_fb_usr"] != "") {
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
                    header("Location: " . $http . "://" . $_SERVER["HTTP_HOST"] . $NxFitbit->getSetting("path", NULL, FALSE) . "admin/");
                    exit();
                }
            } else {
                // Sent the user off too Fitbit to authenticate
                if ($_COOKIE['_nx_fb_usr'] == $NxFitbit->getSetting("fitbit_owner_id")) {
                    $personal = "_personal";
                } else {
                    $personal = "";
                }

                $helper = new djchen\OAuth2\Client\Provider\Fitbit([
                    'clientId'     => $NxFitbit->getSetting("fitbit_clientId" . $personal, NULL, FALSE),
                    'clientSecret' => $NxFitbit->getSetting("fitbit_clientSecret" . $personal, NULL, FALSE),
                    'redirectUri'  => $NxFitbit->getSetting("fitbit_redirectUri" . $personal, NULL, FALSE)
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
            // When we don't know what to do put the user over to the user interface screens
            header("Location: " . $http . "://" . $_SERVER["HTTP_HOST"] . $NxFitbit->getSetting("path", NULL, FALSE) . "admin/");
            exit();
        }

    } else if ($url_namespace == "callback" || $url_namespace == "rti") {
        // Process the return information from a Fitbit authentication flow
        if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        } else {
            try {
                // Setup the App
                require_once(dirname(__FILE__) . "/inc/app.php");
                $NxFitbit = new NxFitbit();

                // Sent the user off too Fitbit to authenticate
                if ($url_namespace == "rti") {
                    $personal = "_personal";
                } else {
                    $personal = "";
                }

                $helper = new djchen\OAuth2\Client\Provider\Fitbit([
                    'clientId'     => $NxFitbit->getSetting("fitbit_clientId" . $personal, NULL, FALSE),
                    'clientSecret' => $NxFitbit->getSetting("fitbit_clientSecret" . $personal, NULL, FALSE),
                    'redirectUri'  => $NxFitbit->getSetting("fitbit_redirectUri" . $personal, NULL, FALSE)
                ]);

                // Try to get an access token using the authorization code grant.
                $accessToken = $helper->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                // Find out who the new OAuth keys belong too
                $resourceOwner = $helper->getResourceOwner($accessToken);

                // Check again that this really is one of our users
                if ($NxFitbit->isUser($resourceOwner->getId())) {
                    // Update the users new keys
                    $NxFitbit->setUserOAuthTokens($resourceOwner->getId(), $accessToken);

                    // Since we're done pass them back to the Admin UI
                    header("Location: " . $http . "://" . $_SERVER["HTTP_HOST"] . $NxFitbit->getSetting("path", NULL, FALSE) . "admin/");
                    exit();

                } else {
                    $pre_auth = $NxFitbit->getSetting("owners_friends");
                    $pre_auth = explode(",", $pre_auth);
                    if (array_search($resourceOwner->getId(), $pre_auth)) {
                        $newUserName = $resourceOwner->getId();
                        $NxFitbit->getFitbitAPI($newUserName)->setUserAccessToken($accessToken);
                        $NxFitbit->getFitbitAPI($newUserName)->setActiveUser($newUserName);
                        $newUserProfile = $NxFitbit->getFitbitAPI($newUserName)->pullBabel('user/-/profile.json', TRUE);

                        if ($NxFitbit->getFitbitAPI($newUserName)->createNewUser($newUserProfile->user)) {
                            echo "Thank you Everything has worked correctly. Sadly this is as far as I can take you for now.";
                        }

                        //TODO: add new user details

                    } else {
                        nxr(" Non Friend registration: " . $resourceOwner->getId());
                        header("Location: " . $http . "://" . $_SERVER["HTTP_HOST"] . $NxFitbit->getSetting("path", NULL, FALSE) . "admin/");
                    }

                    // When we don't know what to do put the user over to the user interface screens
                    exit();
                }

            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                exit($e->getMessage());
            }

        }

    } else if ($url_namespace == "webhook" || $url_namespace == "service") {
        nxr("Namespace Called: " . $url_namespace);

        if (is_array($_GET) && array_key_exists("verify", $_GET)) {
            require_once(dirname(__FILE__) . "/config.inc.php");
            if ((is_array($config['fitbit_subscriber_id']) and array_search($_GET['verify'], $config['fitbit_subscriber_id'])) OR ($_GET['verify'] == $config['fitbit_subscriber_id'])) {
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
            // Deal with Fitbit subscriptions
            require_once(dirname(__FILE__) . "/service.php");
        }

    } else if ($url_namespace != "" && DEBUG_MY_PROJECT) {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('HTTP/1.0 404 Not Found');

        // If we're debugging things print out the unknown namespace
        nxr("Namespace Called: " . $url_namespace);

    } else {
        $sysPath = $_SERVER['SERVER_ADDR'] != "10.1.1.1" ? "/api/fitbit/" : "/";
        // When we don't know what to do put the user over to the user interface screens
        header("Location: " . $http . "://" . $_SERVER["HTTP_HOST"] . $sysPath . "admin/");
        exit();
    }
