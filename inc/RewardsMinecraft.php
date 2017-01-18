<?php

	if (!function_exists("nxr")) {
		/**
		 * NXR is a helper function. Past strings are recorded in a text file
		 * and when run from a command line output is displayed on screen as
		 * well
		 *
		 * @param string $msg String input to be displayed in logs files
		 * @param bool   $includeDate
		 * @param bool   $newline
		 */
		function nxr($msg, $includeDate = TRUE, $newline = TRUE) {
			if ($includeDate) $msg = date("Y-m-d H:i:s") . ": " . $msg;
			if ($newline) $msg = $msg . "\n";

			if (is_writable(dirname(__FILE__) . "/../fitbit.log")) {
				$fh = fopen(dirname(__FILE__) . "/../fitbit.log", "a");
				fwrite($fh, $msg);
				fclose($fh);
			}
		}
	}

	/**
	 * Created by IntelliJ IDEA.
	 * User: stuar
	 * Date: 15/01/2017
	 * Time: 12:45
	 */
	class RewardsMinecraft {

		/**
		 * @var NxFitbit
		 */
		protected $AppClass;

		/**
		 * @var String
		 */
		protected $UserID;
		protected $UserMinecraftID;

		/**
		 * @var boolean
		 */
		protected $createRewards;

		/**
		 * @var array
		 */
		protected $AwardsGiven;

		/**
		 * @param $userFid
		 */
		public function __construct() {
			require_once(dirname(__FILE__) . "/app.php");
			$this->setAppClass(new NxFitbit());
			$this->AwardsGiven = array();
			$this->createRewards = true;
		}

		/**
		 * @param NxFitbit $paramClass
		 */
		private function setAppClass($paramClass) {
			$this->AppClass = $paramClass;
		}

		/**
		 * @return NxFitbit
		 */
		private function getAppClass() {
			return $this->AppClass;
		}

		/**
		 * @return String
		 */
		public function getUserID() {
			return $this->UserID;
		}

		/**
		 * @param String $UserID
		 */
		public function setUserID($UserID) {
			$this->UserID = $UserID;
		}

		/**
		 * @return String
		 */
		public function getUserMinecraftID() {
			return $this->UserMinecraftID;
		}

		/**
		 * @param String $UserID
		 */
		public function setUserMinecraftID($UserMinecraftID) {
			$this->UserMinecraftID = $UserMinecraftID;
		}

		public function query_api() {
			$wmc_key_provided = $_GET['wmc_key'];
			$wmc_key_correct = $this->getAppClass()->getSetting("wmc_key", NULL, TRUE);
			nxr("Minecraft rewards Check");

			if ($wmc_key_provided != $wmc_key_correct) {
				nxr(" Key doesnt match");
				return array("success" => false, "data" => array("msg" => "Incorrect key"));
			}

			$databaseTable = $this->getAppClass()->getSetting("db_prefix", NULL, FALSE) . "rewards_minecraft";

			if ($_SERVER['REQUEST_METHOD'] == "GET") {
				$dbRewards = $this->getAppClass()->getDatabase()->query( "SELECT * FROM `" . $databaseTable . "` WHERE `state` != 'delivered' ORDER BY `rid` ASC;" );
				$data = array();
				foreach ($dbRewards as $dbReward) {
					$minecraftUsername = $this->getAppClass()->getUserSetting($dbReward['fuid'], "minecraft_username", false);

					if (!array_key_exists($minecraftUsername, $data)) {
						$data[$minecraftUsername] = array();
					}
					if (!array_key_exists($dbReward['rid'], $data[$minecraftUsername])) {
						$data[ $minecraftUsername ][ $dbReward['rid'] ] = array();
					}

					$dbReward['reward'] = str_replace("%s", $minecraftUsername, $dbReward['reward']);

					array_push($data[ $minecraftUsername ][ $dbReward['rid'] ], $dbReward['reward']);

					nxr(" " . $minecraftUsername . " awarded " . $dbReward['reward']);
				}

				return array("success" => true, "data" => $data);

			} elseif ($_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists("processedOrders", $_POST)) {

				$_POST['processedOrders'] = json_decode($_POST['processedOrders']);

				if (is_array($_POST['processedOrders'])) {
					foreach ($_POST['processedOrders'] as $processedOrder) {
						if ($this->getAppClass()->getDatabase()->has($databaseTable, array("rid" => $processedOrder))) {

							$this->getAppClass()->getDatabase()->update($databaseTable, array("state" => "delivered"), array("rid" => $processedOrder));

							nxr(" Reward " . $processedOrder . " processed");
						} else {
							nxr(" Reward " . $processedOrder . " is invalid ID");
						}
					}
				} else {
					nxr(" No processed rewards recived");
				}

				nxr(print_r($this->getAppClass()->getDatabase()->log(), TRUE));

				return array("success" => true);

			}

			return array("success" => false, "data" => array("msg" => "Unknown Error"));

		}

		public function check_rewards($user) {
			nxr("Checking $user for rewards");
			$minecraftUsername = $this->getAppClass()->getUserSetting($user, "minecraft_username");

			if (is_null($minecraftUsername)) {
				nxr("  Users is not a Minecraft player");
			} else {
				nxr("  Users Minecraft name is " . $minecraftUsername);

				$this->setUserID($user);
				$this->setUserMinecraftID($minecraftUsername);

				nxr("  Checking Goal Triggers");
				$this->checkForGoalTriggers();

				nxr("  Checking Value Triggers");
				$this->checkForValueTriggers();

				nxr("  Checking Nomie Triggers");
				$this->checkForNomieTriggers();

				nxr("  Checking Recorded Activities");
				$this->checkForRecordedActivity();

				//nxr(print_r($this->getAppClass()->getDatabase()->log(), TRUE));
			}

		}

		private function getReward($cat, $cat_sub, $level) {
			$reward = array();
			$db_prefix = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );

			if ( $this->getAppClass()->getDatabase()->has( $db_prefix . "rewards", array( "AND" => array( 'cat' => $cat, 'cat_sub' => $cat_sub, 'level[<=]' => $level ) ) ) ) {
				$rewards = $this->getAppClass()->getDatabase()->select($db_prefix . "rewards", array("reward", "nuke"), array( "AND" => array( 'cat' => $cat, 'cat_sub' => $cat_sub, 'level[<=]' => $level ), "ORDER"  => array("level ASC", "rcid DESC"), "LIMIT" => 10 ));

				foreach ( $rewards as $dbReward ) {
					array_push($reward, $dbReward["reward"]);
					if(($key = array_search($dbReward["nuke"], $reward)) !== false) {
						unset($reward[$key]);
					}
					if ( $this->getAppClass()->getDatabase()->has( $db_prefix . "rewards_minecraft", array( "AND" => array( 'fuid' => $this->getUserID(), 'reward' => $dbReward["nuke"] ) ) ) ) {
						$this->getAppClass()->getDatabase()->delete( $db_prefix . "rewards_minecraft", array( "AND" => array( 'fuid' => $this->getUserID(), 'reward' => $dbReward["nuke"] ) ) );
					}
				}

			} elseif ($this->createRewards) {
				$reward = $this->createReward($cat, $cat_sub, $level);
			}

			return $reward;
		}

		private function createReward($cat, $cat_sub, $level) {
			$reward = "give %s bread 1";
			$db_prefix = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );

			$this->getAppClass()->getDatabase()->insert( $db_prefix . "rewards", array(
				"sort_order" => 0,
				"cat"        => $cat,
				"cat_sub"    => $cat_sub,
				"level"      => $level,
				"reward"     => $reward
			) );

			return $reward;
		}

		private function award( $for, $reason, $reward ) {
			$duplicateAwards = array();

			if (!is_array($reward)) {
				$reward = array($reward);
			}

			if (in_array($for, $duplicateAwards) && in_array($for, $this->AwardsGiven)) {
				nxr( "  " . $this->getUserMinecraftID() . " has an award for $for" );
			} else {
				$db_prefix = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE) ;

				foreach ($reward as $recordReward) {
					$currentDate = new DateTime ( 'now' );
					$currentDate = $currentDate->format( "Y-m-d" );
					if ( !$this->getAppClass()->getDatabase()->has( $db_prefix . "rewards_minecraft", array( "AND" => array( 'fuid' => $this->getUserID(), 'datetime[~]' => $currentDate, 'reward' => $recordReward ) ) ) ) {
						$this->getAppClass()->getDatabase()->insert( $db_prefix . "rewards_minecraft", array(
							"fuid"   => $this->getUserID(),
							"state"  => 'pending',
							"reward" => $recordReward,
							"reason" => $reason
						) );
						nxr( "    Awarding '$recordReward' for $for, $reason" );
						array_push( $this->AwardsGiven, $for );
					} else {
						nxr( "    Already been rewarded for $for, $reason" );
					}
				}

			}
		}

		private function checkForRecordedActivity() {
			$currentDate = new DateTime ( 'now' );
			$currentDate = $currentDate->format( "Y-m-d" );
			$db_prefix = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );
			$checkForThese = array("Aerobic", "Bicycling", "Bodyweight", "Calisthenics", "Circuit Training", "Elliptical Trainer", "Hike", "Meditating", "Outdoor Bike", "Push-ups", "Run", "Sit-ups", "Skiing", "Stationary bike", "Strength training", "Swimming", "Tai chi", "Treadmill", "Walk", "Workout", "Yoga");

			$minMaxAvg = array();

			foreach ( $checkForThese as $tracker ) {
				$sql_search = array( "user" => $this->getUserID(), "activityName[~]" => $tracker, "startDate" => $currentDate, "logType[!]" => 'auto_detected' );

				if ( $this->getAppClass()->getDatabase()->has( $db_prefix . "activity_log", array( "AND" => $sql_search ) ) ) {

					$dbEvents = $this->getAppClass()->getDatabase()->select($db_prefix . "activity_log", array("activityName", "startTime", "activeDuration", "activityLevelFairly", "activityLevelVery"),
						array("AND" => $sql_search, "ORDER"  => "startTime DESC"));

					foreach ($dbEvents as $dbEvent) {
						$dbEvent['activeDuration'] = ($dbEvent['activeDuration'] / 1000) / 60;

						if (!array_key_exists($tracker, $minMaxAvg)) {
							$minMaxAvg[$tracker] = array();
							$minMaxAvg[$tracker]['min'] = ($this->getAppClass()->getDatabase()->min( $db_prefix . "activity_log", "activeDuration", array( "AND" => $sql_search ) ) / 1000) / 60;
							$minMaxAvg[$tracker]['avg'] = ($this->getAppClass()->getDatabase()->avg( $db_prefix . "activity_log", "activeDuration", array( "AND" => $sql_search ) ) / 1000) / 60;
							$minMaxAvg[$tracker]['max'] = ($this->getAppClass()->getDatabase()->max( $db_prefix . "activity_log", "activeDuration", array( "AND" => $sql_search ) ) / 1000) / 60;

							$minMaxAvg[$tracker]['min2avg'] = (($minMaxAvg[$tracker]['avg'] - $minMaxAvg[$tracker]['min']) / 2) + $minMaxAvg[$tracker]['min'];
							$minMaxAvg[$tracker]['avg2max'] = (($minMaxAvg[$tracker]['max'] - $minMaxAvg[$tracker]['avg']) / 2) + $minMaxAvg[$tracker]['avg'];
						}

						if ($dbEvent['activeDuration'] == $minMaxAvg[$tracker]['max']) {
							$reward = $this->getReward("activity", $tracker, 5);
							$this->award($tracker, "Recorded $tracker activity on $currentDate @ " . $dbEvent['startTime'], $reward);
						} else if ($dbEvent['activeDuration'] >= $minMaxAvg[$tracker]['avg2max']) {
							$reward = $this->getReward("activity", $tracker, 4);
							$this->award($tracker, "Recorded $tracker activity on $currentDate @ " . $dbEvent['startTime'], $reward);
						} else if ($dbEvent['activeDuration'] >= $minMaxAvg[$tracker]['avg']) {
							$reward = $this->getReward("activity", $tracker, 3);
							$this->award($tracker, "Recorded $tracker activity on $currentDate @ " . $dbEvent['startTime'], $reward);
						} else if ($dbEvent['activeDuration'] >= $minMaxAvg[$tracker]['min2avg']) {
							$reward = $this->getReward("activity", $tracker, 2);
							$this->award($tracker, "Recorded $tracker activity on $currentDate @ " . $dbEvent['startTime'], $reward);
						} else {
							$reward = $this->getReward("activity", $tracker, 1);
							$this->award($tracker, "Recorded $tracker activity on $currentDate @ " . $dbEvent['startTime'], $reward);
						}
					}
				}
			}

		}

		private function checkForNomieTriggers() {
			$currentDate = new DateTime ( 'now' );
			$currentDate = $currentDate->format( "Y-m-d" );
			$db_prefix = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );
			$checkForThese = array("Healthy Meal","Unhealthy Meal","Healthy Snack","Unhealthy Snack","Drank Water","Plank","Walk Sit","Went Walkng","Completed Day");

			foreach ( $checkForThese as $tracker ) {
				$nomie_id = $this->getAppClass()->getDatabase()->get( $db_prefix . "nomie_trackers", "id", array( "AND" => array( "fuid" => $this->getUserID(), "label" => $tracker ) ) );
				//nxr( "    Nomie ID for $tracker is $nomie_id" );
				if ( $this->getAppClass()->getDatabase()->has( $db_prefix . "nomie_events", array( "AND" => array( "fuid" => $this->getUserID(), "id" => $nomie_id, "datestamp[~]" => $currentDate ) ) ) ) {

					$dbEvents = $this->getAppClass()->getDatabase()->select($db_prefix . "nomie_events", 'datestamp',
						array("AND" => array( "fuid" => $this->getUserID(), "id" => $nomie_id, "datestamp[~]" => "2016-10-10" ), "ORDER"  => "datestamp DESC"));

					foreach ($dbEvents as $dbEvent) {
						$reward = $this->getReward("nomie", $tracker, 1);
						$this->award($tracker, "Recorded $tracker on $dbEvent", $reward);
					}
				}
			}
		}

		private function checkForValueTriggers() {
			$goalsToCheck = array("steps", "floors");

			$currentDate = new DateTime ( 'now' );
			$currentDate = $currentDate->format( "Y-m-d" );
			$db_prefix = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );

			foreach ( $goalsToCheck as $goal ) {

				if ($goal == "steps") {
					$divider = 100;
				} else {
					$divider = 10;
				}

				if ( $this->getAppClass()->getDatabase()->has( $db_prefix . "steps", array(
					"AND" => array(
						'user' => $this->getUserID(),
						'date' => $currentDate
					)
				) )
				) {
					$recordedValue = round($this->getAppClass()->getDatabase()->get($db_prefix . "steps", $goal, array("AND" => array("user" => $this->getUserID(), "date" => $currentDate))), 3);
					$recordedTarget = round($this->getAppClass()->getDatabase()->get($db_prefix . "steps_goals", $goal, array("AND" => array("user" => $this->getUserID(), "date" => $currentDate))), 3);
					if (!is_numeric($recordedTarget)) {
						$recordedTarget = round($this->getAppClass()->getUserSetting($this->getUserID(), "goal_" . $goal), 3);
					}

					if ($recordedValue < $recordedTarget) {
						$hundredth = round($recordedValue / $divider, 0);
						if ($hundredth > 0) {
							$reward = $this->getReward( "hundredth", $goal, $hundredth );
							$this->award($goal . "_values", "Reached level $hundredth $goal value on $currentDate", $reward);
						}
					}
				}
			}
		}

		private function checkForGoalTriggers() {
			$currentDate = new DateTime ( 'now' );
			$currentDate = $currentDate->format( "Y-m-d" );
			$goalsToCheck = array("steps","floors","distance");
			foreach ( $goalsToCheck as $goal ) {
				// Crushed Step Goal
				if (!$this->crushedGoal($goal)){
					// Smashed Step Goal
					if(!$this->smashedGoal($goal)) {
						// Reached Step Goal
						if(!$this->reachedGoal($goal)) {
							nxr("    " . $this->getUserID() . " hasn't yet reached their $goal goal");
						} else {
							$reward = $this->getReward("goal", $goal, 1);
							$this->award($goal, "Beat $goal goal on $currentDate", $reward);
						}
					} else {
						$reward = $this->getReward("goal", $goal, 2);
						$this->award($goal, "Smashed $goal goal on $currentDate", $reward);
					}
				} else {
					$reward = $this->getReward("goal", $goal, 3);
					$this->award($goal, "Crushed $goal goal on $currentDate", $reward);
				}

			}
		}

		private function reachedGoal($goal, $multiplyer = 1) {
			$currentDate = new DateTime ( 'now' );
			$currentDate = $currentDate->format( "Y-m-d" );
			$db_prefix = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );

			if ( $this->getAppClass()->getDatabase()->has( $db_prefix . "steps", array(
				"AND" => array(
					'user' => $this->getUserID(),
					'date' => $currentDate
				)
			) )
			) {
				$recordedValue = round($this->getAppClass()->getDatabase()->get($db_prefix . "steps", $goal, array("AND" => array("user" => $this->getUserID(), "date" => $currentDate))), 3);
				$recordedTarget = round($this->getAppClass()->getDatabase()->get($db_prefix . "steps_goals", $goal, array("AND" => array("user" => $this->getUserID(), "date" => $currentDate))), 3);
				if (!is_numeric($recordedTarget) || $recordedTarget <= 0) {
					$recordedTarget = round($this->getAppClass()->getUserSetting($this->getUserID(), "goal_" . $goal), 3);
				}

				$requiredTarget = $recordedTarget * $multiplyer;

				if ($recordedValue >= $requiredTarget) {
					return true;
				}
			} else {
				nxr("    No $goal data recorded for $currentDate");
			}

			return false;
		}

		private function smashedGoal($goal) {
			return $this->reachedGoal($goal, 1.5);
		}

		private function crushedGoal($goal) {
			return $this->reachedGoal($goal, 2);
		}
	}