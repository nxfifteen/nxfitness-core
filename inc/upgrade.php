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
	     * @var integer
	     */
	    protected $NumUpdates;

	    /**
	     * @var array
	     */
	    protected $UpdateFunctions;

	    /**
	     * @var String
	     */
	    protected $VersionInstalling = "0.0.0.2";

	    /**
	     * @var String
	     */
	    protected $VersionCurrent;

	    /**
	     * @var array
	     */
	    protected $VersionCurrentArray;

        /**
         * Upgrade constructor.
         *
         * @param $userFid
         */
        public function __construct($userFid) {
	        $this->UserID = $userFid;

		    require_once(dirname(__FILE__) . "/app.php");
	        $this->AppClass = new NxFitbit();

	        if ($this->AppClass->isUser($this->UserID)) {
		        echo " - Valid User\n";
	        } else {
		        echo " - " . $userFid . " is not valid\n";
	        }
	        $this->AppClass->setSetting("version", "0.0.0.1", TRUE);

        }

	    private function getAppClass() {
	    	return $this->AppClass;
	    }

	    public function getInstallingVersion() {
		    return $this->VersionInstalling;
	    }

	    private function getInstallingVersionBrakeDown() {
		    return explode(".", $this->VersionInstalling);
	    }

	    public function getInstallVersion() {
		    if (is_null($this->VersionCurrent)) {
			    $this->VersionCurrent = $this->getAppClass()->getSetting("version", "0.0.0.1", TRUE);

			    $this->VersionCurrentArray = explode(".", $this->VersionCurrent);
		    }

		    return $this->VersionCurrent;
	    }

	    private function getInstallVersionBrakeDown() {
		    if (is_null($this->VersionCurrent)) {
			    $this->getInstallVersion();
		    }

		    return $this->VersionCurrentArray;
	    }

	    public function getUpdatesRequired() {
		    $currentVersion = $this->getInstallVersionBrakeDown();
		    $installVersion = $this->getInstallingVersionBrakeDown();

		    $updateFunctions = array();

		    $currentNumber = ( $currentVersion[0] . $currentVersion[1] . $currentVersion[2] . $currentVersion[3] ) * 1;
		    $installNumber = ( $installVersion[0] . $installVersion[1] . $installVersion[2] . $installVersion[3] ) * 1;

		    for ($x = ($currentNumber + 1); $x <= $installNumber; $x++) {
		    	if (method_exists($this, "update_" . $x))
			        array_push($updateFunctions, "update_" . $x);
		    }

		    $this->UpdateFunctions = $updateFunctions;
		    $this->NumUpdates = count($updateFunctions);

		    return $updateFunctions;
	    }

	    public function getNumUpdates() {
		    if(is_null($this->NumUpdates)) {
			    $this->getUpdatesRequired();
		    }

		    return $this->NumUpdates;
	    }

	    public function getUpdateFunctions() {
		    if(is_null($this->UpdateFunctions)) {
			    $this->getUpdatesRequired();
		    }

		    return $this->UpdateFunctions;
	    }

	    public function runUpdates() {
		    if(is_null($this->UpdateFunctions)) {
			    $this->getUpdatesRequired();
		    }

		    foreach ( $this->UpdateFunctions as $updateFunction ) {
			    if (method_exists($this, $updateFunction)) {
				    echo " Running " . $updateFunction . "\n";
				    $this->$updateFunction();
			    }
		    }

		    //$this->getAppClass()->setSetting("version", $this->VersionInstalling, TRUE);
	    }

	    ///** @noinspection PhpUnusedPrivateMethodInspection */
	    //private function update_2() {
	    //	$this->getAppClass()->setSetting("version", "0.0.0.2", TRUE);
	    //
	    //}

	    /** @noinspection PhpUnusedPrivateMethodInspection */
	    private function update_2() {
	    	

	    	$this->getAppClass()->setSetting("version", "0.0.0.2", TRUE);
	    }

	    /*
CREATE TABLE `nx_fitbit_streak_goal` (
  `fuid` varchar(8) NOT NULL,
  `goal` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `nx_fitbit_streak_goal`
  ADD PRIMARY KEY (`fuid`);
		 */

    }