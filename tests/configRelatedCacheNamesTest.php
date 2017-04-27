<?php
    /**
     * Created by IntelliJ IDEA.
     * User: stuar
     * Date: 27/04/2017
     * Time: 22:53
     */

    use PHPUnit\Framework\TestCase;

    class configRelatedCacheNamesTest extends TestCase
    {
        /**
         * @var config
         */
        protected $configClass;

        protected function setUp()
        {
            $this->configClass = new config();
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_activities(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('activities');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("activity", $activities);
            $this->assertArrayHasKey("dashboard", $activities);
            $this->assertArrayHasKey("weekpedometer", $activities);
            $this->assertArrayHasKey("aboutme", $activities);
            $this->assertArrayHasKey("keypoints", $activities);
            $this->assertArrayHasKey("steps", $activities);
            $this->assertArrayHasKey("tracked", $activities);
            $this->assertArrayHasKey("tracked", $activities);
            $this->assertArrayHasKey("tasker", $activities);
            $this->assertArrayHasKey("challenger", $activities);
            $this->assertArrayHasKey("push", $activities);
            $this->assertArrayHasKey("conky", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_activity_log(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('activity_log');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("activityhistory", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_badges(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('badges');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("topbadges", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_body(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('body');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("trend", $activities);
            $this->assertArrayHasKey("weight", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_caloriesOut(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('caloriesOut');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("tasker", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_devices(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('devices');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("devices", $activities);
            $this->assertArrayHasKey("tasker", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_distance(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('distance');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("dashboard", $activities);
            $this->assertArrayHasKey("weekpedometer", $activities);
            $this->assertArrayHasKey("aboutme", $activities);
            $this->assertArrayHasKey("keypoints", $activities);
            $this->assertArrayHasKey("steps", $activities);
            $this->assertArrayHasKey("tracked", $activities);
            $this->assertArrayHasKey("tasker", $activities);
            $this->assertArrayHasKey("challenger", $activities);
            $this->assertArrayHasKey("push", $activities);
            $this->assertArrayHasKey("conky", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_elevation(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('elevation');
            $this->assertTrue(count($activities) == 0);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_floors(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('floors');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("dashboard", $activities);
            $this->assertArrayHasKey("weekpedometer", $activities);
            $this->assertArrayHasKey("aboutme", $activities);
            $this->assertArrayHasKey("keypoints", $activities);
            $this->assertArrayHasKey("steps", $activities);
            $this->assertArrayHasKey("tracked", $activities);
            $this->assertArrayHasKey("tasker", $activities);
            $this->assertArrayHasKey("challenger", $activities);
            $this->assertArrayHasKey("push", $activities);
            $this->assertArrayHasKey("conky", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_foods(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('foods');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("food", $activities);
            $this->assertArrayHasKey("fooddiary", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_goals(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('goals');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("dashboard", $activities);
            $this->assertArrayHasKey("tracked", $activities);
            $this->assertArrayHasKey("steps", $activities);
            $this->assertArrayHasKey("tasker", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_goals_calories(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('goals_calories');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("trend", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_heart(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('heart');
            $this->assertTrue(count($activities) == 0);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_leaderboard(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('leaderboard');
            $this->assertArrayHasKey("trend", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_minutesFairlyActive(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesFairlyActive');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("activity", $activities);
            $this->assertArrayHasKey("tasker", $activities);
            $this->assertArrayHasKey("challenger", $activities);
            $this->assertArrayHasKey("push", $activities);
            $this->assertArrayHasKey("conky", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_minutesLightlyActive(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesLightlyActive');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("activity", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_minutesSedentary(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesSedentary');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("activity", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_minutesVeryActive(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesVeryActive');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("activity", $activities);
            $this->assertArrayHasKey("tasker", $activities);
            $this->assertArrayHasKey("challenger", $activities);
            $this->assertArrayHasKey("push", $activities);
            $this->assertArrayHasKey("conky", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_nomie_trackers(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('nomie_trackers');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("nomie", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_profile(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('profile');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("trend", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_sleep(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('sleep');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("sleep", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_steps(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('steps');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("dashboard", $activities);
            $this->assertArrayHasKey("weekpedometer", $activities);
            $this->assertArrayHasKey("aboutme", $activities);
            $this->assertArrayHasKey("keypoints", $activities);
            $this->assertArrayHasKey("steps", $activities);
            $this->assertArrayHasKey("tracked", $activities);
            $this->assertArrayHasKey("tasker", $activities);
            $this->assertArrayHasKey("conky", $activities);
        }

        /**
         * @covers config::getRelatedCacheNames
         */
        public function testRelatedCacheNames_water(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('water');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("water", $activities);
            $this->assertArrayHasKey("tasker", $activities);
        }
    }
