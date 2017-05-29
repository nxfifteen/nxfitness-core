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

use Core\Deploy\Upgrade;

parse_str( implode( '&', array_slice( $argv, 1 ) ), $argv );
foreach ( $argv as $key => $value ) {
    $key          = str_ireplace( "--", "", $key );
    $_GET[ $key ] = $value;
}

$dataReturnClass = new Upgrade();

echo "Upgrading from " . $dataReturnClass->getInstallVersion() . " to " . $dataReturnClass->getInstallingVersion() . ". ";
echo $dataReturnClass->getNumUpdates() . " updates outstanding\n";

if ( $dataReturnClass->getNumUpdates() > 0 ) {
    //echo " - ";
    //foreach ( $dataReturnClass->getUpdates() as $update ) {
    //	echo "$update, ";
    //}
    //echo "\n";

    $dataReturnClass->runUpdates();
}
