<?php
    /**
     * Created by IntelliJ IDEA.
     * User: stuar
     * Date: 27/04/2017
     * Time: 22:53
     */

    namespace Core\TestSuite;

    use Core\Config as Config;
    use PHPUnit\Framework\TestCase;

    class ConfigCacheNamesTest extends TestCase
    {
        /**
         * @var Config
         */
        protected $configClass;

        protected function setUp()
        {
            $this->configClass = new Config();
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesActivities(): void
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
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesActivityLog(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('activity_log');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("activityhistory", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesBadges(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('badges');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("topbadges", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesBody(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('body');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("trend", $activities);
            $this->assertArrayHasKey("weight", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesCaloriesOut(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('caloriesOut');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("tasker", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesDevices(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('devices');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("devices", $activities);
            $this->assertArrayHasKey("tasker", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesDistance(): void
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
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesElevation(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('elevation');
            $this->assertTrue(count($activities) == 0);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesFloors(): void
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
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesFoods(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('foods');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("food", $activities);
            $this->assertArrayHasKey("fooddiary", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesGoals(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('goals');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("dashboard", $activities);
            $this->assertArrayHasKey("tracked", $activities);
            $this->assertArrayHasKey("steps", $activities);
            $this->assertArrayHasKey("tasker", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesGoalsCalories(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('goals_calories');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("trend", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesHeart(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('heart');
            $this->assertTrue(count($activities) == 0);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNames_leaderboard(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('leaderboard');
            $this->assertArrayHasKey("trend", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNames_minutesFairlyActive(): void
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
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNames_minutesLightlyActive(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesLightlyActive');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("activity", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNames_minutesSedentary(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesSedentary');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("activity", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNames_minutesVeryActive(): void
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
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNames_nomie_trackers(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('nomie_trackers');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("nomie", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNames_profile(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('profile');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("trend", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNames_sleep(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('sleep');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("sleep", $activities);
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNames_steps(): void
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
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNames_water(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('water');
            $this->assertContainsOnly('string', $activities);
            $this->assertArrayHasKey("water", $activities);
            $this->assertArrayHasKey("tasker", $activities);
        }
    }
