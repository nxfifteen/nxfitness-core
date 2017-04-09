<?php
	define( 'APP_VERSION', '0.0.0.8' );
	if ( file_exists( dirname( __FILE__ ) . "/config.def.php" ) ) {
		require_once( dirname( __FILE__ ) . "/config.def.php" );
	}