<?php

    require_once(dirname(__FILE__) . "/../config.def.dist.php");
    require_once(dirname(__FILE__) . "/app.php");

    /**
     * Upgrade
     *
     * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-Upgrade phpDocumentor
     *            wiki for Upgrade.
     * @version   0.0.1
     * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link      https://nxfifteen.me.uk NxFIFTEEN
     * @copyright 2017 Stuart McCulloch Anderson
     * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
     */
    class Upgrade
    {

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
        protected $VersionInstalling = APP_VERSION;

        /**
         * @var String
         */
        protected $VersionCurrent;

        /**
         * @var array
         */
        protected $VersionCurrentArray;

        /** @noinspection PhpUndefinedClassInspection */
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
         * @var ErrorRecording
         */
        protected $errorRecording;

        /**
         * Upgrade constructor.
         *
         * @param NxFitbit $appClass
         *
         * @internal param $userFid
         */
        public function __construct($appClass = null)
        {
            require_once(dirname(__FILE__) . "/config.php");
            $this->setSettings(new config());

            if (is_null($appClass)) {
                $this->appClass = new NxFitbit();
            } else {
                $this->appClass = $appClass;
            }
            $this->errorRecording = new ErrorRecording($this->appClass);

            $this->setDatabase($this->appClass->getDatabase());

            $this->getSettings()->setDatabase($this->getDatabase());

        }

        /**
         * @param config $settings
         */
        private function setSettings($settings)
        {
            $this->settings = $settings;
        }

        /** @noinspection PhpUndefinedClassInspection */
        /**
         * @param medoo $database
         */
        private function setDatabase($database)
        {
            $this->database = $database;
        }

        private function wasMySQLError($error)
        {
            $this->errorRecording->postDatabaseQuery($this->getDatabase(), array(
                "METHOD" => __METHOD__,
                "LINE"   => __LINE__
            ));

            if (is_null($error[2])) {
                return false;
            } else {
                print_r($error);

                return true;
            }
        }

        private function getInstallingVersionBrakeDown()
        {
            return explode(".", $this->VersionInstalling);
        }

        private function getInstallVersionBrakeDown()
        {
            if (is_null($this->VersionCurrent)) {
                $this->getInstallVersion();
            }

            return $this->VersionCurrentArray;
        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function updateRun2()
        {
            $db_prefix = $this->getSetting("db_prefix", false);

            $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "streak_goal` (  `uid` int(6) NOT NULL,  `fuid` varchar(8) NOT NULL,  `goal` varchar(255) NOT NULL,  `start_date` date NOT NULL,  `end_date` date NOT NULL,  `length` int(3) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "streak_goal`  ADD PRIMARY KEY (`fuid`,`goal`,`start_date`) USING BTREE,  ADD UNIQUE KEY `uid` (`uid`);");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "streak_goal` MODIFY `uid` int(6) NOT NULL AUTO_INCREMENT;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "streak_goal` ADD CONSTRAINT `" . $db_prefix . "streak_goal_ibfk` FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users` (`fuid`) ON DELETE NO ACTION;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "streak_goal` CHANGE `end_date` `end_date` DATE NULL, CHANGE `length` `length` INT(3) NULL");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->setSetting("version", "0.0.0.2", true);

            return true;
        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function updateRun3()
        {
            $db_prefix = $this->getSetting("db_prefix", false);

            $users = $this->getDatabase()->select($db_prefix . "users", "fuid");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            foreach ($users as $user) {
                echo "  " . $user . "\n";
                $steps = $this->getDatabase()->select($db_prefix . "steps", array(
                    "[>]" . $db_prefix . "steps_goals" => array(
                        "user",
                        "date"
                    )
                ), array(
                    $db_prefix . "steps.date",
                    $db_prefix . "steps.steps",
                    $db_prefix . "steps_goals.steps(step_goal)"
                ), array(
                    $db_prefix . "steps.user" => $user,
                    "ORDER"                   => $db_prefix . "steps.date ASC"
                ));
                if ($this->wasMySQLError($this->getDatabase()->error())) {
                    return false;
                }

                $streak          = false;
                $streak_start    = "";
                $streak_prevdate = "";
                foreach ($steps as $step) {
                    echo "   - " . $step['date'] . " " . $step['steps'] . "/" . $step['step_goal'];

                    if ($step['steps'] >= $step['step_goal']) {
                        if (!$streak) {
                            $streak       = true;
                            $streak_start = $step['date'];

                            echo "\tnew streak started";
                        }
                        $streak_prevdate = $step['date'];
                    } else {
                        if ($streak) {
                            $streak     = false;
                            $streak_end = $streak_prevdate;

                            $date1 = new DateTime($streak_end);
                            $date2 = new DateTime($streak_start);

                            $days_between = $date2->diff($date1)->format("%a");
                            $days_between = (int)$days_between + 1;

                            echo "\tnew streak broken. " . $streak_start . " to " . $streak_end . " (" . $days_between . ")";

                            if ($days_between > 1) {
                                if ($this->getDatabase()->has($db_prefix . "streak_goal", array(
                                    "AND" => array(
                                        "fuid"       => $user,
                                        "goal"       => "steps",
                                        "start_date" => $streak_start
                                    )
                                ))
                                ) {
                                    $this->getDatabase()->update($db_prefix . "streak_goal", array(
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
                                    if ($this->wasMySQLError($this->getDatabase()->error())) {
                                        print_r($this->getDatabase()->log());

                                        return false;
                                    }
                                } else {
                                    $this->getDatabase()->insert($db_prefix . "streak_goal", array(
                                        "fuid"       => $user,
                                        "goal"       => "steps",
                                        "start_date" => $streak_start,
                                        "end_date"   => $streak_end,
                                        "length"     => $days_between
                                    ));
                                    if ($this->wasMySQLError($this->getDatabase()->error())) {
                                        print_r($this->getDatabase()->log());

                                        return false;
                                    }
                                }
                            }
                        }
                    }

                    echo "\n";
                }

            }
            $this->setSetting("version", "0.0.0.3", true);

            return true;
        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function update_4()
        {
            $db_prefix = $this->getSetting("db_prefix", false);

            $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "rewards` ( `rid` int(8) NOT NULL, `system` varchar(50) NOT NULL, `reward` longtext NOT NULL, `description` longtext) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "reward_map` ( `rmid` int(6) NOT NULL, `cat` varchar(255) NOT NULL, `event` varchar(255) NOT NULL, `rule` varchar(255) NOT NULL, `name` varchar(255) NOT NULL, `reward` int(6) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "reward_nuke` ( `rid` int(6) NOT NULL, `nukeid` int(6) NOT NULL, `directional` set('true','false') NOT NULL DEFAULT 'false') ENGINE=InnoDB DEFAULT CHARSET=latin1;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "reward_queue` ( `rqid` int(6) NOT NULL, `fuid` varchar(8) CHARACTER SET utf8 NOT NULL, `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `state` varchar(15) NOT NULL, `rmid` int(6) NOT NULL, `reward` int(6) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "rewards` ADD PRIMARY KEY (`rid`), ADD UNIQUE KEY `reward` (`reward`(150),`system`) USING BTREE;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_map` ADD PRIMARY KEY (`rmid`), ADD UNIQUE KEY `cat` (`cat`,`event`,`reward`,`rule`) USING BTREE, ADD KEY `reward` (`reward`);");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_nuke` ADD PRIMARY KEY (`rid`,`nukeid`), ADD KEY `nukeid` (`nukeid`);");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_queue` ADD PRIMARY KEY (`rqid`), ADD KEY `fuid` (`fuid`), ADD KEY `reward` (`reward`), ADD KEY `rmid` (`rmid`);");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "rewards` MODIFY `rid` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_map` MODIFY `rmid` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=468;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_queue` MODIFY `rqid` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1492;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_map` ADD CONSTRAINT `" . $db_prefix . "reward_map_ibfk_1` FOREIGN KEY (`reward`) REFERENCES `" . $db_prefix . "rewards` (`rid`) ON DELETE NO ACTION;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_nuke` ADD CONSTRAINT `" . $db_prefix . "reward_nuke_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `" . $db_prefix . "rewards` (`rid`) ON DELETE NO ACTION, ADD CONSTRAINT `" . $db_prefix . "reward_nuke_ibfk_2` FOREIGN KEY (`nukeid`) REFERENCES `" . $db_prefix . "rewards` (`rid`) ON DELETE NO ACTION;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_queue` ADD CONSTRAINT `" . $db_prefix . "reward_queue_ibfk_1` FOREIGN KEY (`reward`) REFERENCES `" . $db_prefix . "rewards` (`rid`) ON DELETE NO ACTION, ADD CONSTRAINT `" . $db_prefix . "reward_queue_ibfk_2` FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users` (`fuid`) ON DELETE NO ACTION, ADD CONSTRAINT `" . $db_prefix . "reward_queue_ibfk_3` FOREIGN KEY (`rmid`) REFERENCES `" . $db_prefix . "reward_map` (`rmid`) ON DELETE NO ACTION;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->setSetting("version", "0.0.0.4", true);

            return true;

        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function update_5()
        {
            $db_prefix = $this->getSetting("db_prefix", false);

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "steps` ADD `distance_g` BOOLEAN NOT NULL DEFAULT FALSE AFTER `distance`;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "steps` ADD `floors_g` BOOLEAN NOT NULL DEFAULT FALSE AFTER `floors`;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "steps` ADD `steps_g` BOOLEAN NOT NULL DEFAULT FALSE AFTER `steps`;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "steps` CHANGE `distance_g` `distance_g` TINYINT(1) NULL DEFAULT NULL, CHANGE `floors_g` `floors_g` TINYINT(1) NULL DEFAULT NULL, CHANGE `steps_g` `steps_g` TINYINT(1) NULL DEFAULT NULL;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "steps` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->setSetting("version", "0.0.0.5", true);

            return true;

        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function update_6()
        {
            $db_prefix = $this->getSetting("db_prefix", false);

            $users = $this->getDatabase()->select($db_prefix . "users", "fuid");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            foreach ($users as $user) {
                echo "  " . $user . "\n";
                $steps = $this->getDatabase()->select($db_prefix . "steps", array(
                    "[>]" . $db_prefix . "steps_goals" => array(
                        "user",
                        "date"
                    )
                ), array(
                    $db_prefix . "steps.date",
                    $db_prefix . "steps.steps",
                    $db_prefix . "steps_goals.steps(step_goal)",
                    $db_prefix . "steps.distance",
                    $db_prefix . "steps_goals.distance(distance_goal)",
                    $db_prefix . "steps.floors",
                    $db_prefix . "steps_goals.floors(floors_goal)",
                ), array(
                    $db_prefix . "steps.user" => $user,
                    "ORDER"                   => $db_prefix . "steps.date ASC"
                ));
                if ($this->wasMySQLError($this->getDatabase()->error())) {
                    return false;
                }

                foreach ($steps as $step) {
                    echo "   - " . $step['date'];

                    if ($step['steps'] >= $step['step_goal']) {
                        $steps_g = 1;
                    } else {
                        $steps_g = 0;
                    }

                    if ($step['distance'] >= $step['distance_goal']) {
                        $distance_g = 1;
                    } else {
                        $distance_g = 0;
                    }

                    if ($step['floors'] >= $step['floors_goal']) {
                        $floors_g = 1;
                    } else {
                        $floors_g = 0;
                    }

                    $this->getDatabase()->query("UPDATE `" . $db_prefix . "steps` SET `steps_g` = '" . $steps_g . "', `distance_g` = '" . $distance_g . "', `floors_g` = '" . $floors_g . "' WHERE `" . $db_prefix . "steps`.`user` = '" . $user . "' AND `" . $db_prefix . "steps`.`date` = '" . $step['date'] . "'");
                    if ($this->wasMySQLError($this->getDatabase()->error())) {
                        return false;
                    }

                    echo " [DONE]\n";
                }

            }

            $this->setSetting("version", "0.0.0.6", true);

            return true;

        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function update_7()
        {
            $db_prefix = $this->getSetting("db_prefix", false);

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "nomie_trackers` CHANGE `id` `id` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; ");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "nomie_events` CHANGE `id` `id` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; ");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->setSetting("version", "0.0.0.7", true);

            return true;
        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function update_8()
        {
            $db_prefix = $this->getSetting("db_prefix", false);

            $this->getDatabase()->query("SET FOREIGN_KEY_CHECKS=0;DROP TABLE IF EXISTS `" . $db_prefix . "bages`;SET FOREIGN_KEY_CHECKS=1;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("SET FOREIGN_KEY_CHECKS=0;DROP TABLE IF EXISTS `" . $db_prefix . "bages_user`;SET FOREIGN_KEY_CHECKS=1;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "bages` ( `encodedId` varchar(12) NOT NULL, `badgeType` varchar(120) NOT NULL, `value` int(11) NOT NULL, `category` varchar(150) NOT NULL, `description` varchar(255) NOT NULL, `image` varchar(255) NOT NULL, `badgeGradientEndColor` varchar(6) NOT NULL, `badgeGradientStartColor` varchar(6) NOT NULL, `earnedMessage` longtext NOT NULL, `marketingDescription` longtext NOT NULL, `name` varchar(255) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "bages_user` ( `badgeid` varchar(8) NOT NULL, `fuid` varchar(8) NOT NULL, `dateTime` varchar(20) NOT NULL, `timesAchieved` int(11) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "bages` ADD PRIMARY KEY (`encodedId`) USING BTREE;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "bages_user` ADD PRIMARY KEY (`badgeid`,`fuid`), ADD KEY `fuid` (`fuid`);");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "bages_user` ADD CONSTRAINT `" . $db_prefix . "bages_user_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `" . $db_prefix . "bages` (`encodedId`) ON DELETE NO ACTION, ADD CONSTRAINT `" . $db_prefix . "bages_user_ibfk_2` FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users` (`fuid`) ON DELETE NO ACTION;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $users = $this->getDatabase()->select($db_prefix . "users", "fuid");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            echo " Queueing Badges for all users\n";
            foreach ($users as $user) {
                $this->appClass->addCronJob($user, 'badges');
                if (!$this->getDatabase()->has($this->getSetting("db_prefix", null, false) . "queue", array(
                    "AND" => array(
                        "user"    => $user,
                        "trigger" => 'badges'
                    )
                ))
                ) {
                    $this->getDatabase()->insert($this->getSetting("db_prefix", null, false) . "queue", array(
                        "user"    => $user,
                        "trigger" => 'badges',
                        "date"    => date("Y-m-d H:i:s")
                    ));
                    $this->errorRecording->postDatabaseQuery($this->getDatabase(), array(
                        "METHOD" => __METHOD__,
                        "LINE"   => __LINE__
                    ));
                }
            }

            $this->setSetting("version", "0.0.0.8", true);

            return true;
        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function update_9()
        {
            $db_prefix = $this->getSetting("db_prefix", false);

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "nomie_trackers` ADD `type` VARCHAR(20) NULL AFTER `charge`, ADD `math` VARCHAR(20) NULL AFTER `type`,  ADD `uom` VARCHAR(20) NULL AFTER `math`;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "nomie_events` DROP PRIMARY KEY;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "nomie_events` DROP `type`;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "nomie_events` ADD PRIMARY KEY (`fuid`,`id`,`datestamp`);");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->setSetting("version", "0.0.0.9", true);

            return true;
        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function update_10()
        {
            $this->setSetting("version", "0.0.0.10", true);

            return true;
        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function update_12()
        {

            $db_prefix = $this->getSetting("db_prefix", false);
            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "sleep_user` ADD `deep` INT(11) NULL AFTER `totalTimeInBed`, ADD `light` INT(11) NULL AFTER `deep`, ADD `rem` INT(11) NULL AFTER `light`, ADD `wake` INT(11) NULL AFTER `rem`;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->setSetting("version", "0.0.0.12", true);

            return true;
        }

        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function update_13()
        {
            $db_prefix = $this->getSetting("db_prefix", false);

            $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "users_xp` ( `xpid` int(8) NOT NULL, `fuid` varchar(8) NOT NULL, `xp` int(4) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` ADD PRIMARY KEY (`xpid`);");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` MODIFY `xpid` int(8) NOT NULL AUTO_INCREMENT;");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` ADD FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT; ");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $users = $this->getDatabase()->select($db_prefix . "users", "fuid");
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            echo " Queueing Badges for all users\n";
            foreach ($users as $user) {
                $this->getDatabase()->query("INSERT INTO `" . $db_prefix . "users_xp` (`xpid`, `fuid`, `xp`) VALUES (NULL, '" . $user . "', '0'); ");
                if ($this->wasMySQLError($this->getDatabase()->error())) {
                    return false;
                }
            }

            $this->setSetting("version", "0.0.0.13", true);

            return true;
        }

        /**
         * @return config
         */
        public function getSettings()
        {
            return $this->settings;
        }

        /** @noinspection PhpUndefinedClassInspection */
        /**
         * @return medoo
         */
        public function getDatabase()
        {
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
        public function getSetting($key, $default = null, $query_db = true)
        {
            return $this->getSettings()->get($key, $default, $query_db);
        }

        /**
         * Set value in database/config class
         *
         * @param           $key
         * @param           $value
         * @param bool      $query_db
         *
         * @return bool
         */
        public function setSetting($key, $value, $query_db = true)
        {
            return $this->getSettings()->set($key, $value, $query_db);
        }

        public function getInstallingVersion()
        {
            return $this->VersionInstalling;
        }

        public function getInstallVersion()
        {
            if (is_null($this->VersionCurrent)) {
                $this->VersionCurrent = $this->getSetting("version", "0.0.0.1", true);

                $this->VersionCurrentArray = explode(".", $this->VersionCurrent);
            }

            return $this->VersionCurrent;
        }

        public function getUpdatesRequired()
        {
            $currentVersion = $this->getInstallVersionBrakeDown();
            $installVersion = $this->getInstallingVersionBrakeDown();

            $updateFunctions = array();

            $currentNumber = ($currentVersion[0] . $currentVersion[1] . $currentVersion[2] . $currentVersion[3]) * 1;
            $installNumber = ($installVersion[0] . $installVersion[1] . $installVersion[2] . $installVersion[3]) * 1;

            for ($x = ((int)$currentNumber + 1); $x <= $installNumber; $x++) {
                if (method_exists($this, "update_" . $x)) {
                    array_push($updateFunctions, "update_" . $x);
                }
            }

            $this->UpdateFunctions = $updateFunctions;
            $this->NumUpdates      = count($updateFunctions);

            if ($this->NumUpdates == 0 && $currentNumber != $installNumber) {
                nxr("No pending updates, but missmatch version numbers");
                $this->setSetting("version",
                    $installVersion[0] . "." . $installVersion[1] . "." . $installVersion[2] . "." . $installVersion[3],
                    true);
            }

            return $updateFunctions;
        }

        public function getNumUpdates()
        {
            if (is_null($this->NumUpdates)) {
                $this->getUpdatesRequired();
            }

            return $this->NumUpdates;
        }

        public function getUpdates()
        {
            if (is_null($this->UpdateFunctions)) {
                $this->getUpdatesRequired();
            }

            return $this->UpdateFunctions;
        }

        public function getUpdateFunctions()
        {
            if (is_null($this->UpdateFunctions)) {
                $this->getUpdatesRequired();
            }

            return $this->UpdateFunctions;
        }

        public function runUpdates()
        {
            if (is_null($this->UpdateFunctions)) {
                $this->getUpdatesRequired();
            }

            foreach ($this->UpdateFunctions as $updateFunction) {
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

            return true;
        }

    }
