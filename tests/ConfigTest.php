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
         * @param $value
         *
         * @return bool
         */
        private function convertNumberToBool($value)
        {
            if (is_numeric($value)) {
                if ($value == 1) {
                    return true;
                } else {
                    return false;
                }
            }

            return $value;
        }

        /**
         * @return Medoo
         */
        private function setUpDatabase()
        {
            return new medoo(array(
                'database_type' => 'mysql',
                'database_name' => $this->configClass->get("db_name"),
                'server'        => $this->configClass->get("db_server"),
                'username'      => $this->configClass->get("db_username"),
                'password'      => $this->configClass->get("db_password"),
                'charset'       => 'utf8'
            ));
        }

        /**
         * @covers \Core\Config::set
         */
        public function testSetNoDB()
        {
            $storeValue = rand(0, 1000);
            $this->assertTrue($this->configClass->set('test' . __METHOD__ . 'DB' . $storeValue, $storeValue, false));
        }

        /**
         * @covers \Core\Config::del
         */
        public function testDelNoDB()
        {
            $storeValue = rand(0, 1000);
            $this->configClass->set('test' . __METHOD__ . 'DB' . $storeValue, $storeValue, false);

            $dbAction = $this->convertNumberToBool($this->configClass->del('test' . __METHOD__ . 'DB' . $storeValue,
                false));
            $this->assertTrue($dbAction);
        }

        /**
         * @covers \Core\Config::get
         */
        public function testGetNoDB()
        {
            $storeValue = rand(0, 1000);
            $this->configClass->set('test' . __METHOD__ . 'DB' . $storeValue, $storeValue, false);
            $settingsValue = $this->configClass->get('test' . __METHOD__ . 'DB' . $storeValue, 'Not Stored', false);
            $this->assertSame($storeValue, $settingsValue);
        }

        /**
         * @covers \Core\Config::set
         */
        public function testSetInDB()
        {
            $this->configClass->setDatabase($this->setUpDatabase());

            $storeValue = rand(0, 1000);
            $dbAction   = $this->convertNumberToBool($this->configClass->set('test' . __METHOD__ . 'DB' . $storeValue,
                $storeValue, true));
            $this->assertTrue($dbAction);

            $this->configClass->del('test' . __METHOD__ . 'DB' . $storeValue, true);
        }

        /**
         * @covers \Core\Config::get
         */
        public function testGetInDB()
        {
            $this->configClass->setDatabase($this->setUpDatabase());

            $storeValue = rand(0, 1000);
            $this->configClass->set('test' . __METHOD__ . 'DB' . $storeValue, $storeValue, true);

            $settingsValue = $this->configClass->get('test' . __METHOD__ . 'DB' . $storeValue, 'Not Stored', true);

            $this->assertSame($storeValue, $settingsValue);

            $this->configClass->del('test' . __METHOD__ . 'DB' . $storeValue, true);
        }

        /**
         * @covers \Core\Config::del
         */
        public function testDelInDB()
        {
            $this->configClass->setDatabase($this->setUpDatabase());

            $storeValue = rand(0, 1000);
            $this->configClass->set('test' . __METHOD__ . 'DB' . $storeValue, $storeValue, true);

            $dbAction = $this->convertNumberToBool($this->configClass->del('test' . __METHOD__ . 'DB' . $storeValue,
                true));
            $this->assertTrue($dbAction);
        }

        /**
         * @covers \Core\Config::setUser
         */
        public function testUserSetInDB()
        {
            $ownerFuid = $this->configClass->get("ownerFuid");

            $this->configClass->setDatabase($this->setUpDatabase());

            $storeValue = rand(0, 1000);
            $dbAction   = $this->convertNumberToBool($this->configClass->setUser($ownerFuid,
                'test' . __METHOD__ . 'DB' . $storeValue, $storeValue));
            $this->assertTrue($dbAction);

            $this->configClass->delUser($ownerFuid, 'test' . __METHOD__ . 'DB' . $storeValue);
        }

        /**
         * @covers \Core\Config::del
         */
        public function testUserDelNoDB()
        {
            $ownerFuid = $this->configClass->get("ownerFuid");

            $this->configClass->setDatabase($this->setUpDatabase());

            $storeValue = rand(0, 1000);
            $this->configClass->setUser($ownerFuid, 'test' . __METHOD__ . 'DB' . $storeValue, $storeValue);

            $dbAction = $this->convertNumberToBool($this->configClass->delUser($ownerFuid,
                'test' . __METHOD__ . 'DB' . $storeValue));
            $this->assertTrue($dbAction);
        }

        /**
         * @covers \Core\Config::getUser
         */
        public function testUserGetInDB()
        {
            $ownerFuid = $this->configClass->get("ownerFuid");

            $this->configClass->setDatabase($this->setUpDatabase());

            $storeValue = rand(0, 1000);
            $this->configClass->setUser($ownerFuid, 'test' . __METHOD__ . 'DB' . $storeValue, $storeValue);

            $settingsValue = $this->configClass->getUser($ownerFuid, 'test' . __METHOD__ . 'DB' . $storeValue,
                'Not Stored');

            $this->assertSame($storeValue, $settingsValue);

            $this->configClass->delUser($ownerFuid, 'test' . __METHOD__ . 'DB' . $storeValue);
        }
    }
