<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

/**
 * Core - Update commandline tool
 *
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      http://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2015 Stuart McCulloch Anderson
 * @license   http://stuart.nx15.at/mit/2015 MIT
 */

parse_str( implode( '&', array_slice( $argv, 1 ) ), $argv );
foreach ( $argv as $key => $value ) {
    $key          = str_ireplace( "--", "", $key );
    $_GET[ $key ] = $value;
}

echo $_GET[ 'ver' ] . " => ";
$_GET[ 'ver' ] = explode( ".", $_GET[ 'ver' ] );
echo ( ( ( $_GET[ 'ver' ][ 0 ] * 1000 ) . ( $_GET[ 'ver' ][ 1 ] * 100 ) . ( $_GET[ 'ver' ][ 2 ] * 10 ) . $_GET[ 'ver' ][ 3 ] ) * 1 );
