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
 * @subpackage  Deploy
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

$composerInstalledFiles = file_get_contents( dirname( __FILE__ ) . '/../bundle/vendor/composer.lock' );
$composerInstalledFiles = json_decode( $composerInstalledFiles, true );

foreach ( $composerInstalledFiles[ 'packages' ] as $package ) {
    $feedVersion = getFeed( 'https://packagist.org/feeds/package.' . $package[ 'name' ] . '.rss' );

    echo $package[ 'name' ];
    if ( $package[ 'version' ] == $feedVersion ) {
        echo "\t\t[OKAY]\n";
    } else {
        echo "\t\t[UPDATE] -> " . $feedVersion . "\n";
    }
}

/**
 * @param $feed_url
 *
 * @return mixed
 */
function getFeed( $feed_url ) {

    $content = file_get_contents( $feed_url );
    $x       = new SimpleXmlElement( $content );

    return preg_replace( '/.* \(([\w\d.\-]+)\)/im', '$1', $x->channel->item[ 0 ]->title );
}