SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `nx_fitbit_activity`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_activity` (
  `user` varchar(8) NOT NULL,
  `date` varchar(10) NOT NULL,
  `target` int(11) NOT NULL DEFAULT '0',
  `sedentary` int(11) DEFAULT NULL,
  `lightlyactive` int(11) DEFAULT NULL,
  `fairlyactive` int(11) DEFAULT NULL,
  `veryactive` int(11) DEFAULT NULL,
  `syncd` varchar(20) NOT NULL,
  PRIMARY KEY (`user`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_activity_log`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_activity_log` (
  `user` varchar(8) NOT NULL,
  `date` varchar(19) NOT NULL,
  `logId` int(11) NOT NULL,
  `activityId` int(11) NOT NULL,
  `activityParentId` int(11) NOT NULL,
  `activityParentName` varchar(225) NOT NULL,
  `name` varchar(225) NOT NULL,
  `description` longtext,
  `calories` int(4) NOT NULL,
  `steps` int(6) NOT NULL,
  `duration` int(8) NOT NULL,
  `startDate` varchar(10) NOT NULL,
  `startTime` varchar(5) NOT NULL,
  `hasStartTime` int(1) NOT NULL,
  `isFavorite` int(1) NOT NULL,
  PRIMARY KEY (`user`,`logId`,`activityId`,`startDate`,`startTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_bages`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_bages` (
  `badgeType` varchar(120) NOT NULL,
  `value` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `badgeGradientEndColor` varchar(6) NOT NULL,
  `badgeGradientStartColor` varchar(6) NOT NULL,
  `earnedMessage` longtext NOT NULL,
  `marketingDescription` longtext NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`badgeType`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_body`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_body` (
  `user` varchar(8) NOT NULL,
  `date` date NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `weightGoal` decimal(5,2) DEFAULT NULL,
  `weightAvg` decimal(5,2) DEFAULT NULL,
  `fat` decimal(5,2) DEFAULT NULL,
  `fatGoal` decimal(5,2) DEFAULT NULL,
  `fatAvg` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(5,2) DEFAULT NULL,
  `calf` decimal(5,2) DEFAULT NULL,
  `bicep` decimal(5,2) DEFAULT NULL,
  `chest` decimal(5,2) DEFAULT NULL,
  `forearm` decimal(5,2) DEFAULT NULL,
  `hips` decimal(5,2) DEFAULT NULL,
  `neck` decimal(5,2) DEFAULT NULL,
  `thigh` decimal(5,2) DEFAULT NULL,
  `waist` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`user`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_challenge`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_challenge` (
  `user` varchar(8) NOT NULL,
  `startDate` varchar(10) NOT NULL,
  `endDate` varchar(10) NOT NULL,
  `score` float(5,2) NOT NULL,
  `steps` int(5) NOT NULL,
  `distance` float(8,2) NOT NULL,
  `veryactive` int(11) NOT NULL,
  `dayData` longtext NOT NULL,
  PRIMARY KEY (`user`,`startDate`,`endDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_devices`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_devices` (
  `id` varchar(20) NOT NULL,
  `deviceVersion` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `lastSyncTime` varchar(23) NOT NULL,
  `battery` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_goals_calories`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_goals_calories` (
  `user` varchar(8) NOT NULL,
  `date` varchar(10) NOT NULL,
  `calories` int(11) NOT NULL,
  `intensity` varchar(12) NOT NULL,
  `estimatedDate` varchar(10) NOT NULL,
  `personalized` varchar(5) NOT NULL,
  PRIMARY KEY (`user`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_heartAverage`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_heartAverage` (
  `user` varchar(8) NOT NULL,
  `date` varchar(20) NOT NULL,
  `resting` decimal(5,2) DEFAULT NULL,
  `normal` decimal(5,2) DEFAULT NULL,
  `exertive` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`user`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_journeys`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_journeys` (
  `jid` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `blurb` longtext NOT NULL,
  PRIMARY KEY (`jid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

DROP TABLE IF EXISTS `nx_fitbit_journeys_legs`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_journeys_legs` (
  `jid` int(8) NOT NULL,
  `lid` int(8) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `start_mile` decimal(6,2) NOT NULL,
  `end_mile` decimal(6,2) NOT NULL,
  PRIMARY KEY (`lid`),
  UNIQUE KEY `lid` (`jid`,`lid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

DROP TABLE IF EXISTS `nx_fitbit_journeys_narrative`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_journeys_narrative` (
  `lid` int(8) NOT NULL COMMENT 'Leg ID',
  `nid` int(8) NOT NULL AUTO_INCREMENT COMMENT 'Narrative ID',
  `miles` decimal(6,2) NOT NULL COMMENT 'Cumulative Miles for Leg',
  `subtitle` varchar(255) NOT NULL,
  `narrative` longtext NOT NULL,
  PRIMARY KEY (`nid`),
  UNIQUE KEY `nid` (`lid`,`nid`),
  UNIQUE KEY `miles` (`lid`,`nid`,`miles`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1556 ;

DROP TABLE IF EXISTS `nx_fitbit_journeys_travellers`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_journeys_travellers` (
  `jid` int(8) NOT NULL,
  `fuid` varchar(8) NOT NULL,
  `start_date` date NOT NULL,
  PRIMARY KEY (`jid`,`fuid`),
  KEY `fuid` (`fuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_keypoints`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_keypoints` (
  `category` enum('distance','floors','elevation') NOT NULL,
  `value` float(22,2) NOT NULL,
  `less` varchar(255) NOT NULL,
  `more` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`category`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_lnk_badge2usr`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_badge2usr` (
  `user` varchar(8) NOT NULL,
  `badgeType` varchar(120) NOT NULL,
  `dateTime` varchar(20) NOT NULL,
  `timesAchieved` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  PRIMARY KEY (`user`,`badgeType`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_lnk_dev2usr`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_dev2usr` (
  `user` varchar(8) NOT NULL,
  `device` varchar(20) NOT NULL,
  UNIQUE KEY `user` (`user`,`device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_lnk_sleep2usr`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_sleep2usr` (
  `user` varchar(8) NOT NULL,
  `sleeplog` int(11) NOT NULL,
  `totalMinutesAsleep` int(11) DEFAULT NULL,
  `totalSleepRecords` int(11) DEFAULT NULL,
  `totalTimeInBed` int(11) DEFAULT NULL,
  PRIMARY KEY (`user`,`sleeplog`),
  KEY `sleeplog` (`sleeplog`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_logFood`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_logFood` (
  `user` varchar(8) NOT NULL,
  `date` varchar(10) NOT NULL,
  `meal` varchar(30) NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `carbs` int(11) DEFAULT NULL,
  `fat` int(11) DEFAULT NULL,
  `fiber` int(11) DEFAULT NULL,
  `protein` int(11) DEFAULT NULL,
  `sodium` int(11) DEFAULT NULL,
  PRIMARY KEY (`user`,`date`,`meal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_logSleep`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_logSleep` (
  `logId` int(11) NOT NULL,
  `awakeningsCount` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `efficiency` int(11) NOT NULL,
  `isMainSleep` varchar(5) NOT NULL,
  `minutesAfterWakeup` int(11) NOT NULL,
  `minutesAsleep` int(11) NOT NULL,
  `minutesAwake` int(11) NOT NULL,
  `minutesToFallAsleep` int(11) NOT NULL,
  `startTime` varchar(25) NOT NULL,
  `timeInBed` int(11) NOT NULL,
  `minuteData` longtext,
  PRIMARY KEY (`logId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_queue`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_queue` (
  `user` varchar(10) NOT NULL,
  `date` varchar(20) NOT NULL,
  `trigger` varchar(30) NOT NULL,
  PRIMARY KEY (`user`,`trigger`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_runlog`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_runlog` (
  `user` varchar(8) NOT NULL,
  `date` varchar(20) NOT NULL,
  `activity` varchar(30) NOT NULL,
  `cooldown` varchar(20) NOT NULL DEFAULT '1970-01-01 00:00:00',
  `lastrun` varchar(20) NOT NULL DEFAULT '1970-01-01 00:00:00',
  PRIMARY KEY (`user`,`activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_settings`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_settings` (
  `var` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  UNIQUE KEY `var` (`var`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_steps`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_steps` (
  `user` varchar(8) NOT NULL,
  `distance` decimal(21,16) DEFAULT NULL,
  `floors` int(11) DEFAULT NULL,
  `elevation` decimal(9,5) DEFAULT NULL,
  `date` varchar(10) NOT NULL DEFAULT '',
  `steps` int(11) DEFAULT NULL,
  `caloriesOut` int(11) DEFAULT NULL,
  `syncd` varchar(20) NOT NULL,
  PRIMARY KEY (`user`,`date`),
  UNIQUE KEY `distance` (`user`,`date`,`distance`),
  UNIQUE KEY `elevation` (`user`,`date`,`elevation`),
  UNIQUE KEY `floors` (`user`,`date`,`floors`),
  UNIQUE KEY `steps` (`user`,`date`,`steps`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_steps_goals`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_steps_goals` (
  `user` varchar(8) NOT NULL,
  `distance` decimal(21,16) DEFAULT NULL,
  `floors` int(11) DEFAULT NULL,
  `activeMinutes` decimal(9,5) DEFAULT NULL,
  `date` varchar(10) NOT NULL,
  `steps` int(11) DEFAULT NULL,
  `caloriesOut` int(11) DEFAULT NULL,
  `syncd` varchar(20) NOT NULL,
  PRIMARY KEY (`user`,`date`),
  UNIQUE KEY `distance` (`user`,`date`,`distance`),
  UNIQUE KEY `elevation` (`user`,`date`,`activeMinutes`),
  UNIQUE KEY `floors` (`user`,`date`,`floors`),
  UNIQUE KEY `steps` (`user`,`date`,`steps`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nx_fitbit_users`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `fuid` varchar(8) NOT NULL,
  `password` varchar(64) NOT NULL,
  `group` set('user','admin') NOT NULL DEFAULT 'user',
  `lastrun` varchar(20) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '-1',
  `friends` int(11) NOT NULL DEFAULT '-1',
  `distance` int(11) NOT NULL DEFAULT '-1',
  `avatar` varchar(255) NOT NULL,
  `seen` varchar(10) NOT NULL,
  `token` varchar(40) NOT NULL,
  `secret` varchar(40) NOT NULL,
  `gender` varchar(6) DEFAULT NULL,
  `cooldown` varchar(20) DEFAULT NULL,
  `height` decimal(6,2) DEFAULT NULL,
  `stride_running` decimal(20,14) DEFAULT NULL,
  `stride_walking` decimal(20,14) DEFAULT NULL,
  `city` varchar(25) DEFAULT NULL,
  `country` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`fuid`),
  UNIQUE KEY `drupalid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

DROP TABLE IF EXISTS `nx_fitbit_users_auth`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_users_auth` (
  `ID` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(15) NOT NULL,
  `Password` varchar(40) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Activated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Confirmation` char(40) NOT NULL DEFAULT '',
  `RegDate` int(11) unsigned NOT NULL,
  `LastLogin` int(11) unsigned NOT NULL DEFAULT '0',
  `GroupID` int(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `nx_fitbit_users_settings`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_users_settings` (
  `fuid` varchar(8) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT 'The name of the variable.',
  `value` longtext NOT NULL COMMENT 'The value of the variable.',
  PRIMARY KEY (`fuid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Named variable/value pairs created.';

DROP TABLE IF EXISTS `nx_fitbit_water`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_water` (
  `user` varchar(8) NOT NULL,
  `date` varchar(10) NOT NULL,
  `id` int(11) NOT NULL,
  `liquid` decimal(18,12) DEFAULT NULL,
  PRIMARY KEY (`user`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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

ALTER TABLE `nx_fitbit_users_settings`
ADD CONSTRAINT `nx_fitbit_users_settings_ibfk_1` FOREIGN KEY (`fuid`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_water`
ADD CONSTRAINT `nx_fitbit_water_ibfk_1` FOREIGN KEY (`user`) REFERENCES `nx_fitbit_users` (`fuid`);
