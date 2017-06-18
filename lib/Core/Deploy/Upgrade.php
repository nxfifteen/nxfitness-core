<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

namespace Core\Deploy;

require_once(dirname(__FILE__) . "/../../autoloader.php");

use Core\Analytics\ErrorRecording;
use Core\Babel\ApiBabel;
use Core\Config;
use Core\Core;
use DateTime;
use Medoo\Medoo;

require_once(dirname(__FILE__) . "/../../../config/config.def.dist.php");

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

    /**
     * @var Medoo
     */
    protected $database;

    /**
     * @var ApiBabel
     */
    protected $apiBabel;

    /**
     * @var Config
     */
    protected $settings;
    /**
     * @var ErrorRecording
     */
    protected $errorRecording;

    /**
     * Upgrade constructor.
     *
     * @param Core $appClass
     *
     * @todo     Consider test case
     * @internal param $userFid
     */
    public function __construct($appClass = null)
    {
        $this->setSettings(new Config());

        if (is_null($appClass)) {
            $this->appClass = new Core();
        } else {
            $this->appClass = $appClass;
        }
        $this->errorRecording = new ErrorRecording($this->appClass);

        $this->setDatabase($this->appClass->getDatabase());

        $this->getSettings()->setDatabase($this->getDatabase());

    }

    /**
     * @param Config $settings
     */
    private function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param Medoo $database
     */
    private function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @todo Consider test case
     * @return Config
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @todo Consider test case
     * @return Medoo
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @todo Consider test case
     * @return String
     */
    public function getInstallingVersion()
    {
        return $this->VersionInstalling;
    }

    /**
     * @todo Consider test case
     * @return int
     */
    public function getNumUpdates()
    {
        if (is_null($this->NumUpdates)) {
            $this->getUpdatesRequired();
        }

        return $this->NumUpdates;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function getUpdatesRequired()
    {
        $currentVersion = $this->getInstallVersionBrakeDown();
        $installVersion = $this->getInstallingVersionBrakeDown();

        $updateFunctions = [];

        $currentNumber = (($currentVersion[0] * 1000) . ($currentVersion[1] * 100) . ($currentVersion[2] * 10) . $currentVersion[3]) * 1;
        $installNumber = (($installVersion[0] * 1000) . ($installVersion[1] * 100) . ($installVersion[2] * 10) . $installVersion[3]) * 1;

        for ($x = ((int)$currentNumber + 1); $x <= $installNumber; $x++) {
            if (method_exists($this, "updateRun" . $x)) {
                array_push($updateFunctions, "updateRun" . $x);
            }
        }

        $this->UpdateFunctions = $updateFunctions;
        $this->NumUpdates = count($updateFunctions);

        if ($this->NumUpdates == 0 && $currentNumber != $installNumber) {
            nxr(0, "No pending updates, but missmatch version numbers");
            $this->setSetting("version",
                $installVersion[0] . "." . $installVersion[1] . "." . $installVersion[2] . "." . $installVersion[3],
                true);
        }

        return $updateFunctions;
    }

    /**
     * @return array
     */
    private function getInstallVersionBrakeDown()
    {
        if (is_null($this->VersionCurrent)) {
            $this->getInstallVersion();
        }

        return $this->VersionCurrentArray;
    }

    /**
     * @todo Consider test case
     * @return String
     */
    public function getInstallVersion()
    {
        if (is_null($this->VersionCurrent)) {
            $this->VersionCurrent = $this->getSetting("version", "0.0.0.1", true);

            $this->VersionCurrentArray = explode(".", $this->VersionCurrent);
        }

        return $this->VersionCurrent;
    }

    /**
     * Get settings from config class
     *
     * @param string $key
     * @param null $default
     * @param bool $query_db
     *
     * @todo Consider test case
     * @return string
     */
    public function getSetting($key, $default = null, $query_db = true)
    {
        return $this->getSettings()->get($key, $default, $query_db);
    }

    /**
     * @return array
     */
    private function getInstallingVersionBrakeDown()
    {
        return explode(".", $this->VersionInstalling);
    }

    /**
     * Set value in database/config class
     *
     * @param string $key
     * @param string $value
     * @param bool $query_db
     *
     * @todo Consider test case
     * @return bool
     */
    public function setSetting($key, $value, $query_db = true)
    {
        return $this->getSettings()->set($key, $value, $query_db);
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function getUpdates()
    {
        if (is_null($this->UpdateFunctions)) {
            $this->getUpdatesRequired();
        }

        return $this->UpdateFunctions;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function getUpdateFunctions()
    {
        if (is_null($this->UpdateFunctions)) {
            $this->getUpdatesRequired();
        }

        return $this->UpdateFunctions;
    }

    /**
     * @todo Consider test case
     * @return bool
     */
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

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function updateRun2()
    {
        $db_prefix = $this->getSetting("db_prefix", false);

        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "streak_goal` (  `uid` INT(6) NOT NULL,  `fuid` VARCHAR(8) NOT NULL,  `goal` VARCHAR(255) NOT NULL,  `start_date` DATE NOT NULL,  `end_date` DATE NOT NULL,  `length` INT(3) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "streak_goal`  ADD PRIMARY KEY (`fuid`,`goal`,`start_date`) USING BTREE,  ADD UNIQUE KEY `uid` (`uid`);");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "streak_goal` MODIFY `uid` INT(6) NOT NULL AUTO_INCREMENT;");
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

    /**
     * @param array $error
     *
     * @return bool
     */
    private function wasMySQLError($error)
    {
        $this->errorRecording->postDatabaseQuery($this->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        if (is_null($error[2])) {
            return false;
        } else {
            print_r($error);

            return true;
        }
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
            $steps = $this->getDatabase()->select($db_prefix . "steps", [
                "[>]" . $db_prefix . "steps_goals" => [
                    "user",
                    "date"
                ]
            ], [
                $db_prefix . "steps.date",
                $db_prefix . "steps.steps",
                $db_prefix . "steps_goals.steps(step_goal)"
            ], [
                $db_prefix . "steps.user" => $user,
                "ORDER" => $db_prefix . "steps.date ASC"
            ]);
            if ($this->wasMySQLError($this->getDatabase()->error())) {
                return false;
            }

            $streak = false;
            $streak_start = "";
            $streak_prevdate = "";
            foreach ($steps as $step) {
                echo "   - " . $step['date'] . " " . $step['steps'] . "/" . $step['step_goal'];

                if ($step['steps'] >= $step['step_goal']) {
                    if (!$streak) {
                        $streak = true;
                        $streak_start = $step['date'];

                        echo "\tnew streak started";
                    }
                    $streak_prevdate = $step['date'];
                } else {
                    if ($streak) {
                        $streak = false;
                        $streak_end = $streak_prevdate;

                        $date1 = new DateTime($streak_end);
                        $date2 = new DateTime($streak_start);

                        $days_between = $date2->diff($date1)->format("%a");
                        $days_between = (int)$days_between + 1;

                        echo "\tnew streak broken. " . $streak_start . " to " . $streak_end . " (" . $days_between . ")";

                        if ($days_between > 1) {
                            if ($this->getDatabase()->has($db_prefix . "streak_goal", [
                                "AND" => [
                                    "fuid" => $user,
                                    "goal" => "steps",
                                    "start_date" => $streak_start
                                ]
                            ])
                            ) {
                                $this->getDatabase()->update($db_prefix . "streak_goal", [
                                    "end_date" => $streak_end,
                                    "length" => $days_between
                                ],
                                    [
                                        "AND" => [
                                            "fuid" => $user,
                                            "goal" => "steps",
                                            "start_date" => $streak_start
                                        ]
                                    ]
                                );
                                if ($this->wasMySQLError($this->getDatabase()->error())) {
                                    print_r($this->getDatabase()->log());

                                    return false;
                                }
                            } else {
                                $this->getDatabase()->insert($db_prefix . "streak_goal", [
                                    "fuid" => $user,
                                    "goal" => "steps",
                                    "start_date" => $streak_start,
                                    "end_date" => $streak_end,
                                    "length" => $days_between
                                ]);
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
    private function updateRun4()
    {
        $db_prefix = $this->getSetting("db_prefix", false);

        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "rewards` ( `rid` INT(8) NOT NULL, `system` VARCHAR(50) NOT NULL, `reward` LONGTEXT NOT NULL, `description` LONGTEXT) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "reward_map` ( `rmid` INT(6) NOT NULL, `cat` VARCHAR(255) NOT NULL, `event` VARCHAR(255) NOT NULL, `rule` VARCHAR(255) NOT NULL, `name` VARCHAR(255) NOT NULL, `reward` INT(6) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "reward_nuke` ( `rid` INT(6) NOT NULL, `nukeid` INT(6) NOT NULL, `directional` SET('true','false') NOT NULL DEFAULT 'false') ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "reward_queue` ( `rqid` INT(6) NOT NULL, `fuid` VARCHAR(8) CHARACTER SET utf8 NOT NULL, `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `state` VARCHAR(15) NOT NULL, `rmid` INT(6) NOT NULL, `reward` INT(6) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
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

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "rewards` MODIFY `rid` INT(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_map` MODIFY `rmid` INT(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=468;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_queue` MODIFY `rqid` INT(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1492;");
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
    private function updateRun5()
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
    private function updateRun6()
    {
        $db_prefix = $this->getSetting("db_prefix", false);

        $users = $this->getDatabase()->select($db_prefix . "users", "fuid");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        foreach ($users as $user) {
            echo "  " . $user . "\n";
            $steps = $this->getDatabase()->select($db_prefix . "steps", [
                "[>]" . $db_prefix . "steps_goals" => [
                    "user",
                    "date"
                ]
            ], [
                $db_prefix . "steps.date",
                $db_prefix . "steps.steps",
                $db_prefix . "steps_goals.steps(step_goal)",
                $db_prefix . "steps.distance",
                $db_prefix . "steps_goals.distance(distance_goal)",
                $db_prefix . "steps.floors",
                $db_prefix . "steps_goals.floors(floors_goal)",
            ], [
                $db_prefix . "steps.user" => $user,
                "ORDER" => $db_prefix . "steps.date ASC"
            ]);
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
    private function updateRun7()
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
    private function updateRun8()
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

        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "bages` ( `encodedId` VARCHAR(12) NOT NULL, `badgeType` VARCHAR(120) NOT NULL, `value` INT(11) NOT NULL, `category` VARCHAR(150) NOT NULL, `description` VARCHAR(255) NOT NULL, `image` VARCHAR(255) NOT NULL, `badgeGradientEndColor` VARCHAR(6) NOT NULL, `badgeGradientStartColor` VARCHAR(6) NOT NULL, `earnedMessage` LONGTEXT NOT NULL, `marketingDescription` LONGTEXT NOT NULL, `name` VARCHAR(255) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "bages_user` ( `badgeid` VARCHAR(8) NOT NULL, `fuid` VARCHAR(8) NOT NULL, `dateTime` VARCHAR(20) NOT NULL, `timesAchieved` INT(11) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
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
            if (!$this->getDatabase()->has($this->getSetting("db_prefix", null, false) . "queue", [
                "AND" => [
                    "user" => $user,
                    "trigger" => 'badges'
                ]
            ])
            ) {
                $this->getDatabase()->insert($this->getSetting("db_prefix", null, false) . "queue", [
                    "user" => $user,
                    "trigger" => 'badges',
                    "date" => date("Y-m-d H:i:s")
                ]);
                $this->errorRecording->postDatabaseQuery($this->getDatabase(), [
                    "METHOD" => __METHOD__,
                    "LINE" => __LINE__
                ]);
            }
        }

        $this->setSetting("version", "0.0.0.8", true);

        return true;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function updateRun9()
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
    private function updateRun10()
    {
        $this->setSetting("version", "0.0.0.10", true);

        return true;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function updateRun12()
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
    private function updateRun13()
    {
        $db_prefix = $this->getSetting("db_prefix", false);

        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "users_xp` ( `xpid` INT(8) NOT NULL, `fuid` VARCHAR(8) NOT NULL, `xp` INT(4) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` ADD PRIMARY KEY (`xpid`);");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` MODIFY `xpid` INT(8) NOT NULL AUTO_INCREMENT;");
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

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function updateRun107()
    {

        $db_prefix = $this->getSetting("db_prefix", false);
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_queue` ADD `rkey` VARCHAR(40) NULL DEFAULT NULL AFTER `state`;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_map` ADD `xp` INT(4) NOT NULL DEFAULT '0' AFTER `reward`;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_queue` CHANGE `reward` `reward` INT(6) NULL;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->setSetting("version", "0.0.1.7", true);

        return true;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function updateRun108()
    {

        $db_prefix = $this->getSetting("db_prefix", false);
        $this->getDatabase()->query("UPDATE `" . $db_prefix . "reward_map` SET `event` = 'tick' WHERE `event` = 'logged' AND `cat` = 'nomie';");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("UPDATE `" . $db_prefix . "reward_map` SET `cat` = 'Nomie' WHERE `cat` = 'nomie';");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("UPDATE `" . $db_prefix . "reward_map` SET `event` = 'Scored' WHERE `event` = 'score';");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_queue` CHANGE `rmid` `rmid` INT(6) NULL;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->setSetting("version", "0.0.1.8", true);

        return true;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function updateRun109()
    {
        $db_prefix = $this->getSetting("db_prefix", false);

        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "inbox` (`iid` INT(6) NOT NULL,`fuid` VARCHAR(8) NOT NULL,`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,`expires` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',`ico` VARCHAR(255) NOT NULL,`icoColour` VARCHAR(255) NOT NULL,`subject` LONGTEXT NOT NULL,`body` LONGTEXT NOT NULL,`bold` VARCHAR(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "inbox` CHANGE `iid` `iid` INT(6) NOT NULL AUTO_INCREMENT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "minecraft` (`mcrid` INT(6) NOT NULL, `username` VARCHAR(255) NOT NULL, `delivery` VARCHAR(255) NOT NULL, `command` LONGTEXT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "minecraft` ADD PRIMARY KEY (`mcrid`);");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "minecraft` MODIFY `mcrid` INT(6) NOT NULL AUTO_INCREMENT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->setSetting("version", "0.0.1.9", true);

        return true;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function updateRun1010()
    {
        $db_prefix = $this->getSetting("db_prefix", false);

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` CHANGE `xp` `xp` INT(8) NOT NULL;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` ADD `mana` INT(8) NOT NULL DEFAULT '0' AFTER `xp`, ADD `health` INT(8) NOT NULL DEFAULT '100' AFTER `mana`;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` ADD `class` VARCHAR(255) NOT NULL DEFAULT 'Rebel' AFTER `fuid`;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("CREATE TABLE `" . $db_prefix . "blancing` (`bid` INT(8) NOT NULL,`class` VARCHAR(255) NOT NULL, `skill` VARCHAR(255) NOT NULL,`xp` FLOAT(6,3) NOT NULL DEFAULT '0.000',`mana` FLOAT(6,3) NOT NULL DEFAULT '0.000',`health` FLOAT(6,3) NOT NULL DEFAULT '0.000') ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "blancing` ADD PRIMARY KEY (`bid`);");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "blancing` MODIFY `bid` INT(8) NOT NULL AUTO_INCREMENT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->setSetting("version", "0.0.1.10", true);

        return true;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function updateRun1011()
    {
        $db_prefix = $this->getSetting("db_prefix", false);

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` ADD `level` INT(3) NOT NULL DEFAULT '0' AFTER `xp`, ADD `percent` INT(3) NOT NULL DEFAULT '0' AFTER `level`;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->setSetting("version", "0.0.1.11", true);

        return true;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function updateRun1012()
    {
        $db_prefix = $this->getSetting("db_prefix", false);

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` ADD `gold` FLOAT(7,2) NOT NULL DEFAULT '0' AFTER `health`;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->setSetting("version", "0.0.1.12", true);
        return true;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function updateRun1013()
    {
        $db_prefix = $this->getSetting("db_prefix", false);

        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "activity` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "activity_log` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "bages_user` ADD FOREIGN KEY (`badgeid`) REFERENCES `" . $db_prefix . "bages`(`encodedId`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "bages_user` ADD FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "body` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "devices_charges` ADD FOREIGN KEY (`id`) REFERENCES `" . $db_prefix . "devices`(`id`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "devices_user` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "devices_user` ADD FOREIGN KEY (`device`) REFERENCES `" . $db_prefix . "devices`(`id`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "food` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "food_goals` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "heartAverage` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "heart_activity` CHANGE `user` `user` VARCHAR(8) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "heart_activity` CHANGE `json` `json` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "heart_activity` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "inbox` ADD FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "journeys_legs` ADD FOREIGN KEY (`jid`) REFERENCES `" . $db_prefix . "journeys`(`jid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "journeys_narrative` ADD FOREIGN KEY (`lid`) REFERENCES `" . $db_prefix . "journeys_legs`(`lid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "journeys_travellers` ADD FOREIGN KEY (`jid`) REFERENCES `" . $db_prefix . "journeys`(`jid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "journeys_travellers` ADD FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "nomie_events` CHANGE `fuid` `fuid` VARCHAR(8) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `value` `value` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "nomie_trackers` CHANGE `fuid` `fuid` VARCHAR(8) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `label` `label` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `icon` `icon` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `color` `color` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `type` `type` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `math` `math` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `uom` `uom` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "nomie_events` ADD FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "nomie_trackers` ADD FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "push` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "queue` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_map` ADD FOREIGN KEY (`reward`) REFERENCES `" . $db_prefix . "rewards`(`rid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_nuke` ADD FOREIGN KEY (`rid`) REFERENCES `" . $db_prefix . "rewards`(`rid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_nuke` ADD FOREIGN KEY (`nukeid`) REFERENCES `" . $db_prefix . "rewards`(`rid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_queue` ADD FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "reward_queue` ADD FOREIGN KEY (`reward`) REFERENCES `" . $db_prefix . "rewards`(`rid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "runlog` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "settings_users` ADD FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "sleep_user` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "sleep_user` ADD FOREIGN KEY (`sleeplog`) REFERENCES `" . $db_prefix . "sleep`(`logId`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "steps` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "steps_goals` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "streak_goal` ADD FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "units` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "users_xp` ADD FOREIGN KEY (`fuid`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }
        $this->getDatabase()->query("ALTER TABLE `" . $db_prefix . "water` ADD FOREIGN KEY (`user`) REFERENCES `" . $db_prefix . "users`(`fuid`) ON DELETE NO ACTION ON UPDATE RESTRICT;");
        if ($this->wasMySQLError($this->getDatabase()->error())) {
            return false;
        }

        $this->setSetting("version", "0.0.1.13", true);
        return true;
    }

}
