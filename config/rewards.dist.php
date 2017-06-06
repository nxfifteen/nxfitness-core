<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

$rules = [];
$rewards = [];
if ( file_exists( dirname( __FILE__ ) . "/rewards.inc.php" ) ) {
    require_once(dirname(__FILE__) . "/rewards.inc.php");
}

//$rewards = [
//    'TestGamingFileRule' => [
//        "system" => "gaming",
//        "description" => "Test Gaming File Rule",
//        "reward" => '{"skill": "Hello World","xp": 5000,"health": 500,"mana": 100}'
//    ]
//];
//
//
//$rules = [
//    "nomie" => [
//        "tick" => [
//            "peed" => [
//                array_merge(["name" => "You took a pee"], $rewards['TestGamingFileRule'])
//            ]
//        ]
//    ]
//];