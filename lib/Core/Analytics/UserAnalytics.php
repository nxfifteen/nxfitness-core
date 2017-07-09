<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 * Copyright (c) 2017. Stuart McCulloch Anderson
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @subpackage  Analytics
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

namespace Core\Analytics;

require_once( dirname( __FILE__ ) . "/../../autoloader.php" );

/**
 * tracking
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-tracking phpDocumentor
 *            wiki for tracking.
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class UserAnalytics {

    /**
     * @var \PiwikTracker
     */
    protected $PiwikTracker;
    /**
     * @var string
     */
    protected $siteId;

    /**
     * Builds a PiwikTracker object, used to track visits, pages and Goal conversions
     * for a specific website, by using the Piwik Tracking API.
     *
     * @codeCoverageIgnore
     *
     * @param int    $trackingId Id site to be tracked
     * @param string $apiUrl     "http://example.org/piwik/" or "http://piwik.example.org/"
     *                           If set, will overwrite PiwikTracker::$URL
     * @param null   $serverName
     * @param null   $requestUrl
     */
    public function __construct( $trackingId, $apiUrl, $serverName = null, $requestUrl = null ) {
        if ( is_null( $serverName ) ) {
            $serverName = filter_input( INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING );
        }
        if ( is_null( $requestUrl ) ) {
            $requestUrl = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING );
        }

        $this->setSiteId( $trackingId );

        $this->PiwikTracker = new \PiwikTracker( $this->getSiteId(), $apiUrl );

        if ( filter_input( INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_STRING ) == "on" ) {
            $protocol = "https://";
        } else {
            $protocol = "http://";
        }
        $this->PiwikTracker->setUrl( $protocol . $serverName . $requestUrl );

        //Sets the Browser language.
        if ( filter_input( INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING ) ) {
            $lang = filter_input( INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING );
            $lang = explode( ',', $lang );
            $this->PiwikTracker->setBrowserLanguage( $lang[ 0 ] );
        }

        //Sets the user agent, used to detect OS and browser.
        if ( filter_input( INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING ) ) {
            $this->PiwikTracker->setUserAgent( filter_input( INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING ) );
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $siteId
     */
    private function setSiteId( $siteId ) {
        $this->siteId = $siteId;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    private function getSiteId() {
        return $this->siteId;
    }

    /**
     * Tracks a page view
     *
     * @param string $documentTitle Page title as it will appear in the Actions > Page titles report
     *
     * @return mixed Response string or true if using bulk requests.
     */
    public function endEvent( $documentTitle ) {
        return $this->PiwikTracker->doTrackPageView( $documentTitle );
    }

    /**
     * Tracks an event
     *
     * @param string      $category The Event Category (Videos, Music, Games...)
     * @param string      $action   The Event's Action (Play, Pause, Duration, Add Playlist, Downloaded,
     *                              Clicked...)
     * @param string|bool $name     (optional) The Event's object Name (a particular Movie name, or Song name, or
     *                              File name...)
     * @param float|bool  $value    (optional) The Event's value
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return mixed Response string or true if using bulk requests.
     */
    public function track( $category, $action, $name = false, $value = false ) {
        return $this->PiwikTracker->doTrackEvent( $category, $action, $name, $value );
    }
}
