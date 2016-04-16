<?php

    /**
     * Class Upgrade
     */
    class Upgrade {

        /**
         * @var NxFitbit
         */
        protected $AppClass;

        /**
         * @var String
         */
        protected $UserID;

        /**
         * Upgrade constructor.
         *
         * @param $userFid
         */
        public function __construct($userFid) {
            require_once(dirname(__FILE__) . "/app.php");
        }

    }