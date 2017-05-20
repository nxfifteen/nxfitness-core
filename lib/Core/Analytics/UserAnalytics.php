<?php
    /*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 * https://nxfifteen.me.uk
 *
 * Copyright (c) 2017, Stuart McCulloch Anderson
 *
 * Released under the MIT license
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

    namespace Core\Analytics;

    require_once(dirname(__FILE__) . "/../../autoloader.php");

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
     */
    class UserAnalytics
    {

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
         * @param string $api_url    "http://example.org/piwik/" or "http://piwik.example.org/"
         *                           If set, will overwrite PiwikTracker::$URL
         * @param null   $server_name
         * @param null   $request_uri
         */
        public function __construct($trackingId, $api_url, $server_name = null, $request_uri = null)
        {
            if (is_null($server_name)) {
                $server_name = $_SERVER['SERVER_NAME'];
            }
            if (is_null($request_uri)) {
                $request_uri = $_SERVER['REQUEST_URI'];
            }

            $this->setSiteId($trackingId);

            $this->PiwikTracker = new \PiwikTracker($this->getSiteId(), $api_url);

            if (array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] == "on") {
                $protocol = "https://";
            } else {
                $protocol = "http://";
            }
            $this->PiwikTracker->setUrl($protocol . $server_name . $request_uri);

            //Sets the Browser language.
            if (array_key_exists("HTTP_ACCEPT_LANGUAGE", $_SERVER)) {
                $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                $lang = explode(',', $lang);
                $this->PiwikTracker->setBrowserLanguage($lang[0]);
            }

            //Sets the user agent, used to detect OS and browser.
            if (array_key_exists("HTTP_USER_AGENT", $_SERVER)) {
                $this->PiwikTracker->setUserAgent($_SERVER['HTTP_USER_AGENT']);
            }
        }

        /**
         * @codeCoverageIgnore
         * @param string $siteId
         */
        private function setSiteId($siteId)
        {
            $this->siteId = $siteId;
        }

        /**
         * @codeCoverageIgnore
         * @return string
         */
        private function getSiteId()
        {
            return $this->siteId;
        }

        /**
         * Tracks a page view
         *
         * @param string $documentTitle Page title as it will appear in the Actions > Page titles report
         *
         * @return mixed Response string or true if using bulk requests.
         */
        public function endEvent($documentTitle)
        {
            return $this->PiwikTracker->doTrackPageView($documentTitle);
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
         *
         * @return mixed Response string or true if using bulk requests.
         */
        public function track($category, $action, $name = false, $value = false)
        {
            return $this->PiwikTracker->doTrackEvent($category, $action, $name, $value);
        }
    }
