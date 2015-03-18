SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `nx_fitbit_activity` (
  `user` varchar(8) NOT NULL,
  `date` varchar(10) NOT NULL,
  `sedentary` int(11) DEFAULT NULL,
  `lightlyactive` int(11) DEFAULT NULL,
  `fairlyactive` int(11) DEFAULT NULL,
  `veryactive` int(11) DEFAULT NULL,
  `syncd` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `duration` int(8) NOT NULL,
  `startDate` varchar(10) NOT NULL,
  `startTime` varchar(5) NOT NULL,
  `hasStartTime` int(1) NOT NULL,
  `isFavorite` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_bages` (
  `badgeType` varchar(120) NOT NULL,
  `value` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `badgeGradientEndColor` varchar(6) NOT NULL,
  `badgeGradientStartColor` varchar(6) NOT NULL,
  `earnedMessage` longtext NOT NULL,
  `marketingDescription` longtext NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `waist` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_devices` (
  `id` varchar(20) NOT NULL,
  `deviceVersion` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `lastSyncTime` varchar(23) NOT NULL,
  `battery` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_goals_calories` (
  `user` varchar(8) NOT NULL,
  `date` varchar(10) NOT NULL,
  `calories` int(11) NOT NULL,
  `intensity` varchar(12) NOT NULL,
  `estimatedDate` varchar(10) NOT NULL,
  `personalized` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_heartAverage` (
  `user` varchar(8) NOT NULL,
  `date` varchar(20) NOT NULL,
  `resting` decimal(5,2) DEFAULT NULL,
  `normal` decimal(5,2) DEFAULT NULL,
  `exertive` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_keypoints` (
  `category` enum('distance','floors') NOT NULL,
  `value` float(22,2) NOT NULL,
  `less` varchar(255) NOT NULL,
  `more` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_badge2usr` (
  `user` varchar(8) NOT NULL,
  `badgeType` varchar(120) NOT NULL,
  `dateTime` varchar(20) NOT NULL,
  `timesAchieved` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_dev2usr` (
  `user` varchar(8) NOT NULL,
  `device` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_sleep2usr` (
  `user` varchar(8) NOT NULL,
  `sleeplog` int(11) NOT NULL,
  `totalMinutesAsleep` int(11) DEFAULT NULL,
  `totalSleepRecords` int(11) DEFAULT NULL,
  `totalTimeInBed` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_logFood` (
  `user` varchar(8) NOT NULL,
  `date` varchar(10) NOT NULL,
  `meal` varchar(30) NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `carbs` int(11) DEFAULT NULL,
  `fat` int(11) DEFAULT NULL,
  `fiber` int(11) DEFAULT NULL,
  `protein` int(11) DEFAULT NULL,
  `sodium` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `minuteData` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_queue` (
  `user` varchar(10) NOT NULL,
  `date` varchar(20) NOT NULL,
  `trigger` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_runlog` (
  `user` varchar(8) NOT NULL,
  `date` varchar(20) NOT NULL,
  `activity` varchar(30) NOT NULL,
  `cooldown` varchar(20) NOT NULL DEFAULT '1970-01-01 00:00:00',
  `lastrun` varchar(20) NOT NULL DEFAULT '1970-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_settings` (
  `var` varchar(255) NOT NULL,
  `data` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_steps` (
  `user` varchar(8) NOT NULL,
  `distance` decimal(21,16) DEFAULT NULL,
  `floors` int(11) DEFAULT NULL,
  `elevation` decimal(9,5) DEFAULT NULL,
  `date` varchar(10) NOT NULL DEFAULT '',
  `steps` int(11) DEFAULT NULL,
  `caloriesOut` int(11) DEFAULT NULL,
  `syncd` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_steps_goals` (
  `user` varchar(8) NOT NULL,
  `distance` decimal(21,16) DEFAULT NULL,
  `floors` int(11) DEFAULT NULL,
  `activeMinutes` decimal(9,5) DEFAULT NULL,
  `date` varchar(10) NOT NULL,
  `steps` int(11) DEFAULT NULL,
  `caloriesOut` int(11) DEFAULT NULL,
  `syncd` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_users` (
`uid` int(11) NOT NULL,
  `fuid` varchar(8) NOT NULL,
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
  `country` varchar(3) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `nx_fitbit_users_settings` (
  `fuid` varchar(8) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_water` (
  `user` varchar(8) NOT NULL,
  `date` varchar(10) NOT NULL,
  `id` int(11) NOT NULL,
  `liquid` decimal(18,12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `nx_fitbit_activity`
 ADD PRIMARY KEY (`user`,`date`);

ALTER TABLE `nx_fitbit_activity_log`
 ADD PRIMARY KEY (`user`,`logId`,`activityId`,`startDate`,`startTime`);

ALTER TABLE `nx_fitbit_bages`
 ADD PRIMARY KEY (`badgeType`,`value`);

ALTER TABLE `nx_fitbit_body`
 ADD PRIMARY KEY (`user`,`date`);

ALTER TABLE `nx_fitbit_devices`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `nx_fitbit_goals_calories`
 ADD PRIMARY KEY (`user`,`date`);

ALTER TABLE `nx_fitbit_heartAverage`
 ADD PRIMARY KEY (`user`,`date`);

ALTER TABLE `nx_fitbit_keypoints`
 ADD PRIMARY KEY (`category`,`value`);

ALTER TABLE `nx_fitbit_lnk_badge2usr`
 ADD PRIMARY KEY (`user`,`badgeType`,`value`);

ALTER TABLE `nx_fitbit_lnk_dev2usr`
 ADD UNIQUE KEY `user` (`user`,`device`);

ALTER TABLE `nx_fitbit_lnk_sleep2usr`
 ADD PRIMARY KEY (`user`,`sleeplog`), ADD KEY `sleeplog` (`sleeplog`);

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
 ADD PRIMARY KEY (`user`,`date`), ADD UNIQUE KEY `distance` (`user`,`date`,`distance`), ADD UNIQUE KEY `elevation` (`user`,`date`,`elevation`), ADD UNIQUE KEY `floors` (`user`,`date`,`floors`), ADD UNIQUE KEY `steps` (`user`,`date`,`steps`);

ALTER TABLE `nx_fitbit_steps_goals`
 ADD PRIMARY KEY (`user`,`date`), ADD UNIQUE KEY `distance` (`user`,`date`,`distance`), ADD UNIQUE KEY `elevation` (`user`,`date`,`activeMinutes`), ADD UNIQUE KEY `floors` (`user`,`date`,`floors`), ADD UNIQUE KEY `steps` (`user`,`date`,`steps`);

ALTER TABLE `nx_fitbit_users`
 ADD PRIMARY KEY (`fuid`), ADD UNIQUE KEY `drupalid` (`uid`);

ALTER TABLE `nx_fitbit_users_auth`
 ADD PRIMARY KEY (`ID`);

ALTER TABLE `nx_fitbit_users_settings`
 ADD PRIMARY KEY (`fuid`,`name`);

ALTER TABLE `nx_fitbit_water`
 ADD PRIMARY KEY (`user`,`date`);


ALTER TABLE `nx_fitbit_users`
MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
ALTER TABLE `nx_fitbit_users_auth`
MODIFY `ID` int(7) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `nx_fitbit_activity`
ADD CONSTRAINT `nx_fitbit_activity_ibfk_1` FOREIGN KEY (`user`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_body`
ADD CONSTRAINT `nx_fitbit_body_ibfk_1` FOREIGN KEY (`user`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_queue`
ADD CONSTRAINT `nx_fitbit_queue_ibfk_1` FOREIGN KEY (`user`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_users_settings`
ADD CONSTRAINT `nx_fitbit_users_settings_ibfk_1` FOREIGN KEY (`fuid`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_water`
ADD CONSTRAINT `nx_fitbit_water_ibfk_1` FOREIGN KEY (`user`) REFERENCES `nx_fitbit_users` (`fuid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;