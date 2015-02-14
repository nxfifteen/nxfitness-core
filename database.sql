SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nxad`
--

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_activity`
--

DROP TABLE IF EXISTS `nx_fitbit_activity`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_activity` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `sedentary` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `lightlyactive` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `fairlyactive` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `veryactive` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `syncd` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_activity_log`
--

DROP TABLE IF EXISTS `nx_fitbit_activity_log`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_activity_log` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(19) NOT NULL COMMENT 'TODO: please describe this field!',
  `action` varchar(225) NOT NULL COMMENT 'TODO: please describe this field!',
  `time_spent` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_bages`
--

DROP TABLE IF EXISTS `nx_fitbit_bages`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_bages` (
  `badgeType` varchar(120) NOT NULL COMMENT 'TODO: please describe this field!',
  `value` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `image50px` varchar(255) NOT NULL COMMENT 'TODO: please describe this field!',
  `image75px` varchar(255) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_body`
--

DROP TABLE IF EXISTS `nx_fitbit_body`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_body` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `weight` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `weightGoal` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `fat` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `fatGoal` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `bmi` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_devices`
--

DROP TABLE IF EXISTS `nx_fitbit_devices`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_devices` (
  `id` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!',
  `deviceVersion` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `type` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `lastSyncTime` varchar(23) NOT NULL COMMENT 'TODO: please describe this field!',
  `battery` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_goals_calories`
--

DROP TABLE IF EXISTS `nx_fitbit_goals_calories`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_goals_calories` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `calories` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `intensity` varchar(12) NOT NULL COMMENT 'TODO: please describe this field!',
  `estimatedDate` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `personalized` varchar(5) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_heartAverage`
--

DROP TABLE IF EXISTS `nx_fitbit_heartAverage`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_heartAverage` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!',
  `resting` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `normal` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `exertive` decimal(5,2) DEFAULT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_lnk_badge2usr`
--

DROP TABLE IF EXISTS `nx_fitbit_lnk_badge2usr`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_badge2usr` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `badgeType` varchar(120) NOT NULL COMMENT 'TODO: please describe this field!',
  `dateTime` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!',
  `timesAchieved` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `value` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `unit` varchar(50) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_lnk_dev2usr`
--

DROP TABLE IF EXISTS `nx_fitbit_lnk_dev2usr`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_dev2usr` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `device` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_lnk_sleep2usr`
--

DROP TABLE IF EXISTS `nx_fitbit_lnk_sleep2usr`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_sleep2usr` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `sleeplog` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `totalMinutesAsleep` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `totalSleepRecords` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `totalTimeInBed` int(11) DEFAULT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_logFood`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_logSleep`
--

DROP TABLE IF EXISTS `nx_fitbit_logSleep`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_logSleep` (
  `logId` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
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

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_queue`
--

DROP TABLE IF EXISTS `nx_fitbit_queue`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_queue` (
  `user` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!',
  `trigger` varchar(30) NOT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_runlog`
--

DROP TABLE IF EXISTS `nx_fitbit_runlog`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_runlog` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(20) NOT NULL COMMENT 'TODO: please describe this field!',
  `activity` varchar(30) NOT NULL COMMENT 'TODO: please describe this field!',
  `cooldown` varchar(20) NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'TODO: please describe this field!',
  `lastrun` varchar(20) NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_settings`
--

DROP TABLE IF EXISTS `nx_fitbit_settings`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_settings` (
  `var` varchar(255) NOT NULL,
  `data` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_steps`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_steps_goals`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_users`
--

DROP TABLE IF EXISTS `nx_fitbit_users`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_users` (
`uid` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `fuid` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `lastrun` varchar(20) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `name` varchar(25) NOT NULL COMMENT 'TODO: please describe this field!',
  `rank` int(11) NOT NULL DEFAULT '-1' COMMENT 'TODO: please describe this field!',
  `friends` int(11) NOT NULL DEFAULT '-1' COMMENT 'TODO: please describe this field!',
  `distance` int(11) NOT NULL DEFAULT '-1' COMMENT 'TODO: please describe this field!',
  `avatar` varchar(255) NOT NULL COMMENT 'TODO: please describe this field!',
  `seen` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `token` varchar(40) NOT NULL COMMENT 'TODO: please describe this field!',
  `secret` varchar(40) NOT NULL COMMENT 'TODO: please describe this field!',
  `gender` varchar(6) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `cooldown` varchar(20) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `height` decimal(6,2) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `stride_running` decimal(20,14) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `stride_walking` decimal(20,14) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `city` varchar(25) DEFAULT NULL COMMENT 'TODO: please describe this field!',
  `country` varchar(3) DEFAULT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_users_settings`
--

DROP TABLE IF EXISTS `nx_fitbit_users_settings`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_users_settings` (
  `fuid` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT 'The name of the variable.',
  `value` longtext NOT NULL COMMENT 'The value of the variable.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Named variable/value pairs created.';

-- --------------------------------------------------------

--
-- Table structure for table `nx_fitbit_water`
--

DROP TABLE IF EXISTS `nx_fitbit_water`;
CREATE TABLE IF NOT EXISTS `nx_fitbit_water` (
  `user` varchar(8) NOT NULL COMMENT 'TODO: please describe this field!',
  `date` varchar(10) NOT NULL COMMENT 'TODO: please describe this field!',
  `id` int(11) NOT NULL COMMENT 'TODO: please describe this field!',
  `liquid` decimal(18,12) DEFAULT NULL COMMENT 'TODO: please describe this field!'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TODO: please describe this table!';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nx_fitbit_activity`
--
ALTER TABLE `nx_fitbit_activity`
 ADD PRIMARY KEY (`user`,`date`);

--
-- Indexes for table `nx_fitbit_activity_log`
--
ALTER TABLE `nx_fitbit_activity_log`
 ADD PRIMARY KEY (`user`,`date`);

--
-- Indexes for table `nx_fitbit_bages`
--
ALTER TABLE `nx_fitbit_bages`
 ADD PRIMARY KEY (`badgeType`,`value`);

--
-- Indexes for table `nx_fitbit_body`
--
ALTER TABLE `nx_fitbit_body`
 ADD PRIMARY KEY (`user`,`date`);

--
-- Indexes for table `nx_fitbit_devices`
--
ALTER TABLE `nx_fitbit_devices`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nx_fitbit_goals_calories`
--
ALTER TABLE `nx_fitbit_goals_calories`
 ADD PRIMARY KEY (`user`,`date`);

--
-- Indexes for table `nx_fitbit_heartAverage`
--
ALTER TABLE `nx_fitbit_heartAverage`
 ADD PRIMARY KEY (`user`,`date`);

--
-- Indexes for table `nx_fitbit_lnk_badge2usr`
--
ALTER TABLE `nx_fitbit_lnk_badge2usr`
 ADD PRIMARY KEY (`user`,`badgeType`,`value`);

--
-- Indexes for table `nx_fitbit_lnk_dev2usr`
--
ALTER TABLE `nx_fitbit_lnk_dev2usr`
 ADD UNIQUE KEY `user` (`user`,`device`);

--
-- Indexes for table `nx_fitbit_lnk_sleep2usr`
--
ALTER TABLE `nx_fitbit_lnk_sleep2usr`
 ADD PRIMARY KEY (`user`,`sleeplog`), ADD KEY `sleeplog` (`sleeplog`);

--
-- Indexes for table `nx_fitbit_logFood`
--
ALTER TABLE `nx_fitbit_logFood`
 ADD PRIMARY KEY (`user`,`date`,`meal`);

--
-- Indexes for table `nx_fitbit_logSleep`
--
ALTER TABLE `nx_fitbit_logSleep`
 ADD PRIMARY KEY (`logId`);

--
-- Indexes for table `nx_fitbit_queue`
--
ALTER TABLE `nx_fitbit_queue`
 ADD PRIMARY KEY (`user`,`trigger`);

--
-- Indexes for table `nx_fitbit_runlog`
--
ALTER TABLE `nx_fitbit_runlog`
 ADD PRIMARY KEY (`user`,`activity`);

--
-- Indexes for table `nx_fitbit_settings`
--
ALTER TABLE `nx_fitbit_settings`
 ADD UNIQUE KEY `var` (`var`);

--
-- Indexes for table `nx_fitbit_steps`
--
ALTER TABLE `nx_fitbit_steps`
 ADD PRIMARY KEY (`user`,`date`), ADD UNIQUE KEY `distance` (`user`,`date`,`distance`), ADD UNIQUE KEY `elevation` (`user`,`date`,`elevation`), ADD UNIQUE KEY `floors` (`user`,`date`,`floors`), ADD UNIQUE KEY `steps` (`user`,`date`,`steps`);

--
-- Indexes for table `nx_fitbit_steps_goals`
--
ALTER TABLE `nx_fitbit_steps_goals`
 ADD PRIMARY KEY (`user`,`date`), ADD UNIQUE KEY `distance` (`user`,`date`,`distance`), ADD UNIQUE KEY `elevation` (`user`,`date`,`activeMinutes`), ADD UNIQUE KEY `floors` (`user`,`date`,`floors`), ADD UNIQUE KEY `steps` (`user`,`date`,`steps`);

--
-- Indexes for table `nx_fitbit_users`
--
ALTER TABLE `nx_fitbit_users`
 ADD PRIMARY KEY (`fuid`), ADD UNIQUE KEY `drupalid` (`uid`);

--
-- Indexes for table `nx_fitbit_users_settings`
--
ALTER TABLE `nx_fitbit_users_settings`
 ADD PRIMARY KEY (`fuid`,`name`);

--
-- Indexes for table `nx_fitbit_water`
--
ALTER TABLE `nx_fitbit_water`
 ADD PRIMARY KEY (`user`,`date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nx_fitbit_users`
--
ALTER TABLE `nx_fitbit_users`
MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'TODO: please describe this field!',AUTO_INCREMENT=29;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `nx_fitbit_users_settings`
--
ALTER TABLE `nx_fitbit_users_settings`
ADD CONSTRAINT `nx_fitbit_users_settings_ibfk_1` FOREIGN KEY (`fuid`) REFERENCES `nx_fitbit_users` (`fuid`);

ALTER TABLE `nx_fitbit_activity` ADD FOREIGN KEY (`user`) REFERENCES `nxad`.`nx_fitbit_users`(`fuid`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `nx_fitbit_water` ADD FOREIGN KEY (`user`) REFERENCES `nxad`.`nx_fitbit_users`(`fuid`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `nx_fitbit_body` ADD FOREIGN KEY (`user`) REFERENCES `nxad`.`nx_fitbit_users`(`fuid`) ON DELETE RESTRICT ON UPDATE RESTRICT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;