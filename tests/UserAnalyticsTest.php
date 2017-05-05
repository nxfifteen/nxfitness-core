<?php

    namespace Core\Tests;

    require_once(dirname(__FILE__) . '/../lib/autoloader.php');

    use Core\Analytics\UserAnalytics;
    use PHPUnit\Framework\TestCase;

    /**
     * Class UserAnalyticsTest
     *
     * @package Core\Tests
     */
    class UserAnalyticsTest extends TestCase
    {

        /**
         * @var UserAnalytics
         */
        protected $analyticsClass;

        protected function setUp()
        {
            $this->analyticsClass = new UserAnalytics(14, 'https://nxfifteen.me.uk/cogent/', "fit.itsabeta.nx",
                "http://fit.itsabeta.nx/api/fitbit/ux/");
        }

        /**
         * @covers \Core\Analytics\UserAnalytics::track
         */
        public function testTrackEvent()
        {
            $classSiteId = $this->analyticsClass->track("Test", "Test Case", "testTrackEvent", 1);
            print_r($classSiteId);
        }

        /**
         * @covers \Core\Analytics\UserAnalytics::endEvent
         */
        public function testendEvent()
        {
            $classSiteId = $this->analyticsClass->endEvent("Test Page Tracked");
            print_r($classSiteId);
        }
    }
