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
            $this->analyticsClass = new UserAnalytics(15, 'https://nxfifteen.me.uk/cogent/', "fit.itsabeta.nx",
                "http://fit.itsabeta.nx/api/fitbit/ux/");
        }

        /**
         * @covers \Core\Analytics\UserAnalytics::track
         */
        public function testTrackEvent()
        {
            $classSiteId = $this->analyticsClass->track("Test", "Test Case", "testTrackEvent", 1);
            $this->assertTrue(is_string($classSiteId));
        }

        /**
         * @covers \Core\Analytics\UserAnalytics::endEvent
         */
        public function testEndEvent()
        {
            $classSiteId = $this->analyticsClass->endEvent("Test Page Tracked");
            $this->assertTrue(is_string($classSiteId));
        }
    }
