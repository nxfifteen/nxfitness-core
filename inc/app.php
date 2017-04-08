<?php

	date_default_timezone_set( 'Europe/London' );
	error_reporting( E_ALL );

	/**
	 * @param $msg
	 */
	if ( ! function_exists( "nxr" ) ) {
		/**
		 * NXR is a helper function. Past strings are recorded in a text file
		 * and when run from a command line output is displayed on screen as
		 * well
		 *
		 * @param string $msg String input to be displayed in logs files
		 * @param bool   $includeDate
		 * @param bool   $newline
		 */
		function nxr( $msg, $includeDate = TRUE, $newline = TRUE ) {
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

			if ( php_sapi_name() == "cli" ) {
				echo $msg;
			}
		}
	}

	// composer require djchen/oauth2-fitbit
	require_once( dirname( __FILE__ ) . "/../vendor/autoload.php" );

	/**
	 * NxFitbit
	 *
	 * @version   0.0.1
	 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
	 * @link      http://nxfifteen.me.uk NxFIFTEEN
	 * @copyright 2015 Stuart McCulloch Anderson
	 * @license   http://stuart.nx15.at/mit/2015 MIT
	 */
	class NxFitbit {
		/**
		 * @var medoo
		 */
		protected $database;
		/**
		 * @var fitbit
		 */
		protected $fitbitapi;
		/**
		 * @var config
		 */
		protected $settings;
		/**
		 * @var ErrorRecording
		 */
		protected $errorRecording;

		/**
		 *
		 */
		public function __construct() {
			require_once( dirname( __FILE__ ) . "/config.php" );
			$this->setSettings( new config() );

			require_once( dirname( __FILE__ ) . "/../library/medoo.php" );
			$this->setDatabase( new medoo( array(
				'database_type' => 'mysql',
				'database_name' => $this->getSetting( "db_name" ),
				'server'        => $this->getSetting( "db_server" ),
				'username'      => $this->getSetting( "db_username" ),
				'password'      => $this->getSetting( "db_password" ),
				'charset'       => 'utf8'
			) ) );

			$this->getSettings()->setDatabase( $this->getDatabase() );

			$this->errorRecording = new ErrorRecording( $this );

		}

		/**
		 * @param config $settings
		 */
		private function setSettings( $settings ) {
			$this->settings = $settings;
		}

		/**
		 * @param medoo $database
		 */
		private function setDatabase( $database ) {
			$this->database = $database;
		}

		/**
		 * Cron job / queue management
		 */

		/**
		 * @return ErrorRecording
		 */
		public function getErrorRecording() {
			return $this->errorRecording;
		}

		/**
		 * Get settings from config class
		 *
		 * @param                $key
		 * @param null           $default
		 * @param bool           $query_db
		 *
		 * @return string
		 */
		public function getSetting( $key, $default = NULL, $query_db = TRUE ) {
			return $this->getSettings()->get( $key, $default, $query_db );
		}

		/**
		 * Get settings from config class
		 *
		 * @param string $fuid
		 * @param string $key
		 * @param null   $default
		 * @param bool   $query_db
		 *
		 * @return string
		 */
		public function getUserSetting( $fuid, $key, $default = NULL, $query_db = TRUE ) {
			return $this->getSettings()->getUser( $fuid, $key, $default, $query_db );
		}

		/**
		 * @return config
		 */
		public function getSettings() {
			return $this->settings;
		}

		/**
		 * Users
		 */

		/**
		 * @return medoo
		 */
		public function getDatabase() {
			return $this->database;
		}

		/**
		 * Add new cron jobs to queue
		 *
		 * @param string $user_fitbit_id
		 * @param string $trigger
		 * @param bool   $force
		 */
		public function addCronJob( $user_fitbit_id, $trigger, $force = FALSE ) {
			if ( $force || $this->getSetting( 'scope_' . $trigger . '_cron', FALSE ) ) {
				if ( ! $this->getDatabase()->has( $this->getSetting( "db_prefix", NULL, FALSE ) . "queue", array(
					"AND" => array(
						"user"    => $user_fitbit_id,
						"trigger" => $trigger
					)
				) )
				) {
					$this->getDatabase()->insert( $this->getSetting( "db_prefix", NULL, FALSE ) . "queue", array(
						"user"    => $user_fitbit_id,
						"trigger" => $trigger,
						"date"    => date( "Y-m-d H:i:s" )
					) );
					$this->getErrorRecording()->postDatabaseQuery( $this->getDatabase(), array(
						"METHOD" => __METHOD__,
						"LINE"   => __LINE__
					) );
				} else {
					nxr( "Cron job already present" );
				}
			} else {
				nxr( "I am not allowed to queue $trigger" );
			}
		}

		/**
		 * Settings and configuration
		 */

		/**
		 * Delete cron jobs from queue
		 *
		 * @param $user_fitbit_id
		 * @param $trigger
		 */
		public function delCronJob( $user_fitbit_id, $trigger ) {
			if ( $this->getDatabase()->has( $this->getSetting( "db_prefix", NULL, FALSE ) . "queue", array(
				"AND" => array(
					"user"    => $user_fitbit_id,
					"trigger" => $trigger
				)
			) )
			) {
				if ( $this->getDatabase()->delete( $this->getSetting( "db_prefix", NULL, FALSE ) . "queue", array(
					"AND" => array(
						"user"    => $user_fitbit_id,
						"trigger" => $trigger
					)
				) )
				) {
					//nxr("Cron job $trigger deleted");
				} else {
					$this->getErrorRecording()->postDatabaseQuery( $this->getDatabase(), array(
						"METHOD" => __METHOD__,
						"LINE"   => __LINE__
					) );
					nxr( "Failed to delete $trigger Cron job" );
				}
			} else {
				$this->getErrorRecording()->postDatabaseQuery( $this->getDatabase(), array(
					"METHOD" => __METHOD__,
					"LINE"   => __LINE__
				) );
				nxr( "Failed to delete $trigger Cron job" );
			}
		}

		/**
		 * Get list of pending cron jobs from database
		 *
		 * @return array|bool
		 */
		public function getCronJobs() {
			return $this->getDatabase()->select( $this->getSetting( "db_prefix", NULL, FALSE ) . "queue", "*", array( "ORDER" => "date ASC" ) );
		}

		/**
		 * @param bool   $reset
		 * @param string $userFitbitId
		 *
		 * @return fitbit
		 */
		public function getFitbitAPI( $userFitbitId = "", $reset = FALSE ) {
			if ( is_null( $this->fitbitapi ) || $reset ) {
				require_once( dirname( __FILE__ ) . "/fitbit.php" );
				if ( $userFitbitId == $this->getSetting( "ownerFuid", NULL, FALSE ) ) {
					$this->fitbitapi = new fitbit( $this, TRUE );
				} else {
					$this->fitbitapi = new fitbit( $this, FALSE );
				}
			}

			return $this->fitbitapi;
		}

		/**
		 * @param fitbit $fitbitapi
		 */
		public function setFitbitapi( $fitbitapi ) {
			$this->fitbitapi = $fitbitapi;
		}

		/**
		 * Database functions
		 */

		/**
		 * @param $user_fitbit_id
		 *
		 * @return int|array
		 */
		public function setUserCooldown( $user_fitbit_id, $datetime ) {
			if ( $this->isUser( $user_fitbit_id ) ) {
				if ( is_string( $datetime ) ) {
					$datetime = new DateTime ( $datetime );
				}

				return $this->getDatabase()->update( $this->getSetting( "db_prefix", NULL, FALSE ) . "users", array(
					'cooldown' => $datetime->format( "Y-m-d H:i:s" )
				), array( "AND" => array( 'fuid' => $user_fitbit_id ) ) );
			} else {
				return 0;
			}
		}

		/**
		 * @param $user_fitbit_id
		 *
		 * @return int|array
		 */
		public function getUserCooldown( $user_fitbit_id ) {
			if ( $this->isUser( $user_fitbit_id ) ) {
				return $this->getDatabase()->get( $this->getSetting( "db_prefix", NULL, FALSE ) . "users", "cooldown", array( "fuid" => $user_fitbit_id ) );
			} else {
				return 0;
			}
		}

		/**
		 * @param string $user_fitbit_id
		 *
		 * @return bool
		 */
		public function isUser( $user_fitbit_id ) {
			if ( $this->getDatabase()->has( $this->getSetting( "db_prefix", NULL, FALSE ) . "users", array( "fuid" => $user_fitbit_id ) ) ) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		/**
		 * @param string                                 $user_fitbit_id
		 * @param League\OAuth2\Client\Token\AccessToken $accessToken
		 */
		public function setUserOAuthTokens( $user_fitbit_id, $accessToken ) {
			$this->getDatabase()->update( $this->getSetting( "db_prefix", FALSE ) . "users",
				array(
					'tkn_access'  => $accessToken->getToken(),
					'tkn_refresh' => $accessToken->getRefreshToken(),
					'tkn_expires' => $accessToken->getExpires()
				), array( "fuid" => $user_fitbit_id ) );
		}

		/**
		 * @param $user_fitbit_id
		 */
		public function delUserOAuthTokens( $user_fitbit_id ) {
			$this->getDatabase()->update( $this->getSetting( "db_prefix", FALSE ) . "users",
				array(
					'tkn_access'  => '',
					'tkn_refresh' => '',
					'tkn_expires' => 0
				), array( "fuid" => $user_fitbit_id ) );
		}

		/**
		 * @param      $user_fitbit_id
		 * @param bool $validate
		 *
		 * @return bool
		 */
		public function getUserOAuthTokens( $user_fitbit_id, $validate = TRUE ) {
			$userArray = $this->getDatabase()->get( $this->getSetting( "db_prefix", NULL, FALSE ) . "users", array(
				'tkn_access',
				'tkn_refresh',
				'tkn_expires'
			), array( "fuid" => $user_fitbit_id ) );
			if ( is_array( $userArray ) ) {
				if ( $validate && $this->valdidateOAuth( $userArray ) ) {
					return $userArray;
				} else if ( ! $validate ) {
					return $userArray;
				}
			}

			return FALSE;
		}

		/**
		 * @param $userArray
		 *
		 * @return bool
		 */
		public function valdidateOAuth( $userArray ) {
			if ( $userArray['tkn_access'] == "" || $userArray['tkn_refresh'] == "" || $userArray['tkn_expires'] == "" ) {
				//nxr("OAuth is not fully setup for this user");
				return FALSE;
			} else {
				return TRUE;
			}
		}

		/**
		 * @param string $user_fitbit_id
		 * @param string $user_fitbit_password
		 *
		 * @return bool
		 */
		public function isUserValid( $user_fitbit_id, $user_fitbit_password ) {
			if ( strpos( $user_fitbit_id, '@' ) !== FALSE ) {
				$user_fitbit_id = $this->isUserValidEml( $user_fitbit_id );
			}

			if ( $this->isUser( $user_fitbit_id ) ) {
				if ( $this->getDatabase()->has( $this->getSetting( "db_prefix", NULL, FALSE ) . "users", array(
					"AND" => array(
						"fuid"     => $user_fitbit_id,
						"password" => $user_fitbit_password
					)
				) )
				) {
					return $user_fitbit_id;
				} else if ( $this->getDatabase()->has( $this->getSetting( "db_prefix", NULL, FALSE ) . "users", array(
					"AND" => array(
						"fuid"     => $user_fitbit_id,
						"password" => ''
					)
				) )
				) {
					return - 1;
				} else {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		}

		/**
		 * @param string $user_fitbit_id
		 *
		 * @return bool
		 */
		public function isUserValidEml( $user_fitbit_id ) {
			if ( $this->getDatabase()->has( $this->getSetting( "db_prefix", NULL, FALSE ) . "users", array( "eml" => $user_fitbit_id ) ) ) {
				$user_fuid = $this->getDatabase()->get( $this->getSetting( "db_prefix", NULL, FALSE ) . "users", "fuid", array( "eml" => $user_fitbit_id ) );

				return $user_fuid;
			} else {
				return $user_fitbit_id;
			}
		}

		/**
		 * @param      $errCode
		 * @param null $user
		 *
		 * @return string
		 */
		public function lookupErrorCode( $errCode, $user = NULL ) {
			switch ( $errCode ) {
				case "-146":
					return "Disabled in user config.";
				case "-145":
					return "Disabled in system config.";
				case "-144":
					return "Username missmatch.";
				case "-143":
					return "API cool down in effect.";
				case "-142":
					return "Unable to create required directory.";
				case "429":
					if ( ! is_null( $user ) ) {
						$hour = date( "H" ) + 1;
						$this->getDatabase()->update( $this->getSetting( "db_prefix", NULL, FALSE ) . "users", array(
							'cooldown' => date( "Y-m-d " . $hour . ":01:00" ),
						), array( 'fuid' => $user ) );
					}

					return "Either you hit the rate limiting quota for the client or for the viewer";
				default:
					return $errCode;
			}
		}

		/**
		 * Set value in database/config class
		 *
		 * @param           $key
		 * @param           $value
		 * @param bool      $query_db
		 *
		 * @return bool
		 */
		public function setSetting( $key, $value, $query_db = TRUE ) {
			return $this->getSettings()->set( $key, $value, $query_db );
		}

		/**
		 * Get settings from config class
		 *
		 * @param string $fuid
		 * @param string $key
		 * @param string $value
		 *
		 * @return string
		 */
		public function setUserSetting( $fuid, $key, $value ) {
			return $this->getSettings()->setUser( $fuid, $key, $value );
		}

		/**
		 * Helper function to check for supported API calls
		 *
		 * @param null $key
		 *
		 * @return array|null|string
		 */
		public function supportedApi( $key = NULL ) {
			$database_array = array(
				'all'                  => 'Everything',
				'floors'               => 'Floors Climed',
				'foods'                => 'Calorie Intake',
				'badges'               => 'Badges',
				'sleep'                => 'Sleep Records',
				'body'                 => 'Weight & Body Fat Records',
				'goals'                => 'Personal Goals',
				'water'                => 'Water Intake',
				'activities'           => 'Pedomitor & Activities',
				'leaderboard'          => 'Friends',
				'devices'              => 'Device Status',
				'caloriesOut'          => 'Calories Out',
				'goals_calories'       => 'Calorie Goals',
				'minutesVeryActive'    => 'Minutes Very Active',
				'minutesFairlyActive'  => 'Minutes Fairly Active',
				'minutesLightlyActive' => 'Minutes Lightly Active',
				'minutesSedentary'     => 'Minutes Sedentary',
				'elevation'            => 'Elevation',
				'distance'             => 'Distance Traveled',
				'steps'                => 'Steps Taken',
				'profile'              => 'User Profile',
				'heart'                => 'Heart Rates',
				'activity_log'         => 'Activities',
				'nomie_trackers'       => "Nomie Trackers"
			);
			ksort( $database_array );

			if ( is_null( $key ) ) {
				return $database_array;
			} else {
				if ( array_key_exists( $key, $database_array ) ) {
					return $database_array[ $key ];
				} else {
					return $key;
				}
			}
		}

		public function isUserOAuthAuthorised( $_nx_fb_usr ) {
			if ( array_key_exists( "userIsOAuth_" . $_nx_fb_usr, $_SESSION ) && is_bool( $_SESSION[ 'userIsOAuth_' . $_nx_fb_usr ] ) && $_SESSION[ 'userIsOAuth_' . $_nx_fb_usr ] !== FALSE ) {
				return $_SESSION[ 'userIsOAuth_' . $_nx_fb_usr ];
			} else {
				if ( $this->valdidateOAuth( $this->getUserOAuthTokens( $_nx_fb_usr, FALSE ) ) ) {
					$_SESSION[ 'userIsOAuth_' . $_nx_fb_usr ] = TRUE;

					return TRUE;
				} else {
					return FALSE;
				}
			}
		}

	}

	class ErrorRecording {
		/**
		 * @var Raven_Client
		 */
		protected $sentryClient;
		/**
		 * @var Raven_ErrorHandler
		 */
		protected $sentryErrorHandler;
		/**
		 * @var NxFitbit
		 */
		protected $appClass;

		public function __construct( $appClass ) {
			require_once( dirname( __FILE__ ) . "/../config.def.php" );
			require_once( dirname( __FILE__ ) . "/../library/sentry/lib/Raven/Autoloader.php" );
			Raven_Autoloader::register();

			$this->appClass = $appClass;
		}

		/**
		 * @return Raven_Client
		 */
		public function getSentryClient() {
			if ( is_null( $this->sentryClient ) ) {
				$this->sentryClient = ( new Raven_Client( SENTRY_DSN ) )
					->setAppPath( __DIR__ )
					->setRelease( $this->appClass->getSetting( "version", "0.0.0.1", TRUE ) )
					->setEnvironment( $this->appClass->getSetting( "environment", "development", FALSE ) )
					->setPrefixes( array( __DIR__ ) )
					->install();

				$this->sentryClient->user_context( array(
					'id'         => sha1( gethostbyname( gethostname() ) . gethostname() . $this->appClass->getSetting( "ownerFuid", "Unknown", FALSE ) ),
					'username'   => $this->appClass->getSetting( "ownerFuid", "Unknown", FALSE ),
					'ip_address' => gethostbyname( gethostname() )
				) );
			}

			return $this->sentryClient;
		}

		/**
		 * @return Raven_ErrorHandler
		 */
		public function getSentryErrorHandler() {
			if ( is_null( $this->sentryErrorHandler ) ) {
				$this->sentryErrorHandler = new Raven_ErrorHandler( $this->getSentryClient() );
				$this->sentryErrorHandler->registerExceptionHandler();
				$this->sentryErrorHandler->registerErrorHandler();
				$this->sentryErrorHandler->registerShutdownFunction();
			}

			return $this->sentryErrorHandler;
		}

		/**
		 * Log an exception to sentry
		 *
		 * @param Exception $exception The Exception object.
		 * @param array     $data      Additional attributes to pass with this event (see Sentry docs).
		 */
		public function captureException( $exception, $data = NULL, $logger = NULL, $vars = NULL ) {
			$this->getSentryClient()->captureException( $exception, $data, $logger, $vars );
			nxr( "### Exception Recorded ###" );
		}

		/**
		 * Log a message to sentry
		 *
		 * @param string $message The message (primary description) for the event.
		 * @param array  $params  params to use when formatting the message.
		 * @param array  $data    Additional attributes to pass with this event (see Sentry docs).
		 */
		public function captureMessage( $message, $params = array(), $data = array(), $stack = FALSE, $vars = NULL ) {
			$this->getSentryClient()->captureMessage( $message, $params, $data, $stack, $vars );
			nxr( "### Message Recorded ###" );
		}

		/**
		 * @param medoo $medoo
		 * @param       $parameters
		 */
		public function postDatabaseQuery( $medoo, $parameters ) {
			$medoo_error = $medoo->error();
			if ( $medoo_error[0] != 0000 ) {
				$medoo_info = $medoo->info();
				$this->captureMessage( $medoo_error[2], array( 'database' ), array(
					'level' => 'error',
					'extra' => array(
						'method'         => $parameters['METHOD'],
						'method_line'    => $parameters['LINE'],
						'sql_server'     => $medoo_info['server'],
						'sql_client'     => $medoo_info['client'],
						'sql_driver'     => $medoo_info['driver'],
						'sql_version'    => $medoo_info['version'],
						'sql_connection' => $medoo_info['connection'],
						'sql_last_query' => $medoo->last_query(),
						'php_version'    => phpversion(),
						'core_version'   => $this->appClass->getSetting( "version", "0.0.0.1", TRUE )
					)
				) );

			}
		}
	}
