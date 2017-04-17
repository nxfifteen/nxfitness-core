<?php
	header( 'Access-Control-Allow-Origin: https://wp.dev.psi.nxfifteen.me.uk' );
	header( 'Cache-Control: no-cache, must-revalidate' );
	header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	header( 'Content-type: application/json' );

	if ( array_key_exists( "user", $_GET ) && array_key_exists( "data", $_GET ) ) {
		$start = microtime( TRUE );
		if (
			is_writable( 'cache' )
			&& ( ! array_key_exists( "debug", $_GET ) || ( array_key_exists( "debug", $_GET ) && $_GET['debug'] != "true" ) )
			&& ( ! array_key_exists( "cache", $_GET ) || ( array_key_exists( "cache", $_GET ) && $_GET['cache'] != "false" ) )
			/* && (!array_key_exists('_nx_fb_usr', $_COOKIE ) || $_COOKIE['_nx_fb_usr'] != $_GET['user'])*/
		) {
			// cache files are created like cache/...
			$cacheFileName = '';
			//user" => "269VLG", "data" => "Tracked", "period
			if ( array_key_exists( "user", $_GET ) ) {
				$cacheFileName = $cacheFileName . '_' . $_GET['user'];
			}
			if ( array_key_exists( "data", $_GET ) ) {
				$cacheFileName = $cacheFileName . '_' . $_GET['data'];
			}
			if ( array_key_exists( "tcx", $_GET ) ) {
				$cacheFileName = $cacheFileName . '_' . $_GET['tcx'];
			}
			if ( array_key_exists( "date", $_GET ) ) {
				$cacheFileName = $cacheFileName . '_' . $_GET['date'];
			}
			if ( array_key_exists( "period", $_GET ) ) {
				$cacheFileName = $cacheFileName . '_' . $_GET['period'];
			}
			if ( array_key_exists( "start", $_GET ) ) {
				$cacheFileName = $cacheFileName . '_' . $_GET['start'];
			}
			if ( array_key_exists( "end", $_GET ) ) {
				$cacheFileName = $cacheFileName . '_' . $_GET['end'];
			}
			if ( array_key_exists( "debug", $_GET ) ) {
				$cacheFileName = $cacheFileName . '_' . $_GET['debug'];
			}
			if ( array_key_exists( "tracker", $_GET ) ) {
				$cacheFileName = $cacheFileName . '_' . $_GET['tracker'];
			}
			$cacheFile = 'cache' . DIRECTORY_SEPARATOR . $cacheFileName;

			if ( file_exists( $cacheFile ) ) {
				$fh        = fopen( $cacheFile, 'r' );
				$cacheTime = trim( fgets( $fh ) );

				// if data was cached recently, return cached data
				if ( $cacheTime > strtotime( '-45 minutes' ) ) {
					$json = json_decode( fread( $fh, filesize( $cacheFile ) ), TRUE );

					$end = microtime( TRUE );

					$json['time'] = round( ( $end - $start ), 4 );

					echo json_encode( $json );

					return TRUE;
				}

				// else delete cache file
				fclose( $fh );
				unlink( $cacheFile );
			}

			$json = query_api();
			if ( $json != "" ) {
				$end = microtime( TRUE );

				$json['time'] = round( ( $end - $start ), 4 );

				$json_encoded = json_encode( $json );
				echo $json_encoded;

				if ( array_key_exists( "cache", $json ) and $json['cache'] <> 0 ) {
					$fh = fopen( $cacheFile, 'w' );
					fwrite( $fh, time() . "\n" );
					fwrite( $fh, $json_encoded );
					fclose( $fh );
				}
			}
		} else {
			$json = query_api();

			$end = microtime( TRUE );

			$json['time'] = round( ( $end - $start ), 4 );

			if ( array_key_exists( "debug", $_GET ) and $_GET['debug'] == "true" ) {
				print_r( $json );
			} else {
				echo json_encode( $json );
			}

		}
	} elseif ( array_key_exists( "wmc_key", $_GET ) ) {
		$start = microtime( TRUE );

		require_once( dirname( __FILE__ ) . "/inc/RewardsMinecraft.php" );
		$RewardsMinecraft = new RewardsMinecraft();

		$json = $RewardsMinecraft->query_api();

		$end = microtime( TRUE );

		$json['time'] = round( ( $end - $start ), 4 );

		if ( array_key_exists( "debug", $_GET ) and $_GET['debug'] == "true" ) {
			print_r( $json );
		} else {
			echo json_encode( $json );
		}
	} elseif ( ! array_key_exists( "user", $_GET ) ) {
		echo json_error( 100 );
	} elseif ( ! array_key_exists( "data", $_GET ) ) {
		echo json_error( 102 );
	}

	/**
	 * @return mixed|string
	 */
	function query_api() {
		require_once( dirname( __FILE__ ) . "/inc/dataReturn.php" );
		$dataReturnClass = new dataReturn( $_GET['user'] );
		if ( $dataReturnClass->isUser() ) {
			$json = $dataReturnClass->returnUserRecords( $_GET );

			return $json;
		} else {
			echo json_error( 101 );

			return "";
		}
	}

	/**
	 * @param $errNumber
	 *
	 * @return string
	 */
	function json_error( $errNumber ) {
		$errMessage = "";
		switch ( $errNumber ) {
			case "100":
				$errMessage = "You must specify a user";
				break;
			case "101":
				$errMessage = "You must specify a valid user";
				break;
			case "102":
				$errMessage = "You haven't stated what to return";
				break;
		}

		return json_encode( array( "error" => "true", "code" => $errNumber, "msg" => $errMessage ) );
	}
