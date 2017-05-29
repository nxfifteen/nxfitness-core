<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

header( 'Content-Type: application/javascript' );

require_once( dirname( __FILE__ ) . "/../../../lib/autoloader.php" );

use Core\UX\NxFitAdmin;

session_start();
header( 'Content-type: text/javascript' );

define( 'CORE_PROJECT_ROOT', $_SESSION[ 'CORE_PROJECT_ROOT' ] );
define( "CORE_UX", $_SESSION[ 'CORE_UX' ] );
define( "CORE_ROOT", $_SESSION[ 'CORE_ROOT' ] );

$App = new NxFitAdmin( $_COOKIE[ '_nx_fb_usr' ] );

echo "var localWeatherImage = '" . $App->getLocalWeatherImage() . "';";

unset( $App );
