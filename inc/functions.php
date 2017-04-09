<?php
	/**
	 * NXR is a helper function. Past strings are recorded in a text file
	 * and when run from a command line output is displayed on screen as
	 * well
	 *
	 * @param string $msg         String input to be displayed in logs files
	 * @param bool   $includeDate If true appends datetime stamp
	 * @param bool   $newline     If true adds a new line character
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