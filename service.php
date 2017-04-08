<?php
	// JSon request format is :
	//[{"collectionType":"activities","date":"2015-03-06","ownerId":"269VLG","ownerType":"user","subscriptionId":"1"}]

	date_default_timezone_set( 'Europe/London' );

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

			if ( is_writable( dirname( __FILE__ ) . "/fitbit.log" ) ) {
				$fh = fopen( dirname( __FILE__ ) . "/fitbit.log", "a" );
				fwrite( $fh, $msg );
				fclose( $fh );
			}
		}
	}

	header( 'Cache-Control: no-cache, must-revalidate' );
	header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	header( 'Content-type: application/json' );

	// read JSon input
	$data = json_decode( file_get_contents( 'php://input' ) );

	//    nxr(print_r($data, true));

	$logMsg = '';

	if ( validate_client() ) {
		// set json string to php variables
		if ( isset( $data ) and is_array( $data ) ) {
			foreach ( $data as $upLoadedRequest ) {
				// Do some data validation to make sure we are getting all we expect
				if ( empty( $upLoadedRequest->collectionType ) or $upLoadedRequest->collectionType == "" ) {
					$logMsg .= "collectionType not sent";
				} else if ( empty( $upLoadedRequest->date ) or $upLoadedRequest->date == "" ) {
					$logMsg .= "date not sent";
				} else if ( empty( $upLoadedRequest->ownerId ) or $upLoadedRequest->ownerId == "" ) {
					$logMsg .= "ownerId not sent";
				} else if ( empty( $upLoadedRequest->ownerType ) or $upLoadedRequest->ownerType == "" ) {
					$logMsg .= "ownerType not sent";
				} else if ( empty( $upLoadedRequest->subscriptionId ) or $upLoadedRequest->subscriptionId == "" ) {
					$logMsg .= "subscriptionId not sent";
				} else {
					if ( $upLoadedRequest->collectionType == "nomie" ) {
						$upLoadedRequest->collectionType = "nomie_trackers";
					}

					require_once( dirname( __FILE__ ) . "/inc/app.php" );
					$fitbitApp = new NxFitbit();
					if ( $fitbitApp->isUser( $upLoadedRequest->ownerId ) ) {
						$cooldown = $fitbitApp->getUserCooldown( $upLoadedRequest->ownerId );
						if ( strtotime( $cooldown ) < strtotime( date( "Y-m-d H:i:s" ) ) ) {
							$logMsg .= "Processing queue item " . $fitbitApp->supportedApi( $upLoadedRequest->collectionType ) . " for " . $upLoadedRequest->ownerId . "";
							if ( $fitbitApp->getDatabase()->has( $fitbitApp->getSetting( "db_prefix", NULL, FALSE ) . "runlog", array(
								"AND" => array(
									"user"     => $upLoadedRequest->ownerId,
									"activity" => $upLoadedRequest->collectionType
								)
							) )
							) {
								$fields = array(
									"date"     => date( "Y-m-d H:i:s" ),
									"cooldown" => "1970-01-01 01:00:00"
								);
								$fitbitApp->getDatabase()->update( $fitbitApp->getSetting( "db_prefix", NULL, FALSE ) . "runlog", $fields, array(
									"AND" => array(
										"user"     => $upLoadedRequest->ownerId,
										"activity" => $upLoadedRequest->collectionType
									)
								) );
								$fitbitApp->getErrorRecording()->postDatabaseQuery( $fitbitApp->getDatabase(), array(
									"METHOD" => __FILE__,
									"LINE"   => __LINE__
								) );
							} else {
								$fields = array(
									"user"     => $upLoadedRequest->ownerId,
									"activity" => $upLoadedRequest->collectionType,
									"date"     => date( "Y-m-d H:i:s" ),
									"cooldown" => "1970-01-01 01:00:00"
								);
								$fitbitApp->getDatabase()->insert( $fitbitApp->getSetting( "db_prefix", NULL, FALSE ) . "runlog", $fields );
								$fitbitApp->getErrorRecording()->postDatabaseQuery( $fitbitApp->getDatabase(), array(
									"METHOD" => __FILE__,
									"LINE"   => __LINE__
								) );
							}

							if ( $upLoadedRequest->collectionType == "foods" ) {
								if ( $fitbitApp->getDatabase()->has( $fitbitApp->getSetting( "db_prefix", NULL, FALSE ) . "runlog", array(
									"AND" => array(
										"user"     => $upLoadedRequest->ownerId,
										"activity" => "water"
									)
								) )
								) {
									$fields = array(
										"date"     => date( "Y-m-d H:i:s" ),
										"cooldown" => "1970-01-01 01:00:00"
									);
									$fitbitApp->getDatabase()->update( $fitbitApp->getSetting( "db_prefix", NULL, FALSE ) . "runlog", $fields, array(
										"AND" => array(
											"user"     => $upLoadedRequest->ownerId,
											"activity" => "water"
										)
									) );
									$fitbitApp->getErrorRecording()->postDatabaseQuery( $fitbitApp->getDatabase(), array(
										"METHOD" => __FILE__,
										"LINE"   => __LINE__
									) );
								} else {
									$fields = array(
										"user"     => $upLoadedRequest->ownerId,
										"activity" => "water",
										"date"     => date( "Y-m-d H:i:s" ),
										"cooldown" => "1970-01-01 01:00:00"
									);
									$fitbitApp->getDatabase()->insert( $fitbitApp->getSetting( "db_prefix", NULL, FALSE ) . "runlog", $fields );
									$fitbitApp->getErrorRecording()->postDatabaseQuery( $fitbitApp->getDatabase(), array(
										"METHOD" => __FILE__,
										"LINE"   => __LINE__
									) );
								}
							}

							$fitbitApp->addCronJob( $upLoadedRequest->ownerId, $upLoadedRequest->collectionType, TRUE );
						} else {
							$logMsg .= "Can not process " . $fitbitApp->supportedApi( $upLoadedRequest->collectionType ) . ". API limit reached for " . $upLoadedRequest->ownerId . ". Cooldown period ends " . $cooldown . "";
						}
					} else {
						$logMsg .= "Can not process " . $fitbitApp->supportedApi( $upLoadedRequest->collectionType ) . " since " . $upLoadedRequest->ownerId . " is no longer a user.";
					}
				}
			}
		} else if ( isset( $data ) and is_object( $data ) ) {
			require_once( dirname( __FILE__ ) . "/inc/app.php" );
			$fitbitApp = new NxFitbit();
			if ( $fitbitApp->isUser( $data->ownerId ) ) {
				nxr( $data->ownerId . " is a valid user" );
				$api = $fitbitApp->getDatabase()->get( $fitbitApp->getSetting( "db_prefix", NULL, FALSE ) . "users", "api", array( "fuid" => $data->ownerId ) );
				if ( isset( $api ) ) {
					if ( hash( 'sha256', $api . date( "Y-m-d H:i" ) ) == $data->auth ) {
						nxr( " Valid API Access Key" );
						foreach ( $data->unit as $unit ) {
							nxr( "  Recording " . $unit->key . " as " . $unit->value );
							$fitbitApp->getDatabase()->insert( $fitbitApp->getSetting( "db_prefix", NULL, FALSE ) . "units", array(
								"user"  => $data->ownerId,
								"unit"  => $unit->key,
								"value" => $unit->value
							) );
							$fitbitApp->getErrorRecording()->postDatabaseQuery( $fitbitApp->getDatabase(), array(
								"METHOD" => __FILE__,
								"LINE"   => __LINE__
							) );
						}
					} else {
						nxr( " Invalid API" );
						nxr( "  Expected: "
						     . substr( hash( 'sha256', $api . date( "Y-m-d H:i" ) ), 0, 5 )
						     . "......................................................"
						     . substr( hash( 'sha256', $api . date( "Y-m-d H:i" ) ), - 5 ) );
						nxr( "  Received: " . $data->auth );
						echo date( "Y-m-d H:i:s" ) . ":: Invalid API";
						die();
					}
				} else {
					nxr( " No API Access" );
					echo date( "Y-m-d H:i:s" ) . ":: No API Access";
					die();
				}
			}
		}
	} else {
		$logMsg .= "Could not authorise client IP";
	}

	if ( isset( $logMsg ) && $logMsg != "" ) {
		nxr( "New API request: " . $logMsg );
	}

	header( 'HTTP/1.0 204 No Content' );

	/**
	 * @return bool
	 */
	function validate_client() {
		return TRUE;
	}

