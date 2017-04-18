<?php
	define( 'APP_VERSION', '0.0.0.9' );
	//define( 'SENTRY_DSN', 'https://80a480ea986d4ee993ac89a54a0d1f0e@sentry.io/156527' );
	if ( file_exists( dirname( __FILE__ ) . "/config.def.php" ) ) {
		require_once( dirname( __FILE__ ) . "/config.def.php" );
	}