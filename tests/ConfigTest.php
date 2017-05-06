<?php
/**
 * Copyright (c) 2017. Stuart McCulloch Anderson
 */

namespace Core\Tests;

    use Core\Config;
    use Medoo\Medoo;
    use PHPUnit\Framework\TestCase;

    /**
     * Class ConfigTest
     *
     * @package Core\Tests
     */
    class ConfigTest extends TestCase
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
         * @covers \Core\Config::set
         */
        public function testSetNoDB()
        {
            $storeValue = rand(0, 1000);
            $this->assertTrue($this->configClass->set('testSetNoDB', $storeValue, false));
        }

        /**
         * @covers \Core\Config::get
         */
        public function testGetNoDB()
        {
            $storeValue = rand(0, 1000);
            $this->configClass->set('testSetNoDB', $storeValue, false);
            $settingsValue = $this->configClass->get('testSetNoDB', 'Not Stored', false);
            $this->assertSame($storeValue, $settingsValue);
        }

        /**
         * @covers \Core\Config::set
         */
        public function testSetInDB()
        {
            $this->configClass->setDatabase(new medoo(array(
                'database_type' => 'mysql',
                'database_name' => $this->configClass->get("db_name"),
                'server'        => $this->configClass->get("db_server"),
                'username'      => $this->configClass->get("db_username"),
                'password'      => $this->configClass->get("db_password"),
                'charset'       => 'utf8'
            )));

            $storeValue = rand(0, 1000);
            $dbAction   = $this->configClass->set('testSetNoDB', $storeValue, true);
            if (is_numeric($dbAction)) {
                if ($dbAction == 1) {
                    $dbAction = true;
                } else {
                    $dbAction = false;
                }
            }

            $this->assertTrue($dbAction);
        }

        /**
         * @covers \Core\Config::get
         */
        public function testGetInDB()
        {
            $this->configClass->setDatabase(new medoo(array(
                'database_type' => 'mysql',
                'database_name' => $this->configClass->get("db_name"),
                'server'        => $this->configClass->get("db_server"),
                'username'      => $this->configClass->get("db_username"),
                'password'      => $this->configClass->get("db_password"),
                'charset'       => 'utf8'
            )));

            $storeValue = rand(0, 1000);
            $this->configClass->set('testSetNoDB', $storeValue, true);

            $settingsValue = $this->configClass->get('testSetNoDB', 'Not Stored', true);

            $this->assertSame($storeValue, $settingsValue);
        }

        /**
         * @covers \Core\Config::setUser
         */
        public function testUserSetInDB()
        {
            $ownerFuid = $this->configClass->get("ownerFuid");

            $this->configClass->setDatabase(new medoo(array(
                'database_type' => 'mysql',
                'database_name' => $this->configClass->get("db_name"),
                'server'        => $this->configClass->get("db_server"),
                'username'      => $this->configClass->get("db_username"),
                'password'      => $this->configClass->get("db_password"),
                'charset'       => 'utf8'
            )));

            $storeValue = rand(0, 1000);
            $dbAction   = $this->configClass->setUser($ownerFuid, 'testSetNoDB', $storeValue);
            if (is_numeric($dbAction)) {
                if ($dbAction == 1) {
                    $dbAction = true;
                } else {
                    $dbAction = false;
                }
            }

            $this->assertTrue($dbAction);
        }

        /**
         * @covers \Core\Config::getUser
         */
        public function testUserGetInDB()
        {
            $ownerFuid = $this->configClass->get("ownerFuid");

            $this->configClass->setDatabase(new medoo(array(
                'database_type' => 'mysql',
                'database_name' => $this->configClass->get("db_name"),
                'server'        => $this->configClass->get("db_server"),
                'username'      => $this->configClass->get("db_username"),
                'password'      => $this->configClass->get("db_password"),
                'charset'       => 'utf8'
            )));

            $storeValue = rand(0, 1000);
            $this->configClass->setUser($ownerFuid, 'testSetNoDB', $storeValue, true);

            $settingsValue = $this->configClass->getUser($ownerFuid, 'testSetNoDB', 'Not Stored');

            $this->assertSame($storeValue, $settingsValue);
        }

    }
