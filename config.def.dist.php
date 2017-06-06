<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

define( "APP_VERSION", "0.0.1.11" );
//define( 'SENTRY_DSN', 'https://80a480ea986d4ee993ac89a54a0d1f0e@sentry.io/156527' );
if ( file_exists( dirname( __FILE__ ) . "/config.def.php" ) ) {
    require_once( dirname( __FILE__ ) . "/config.def.php" );
}