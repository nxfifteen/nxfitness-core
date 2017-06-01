<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

echo $argv[1] . " => ";
$argv[1] = explode( ".", $argv[1] );
echo ( ( ( $argv[1][ 0 ] * 1000 ) . ( $argv[1][ 1 ] * 100 ) . ( $argv[1][ 2 ] * 10 ) . $argv[1][ 3 ] ) * 1 );
