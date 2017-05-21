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

    use Core\Analytics\ErrorRecording;
    use Core\Core;
    use Exception;
    use PHPUnit\Framework\TestCase;

    /**
     * Class ErrorRecordingTest
     *
     * @package Core\Tests
     */
    class ErrorRecordingTest extends TestCase
    {

        /**
         * @var ErrorRecording
         */
        protected $testingClass;

        protected function setUp()
        {
            $this->testingClass = new ErrorRecording(new Core());
        }

        /**
         * @covers ErrorRecording::getSentryClient
         */
        public function testSentryClientTest()
        {
            if (defined('SENTRY_DSN')) {
                $this->assertInstanceOf("Raven_Client", $this->testingClass->getSentryClient());
            } else {
                $this->assertNull($this->testingClass->getSentryClient());
            }
        }

        /**
         * @covers ErrorRecording::getSentryErrorHandler
         */
        public function testSentryErrorHandlerTest()
        {
            if (defined('SENTRY_DSN')) {
                $this->assertInstanceOf("Raven_ErrorHandler", $this->testingClass->getSentryErrorHandler());
            } else {
                $this->assertNull($this->testingClass->getSentryErrorHandler());
            }
        }

        /**
         * @covers ErrorRecording::captureException
         */
        public function testCaptureExceptionTest()
        {
            if (defined('SENTRY_DSN')) {
                $this->assertNotNull($this->testingClass->captureException(new Exception()));
            } else {
                $this->assertNull($this->testingClass->captureException(new Exception()));
            }
        }

        /**
         * @covers ErrorRecording::captureMessage
         */
        public function testCaptureMessageTest()
        {
            if (defined('SENTRY_DSN')) {
                $this->assertNotNull($this->testingClass->captureMessage("Test Message"));
            } else {
                $this->assertNull($this->testingClass->captureMessage("Test Message"));
            }
        }
    }
