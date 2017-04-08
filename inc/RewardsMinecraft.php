<?php

	if ( ! function_exists( "nxr" ) ) {
		/**
		 * NXR is a helper function. Past strings are recorded in a text file
		 * and when run from a command line output is displayed on screen as
		 * well
		 *
		 * @param string $msg String input to be displayed in logs files
		 * @param bool   $includeDate
		 * @param bool   $newline
		 */
		function nxr( $msg, $includeDate = TRUE, $newline = TRUE ) {
			if ( $includeDate ) {
				$msg = date( "Y-m-d H:i:s" ) . ": " . $msg;
			}
			if ( $newline ) {
				$msg = $msg . "\n";
			}

			if ( is_writable( dirname( __FILE__ ) . "/../fitbit.log" ) ) {
				$fh = fopen( dirname( __FILE__ ) . "/../fitbit.log", "a" );
				fwrite( $fh, $msg );
				fclose( $fh );
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
		public function __construct( $user = NULL ) {
			require_once( dirname( __FILE__ ) . "/app.php" );
			$this->setAppClass( new NxFitbit() );
			$this->AwardsGiven   = array();
			$this->createRewards = TRUE;
			$this->setUserID( $user );
		}

		/**
		 * @param NxFitbit $paramClass
		 */
		private function setAppClass( $paramClass ) {
			$this->AppClass = $paramClass;
		}

		/**
		 * @return NxFitbit
		 */
		private function getAppClass() {
			return $this->AppClass;
		}

		private function CheckForAward( $cat, $event, $score ) {
			$reward    = array();
			$db_prefix = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );

			if ( $this->getAppClass()->getDatabase()->has( $db_prefix . "reward_map", array(
				"AND" => array(
					'cat'   => $cat,
					'event' => $event,
					'rule'  => $score
				)
			) )
			) {
				$rewards = $this->getAppClass()->getDatabase()->query(
					"SELECT `" . $db_prefix . "reward_map`.`rmid` AS `rmid`,`" . $db_prefix . "reward_map`.`reward` AS `rid`,`" . $db_prefix . "rewards`.`reward` AS `reward`,`" . $db_prefix . "rewards`.`description` AS `description`"
					. " FROM `" . $db_prefix . "reward_map`"
					. " JOIN `" . $db_prefix . "rewards` ON (`" . $db_prefix . "reward_map`.`reward` = `" . $db_prefix . "rewards`.`rid`)"
					. " WHERE `" . $db_prefix . "reward_map`.`cat` = '" . $cat . "' AND `" . $db_prefix . "reward_map`.`event` = '" . $event . "' AND `" . $db_prefix . "reward_map`.`rule` = '" . $score . "' " );
				$this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), array(
					"METHOD" => __METHOD__,
					"LINE"   => __LINE__
				) );
				foreach ( $rewards as $dbReward ) {
					array_push( $reward, array(
						"rid"         => $dbReward['rid'],
						"rmid"        => $dbReward['rmid'],
						"reward"      => $dbReward['reward'],
						"description" => $dbReward['description']
					) );
				}
			} elseif ( $this->createRewards ) {
				$this->getAppClass()->getDatabase()->insert( $db_prefix . "reward_map", array(
					"cat"   => $cat,
					"event" => $event,
					"rule"  => $score
				) );
				$this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), array(
					"METHOD" => __METHOD__,
					"LINE"   => __LINE__
				) );
			}

			if ( count( $reward ) == 0 ) {
				return FALSE;
			} else {

				foreach ( $reward as $recordReward ) {

					$currentDate = new DateTime ( 'now' );
					$currentDate = $currentDate->format( "Y-m-d" );
					if ( ! $this->getAppClass()->getDatabase()->has( $db_prefix . "reward_queue", array(
						"AND" => array(
							'fuid'    => $this->getUserID(),
							'date[~]' => $currentDate,
							'rmid'    => $recordReward['rmid']
						)
					) )
					) {
						$nukeOne = $this->getAppClass()->getDatabase()->select( $db_prefix . "reward_nuke", 'rid', array(
							"AND" => array(
								"nukeid"      => $recordReward['rid'],
								"directional" => "true"
							)
						) );
						$this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), array(
							"METHOD" => __METHOD__,
							"LINE"   => __LINE__
						) );
						if ( count( $nukeOne ) > 0 ) {
							foreach ( $nukeOne as $nukeId ) {
								if ( $this->getAppClass()->getDatabase()->has( $db_prefix . "reward_queue", array(
									"AND" => array(
										'fuid'   => $this->getUserID(),
										'reward' => $nukeId
									)
								) )
								) {
									$this->getAppClass()->getDatabase()->delete( $db_prefix . "reward_queue", array(
										"AND" => array(
											'fuid'   => $this->getUserID(),
											'reward' => $nukeId
										)
									) );
								}
							}
						}

						$nukeTwo = $this->getAppClass()->getDatabase()->select( $db_prefix . "reward_nuke", 'nukeid', array(
							"AND" => array(
								"rid"         => $recordReward['rid'],
								"directional" => "false"
							)
						) );
						$this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), array(
							"METHOD" => __METHOD__,
							"LINE"   => __LINE__
						) );
						if ( count( $nukeTwo ) > 0 ) {
							foreach ( $nukeTwo as $nukeId ) {
								if ( $this->getAppClass()->getDatabase()->has( $db_prefix . "reward_queue", array(
									"AND" => array(
										'fuid'   => $this->getUserID(),
										'reward' => $nukeId
									)
								) )
								) {
									$this->getAppClass()->getDatabase()->delete( $db_prefix . "reward_queue", array(
										"AND" => array(
											'fuid'   => $this->getUserID(),
											'reward' => $nukeId
										)
									) );
								}
							}
						}

						$this->getAppClass()->getDatabase()->insert( $db_prefix . "reward_queue", array(
							"fuid"   => $this->getUserID(),
							"state"  => 'pending',
							"rmid"   => $recordReward['rmid'],
							"reward" => $recordReward['rid']
						) );
						$this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), array(
							"METHOD" => __METHOD__,
							"LINE"   => __LINE__
						) );
						nxr( "    Awarding $cat / $event ($score) = " . print_r( $recordReward['description'], TRUE ) );
					} else {
						nxr( "    Already awarded $cat / $event ($score) = " . print_r( $recordReward['description'], TRUE ) );
					}


				}

				return $reward;
			}

		}

		private function reachedGoal( $goal, $value, $multiplyer = 1 ) {
			$currentDate = new DateTime ( 'now' );
			$currentDate = $currentDate->format( "Y-m-d" );
			$db_prefix   = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );
			if ( $value >= 1 ) {
				$recordedValue  = $value;
				$recordedTarget = round( $this->getAppClass()->getDatabase()->get( $db_prefix . "steps_goals", $goal, array(
					"AND" => array(
						"user" => $this->getUserID(),
						"date" => $currentDate
					)
				) ), 3 );
				if ( ! is_numeric( $recordedTarget ) || $recordedTarget <= 0 ) {
					$recordedTarget = round( $this->getAppClass()->getUserSetting( $this->getUserID(), "goal_" . $goal ), 3 );
				}
				$requiredTarget = $recordedTarget * $multiplyer;
				if ( $recordedValue >= $requiredTarget ) {
					return TRUE;
				}
			} else {
				nxr( "    No $goal data recorded for $currentDate" );
			}

			return FALSE;
		}

		private function smashedGoal( $goal, $value ) { return $this->reachedGoal( $goal, $value, 1.5 ); }

		private function crushedGoal( $goal, $value ) { return $this->reachedGoal( $goal, $value, 2 ); }

		/**
		 * @return String
		 */
		public function getUserID() {
			return $this->UserID;
		}

		/**
		 * @param String $UserID
		 */
		public function setUserID( $UserID ) {
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
		public function setUserMinecraftID( $UserMinecraftID ) {
			$this->UserMinecraftID = $UserMinecraftID;
		}

		public function query_api() {
			$wmc_key_provided = $_GET['wmc_key'];
			$wmc_key_correct  = $this->getAppClass()->getSetting( "wmc_key", NULL, TRUE );
			nxr( "Minecraft rewards Check" );

			if ( $wmc_key_provided != $wmc_key_correct ) {
				nxr( " Key doesnt match" );

				return array( "success" => FALSE, "data" => array( "msg" => "Incorrect key" ) );
			}

			$databaseTable = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "rewards_minecraft";

			if ( $_SERVER['REQUEST_METHOD'] == "GET" ) {
				$rewards = $this->getAppClass()->getDatabase()->query(
					"SELECT `" . $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "rewards`.`reward` AS `reward`,"
					. " `" . $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "reward_queue`.`fuid` AS `fuid`,"
					. " `" . $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "reward_queue`.`rqid` AS `rqid`"
					. " FROM `" . $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "rewards`"
					. " JOIN `" . $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "reward_queue` ON (`" . $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "reward_queue`.`reward` = `" . $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "rewards`.`rid`)"
					. " WHERE `" . $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "reward_queue`.`state` = 'pending' LIMIT 50" );

				$data = array();
				foreach ( $rewards as $dbReward ) {
					$minecraftUsername = $this->getAppClass()->getUserSetting( $dbReward['fuid'], "minecraft_username", FALSE );

					if ( ! array_key_exists( $minecraftUsername, $data ) ) {
						$data[ $minecraftUsername ] = array();
					}
					if ( ! array_key_exists( $dbReward['rqid'], $data[ $minecraftUsername ] ) ) {
						$data[ $minecraftUsername ][ $dbReward['rqid'] ] = array();
					}
					$dbReward['reward'] = str_replace( "%s", $minecraftUsername, $dbReward['reward'] );
					array_push( $data[ $minecraftUsername ][ $dbReward['rqid'] ], $dbReward['reward'] );
				}

				return array( "success" => TRUE, "data" => $data );

			} elseif ( $_SERVER['REQUEST_METHOD'] == "POST" && array_key_exists( "processedOrders", $_POST ) ) {

				$_POST['processedOrders'] = json_decode( $_POST['processedOrders'] );

				if ( is_array( $_POST['processedOrders'] ) ) {
					foreach ( $_POST['processedOrders'] as $processedOrder ) {
						if ( $this->getAppClass()->getDatabase()->has( $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "reward_queue", array( "rqid" => $processedOrder ) ) ) {

							$this->getAppClass()->getDatabase()->update( $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE ) . "reward_queue", array( "state" => "delivered" ), array( "rqid" => $processedOrder ) );
							$this->getAppClass()->getErrorRecording()->postDatabaseQuery( $this->getAppClass()->getDatabase(), array(
								"METHOD" => __METHOD__,
								"LINE"   => __LINE__
							) );

							nxr( " Reward " . $processedOrder . " processed" );
						} else {
							nxr( " Reward " . $processedOrder . " is invalid ID" );
						}
					}
				} else {
					nxr( " No processed rewards recived" );
				}

				return array( "success" => TRUE );

			}

			return array( "success" => FALSE, "data" => array( "msg" => "Unknown Error" ) );

		}

		public function EventTriggerActivity( $activity ) {
			$currentDate   = new DateTime ( 'now' );
			$currentDate   = $currentDate->format( "Y-m-d" );
			$db_prefix     = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );
			$checkForThese = array(
				"Aerobic",
				"Bicycling",
				"Bodyweight",
				"Calisthenics",
				"Circuit Training",
				"Elliptical Trainer",
				"Hike",
				"Meditating",
				"Outdoor Bike",
				"Push-ups",
				"Run",
				"Sit-ups",
				"Skiing",
				"Stationary bike",
				"Strength training",
				"Swimming",
				"Tai chi",
				"Treadmill",
				"Walk",
				"Workout",
				"Yoga"
			);

			$supportActivity = FALSE;
			if ( $activity->activityName != "auto_detected" ) {
				foreach ( $checkForThese as $tracker ) {
					if ( ! $supportActivity && strpos( $activity->activityName, $tracker ) !== FALSE ) {
						$supportActivity = TRUE;
					}
				}
			}

			if ( $supportActivity ) {
				$sql_search       = array(
					"user"            => $this->getUserID(),
					"activityName[~]" => $tracker,
					"startDate"       => $currentDate,
					"logType[!]"      => 'auto_detected'
				);
				$minMaxAvg        = array();
				$minMaxAvg['min'] = ( $this->getAppClass()->getDatabase()->min( $db_prefix . "activity_log", "activeDuration", array( "AND" => $sql_search ) ) / 1000 ) / 60;
				$minMaxAvg['avg'] = ( $this->getAppClass()->getDatabase()->avg( $db_prefix . "activity_log", "activeDuration", array( "AND" => $sql_search ) ) / 1000 ) / 60;
				$minMaxAvg['max'] = ( $this->getAppClass()->getDatabase()->max( $db_prefix . "activity_log", "activeDuration", array( "AND" => $sql_search ) ) / 1000 ) / 60;

				$minMaxAvg['min2avg'] = ( ( $minMaxAvg['avg'] - $minMaxAvg['min'] ) / 2 ) + $minMaxAvg['min'];
				$minMaxAvg['avg2max'] = ( ( $minMaxAvg['max'] - $minMaxAvg['avg'] ) / 2 ) + $minMaxAvg['avg'];

				$activeDuration = $activity->duration / 1000 / 60;

				if ( $activeDuration == $minMaxAvg['max'] ) {
					$this->CheckForAward( "activity", $tracker, "max" );
				} else if ( $activeDuration >= $minMaxAvg['avg2max'] ) {
					$this->CheckForAward( "activity", $tracker, "avg2max" );
				} else if ( $activeDuration >= $minMaxAvg['avg'] ) {
					$this->CheckForAward( "activity", $tracker, "avg" );
				} else if ( $activeDuration >= $minMaxAvg['min2avg'] ) {
					$this->CheckForAward( "activity", $tracker, "min2avg" );
				} else {
					$this->CheckForAward( "activity", $tracker, "other" );
				}
			}

		}

		public function EventTriggerBadgeAwarded( $badge ) {
			nxr( " ** API Event Trigger Badge" );

			//if (date('Y-m-d') == $badge->dateTime) {
			nxr( "    " . $badge->shortName . " (" . $badge->category . ") awarded " . $badge->timesAchieved . " on " . $badge->dateTime );

			if ( $this->CheckForAward( "badge", $badge->category . " | " . $badge->shortName, "awarded" ) ) {

			} else if ( $this->CheckForAward( "badge", $badge->category, "awarded" ) ) {

			} else if ( $this->CheckForAward( "badge", $badge->category . " | " . $badge->shortName, $badge->timesAchieved ) ) {

			} else if ( $this->CheckForAward( "badge", $badge->category, $badge->timesAchieved ) ) {

			}
			//}
		}

		public function EventTriggerWeightChange( $current, $goal, $last ) {
			if ( $current <= $goal ) {
				$this->CheckForAward( "body", "weight", "goal" );
			} else if ( $current < $last ) {
				$this->CheckForAward( "body", "weight", "decreased" );
			} else if ( $current > $last ) {
				$this->CheckForAward( "body", "weight", "increased" );
			}
		}

		public function EventTriggerFatChange( $current, $goal, $last ) {
			if ( $current <= $goal ) {
				$this->CheckForAward( "body", "fat", "goal" );
			} else if ( $current < $last ) {
				$this->CheckForAward( "body", "fat", "decreased" );
			} else if ( $current > $last ) {
				$this->CheckForAward( "body", "fat", "increased" );
			}
		}

		public function EventTriggerNewMeal( $meal ) {
			nxr( " ** API Event Meal Logged" );
			nxr( "      " . $meal->loggedFood->name . " recorded" );
		}

		public function EventTriggerVeryActive( $veryActive ) {
			$currentDate = new DateTime ( 'now' );
			$currentDate = $currentDate->format( "Y-m-d" );
			$db_prefix   = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );
			if ( $veryActive >= 1 ) {
				$recordedValue  = $veryActive;
				$recordedTarget = $this->getAppClass()->getDatabase()->get( $db_prefix . "steps_goals", "activeMinutes", array(
					"AND" => array(
						"user" => $this->getUserID(),
						"date" => $currentDate
					)
				) );
				if ( ! is_numeric( $recordedTarget ) || $recordedTarget <= 0 ) {
					$recordedTarget = round( $this->getAppClass()->getUserSetting( $this->getUserID(), "goal_activity" ), 30 );
				}

				if ( $recordedValue >= $recordedTarget ) {
					$this->CheckForAward( "goal", "veryactive", "reached" );
				}
			}
		}

		public function EventTriggerTracker( $date, $trigger, $value ) {
			$goalsToCheck = array( "steps", "floors", "distance" );

			if ( in_array( $trigger, $goalsToCheck ) && date( 'Y-m-d' ) == $date ) {
				// Crushed Step Goal
				if ( ! $this->crushedGoal( $trigger, $value ) ) {
					// Smashed Step Goal
					if ( ! $this->smashedGoal( $trigger, $value ) ) {
						// Reached Step Goal
						if ( $this->reachedGoal( $trigger, $value ) ) {
							$reward = $this->CheckForAward( "goal", $trigger, "reached" );
						}
					} else {
						$reward = $this->CheckForAward( "goal", $trigger, "smashed" );
					}
				} else {
					$reward = $this->CheckForAward( "goal", $trigger, "crushed" );
				}

				if ( $trigger == "steps" ) {
					$divider = 100;
				} else {
					$divider = 10;
				}

				$db_prefix = $this->getAppClass()->getSetting( "db_prefix", NULL, FALSE );
				if ( $value >= 1 ) {
					$recordedValue = round( $value, 3 );
					$hundredth     = round( $recordedValue / $divider, 0 );
					nxr( " checking awards for $trigger $hundredth" );
					$this->CheckForAward( "hundredth", $trigger, $hundredth );

				}
			}
		}

		public function EventTriggerNomie( $inputArray ) {
			$event = $inputArray[2];
			$date  = $inputArray[5];
			$score = $inputArray[4];

			nxr( "  ** API Event Nomie - " . $event . " logged on " . $date . " and scored " . $score );

			if ( ! $this->CheckForAward( "nomie", "logged", $event ) ) {
				$this->CheckForAward( "nomie", "score", $score );
			}
		}

		public function EventTriggerStreak( $goal, $length, $ended = FALSE ) {
			$this->CheckForAward( "streak", $goal, $length );
		}
	}