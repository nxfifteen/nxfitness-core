<?php

    /**
     * Class Upgrade
     */
    class Upgrade {

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
	    protected $VersionInstalling = "0.0.0.5";

	    /**
	     * @var String
	     */
	    protected $VersionCurrent;

	    /**
	     * @var array
	     */
	    protected $VersionCurrentArray;
	    
	    /**
	     * @var medoo
	     */
	    protected $database;

	    /**
	     * @var fitbit
	     */
	    protected $fitbitapi;

	    /**
	     * @var config
	     */
	    protected $settings;

        /**
         * Upgrade constructor.
         *
         * @param $userFid
         */
        public function __construct() {
	        require_once(dirname(__FILE__) . "/config.php");
	        $this->setSettings(new config());

	        require_once(dirname(__FILE__) . "/../library/medoo.php");
	        $this->setDatabase(new medoo(array(
		        'database_type' => 'mysql',
		        'database_name' => $this->getSetting("db_name"),
		        'server'        => $this->getSetting("db_server"),
		        'username'      => $this->getSetting("db_username"),
		        'password'      => $this->getSetting("db_password"),
		        'charset'       => 'utf8'
	        )));

	        $this->getSettings()->setDatabase($this->getDatabase());
        }

	    /**
	     * @param config $settings
	     */
	    private function setSettings($settings) {
		    $this->settings = $settings;
	    }

	    /**
	     * @return config
	     */
	    public function getSettings() {
		    return $this->settings;
	    }

	    /**
	     * @param medoo $database
	     */
	    private function setDatabase($database) {
		    $this->database = $database;
	    }

	    /**
	     * @return medoo
	     */
	    public function getDatabase() {
		    return $this->database;
	    }

	    /**
	     * Get settings from config class
	     *
	     * @param                $key
	     * @param null           $default
	     * @param bool           $query_db
	     *
	     * @return string
	     */
	    public function getSetting($key, $default = NULL, $query_db = TRUE) {
		    return $this->getSettings()->get($key, $default, $query_db);
	    }

	    /**
	     * Set value in database/config class
	     *
	     * @param           $key
	     * @param           $value
	     * @param bool   $query_db
	     *
	     * @return bool
	     */
	    public function setSetting($key, $value, $query_db = TRUE) {
		    return $this->getSettings()->set($key, $value, $query_db);
	    }

	    private function wasMySQLError( $error ) {
	    	if (is_null($error[2])) {
	    		return false;
		    } else {
			    print_r($error);
		    	return true;
		    }
	    }

	    public function getInstallingVersion() {
		    return $this->VersionInstalling;
	    }

	    private function getInstallingVersionBrakeDown() {
		    return explode(".", $this->VersionInstalling);
	    }

	    public function getInstallVersion() {
		    if (is_null($this->VersionCurrent)) {
			    $this->VersionCurrent = $this->getSetting("version", "0.0.0.1", TRUE);

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
				    if ($this->$updateFunction()) {
					    echo "  + " . $updateFunction . " [OKAY]\n";
				    } else {
					    echo "  + " . $updateFunction . " [FAILED]\n";
					    return false;
				    }
			    }
		    }

		    //$this->setSetting("version", $this->VersionInstalling, TRUE);
	    }

	    /** @noinspection PhpUnusedPrivateMethodInspection */
	    private function update_2() {
		    $db_prefix = $this->getSetting("db_prefix", FALSE);

		    $this->getDatabase()->query("CREATE TABLE `".$db_prefix."streak_goal` (  `uid` int(6) NOT NULL,  `fuid` varchar(8) NOT NULL,  `goal` varchar(255) NOT NULL,  `start_date` date NOT NULL,  `end_date` date NOT NULL,  `length` int(3) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		    if ($this->wasMySQLError($this->getDatabase()->error())) return false;

		    $this->getDatabase()->query("ALTER TABLE `".$db_prefix."streak_goal`  ADD PRIMARY KEY (`fuid`,`goal`,`start_date`) USING BTREE,  ADD UNIQUE KEY `uid` (`uid`);");
		    if ($this->wasMySQLError($this->getDatabase()->error())) return false;

		    $this->getDatabase()->query("ALTER TABLE `".$db_prefix."streak_goal` MODIFY `uid` int(6) NOT NULL AUTO_INCREMENT;");
		    if ($this->wasMySQLError($this->getDatabase()->error())) return false;

		    $this->getDatabase()->query("ALTER TABLE `".$db_prefix."streak_goal` ADD CONSTRAINT `".$db_prefix."streak_goal_ibfk` FOREIGN KEY (`fuid`) REFERENCES `".$db_prefix."users` (`fuid`) ON DELETE NO ACTION;");
		    if ($this->wasMySQLError($this->getDatabase()->error())) return false;

		    $this->getDatabase()->query("ALTER TABLE `".$db_prefix."streak_goal` CHANGE `end_date` `end_date` DATE NULL, CHANGE `length` `length` INT(3) NULL");
		    if ($this->wasMySQLError($this->getDatabase()->error())) return false;

		    $this->setSetting("version", "0.0.0.2", TRUE);
		    return true;
	    }

	    /** @noinspection PhpUnusedPrivateMethodInspection */
	    private function update_3() {
		    $db_prefix = $this->getSetting("db_prefix", FALSE);

		    $users = $this->getDatabase()->select($db_prefix . "users", "fuid");
		    if ($this->wasMySQLError($this->getDatabase()->error())) return false;

		    foreach ( $users as $user ) {
			    echo "  " . $user . "\n";
			    $steps = $this->getDatabase()->select($db_prefix . "steps", array(
				    "[>]" . $db_prefix . "steps_goals" => array(
					    "user",
					    "date"
				    ) ), array(
				    $db_prefix . "steps.date",
				    $db_prefix . "steps.steps",
				    $db_prefix . "steps_goals.steps(step_goal)"
			    ), array(
				    $db_prefix . "steps.user" => $user,
				    "ORDER" => $db_prefix . "steps.date ASC"
			    ));
			    if ($this->wasMySQLError($this->getDatabase()->error())) return false;

			    $streak = false;
			    $streak_start = "";
			    $streak_prevdate = "";
			    $streak_end = "";
			    foreach ( $steps as $step ) {
				    echo "   - " . $step['date'] . " " . $step['steps'] . "/" . $step['step_goal'];

			    	if ( $step['steps'] >= $step['step_goal'] ) {
					    if ( !$streak ) {
						    $streak = true;
						    $streak_start = $step['date'];

						    echo "\tnew streak started";
					    }
					    $streak_prevdate = $step['date'];
				    } else {
					    if ( $streak ) {
						    $streak = false;
						    $streak_end = $streak_prevdate;

						    $date1 = new DateTime($streak_end);
						    $date2 = new DateTime($streak_start);

						    $days_between = $date2->diff($date1)->format("%a");
						    $days_between = $days_between + 1;

						    echo "\tnew streak broken. " . $streak_start . " to " . $streak_end . " (".$days_between.")";

						    if ($days_between > 1) {
							    if ( $this->getDatabase()->has( $db_prefix . "streak_goal", array(
								    "AND" => array(
									    "fuid"       => $user,
									    "goal"       => "steps",
									    "start_date" => $streak_start
								    )
							    ) )
							    ) {
								    $this->getDatabase()->update( $db_prefix . "streak_goal", array(
									    "end_date" => $streak_end,
									    "length"   => $days_between
								    ),
									    array(
										    "AND" => array(
											    "fuid"       => $user,
											    "goal"       => "steps",
											    "start_date" => $streak_start
										    )
									    )
								    );
								    if ( $this->wasMySQLError( $this->getDatabase()->error() ) ) {
									    print_r( $this->getDatabase()->log() );

									    return FALSE;
								    }
							    } else {
								    $this->getDatabase()->insert( $db_prefix . "streak_goal", array(
									    "fuid"       => $user,
									    "goal"       => "steps",
									    "start_date" => $streak_start,
									    "end_date"   => $streak_end,
									    "length"     => $days_between
								    ) );
								    if ( $this->wasMySQLError( $this->getDatabase()->error() ) ) {
									    print_r( $this->getDatabase()->log() );

									    return FALSE;
								    }
							    }
						    }
					    }
				    }

				    echo "\n";
			    }

		    }
		    $this->setSetting("version", "0.0.0.3", TRUE);
		    return true;
	    }

	    /** @noinspection PhpUnusedPrivateMethodInspection */
	    private function update_4() {

	    	/*
CREATE TABLE `nx_fitbit_rewards_minecraft` (
  `rid` int(5) NOT NULL,
  `fuid` varchar(8) NOT NULL,
  `state` varchar(15) NOT NULL,
  `reward` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
	    	 */


		    $this->setSetting("version", "0.0.0.4", TRUE);
		    return true;

	    }


    }