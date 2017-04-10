<?php
	define( 'APP_VERSION', '0.0.0.8' );
	//define( 'SENTRY_DSN', 'https://764e9491e75c4438ac0608d9ad2fd17a:62d9b7d807b0422e803bfe75e0d70998@sentry.io/156527' );
	if ( file_exists( dirname( __FILE__ ) . "/config.def.php" ) ) {
		require_once( dirname( __FILE__ ) . "/config.def.php" );
	}