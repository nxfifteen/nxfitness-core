<?php
    /**
     * Created by IntelliJ IDEA.
     * User: stuar
     * Date: 05/05/2017
     * Time: 22:03
     */

    namespace Core\Tests;

    require_once(dirname(__FILE__) . '/../lib/autoloader.php');

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
        public function getSentryClientTest() {
            if (defined('SENTRY_DSN')) {
                $this->assertInstanceOf("Raven_Client", $this->testingClass->getSentryClient());
            } else {
                $this->assertNull($this->testingClass->getSentryClient());
            }
        }

        /**
         * @covers \Core\Analytics\ErrorRecording::getSentryErrorHandler
         */
        public function getSentryErrorHandlerTest() {
            if (defined('SENTRY_DSN')) {
                $this->assertInstanceOf("Raven_ErrorHandler", $this->testingClass->getSentryErrorHandler());
            } else {
                $this->assertNull($this->testingClass->getSentryErrorHandler());
            }
        }

        /**
         * @covers \Core\Analytics\ErrorRecording::captureException
         */
        public function captureExceptionTest() {
            if (defined('SENTRY_DSN')) {
                $this->assertNotNull($this->testingClass->captureException(new Exception()));
            } else {
                $this->assertNull($this->testingClass->captureException(new Exception()));
            }
        }

        /**
         * @covers \Core\Analytics\ErrorRecording::captureMessage
         */
        public function captureMessageTest() {
            if (defined('SENTRY_DSN')) {
                $this->assertNotNull($this->testingClass->captureMessage("Test Message"));
            } else {
                $this->assertNull($this->testingClass->captureMessage("Test Message"));
            }
        }
    }
