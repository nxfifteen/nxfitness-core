<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

require_once( dirname( __FILE__ ) . "/lib/autoloader.php" );

use Core\Core;

header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Force HTTPS
if ( $_SERVER[ 'SERVER_ADDR' ] != "10.1.1.1" && $_SERVER[ "HTTPS" ] != "on" ) {
    header( "Location: https://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] );
    exit();
}

if ( ! defined( 'DEBUG_MY_PROJECT' ) ) {
    define( 'DEBUG_MY_PROJECT', false );
}

// start the session
session_start();
if ( ! array_key_exists( "timeout", $_SESSION ) || ! is_numeric( $_SESSION[ 'timeout' ] ) ) {
    $_SESSION[ 'timeout' ] = time() + 60 * 5;
} else if ( $_SESSION[ 'timeout' ] < time() ) {
    nxr_destroy_session();
    header( "Location: ./" );
}

if ( ! array_key_exists( "core_config",
        $_SESSION ) || ! is_array( $_SESSION[ 'core_config' ] ) || count( $_SESSION[ 'core_config' ] ) == 0
) {
    require_once(dirname(__FILE__) . "/config/config.inc.php");
    if ( isset( $config ) ) {
        $_SESSION[ 'core_config' ] = $config;
    }
}

// Split-up the input URL to workout whats required
if ( array_key_exists( "REDIRECT_URL", $_SERVER ) ) {
    $inputURL = $_SERVER[ 'REDIRECT_URL' ];
} else {
    $inputURL = "";
}
$sysPath = str_ireplace( $_SESSION[ 'core_config' ][ 'url' ], "", $_SESSION[ 'core_config' ][ 'http/' ] );

if ( $sysPath != "/" ) {
    $inputURL = str_replace( $sysPath, "", $inputURL );
}
if ( substr( $inputURL, 0, 1 ) == "/" ) {
    $inputURL = substr( $inputURL, 1 );
}

$inputURL      = explode( "/", $inputURL );
$url_namespace = $inputURL[ 0 ];

nxr( 0, "Namespace Called: " . $url_namespace );

if ( $url_namespace == "register" && ! array_key_exists( "_nx_fb_usr", $_COOKIE ) ) {
    // Authorise a user against Fitbit's OAuth AIP
    nxr( 0, "New user registration started" );

    // Setup the App
    $NxFitbit = new Core();

    // Sent the user off too Fitbit to authenticate
    $helper = new djchen\OAuth2\Client\Provider\Fitbit( [
        'clientId'     => $NxFitbit->getSetting( "api_clientId", null, false ),
        'clientSecret' => $NxFitbit->getSetting( "api_clientSecret", null, false ),
        'redirectUri'  => $NxFitbit->getSetting( "api_redirectUri", null, false )
    ] );

    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $helper->getAuthorizationUrl( [
        'scope' => [
            'activity',
            'heartrate',
            'location',
            'nutrition',
            'profile',
            'settings',
            'sleep',
            'social',
            'weight'
        ]
    ] );

    // Get the state generated for you and store it to the session.
    $_SESSION[ 'oauth2state' ] = $helper->getState();

    // Redirect the user to the authorization URL.
    header( 'Location: ' . $authorizationUrl );
    exit;

} else if ( $url_namespace == "authorise" && array_key_exists( "_nx_fb_usr",
        $_COOKIE ) && $_COOKIE[ "_nx_fb_usr" ] != ""
) {
    // Authorise a user against Fitbit's OAuth AIP
    if ( DEBUG_MY_PROJECT ) {
        echo __FILE__ . " @" . __LINE__ . " ## authorise - " . $_COOKIE[ '_nx_fb_usr' ] . "<br />\n";
    }

    // Setup the App
    $NxFitbit = new Core();

    // We're even talking about a valid user right?
    if ( $NxFitbit->isUser( $_COOKIE[ '_nx_fb_usr' ] ) ) {

        // And lets double check we still need to register
        if ( $NxFitbit->valdidateOAuth( $NxFitbit->getUserOAuthTokens( $_COOKIE[ '_nx_fb_usr' ], false ) ) ) {
            if ( DEBUG_MY_PROJECT ) {
                echo __FILE__ . " @" . __LINE__ . " ## " . $_COOKIE[ '_nx_fb_usr' ] . " is already authorised with Fitbit<br />\n";
            } else {
                header( "Location: " . $_SESSION[ 'core_config' ][ 'http/admin' ] . "/" );
                exit();
            }
        } else {
            // Sent the user off too Fitbit to authenticate
            if ( $_COOKIE[ '_nx_fb_usr' ] == $NxFitbit->getSetting( "ownerFuid" ) ) {
                $personal = "_personal";
            } else {
                $personal = "";
            }

            $helper = new djchen\OAuth2\Client\Provider\Fitbit( [
                'clientId'     => $NxFitbit->getSetting( "api_clientId" . $personal, null, false ),
                'clientSecret' => $NxFitbit->getSetting( "api_clientSecret" . $personal, null, false ),
                'redirectUri'  => $NxFitbit->getSetting( "api_redirectUri" . $personal, null, false )
            ] );

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $helper->getAuthorizationUrl( [
                'scope' => [
                    'activity',
                    'heartrate',
                    'location',
                    'nutrition',
                    'profile',
                    'settings',
                    'sleep',
                    'social',
                    'weight'
                ]
            ] );

            // Get the state generated for you and store it to the session.
            $_SESSION[ 'oauth2state' ] = $helper->getState();

            // Redirect the user to the authorization URL.
            header( 'Location: ' . $authorizationUrl );
            exit;

        }

    } else if ( DEBUG_MY_PROJECT ) {
        echo __FILE__ . " @" . __LINE__ . " ## This is not a valid user<br />\n";
    } else {
        // When we don't know what to do put the user over to the user interface screens
        header( "Location: " . $_SESSION[ 'core_config' ][ 'http/admin' ] . "/" );
        exit();
    }

} else if ( $url_namespace == "callback" || $url_namespace == "rti" ) {
    // Process the return information from a Fitbit authentication flow
    if ( empty( $_GET[ 'state' ] ) || ( $_GET[ 'state' ] !== $_SESSION[ 'oauth2state' ] ) ) {
        unset( $_SESSION[ 'oauth2state' ] );
        exit( 'Invalid state' );
    } else {
        try {
            // Setup the App
            $NxFitbit = new Core();

            // Sent the user off too Fitbit to authenticate
            if ( $url_namespace == "rti" ) {
                $personal = "_personal";
            } else {
                $personal = "";
            }

            $helper = new djchen\OAuth2\Client\Provider\Fitbit( [
                'clientId'     => $NxFitbit->getSetting( "api_clientId" . $personal, null, false ),
                'clientSecret' => $NxFitbit->getSetting( "api_clientSecret" . $personal, null, false ),
                'redirectUri'  => $NxFitbit->getSetting( "api_redirectUri" . $personal, null, false )
            ] );

            // Try to get an access token using the authorization code grant.
            $accessToken = $helper->getAccessToken( 'authorization_code', [
                'code' => $_GET[ 'code' ]
            ] );

            // Find out who the new OAuth keys belong too
            $resourceOwner = $helper->getResourceOwner( $accessToken );

            // Check again that this really is one of our users
            if ( $NxFitbit->isUser( $resourceOwner->getId() ) ) {
                nxr( 0, "User OAuth credentials installed" );

                // Update the users new keys
                $NxFitbit->setUserOAuthTokens( $resourceOwner->getId(), $accessToken );

                // Since we're done pass them back to the Admin UI
                header( "Location: " . $_SESSION[ 'core_config' ][ 'http/admin' ] . "/" );
                exit();

            } else {
                nxr( 1, "OAuth return for new user: " . $resourceOwner->getId() );

                $pre_auth = $NxFitbit->getSetting( "owners_friends" );
                $pre_auth = explode( ",", $pre_auth );
                array_push( $pre_auth, $NxFitbit->getSetting( "ownerFuid" ) );
                if ( array_search( $resourceOwner->getId(), $pre_auth ) ) {
                    $newUserName = $resourceOwner->getId();
                    $NxFitbit->getFitbitAPI( $newUserName )->setUserAccessToken( $accessToken );
                    $NxFitbit->getFitbitAPI( $newUserName )->setActiveUser( $newUserName );
                    $newUserProfile = $NxFitbit->getFitbitAPI( $newUserName )->pullBabel( 'user/-/profile.json', true );

                    if ( $NxFitbit->getFitbitAPI( $newUserName )->createNewUser( $newUserProfile->user ) ) {
                        nxr( 2, "User sent to new password screen" );
                        header( "Location: " . $_SESSION[ 'core_config' ][ 'http/admin' ] . "/views/pages/register.php?usr=" . $newUserName );
                    }
                } else {
                    nxr( 2, "Non Friend registration: " . $resourceOwner->getId() );
                    header( "Location: " . $_SESSION[ 'core_config' ][ 'http/admin' ] . "/?err=500" );
                }

                // When we don't know what to do put the user over to the user interface screens
                exit();
            }

        } catch ( \League\OAuth2\Client\Provider\Exception\IdentityProviderException $e ) {
            $this->getAppClass()->getErrorRecording()->captureException( $e, [
                'extra' => [
                    'php_version'  => phpversion(),
                    'core_version' => $this->getAppClass()->getSetting( "version", "0.0.0.1", true )
                ],
            ] );
            exit( $e->getMessage() );
        }

    }

} else if ( $url_namespace == "webhook" || $url_namespace == "service" ) {
    if ( is_array( $_GET ) && array_key_exists( "verify", $_GET ) ) {
        $config = [];
        require_once(dirname(__FILE__) . "/config/config.inc.php");
        if ( ( is_array( $config[ 'api_subValidity' ] ) and in_array( $_GET[ 'verify' ],
                    $config[ 'api_subValidity' ] ) ) OR ( $_GET[ 'verify' ] == $config[ 'api_subValidity' ] )
        ) {
            header( 'Cache-Control: no-cache, must-revalidate' );
            header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
            header( 'Content-type: text/plain' );
            header( 'HTTP/1.0 204 No Content' );

            nxr( 0, "Valid subscriber request - " . $url_namespace );
        } else {
            header( 'Cache-Control: no-cache, must-revalidate' );
            header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
            header( 'HTTP/1.0 404 Not Found' );

            nxr( 0, "Invalid subscriber request - '" . $_GET[ 'verify' ] . "' - " . $url_namespace );
            nxr( 0, print_r( $config[ 'api_subValidity' ], true ) );
        }

    } else {
        // Deal with Fitbit subscriptions
        require_once( dirname( __FILE__ ) . "/service.php" );
    }

} else if ( $url_namespace == "habitica" ) {
    date_default_timezone_set( 'Europe/London' );

    header( 'Cache-Control: no-cache, must-revalidate' );
    header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
    header( 'Content-type: application/json' );

    //nxr( 1, "Processing Habitica Webhook" );

    $payload = file_get_contents( 'php://input' );
    $data = json_decode( $payload, TRUE );

    //nxr( 2, "Hook for user ID: " . $data['user']['_id'] );
    $config = [];
    require(dirname(__FILE__) . "/config/config.inc.php");
    if (in_array($data['user']['_id'], $config)) {
        $coreUserId = str_replace("user_id_", "", array_search($data['user']['_id'], $config));
        //nxr( 2, "User key: " . $coreUserId );

        $fitbitApp = new Core();
        if ( $fitbitApp->isUser( $coreUserId ) ) {
            if ( $fitbitApp->getDatabase()->has( $fitbitApp->getSetting( "db_prefix", null,
                    false ) . "runlog", [
                "AND" => [
                    "user"     => $coreUserId,
                    "activity" => 'habitica'
                ]
            ] )
            ) {
                $fields = [
                    "date"     => date( "Y-m-d H:i:s" ),
                    "cooldown" => "1970-01-01 01:00:00"
                ];
                $fitbitApp->getDatabase()->update( $fitbitApp->getSetting( "db_prefix", null,
                        false ) . "runlog", $fields, [
                    "AND" => [
                        "user"     => $coreUserId,
                        "activity" => 'habitica'
                    ]
                ] );
                $fitbitApp->getErrorRecording()->postDatabaseQuery( $fitbitApp->getDatabase(), [
                    "METHOD" => __FILE__,
                    "LINE"   => __LINE__
                ] );
            } else {
                $fields = [
                    "user"     => $coreUserId,
                    "activity" => 'habitica',
                    "date"     => date( "Y-m-d H:i:s" ),
                    "cooldown" => "1970-01-01 01:00:00"
                ];
                $fitbitApp->getDatabase()->insert( $fitbitApp->getSetting( "db_prefix", null,
                        false ) . "runlog", $fields );
                $fitbitApp->getErrorRecording()->postDatabaseQuery( $fitbitApp->getDatabase(), [
                    "METHOD" => __FILE__,
                    "LINE"   => __LINE__
                ] );
            }

            $fitbitApp->addCronJob( $coreUserId, 'habitica', true );
            nxr(1, "New API request: " . $coreUserId);

            if ($data['direction'] == "up") {
                $icoColour = "bg-success";
            } else {
                $icoColour = "bg-danger";
            }

            if (preg_match('/^:/m', $data['task']['text'])) {
                $data['task']['text'] = preg_replace('/^:([\w_]+): (.+)/m', '$1', $data['task']['text']);
            }

            $db_prefix = $fitbitApp->getSetting("db_prefix", null, false);
            $fitbitApp->getDatabase()->insert($db_prefix . "inbox",
                [
                    "fuid" => $coreUserId,
                    "expires" => date("Y-m-d H:i:s", strtotime('+6 hours')),
                    "ico" => 'fa fa-rebel',
                    "icoColour" => $icoColour,
                    "subject" => $data['task']['text'],
                    "body" => $data['task']['updatedAt'],
                    "bold" => round($data['task']['value'], 2)
                ]
            );


        } else {
            nxr( 2, "Not a valid database user" );
        }
    } else {
        nxr( 2, "Unknown user ID" );
    }

    header( 'HTTP/1.0 204 No Content' );

} else if ( $url_namespace != "" && DEBUG_MY_PROJECT ) {
    header( 'Cache-Control: no-cache, must-revalidate' );
    header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
    header( 'HTTP/1.0 404 Not Found' );

    // If we're debugging things print out the unknown namespace
    nxr( 1, "404 Not Found" );

} else {
    // When we don't know what to do put the user over to the user interface screens
    header( "Location: " . $_SESSION[ 'core_config' ][ 'http/admin' ] . "/" );
    exit();
}
