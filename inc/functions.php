<?php
	if (!function_exists("nxr")) {
		/**
		 * NXR is a helper function. Past strings are recorded in a text file
		 * and when run from a command line output is displayed on screen as
		 * well
		 *
		 * @param string $msg         String input to be displayed in logs files
		 * @param bool   $includeDate If true appends datetime stamp
		 * @param bool   $newline     If true adds a new line character
		 * @param bool   $echoLine
		 */
		function nxr( $msg, $includeDate = TRUE, $newline = TRUE, $echoLine = TRUE ) {
			if ( $includeDate ) {
				$msg = date( "Y-m-d H:i:s" ) . ": " . $msg;
			}
			if ( $newline ) {
				$msg = $msg . "\n";
			}

			if ( is_writable( dirname( __FILE__ ) . "/../fitbit.log" ) ) {
				$fh = fopen( dirname( __FILE__ ) . "/../fitbit.log", "a" );
				fwrite( $fh, $msg );
				fclose( $fh );
			}

			if ( $echoLine !== FALSE && ( ! defined( 'IS_CRON_RUN' ) || ! IS_CRON_RUN ) && php_sapi_name() == "cli" ) {
				echo $msg;
			}
		}
	}

	if (!function_exists("nxr_destroy_session")) {
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
