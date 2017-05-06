<?php

    namespace Core\Tests;

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
            echo "testTrackEvent\n";
            var_dump($classSiteId);
            $this->assertTrue(is_string($classSiteId));
        }

        /**
         * @covers \Core\Analytics\UserAnalytics::endEvent
         */
        public function testEndEvent()
        {
            $classSiteId = $this->analyticsClass->endEvent("Test Page Tracked");
            echo "testEndEvent\n";
            var_dump($classSiteId);
            $this->assertTrue(is_string($classSiteId));
        }
    }
