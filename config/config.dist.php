<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @subpackage  Config
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

$config = [];

$config['scope_habitica_timeout'] = (3 * 60 * 60);
$config['scope_activities_timeout'] = (6 * 60 * 60);
$config['scope_activity_log_timeout'] = (3 * 60 * 60);
$config['scope_all_timeout'] = (24 * 60 * 60);
$config['scope_badges_timeout'] = (48 * 60 * 60);
$config['scope_body_timeout'] = (12 * 60 * 60);
$config['scope_caloriesOut_timeout'] = (10 * 60);
$config['scope_devices_timeout'] = (10 * 60);
$config['scope_distance_timeout'] = (3 * 60 * 60);
$config['scope_elevation_timeout'] = (3 * 60 * 60);
$config['scope_floors_timeout'] = (3 * 60 * 60);
$config['scope_foods_timeout'] = (2 * 60 * 60);
$config['scope_goals_calories_timeout'] = (75 * 60);
$config['scope_goals_timeout'] = (10 * 60 * 60);
$config['scope_heart_timeout'] = (12 * 60 * 60);
$config['scope_leaderboard_timeout'] = (30 * 60);
$config['scope_minutesFairlyActive_timeout'] = (60 * 60);
$config['scope_minutesLightlyActive_timeout'] = (60 * 60);
$config['scope_minutesSedentary_timeout'] = (60 * 60);
$config['scope_minutesVeryActive_timeout'] = (60 * 60);
$config['scope_nomie_trackers_timeout'] = (30 * 60);
$config['scope_pedomitor_timeout'] = (3 * 60 * 60);
$config['scope_profile_timeout'] = (3 * 24 * 60 * 60);
$config['scope_sleep_timeout'] = (20 * 60 * 60);
$config['scope_water_timeout'] = (2 * 60 * 60);
$config[ 'scope_habitica_timeout' ] = ( 10 * 60 );
$config[ 'scope_habitica_hatch_timeout' ] = ( 30 * 60 );
$config[ 'scope_habitica_feed_timeout' ] = ( 20 * 60 );
$config[ 'scope_habitica_rand_pet_timeout' ] = ( 45 * 60 );
$config[ 'scope_habitica_rand_mount_timeout' ] = ( 45 * 60 );
$config[ 'scope_habitica_avatar_timeout' ] = ( 60 * 60 );

/*
 * Personal settings will therefor overright settings above
 */
if ( file_exists( dirname( __FILE__ ) . "/config.inc.php" ) ) {
    require(dirname(__FILE__) . "/config.inc.php");
}