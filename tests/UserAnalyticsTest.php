<?php
    /*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

    namespace Core\Tests;

    print dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "autoloader.php\n";
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "autoloader.php");

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
            $this->analyticsClass = new UserAnalytics(15, 'https://nxfifteen.me.uk/cogent/', "fit.itsabeta.nx",
                "http://fit.itsabeta.nx/api/fitbit/ux/");
        }

        /**
         * @covers UserAnalytics::track
         */
        public function testTrackEvent()
        {
            $classSiteId = $this->analyticsClass->track("Test", "Test Case", "testTrackEvent", 1);
            $this->assertTrue(is_string($classSiteId));
        }

        /**
         * @covers UserAnalytics::endEvent
         */
        public function testEndEvent()
        {
            $classSiteId = $this->analyticsClass->endEvent("Test Page Tracked");
            $this->assertTrue(is_string($classSiteId));
        }
    }
