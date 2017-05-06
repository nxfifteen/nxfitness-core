<?php

    namespace Core\Tests;

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
         * @covers \Core\Analytics\ErrorRecording::getSentryClient
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
         * @covers \Core\Analytics\ErrorRecording::getSentryErrorHandler
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
         * @covers \Core\Analytics\ErrorRecording::captureException
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
         * @covers \Core\Analytics\ErrorRecording::captureMessage
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
