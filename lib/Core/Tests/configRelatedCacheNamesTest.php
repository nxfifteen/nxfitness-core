<?php

    namespace Core\Tests;

    require_once(dirname(__FILE__) . '/../../autoloader.php');

    use Core\Config as Config;
    use \PHPUnit\Framework\TestCase;

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
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesActivities()
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
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesActivityLog()
        {
            $activities = $this->configClass->getRelatedCacheNames('activity_log');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("activityhistory", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesBadges()
        {
            $activities = $this->configClass->getRelatedCacheNames('badges');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("topbadges", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesBody()
        {
            $activities = $this->configClass->getRelatedCacheNames('body');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("trend", $activities));
            $this->assertTrue(in_array("weight", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesCaloriesOut()
        {
            $activities = $this->configClass->getRelatedCacheNames('caloriesOut');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("tasker", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesDevices()
        {
            $activities = $this->configClass->getRelatedCacheNames('devices');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("devices", $activities));
            $this->assertTrue(in_array("tasker", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesDistance()
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
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesElevation()
        {
            $activities = $this->configClass->getRelatedCacheNames('elevation');
            $this->assertTrue(count($activities) == 0);
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesFloors()
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
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesFoods()
        {
            $activities = $this->configClass->getRelatedCacheNames('foods');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("food", $activities));
            $this->assertTrue(in_array("fooddiary", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesGoals()
        {
            $activities = $this->configClass->getRelatedCacheNames('goals');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("dashboard", $activities));
            $this->assertTrue(in_array("tracked", $activities));
            $this->assertTrue(in_array("steps", $activities));
            $this->assertTrue(in_array("tasker", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesGoalsCalories()
        {
            $activities = $this->configClass->getRelatedCacheNames('goals_calories');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("trend", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesHeart()
        {
            $activities = $this->configClass->getRelatedCacheNames('heart');
            $this->assertTrue(count($activities) == 0);
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesLeaderboard()
        {
            $activities = $this->configClass->getRelatedCacheNames('leaderboard');
            $this->assertTrue(in_array("trend", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesMinutesFairlyActive()
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
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesMinutesLightlyActive()
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesLightlyActive');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("activity", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesMinutesSedentary()
        {
            $activities = $this->configClass->getRelatedCacheNames('minutesSedentary');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("activity", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesMinutesVeryActive()
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
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesNomieTrackers()
        {
            $activities = $this->configClass->getRelatedCacheNames('nomie_trackers');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("nomie", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesProfile()
        {
            $activities = $this->configClass->getRelatedCacheNames('profile');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("trend", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesSleep()
        {
            $activities = $this->configClass->getRelatedCacheNames('sleep');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("sleep", $activities));
        }

        /**
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesSteps()
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
         * @covers \Core\Config::getRelatedCacheNames
         */
        public function testCacheNamesWater()
        {
            $activities = $this->configClass->getRelatedCacheNames('water');
            $this->assertContainsOnly('string', $activities);
            $this->assertTrue(in_array("water", $activities));
            $this->assertTrue(in_array("tasker", $activities));
        }
    }
