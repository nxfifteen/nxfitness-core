<?php

    /**
     * Created by PhpStorm.
     * User: nxad
     * Date: 21/02/15
     * Time: 13:47
     */
    class tracking {

        /**
         * @var PiwikTracker
         */
        protected $PiwikTracker;
        /**
         * @var string
         */
        protected $siteId;

        /**
         * @param $trackingId
         * @param $api_url
         */
        public function __construct($trackingId, $api_url) {
            $this->setSiteId($trackingId);
            require_once(dirname(__FILE__) . "/../library/PiwikTracker.php");

            $this->PiwikTracker = new PiwikTracker($this->getSiteId(), $api_url);

            if (array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] == "on") {
                $protical = "https://";
            } else {
                $protical = "http://";
            }
            $this->PiwikTracker->setUrl($protical . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);

            //Sets the Browser language.
            $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $lang = explode(',', $lang);
            $this->PiwikTracker->setBrowserLanguage($lang[0]);

            //Sets the user agent, used to detect OS and browser.
            $this->PiwikTracker->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        }

        /**
         * @param mixed $siteId
         */
        private function setSiteId($siteId) {
            $this->siteId = $siteId;
        }

        /**
         * @return mixed
         */
        private function getSiteId() {
            return $this->siteId;
        }

        /**
         * @param string $documentTitle Page title as it will appear in the Actions > Page titles report
         */
        public function endEvent($documentTitle) {
            $this->PiwikTracker->doTrackPageView($documentTitle);
        }

        /**
         * @param string $category The Event Category (Videos, Music, Games...)
         * @param string $action The Event's Action (Play, Pause, Duration, Add Playlist, Downloaded, Clicked...)
         * @param string|bool $name (optional) The Event's object Name (a particular Movie name, or Song name, or File name...)
         * @param float|bool $value (optional) The Event's value
         */
        public function track($category, $action, $name = FALSE, $value = FALSE) {
            $this->PiwikTracker->doTrackEvent($category, $action, $name, $value);
        }
    }