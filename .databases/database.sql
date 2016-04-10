SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `nxfifteen_me_uk` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `nxfifteen_me_uk`;

DROP TABLE IF EXISTS `nx_fitbit_activity`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_activity` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `target` int(11) NOT NULL DEFAULT '0',
  `sedentary` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `lightlyactive` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `fairlyactive` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `veryactive` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `syncd` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_activity_log`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_activity_log` (
  `user` varchar(8) NOT NULL,
  `date` varchar(19) NOT NULL,
  `logId` bigint(20) NOT NULL,
  `activityId` bigint(20) NOT NULL,
  `activityParentId` bigint(20) NOT NULL,
  `activityParentName` varchar(225) NOT NULL,
  `name` varchar(225) NOT NULL,
  `description` longtext,
  `calories` int(4) NOT NULL,
  `steps` int(6) NOT NULL,
  `duration` int(8) NOT NULL,
  `startDate` varchar(10) NOT NULL,
  `startTime` varchar(5) NOT NULL,
  `hasStartTime` int(1) NOT NULL,
  `isFavorite` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_bages`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_bages` (
  `badgeType` varchar(120) NOT NULL COMMENT 'TODO: please describe this field!',
  `value` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `image` varchar(255) NOT NULL,
  `badgeGradientEndColor` varchar(6) NOT NULL,
  `badgeGradientStartColor` varchar(6) NOT NULL,
  `earnedMessage` longtext NOT NULL,
  `marketingDescription` longtext NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_body`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_body` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` date NOT NULL COMMENT 'TODO: please describe this field!',
  `weight` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `weightGoal` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `weightAvg` decimal(5,2) DEFAULT NULL,
  `fat` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `fatGoal` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `fatAvg` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `calf` decimal(5,2) DEFAULT NULL,
  `bicep` decimal(5,2) DEFAULT NULL,
  `chest` decimal(5,2) DEFAULT NULL,
  `forearm` decimal(5,2) DEFAULT NULL,
  `hips` decimal(5,2) DEFAULT NULL,
  `neck` decimal(5,2) DEFAULT NULL,
  `thigh` decimal(5,2) DEFAULT NULL,
  `waist` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_challenge`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_challenge` (
  `user` varchar(8) NOT NULL,
  `startDate` varchar(10) NOT NULL,
  `endDate` varchar(10) NOT NULL,
  `score` float(5,2) NOT NULL,
  `steps` int(5) NOT NULL,
  `distance` float(8,2) NOT NULL,
  `veryactive` int(11) NOT NULL,
  `dayData` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_devices`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_devices` (
  `id` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!',
  `deviceVersion` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `type` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `lastSyncTime` varchar(23) NOT NULL COMMENT 'TODO: please describe this field!',
  `battery` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_goals_calories`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_goals_calories` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `calories` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `intensity` varchar(12) NOT NULL COMMENT 'TODO: please describe this field!',
  `estimatedDate` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `personalized` varchar(5) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_heartAverage`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_heartAverage` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!',
  `resting` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `normal` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `exertive` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_journeys`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_journeys` (
  `jid` int(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `blurb` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_journeys_legs`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_journeys_legs` (
  `jid` int(8) NOT NULL,
  `lid` int(8) NOT NULL,
  `name` longtext NOT NULL,
  `start_mile` decimal(6,2) NOT NULL,
  `end_mile` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_journeys_narrative`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_journeys_narrative` (
  `lid` int(8) NOT NULL COMMENT 'Leg ID',
  `nid` int(8) NOT NULL COMMENT 'Narrative ID',
  `miles` decimal(6,2) NOT NULL COMMENT 'Cumulative Miles for Leg',
  `subtitle` varchar(255) NOT NULL,
  `narrative` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_journeys_travellers`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_journeys_travellers` (
  `jid` int(8) NOT NULL,
  `fuid` varchar(8) NOT NULL,
  `start_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_keypoints`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_keypoints` (
  `category` enum('distance','floors','elevation') NOT NULL,
  `value` float(22,2) NOT NULL,
  `less` varchar(255) NOT NULL,
  `more` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_lnk_badge2usr`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_badge2usr` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `badgeType` varchar(120) NOT NULL COMMENT 'TODO: please describe this field!',
  `dateTime` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!',
  `timesAchieved` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `value` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `unit` varchar(50) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_lnk_dev2usr`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_dev2usr` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `device` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_lnk_sleep2usr`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_sleep2usr` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `sleeplog` bigint(20) NOT NULL,
  `totalMinutesAsleep` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `totalSleepRecords` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `totalTimeInBed` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_logFood`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_logFood` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `meal` varchar(30) NOT NULL COMMENT 'TODO: please describe this field!',
  `calories` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `carbs` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `fat` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `fiber` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `protein` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `sodium` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_logSleep`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_logSleep` (
  `logId` bigint(20) NOT NULL,
  `awakeningsCount` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `duration` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `efficiency` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `isMainSleep` varchar(5) NOT NULL COMMENT 'TODO: please describe this field!',
  `minutesAfterWakeup` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `minutesAsleep` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `minutesAwake` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `minutesToFallAsleep` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `startTime` varchar(25) NOT NULL COMMENT 'TODO: please describe this field!',
  `timeInBed` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `minuteData` longtext COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_queue`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_queue` (
  `user` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!',
  `trigger` varchar(30) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_runlog`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_runlog` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!',
  `activity` varchar(30) NOT NULL COMMENT 'TODO: please describe this field!',
  `cooldown` varchar(20) NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'TODO: please describe this field!',
  `lastrun` varchar(20) NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_settings`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_settings` (
  `var` varchar(255) NOT NULL,
  `data` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_steps`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_steps` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `distance` decimal(21,16) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `floors` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `elevation` decimal(9,5) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(10) NOT NULL DEFAULT '' COMMENT 'TODO: please describe this field!',
  `steps` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `caloriesOut` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `syncd` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_steps_goals`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_steps_goals` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `distance` decimal(21,16) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `floors` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `activeMinutes` decimal(9,5) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `steps` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `caloriesOut` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `syncd` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_units`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_units` (
  `user` varchar(8) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unit` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  `note` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_users`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_users` (
  `uid` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `fuid` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `password` varchar(64) NOT NULL,
  `api` varchar(64) DEFAULT NULL,
  `group` set('user','admin') NOT NULL DEFAULT 'user',
  `lastrun` varchar(20) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `name` varchar(255) NOT NULL COMMENT 'TODO: please describe this field!',
  `rank` int(11) NOT NULL DEFAULT '-1' COMMENT 'TODO: please describe this field!',
  `friends` int(11) NOT NULL DEFAULT '-1' COMMENT 'TODO: please describe this field!',
  `distance` int(11) NOT NULL DEFAULT '-1' COMMENT 'TODO: please describe this field!',
  `avatar` varchar(255) NOT NULL COMMENT 'TODO: please describe this field!',
  `seen` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `gender` varchar(6) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `cooldown` varchar(20) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `height` decimal(6,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `stride_running` decimal(20,14) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `stride_walking` decimal(20,14) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `city` varchar(25) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `country` varchar(3) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `tkn_access` varchar(70) NOT NULL COMMENT 'TODO: please describe this field!',
  `tkn_refresh` varchar(65) NOT NULL COMMENT 'TODO: please describe this field!',
  `tkn_expires` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

DROP TABLE IF EXISTS `nx_fitbit_users_auth`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_users_auth` (
  `ID` int(7) unsigned NOT NULL,
  `Username` varchar(15) NOT NULL,
  `Password` varchar(40) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Activated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Confirmation` char(40) NOT NULL DEFAULT '',
  `RegDate` int(11) unsigned NOT NULL,
  `LastLogin` int(11) unsigned NOT NULL DEFAULT '0',
  `GroupID` int(2) unsigned NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_users_settings`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_users_settings` (
  `fuid` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT 'The name of the variable.',
  `value` longtext NOT NULL COMMENT 'The value of the variable.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Named variable/value pairs created.';

DROP TABLE IF EXISTS `nx_fitbit_water`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_water` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `id` bigint(20) NOT NULL,
  `liquid` decimal(18,12) DEFAULT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';


ALTER TABLE `nx_fitbit_activity`
ADD PRIMARY KEY (`user`,`date`);

ALTER TABLE `nx_fitbit_activity_log`
ADD PRIMARY KEY (`user`,`logId`,`activityId`,`startDate`,`startTime`);

ALTER TABLE `nx_fitbit_bages`
ADD PRIMARY KEY (`badgeType`,`value`);

ALTER TABLE `nx_fitbit_body`
ADD PRIMARY KEY (`user`,`date`);

ALTER TABLE `nx_fitbit_challenge`
ADD PRIMARY KEY (`user`,`startDate`,`endDate`);

ALTER TABLE `nx_fitbit_devices`
ADD PRIMARY KEY (`id`);

ALTER TABLE `nx_fitbit_goals_calories`
ADD PRIMARY KEY (`user`,`date`);

ALTER TABLE `nx_fitbit_heartAverage`
ADD PRIMARY KEY (`user`,`date`);

ALTER TABLE `nx_fitbit_journeys`
ADD PRIMARY KEY (`jid`);

ALTER TABLE `nx_fitbit_journeys_legs`
ADD PRIMARY KEY (`lid`),
ADD UNIQUE KEY `lid` (`jid`,`lid`);

ALTER TABLE `nx_fitbit_journeys_narrative`
ADD PRIMARY KEY (`nid`),
ADD UNIQUE KEY `nid` (`lid`,`nid`),
ADD UNIQUE KEY `miles` (`lid`,`nid`,`miles`);

ALTER TABLE `nx_fitbit_journeys_travellers`
ADD PRIMARY KEY (`jid`,`fuid`),
ADD KEY `fuid` (`fuid`);

ALTER TABLE `nx_fitbit_keypoints`
ADD PRIMARY KEY (`category`,`value`);

ALTER TABLE `nx_fitbit_lnk_badge2usr`
ADD PRIMARY KEY (`user`,`badgeType`,`value`);

ALTER TABLE `nx_fitbit_lnk_dev2usr`
ADD UNIQUE KEY `user` (`user`,`device`);

ALTER TABLE `nx_fitbit_lnk_sleep2usr`
ADD PRIMARY KEY (`user`,`sleeplog`),
ADD KEY `sleeplog` (`sleeplog`);

ALTER TABLE `nx_fitbit_logFood`
ADD PRIMARY KEY (`user`,`date`,`meal`);

ALTER TABLE `nx_fitbit_logSleep`
ADD PRIMARY KEY (`logId`);

ALTER TABLE `nx_fitbit_queue`
ADD PRIMARY KEY (`user`,`trigger`);

ALTER TABLE `nx_fitbit_runlog`
ADD PRIMARY KEY (`user`,`activity`);

ALTER TABLE `nx_fitbit_settings`
ADD UNIQUE KEY `var` (`var`);

ALTER TABLE `nx_fitbit_steps`
ADD PRIMARY KEY (`user`,`date`),
ADD UNIQUE KEY `distance` (`user`,`date`,`distance`),
ADD UNIQUE KEY `elevation` (`user`,`date`,`elevation`),
ADD UNIQUE KEY `floors` (`user`,`date`,`floors`),
ADD UNIQUE KEY `steps` (`user`,`date`,`steps`);

ALTER TABLE `nx_fitbit_steps_goals`
ADD PRIMARY KEY (`user`,`date`),
ADD UNIQUE KEY `distance` (`user`,`date`,`distance`),
ADD UNIQUE KEY `elevation` (`user`,`date`,`activeMinutes`),
ADD UNIQUE KEY `floors` (`user`,`date`,`floors`),
ADD UNIQUE KEY `steps` (`user`,`date`,`steps`);

ALTER TABLE `nx_fitbit_units`
ADD PRIMARY KEY (`user`,`date`,`unit`(10));

ALTER TABLE `nx_fitbit_users`
ADD PRIMARY KEY (`fuid`),
ADD UNIQUE KEY `drupalid` (`uid`);

ALTER TABLE `nx_fitbit_users_auth`
ADD PRIMARY KEY (`ID`);

ALTER TABLE `nx_fitbit_users_settings`
ADD PRIMARY KEY (`fuid`,`name`);

ALTER TABLE `nx_fitbit_water`
ADD PRIMARY KEY (`user`,`date`);


ALTER TABLE `nx_fitbit_journeys`
MODIFY `jid` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `nx_fitbit_journeys_legs`
MODIFY `lid` int(8) NOT NULL AUTO_INCREMENT;
ALTER TABLE `nx_fitbit_journeys_narrative`
MODIFY `nid` int(8) NOT NULL AUTO_INCREMENT COMMENT 'Narrative ID';
ALTER TABLE `nx_fitbit_users`
MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'TODO: please describe this field!';
ALTER TABLE `nx_fitbit_users_auth`
MODIFY `ID` int(7) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `nx_fitbit_activity`
ADD CONSTRAINT `nx_fitbit_activity_ibfk_1` FOREIGN KEY (`user`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_body`
ADD CONSTRAINT `nx_fitbit_body_ibfk_1` FOREIGN KEY (`user`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_journeys_legs`
ADD CONSTRAINT `nx_fitbit_journeys_legs_ibfk_1` FOREIGN KEY (`jid`) REFERENCES `nx_fitbit_journeys` (`jid`);

ALTER TABLE `nx_fitbit_journeys_narrative`
ADD CONSTRAINT `nx_fitbit_journeys_narrative_ibfk_1` FOREIGN KEY (`lid`) REFERENCES `nx_fitbit_journeys_legs` (`lid`);

ALTER TABLE `nx_fitbit_journeys_travellers`
ADD CONSTRAINT `nx_fitbit_journeys_travellers_ibfk_1` FOREIGN KEY (`jid`) REFERENCES `nx_fitbit_journeys` (`jid`),
ADD CONSTRAINT `nx_fitbit_journeys_travellers_ibfk_2` FOREIGN KEY (`fuid`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_queue`
ADD CONSTRAINT `nx_fitbit_queue_ibfk_1` FOREIGN KEY (`user`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_runlog`
ADD CONSTRAINT `nx_fitbit_runlog_ibfk_1` FOREIGN KEY (`user`) REFERENCES `nx_fitbit_users` (`fuid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `nx_fitbit_users_settings`
ADD CONSTRAINT `nx_fitbit_users_settings_ibfk_1` FOREIGN KEY (`fuid`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_water`
ADD CONSTRAINT `nx_fitbit_water_ibfk_1` FOREIGN KEY (`user`) REFERENCES `nx_fitbit_users` (`fuid`);
