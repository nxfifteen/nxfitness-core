<?php

    class Raven_Breadcrumbs_ErrorHandler
    {

        /**
         * @var Raven_Client the client object that sends the message to the server
         */
        protected $ravenClient;
        private $existingHandler;

        /**
         * @param Raven_Client $ravenClient
         * @param int          $level  The minimum logging level at which this handler will be triggered
         * @param Boolean      $bubble Whether the messages that are handled can bubble up the stack or not
         */
        public function __construct(Raven_Client $ravenClient)
        {
            $this->ravenClient = $ravenClient;
        }

        public function handleError($code, $message, $file = '', $line = 0, $context = array())
        {
            $this->ravenClient->breadcrumbs->record(array(
                'category' => 'error_reporting',
                'message'  => $message,
                'level'    => $this->ravenClient->translateSeverity($code),
                'data'     => array(
                    'code' => $code,
                    'line' => $line,
                    'file' => $file,
                ),
            ));

            if ($this->existingHandler !== null) {
                return call_user_func($this->existingHandler, $code, $message, $file, $line, $context);
            } else {
                return false;
            }
        }

        public function install()
        {
            $this->existingHandler = set_error_handler(array($this, 'handleError'), E_ALL);

            return $this;
        }
    }
