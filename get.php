<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

require_once( dirname( __FILE__ ) . "/lib/autoloader.php" );

use Core\Core;

parse_str( implode( '&', array_slice( $argv, 1 ) ), $argv );
foreach ( $argv as $key => $value ) {
    $key          = str_ireplace( "--", "", $key );
    $_GET[ $key ] = $value;
}

$fitbitApp = new Core();

if ( $fitbitApp->isUser( $_GET[ 'user' ] ) ) {
    $cooldown = $fitbitApp->getUserCooldown( $_GET[ 'user' ] );
    if ( strtotime( $cooldown ) < strtotime( date( "Y-m-d H:i:s" ) ) ) {
        if ( $fitbitApp->supportedApi( $_GET[ 'get' ] ) != $_GET[ 'get' ] ) {
            nxr( 0, "Forcing pull of " . $fitbitApp->supportedApi( $_GET[ 'get' ] ) . " for " . $_GET[ 'user' ] );
            $fitbitApp->getFitbitAPI( $_GET[ 'user' ] )->setForceSync( true );
            $fitbitApp->getFitbitAPI( $_GET[ 'user' ] )->pull( $_GET[ 'user' ], $_GET[ 'get' ] );
        } else {
            nxr( 0, "Unknown trigger " . $_GET[ 'get' ] . ". Supported calls are:" );
            print_r( $fitbitApp->supportedApi() );
        }
    } else {
        $fitbitApp->getErrorRecording()->captureMessage( "API limit reached", [ 'remote_api' ], [
            'level' => 'info',
            'extra' => [
                'api_req'      => $_GET[ 'get' ],
                'user'         => $_GET[ 'user' ],
                'cooldown'     => $cooldown,
                'php_version'  => phpversion(),
                'core_version' => $fitbitApp->getSetting( "version", "0.0.0.1", true )
            ]
        ] );
        nxr( 0, "Can not process " . $fitbitApp->supportedApi( $_GET[ 'get' ] ) . ". API limit reached for " . $_GET[ 'user' ] . ". Cooldown period ends " . $cooldown );
    }
} else {
    $fitbitApp->getErrorRecording()->captureMessage( "Unknown User", [ 'authentication' ], [
        'level' => 'info',
        'extra' => [
            'api_req'      => $_GET[ 'get' ],
            'user'         => $_GET[ 'user' ],
            'php_version'  => phpversion(),
            'core_version' => $fitbitApp->getSetting( "version", "0.0.0.1", true )
        ]
    ] );
    nxr( 0, "Can not process " . $fitbitApp->supportedApi( $_GET[ 'get' ] ) . " since " . $_GET[ 'user' ] . " is no longer a user." );
}
