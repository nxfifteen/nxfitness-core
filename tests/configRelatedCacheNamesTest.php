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

    require_once (dirname(__FILE__) . '/../inc/Config.php');

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
            $this->assertTrue(in_array("activity", $activities));
            $this->assertTrue(in_array("dashboard", $activities));
            $this->assertTrue(in_array("weekpedometer", $activities));
            $this->assertTrue(in_array("aboutme", $activities));
            $this->assertTrue(in_array("keypoints", $activities));
            $this->assertTrue(in_array("steps", $activities));
            $this->assertTrue(in_array("tracked", $activities));
            $this->assertTrue(in_array("tracked", $activities));
            $this->assertTrue(in_array("tasker", $activities));
            $this->assertTrue(in_array("challenger", $activities));
            $this->assertTrue(in_array("push", $activities));
            $this->assertTrue(in_array("conky", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesActivityLog(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('activity_log');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("activityhistory", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesBadges(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('badges');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("topbadges", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesBody(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('body');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("trend", $activities));
            $this->assertTrue(in_array("weight", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesCaloriesOut(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('caloriesOut');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("tasker", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesDevices(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('devices');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("devices", $activities));
            $this->assertTrue(in_array("tasker", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesDistance(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('distance');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("dashboard", $activities));
            $this->assertTrue(in_array("weekpedometer", $activities));
            $this->assertTrue(in_array("aboutme", $activities));
            $this->assertTrue(in_array("keypoints", $activities));
            $this->assertTrue(in_array("steps", $activities));
            $this->assertTrue(in_array("tracked", $activities));
            $this->assertTrue(in_array("tasker", $activities));
            $this->assertTrue(in_array("challenger", $activities));
            $this->assertTrue(in_array("push", $activities));
            $this->assertTrue(in_array("conky", $activities));
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
            $this->assertTrue(in_array("dashboard", $activities));
            $this->assertTrue(in_array("weekpedometer", $activities));
            $this->assertTrue(in_array("aboutme", $activities));
            $this->assertTrue(in_array("keypoints", $activities));
            $this->assertTrue(in_array("steps", $activities));
            $this->assertTrue(in_array("tracked", $activities));
            $this->assertTrue(in_array("tasker", $activities));
            $this->assertTrue(in_array("challenger", $activities));
            $this->assertTrue(in_array("push", $activities));
            $this->assertTrue(in_array("conky", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesFoods(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('foods');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("food", $activities));
            $this->assertTrue(in_array("fooddiary", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesGoals(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('goals');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("dashboard", $activities));
            $this->assertTrue(in_array("tracked", $activities));
            $this->assertTrue(in_array("steps", $activities));
            $this->assertTrue(in_array("tasker", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesGoalsCalories(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('goals_calories');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("trend", $activities));
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
        public function testCacheNamesLeaderboard(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('leaderboard');
            $this->assertTrue(in_array("trend", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesMinutesFairlyActive(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesFairlyActive');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("activity", $activities));
            $this->assertTrue(in_array("tasker", $activities));
            $this->assertTrue(in_array("challenger", $activities));
            $this->assertTrue(in_array("push", $activities));
            $this->assertTrue(in_array("conky", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesMinutesLightlyActive(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesLightlyActive');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("activity", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesMinutesSedentary(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesSedentary');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("activity", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesMinutesVeryActive(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesVeryActive');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("activity", $activities));
            $this->assertTrue(in_array("tasker", $activities));
            $this->assertTrue(in_array("challenger", $activities));
            $this->assertTrue(in_array("push", $activities));
            $this->assertTrue(in_array("conky", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesNomieTrackers(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('nomie_trackers');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("nomie", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesProfile(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('profile');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("trend", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesSleep(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('sleep');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("sleep", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesSteps(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('steps');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("dashboard", $activities));
            $this->assertTrue(in_array("weekpedometer", $activities));
            $this->assertTrue(in_array("aboutme", $activities));
            $this->assertTrue(in_array("keypoints", $activities));
            $this->assertTrue(in_array("steps", $activities));
            $this->assertTrue(in_array("tracked", $activities));
            $this->assertTrue(in_array("tasker", $activities));
            $this->assertTrue(in_array("conky", $activities));
        }

        /**
         * @covers Config::getRelatedCacheNames
         */
        public function testCacheNamesWater(): void
        {
            $activities = $this->configClass->getRelatedCacheNames('water');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("water", $activities));
            $this->assertTrue(in_array("tasker", $activities));
        }
    }
