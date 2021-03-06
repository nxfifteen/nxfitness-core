<?php
	header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', FALSE );
	header( 'Pragma: no-cache' );

	// Force HTTPS
	if ( $_SERVER['SERVER_ADDR'] != "10.1.1.1" && $_SERVER["HTTPS"] != "on" ) {
		header( "Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
		exit();
	}

	if ( ! function_exists( "nxr_destroy_session" ) ) {
		function nxr_destroy_session() {
			// Unset all of the session variables.
			$_SESSION = array();

			// If it's desired to kill the session, also delete the session cookie.
			// Note: This will destroy the session, and not just the session data!
			if ( ini_get( "session.use_cookies" ) ) {
				$params = session_get_cookie_params();
				setcookie( session_name(), '', time() - 42000,
					$params["path"], $params["domain"],
					$params["secure"], $params["httponly"]
				);
			}

			// Finally, destroy the session.
			session_destroy();
		}
	}

	if ( ! function_exists( "nxr" ) ) {
		require_once( dirname( __FILE__ ) . "/inc/functions.php" );
	}

	if ( ! defined( 'DEBUG_MY_PROJECT' ) ) {
		define( 'DEBUG_MY_PROJECT', FALSE );
	}

	// start the session
	session_start();
	if ( ! array_key_exists( "timeout", $_SESSION ) || ! is_numeric( $_SESSION['timeout'] ) ) {
		$_SESSION['timeout'] = time() + 60 * 5;
	} else if ( $_SESSION['timeout'] < time() ) {
		nxr_destroy_session();
		header( "Location: ./" );
	}

	if ( ! array_key_exists( "core_config", $_SESSION ) || ! is_array( $_SESSION['core_config'] ) || count( $_SESSION['core_config'] ) == 0 ) {
		require_once( dirname( __FILE__ ) . "/config.inc.php" );
		if ( isset( $config ) ) {
			$_SESSION['core_config'] = $config;
		}
	}

	// Split-up the input URL to workout whats required
	if ( array_key_exists( "REDIRECT_URL", $_SERVER ) ) {
		$inputURL = $_SERVER['REDIRECT_URL'];
	} else {
		$inputURL = "";
	}
	$sysPath = str_ireplace( $_SESSION['core_config']['url'], "", $_SESSION['core_config']['http/'] );

	nxr( "inputURL: " . $inputURL );
	nxr( "sysPath: " . $sysPath );
	//nxr("sysPath: " . $_SERVER['']);

	if ( $sysPath != "/" ) {
		$inputURL = str_replace( $sysPath, "", $inputURL );
	}
	if ( substr( $inputURL, 0, 1 ) == "/" ) {
		$inputURL = substr( $inputURL, 1 );
	}

	$inputURL      = explode( "/", $inputURL );
	$url_namespace = $inputURL[0];

	nxr( "Namespace Called: " . $url_namespace );

	if ( $url_namespace == "register" && ! array_key_exists( "_nx_fb_usr", $_COOKIE ) ) {
		// Authorise a user against Fitbit's OAuth AIP
		nxr( "New user registration started" );

		// Setup the App
		require_once( dirname( __FILE__ ) . "/inc/app.php" );
		$NxFitbit = new NxFitbit();

		// Sent the user off too Fitbit to authenticate
		$helper = new djchen\OAuth2\Client\Provider\Fitbit( [
			'clientId'     => $NxFitbit->getSetting( "api_clientId", NULL, FALSE ),
			'clientSecret' => $NxFitbit->getSetting( "api_clientSecret", NULL, FALSE ),
			'redirectUri'  => $NxFitbit->getSetting( "api_redirectUri", NULL, FALSE )
		] );

		// Fetch the authorization URL from the provider; this returns the
		// urlAuthorize option and generates and applies any necessary parameters
		// (e.g. state).
		$authorizationUrl = $helper->getAuthorizationUrl( array(
			'scope' => array(
				'activity',
				'heartrate',
				'location',
				'nutrition',
				'profile',
				'settings',
				'sleep',
				'social',
				'weight'
			)
		) );

		// Get the state generated for you and store it to the session.
		$_SESSION['oauth2state'] = $helper->getState();

		// Redirect the user to the authorization URL.
		header( 'Location: ' . $authorizationUrl );
		exit;

	} else if ( $url_namespace == "authorise" && array_key_exists( "_nx_fb_usr", $_COOKIE ) && $_COOKIE["_nx_fb_usr"] != "" ) {
		// Authorise a user against Fitbit's OAuth AIP
		if ( DEBUG_MY_PROJECT ) {
			echo __FILE__ . " @" . __LINE__ . " ## authorise - " . $_COOKIE['_nx_fb_usr'] . "<br />\n";
		}

		// Setup the App
		require_once( dirname( __FILE__ ) . "/inc/app.php" );
		$NxFitbit = new NxFitbit();

		// We're even talking about a valid user right?
		if ( $NxFitbit->isUser( $_COOKIE['_nx_fb_usr'] ) ) {

			// And lets double check we still need to register
			if ( $NxFitbit->valdidateOAuth( $NxFitbit->getUserOAuthTokens( $_COOKIE['_nx_fb_usr'], FALSE ) ) ) {
				if ( DEBUG_MY_PROJECT ) {
					echo __FILE__ . " @" . __LINE__ . " ## " . $_COOKIE['_nx_fb_usr'] . " is already authorised with Fitbit<br />\n";
				} else {
					header( "Location: " . $_SESSION['core_config']['http/admin'] . "/" );
					exit();
				}
			} else {
				// Sent the user off too Fitbit to authenticate
				if ( $_COOKIE['_nx_fb_usr'] == $NxFitbit->getSetting( "ownerFuid" ) ) {
					$personal = "_personal";
				} else {
					$personal = "";
				}

				$helper = new djchen\OAuth2\Client\Provider\Fitbit( [
					'clientId'     => $NxFitbit->getSetting( "api_clientId" . $personal, NULL, FALSE ),
					'clientSecret' => $NxFitbit->getSetting( "api_clientSecret" . $personal, NULL, FALSE ),
					'redirectUri'  => $NxFitbit->getSetting( "api_redirectUri" . $personal, NULL, FALSE )
				] );

				// Fetch the authorization URL from the provider; this returns the
				// urlAuthorize option and generates and applies any necessary parameters
				// (e.g. state).
				$authorizationUrl = $helper->getAuthorizationUrl( array(
					'scope' => array(
						'activity',
						'heartrate',
						'location',
						'nutrition',
						'profile',
						'settings',
						'sleep',
						'social',
						'weight'
					)
				) );

				// Get the state generated for you and store it to the session.
				$_SESSION['oauth2state'] = $helper->getState();

				// Redirect the user to the authorization URL.
				header( 'Location: ' . $authorizationUrl );
				exit;

			}

		} else if ( DEBUG_MY_PROJECT ) {
			echo __FILE__ . " @" . __LINE__ . " ## This is not a valid user<br />\n";
		} else {
			// When we don't know what to do put the user over to the user interface screens
			header( "Location: " . $_SESSION['core_config']['http/admin'] . "/" );
			exit();
		}

	} else if ( $url_namespace == "callback" || $url_namespace == "rti" ) {
		// Process the return information from a Fitbit authentication flow
		if ( empty( $_GET['state'] ) || ( $_GET['state'] !== $_SESSION['oauth2state'] ) ) {
			unset( $_SESSION['oauth2state'] );
			exit( 'Invalid state' );
		} else {
			try {
				// Setup the App
				require_once( dirname( __FILE__ ) . "/inc/app.php" );
				$NxFitbit = new NxFitbit();

				// Sent the user off too Fitbit to authenticate
				if ( $url_namespace == "rti" ) {
					$personal = "_personal";
				} else {
					$personal = "";
				}

				$helper = new djchen\OAuth2\Client\Provider\Fitbit( [
					'clientId'     => $NxFitbit->getSetting( "api_clientId" . $personal, NULL, FALSE ),
					'clientSecret' => $NxFitbit->getSetting( "api_clientSecret" . $personal, NULL, FALSE ),
					'redirectUri'  => $NxFitbit->getSetting( "api_redirectUri" . $personal, NULL, FALSE )
				] );

				// Try to get an access token using the authorization code grant.
				$accessToken = $helper->getAccessToken( 'authorization_code', [
					'code' => $_GET['code']
				] );

				// Find out who the new OAuth keys belong too
				$resourceOwner = $helper->getResourceOwner( $accessToken );

				// Check again that this really is one of our users
				if ( $NxFitbit->isUser( $resourceOwner->getId() ) ) {
					nxr( "User OAuth credentials installed" );

					// Update the users new keys
					$NxFitbit->setUserOAuthTokens( $resourceOwner->getId(), $accessToken );

					// Since we're done pass them back to the Admin UI
					header( "Location: " . $_SESSION['core_config']['http/admin'] . "/" );
					exit();

				} else {
					nxr( " OAuth return for new user: " . $resourceOwner->getId() );

					$pre_auth = $NxFitbit->getSetting( "owners_friends" );
					$pre_auth = explode( ",", $pre_auth );
					array_push( $pre_auth, $NxFitbit->getSetting( "ownerFuid" ) );
					if ( array_search( $resourceOwner->getId(), $pre_auth ) ) {
						$newUserName = $resourceOwner->getId();
						$NxFitbit->getFitbitAPI( $newUserName )->setUserAccessToken( $accessToken );
						$NxFitbit->getFitbitAPI( $newUserName )->setActiveUser( $newUserName );
						$newUserProfile = $NxFitbit->getFitbitAPI( $newUserName )->pullBabel( 'user/-/profile.json', TRUE );

						if ( $NxFitbit->getFitbitAPI( $newUserName )->createNewUser( $newUserProfile->user ) ) {
							nxr( "  User sent to new password screen" );
							header( "Location: " . $_SESSION['core_config']['http/admin'] . "/views/pages/register.php?usr=" . $newUserName );
						}
					} else {
						nxr( "  Non Friend registration: " . $resourceOwner->getId() );
						header( "Location: " . $_SESSION['core_config']['http/admin'] . "/?err=500" );
					}

					// When we don't know what to do put the user over to the user interface screens
					exit();
				}

			} catch ( \League\OAuth2\Client\Provider\Exception\IdentityProviderException $e ) {
				$this->getAppClass()->getErrorRecording()->captureException( $e, array(
					'extra' => array(
						'php_version'  => phpversion(),
						'core_version' => $this->getAppClass()->getSetting( "version", "0.0.0.1", TRUE )
					),
				) );
				exit( $e->getMessage() );
			}

		}

	} else if ( $url_namespace == "webhook" || $url_namespace == "service" ) {
		nxr( "Namespace Called: " . $url_namespace );

		if ( is_array( $_GET ) && array_key_exists( "verify", $_GET ) ) {
			require_once( dirname( __FILE__ ) . "/config.inc.php" );
			if ( ( is_array( $config['api_subValidity'] ) and in_array( $_GET['verify'], $config['api_subValidity'] ) ) OR ( $_GET['verify'] == $config['api_subValidity'] ) ) {
				header( 'Cache-Control: no-cache, must-revalidate' );
				header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
				header( 'Content-type: text/plain' );
				header( 'HTTP/1.0 204 No Content' );

				nxr( "Valid subscriber request - " . $url_namespace );
			} else {
				header( 'Cache-Control: no-cache, must-revalidate' );
				header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
				header( 'HTTP/1.0 404 Not Found' );

				nxr( "Invalid subscriber request - '" . $_GET['verify'] . "' - " . $url_namespace );
				nxr(print_r($config['api_subValidity'], true));
			}

		} else {
			// Deal with Fitbit subscriptions
			require_once( dirname( __FILE__ ) . "/service.php" );
		}

	} else if ( $url_namespace != "" && DEBUG_MY_PROJECT ) {
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'HTTP/1.0 404 Not Found' );

		// If we're debugging things print out the unknown namespace
		nxr( "Namespace Called: " . $url_namespace );

	} else {
		// When we don't know what to do put the user over to the user interface screens
		header( "Location: " . $_SESSION['core_config']['http/admin'] . "/" );
		exit();
	}
