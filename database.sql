SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `nx_fitbit_activity` (
  `user`          VARCHAR(8)  NOT NULL,
  `date`          VARCHAR(10) NOT NULL,
  `sedentary`     INT(11) DEFAULT NULL,
  `lightlyactive` INT(11) DEFAULT NULL,
  `fairlyactive`  INT(11) DEFAULT NULL,
  `veryactive`    INT(11) DEFAULT NULL,
  `syncd`         VARCHAR(20) NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_activity_log` (
  `user`               VARCHAR(8)   NOT NULL,
  `date`               VARCHAR(19)  NOT NULL,
  `logId`              INT(11)      NOT NULL,
  `activityId`         INT(11)      NOT NULL,
  `activityParentId`   INT(11)      NOT NULL,
  `activityParentName` VARCHAR(225) NOT NULL,
  `name`               VARCHAR(225) NOT NULL,
  `description`        LONGTEXT,
  `calories`           INT(4)       NOT NULL,
  `duration`           INT(8)       NOT NULL,
  `startDate`          VARCHAR(10)  NOT NULL,
  `startTime`          VARCHAR(5)   NOT NULL,
  `hasStartTime`       INT(1)       NOT NULL,
  `isFavorite`         INT(1)       NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_bages` (
  `badgeType`               VARCHAR(120) NOT NULL,
  `value`                   INT(11)      NOT NULL,
  `image`                   VARCHAR(255) NOT NULL,
  `badgeGradientEndColor`   VARCHAR(6)   NOT NULL,
  `badgeGradientStartColor` VARCHAR(6)   NOT NULL,
  `earnedMessage`           LONGTEXT     NOT NULL,
  `marketingDescription`    LONGTEXT     NOT NULL,
  `name`                    VARCHAR(255) NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_body` (
  `user`       VARCHAR(8) NOT NULL,
  `date`       DATE       NOT NULL,
  `weight`     DECIMAL(5, 2) DEFAULT NULL,
  `weightGoal` DECIMAL(5, 2) DEFAULT NULL,
  `weightAvg`  DECIMAL(5, 2) DEFAULT NULL,
  `fat`        DECIMAL(5, 2) DEFAULT NULL,
  `fatGoal`    DECIMAL(5, 2) DEFAULT NULL,
  `fatAvg`     DECIMAL(5, 2) DEFAULT NULL,
  `bmi`        DECIMAL(5, 2) DEFAULT NULL,
  `calf`       DECIMAL(5, 2) DEFAULT NULL,
  `bicep`      DECIMAL(5, 2) DEFAULT NULL,
  `chest`      DECIMAL(5, 2) DEFAULT NULL,
  `forearm`    DECIMAL(5, 2) DEFAULT NULL,
  `hips`       DECIMAL(5, 2) DEFAULT NULL,
  `neck`       DECIMAL(5, 2) DEFAULT NULL,
  `thigh`      DECIMAL(5, 2) DEFAULT NULL,
  `waist`      DECIMAL(5, 2) DEFAULT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_devices` (
  `id`            VARCHAR(20) NOT NULL,
  `deviceVersion` VARCHAR(10) NOT NULL,
  `type`          VARCHAR(10) NOT NULL,
  `lastSyncTime`  VARCHAR(23) NOT NULL,
  `battery`       VARCHAR(10) NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_goals_calories` (
  `user`          VARCHAR(8)  NOT NULL,
  `date`          VARCHAR(10) NOT NULL,
  `calories`      INT(11)     NOT NULL,
  `intensity`     VARCHAR(12) NOT NULL,
  `estimatedDate` VARCHAR(10) NOT NULL,
  `personalized`  VARCHAR(5)  NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_heartAverage` (
  `user`     VARCHAR(8)  NOT NULL,
  `date`     VARCHAR(20) NOT NULL,
  `resting`  DECIMAL(5, 2) DEFAULT NULL,
  `normal`   DECIMAL(5, 2) DEFAULT NULL,
  `exertive` DECIMAL(5, 2) DEFAULT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_keypoints` (
  `category` ENUM('distance', 'floors') NOT NULL,
  `value`    FLOAT(22, 2)               NOT NULL,
  `less`     VARCHAR(255)               NOT NULL,
  `more`     VARCHAR(255) DEFAULT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_badge2usr` (
  `user`          VARCHAR(8)   NOT NULL,
  `badgeType`     VARCHAR(120) NOT NULL,
  `dateTime`      VARCHAR(20)  NOT NULL,
  `timesAchieved` INT(11)      NOT NULL,
  `value`         INT(11)      NOT NULL,
  `unit`          VARCHAR(50)  NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_dev2usr` (
  `user`   VARCHAR(8)  NOT NULL,
  `device` VARCHAR(20) NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_lnk_sleep2usr` (
  `user`               VARCHAR(8) NOT NULL,
  `sleeplog`           INT(11)    NOT NULL,
  `totalMinutesAsleep` INT(11) DEFAULT NULL,
  `totalSleepRecords`  INT(11) DEFAULT NULL,
  `totalTimeInBed`     INT(11) DEFAULT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_logFood` (
  `user`     VARCHAR(8)  NOT NULL,
  `date`     VARCHAR(10) NOT NULL,
  `meal`     VARCHAR(30) NOT NULL,
  `calories` INT(11) DEFAULT NULL,
  `carbs`    INT(11) DEFAULT NULL,
  `fat`      INT(11) DEFAULT NULL,
  `fiber`    INT(11) DEFAULT NULL,
  `protein`  INT(11) DEFAULT NULL,
  `sodium`   INT(11) DEFAULT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_logSleep` (
  `logId`               INT(11)     NOT NULL,
  `awakeningsCount`     INT(11)     NOT NULL,
  `duration`            INT(11)     NOT NULL,
  `efficiency`          INT(11)     NOT NULL,
  `isMainSleep`         VARCHAR(5)  NOT NULL,
  `minutesAfterWakeup`  INT(11)     NOT NULL,
  `minutesAsleep`       INT(11)     NOT NULL,
  `minutesAwake`        INT(11)     NOT NULL,
  `minutesToFallAsleep` INT(11)     NOT NULL,
  `startTime`           VARCHAR(25) NOT NULL,
  `timeInBed`           INT(11)     NOT NULL,
  `minuteData`          LONGTEXT
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_queue` (
  `user`    VARCHAR(10) NOT NULL,
  `date`    VARCHAR(20) NOT NULL,
  `trigger` VARCHAR(30) NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_runlog` (
  `user`     VARCHAR(8)  NOT NULL,
  `date`     VARCHAR(20) NOT NULL,
  `activity` VARCHAR(30) NOT NULL,
  `cooldown` VARCHAR(20) NOT NULL DEFAULT '1970-01-01 00:00:00',
  `lastrun`  VARCHAR(20) NOT NULL DEFAULT '1970-01-01 00:00:00'
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_settings` (
  `var`  VARCHAR(255) NOT NULL,
  `data` LONGTEXT     NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_steps` (
  `user`        VARCHAR(8)  NOT NULL,
  `distance`    DECIMAL(21, 16)      DEFAULT NULL,
  `floors`      INT(11)              DEFAULT NULL,
  `elevation`   DECIMAL(9, 5)        DEFAULT NULL,
  `date`        VARCHAR(10) NOT NULL DEFAULT '',
  `steps`       INT(11)              DEFAULT NULL,
  `caloriesOut` INT(11)              DEFAULT NULL,
  `syncd`       VARCHAR(20) NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_steps_goals` (
  `user`          VARCHAR(8)  NOT NULL,
  `distance`      DECIMAL(21, 16) DEFAULT NULL,
  `floors`        INT(11)         DEFAULT NULL,
  `activeMinutes` DECIMAL(9, 5)   DEFAULT NULL,
  `date`          VARCHAR(10) NOT NULL,
  `steps`         INT(11)         DEFAULT NULL,
  `caloriesOut`   INT(11)         DEFAULT NULL,
  `syncd`         VARCHAR(20) NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_users` (
  `uid`            INT(11)      NOT NULL,
  `fuid`           VARCHAR(8)   NOT NULL,
  `lastrun`        VARCHAR(20)           DEFAULT NULL,
  `name`           VARCHAR(255) NOT NULL,
  `rank`           INT(11)      NOT NULL DEFAULT '-1',
  `friends`        INT(11)      NOT NULL DEFAULT '-1',
  `distance`       INT(11)      NOT NULL DEFAULT '-1',
  `avatar`         VARCHAR(255) NOT NULL,
  `seen`           VARCHAR(10)  NOT NULL,
  `token`          VARCHAR(40)  NOT NULL,
  `secret`         VARCHAR(40)  NOT NULL,
  `gender`         VARCHAR(6)            DEFAULT NULL,
  `cooldown`       VARCHAR(20)           DEFAULT NULL,
  `height`         DECIMAL(6, 2)         DEFAULT NULL,
  `stride_running` DECIMAL(20, 14)       DEFAULT NULL,
  `stride_walking` DECIMAL(20, 14)       DEFAULT NULL,
  `city`           VARCHAR(25)           DEFAULT NULL,
  `country`        VARCHAR(3)            DEFAULT NULL
)
  ENGINE =InnoDB
  AUTO_INCREMENT =2
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_users_auth` (
  `ID`           INT(7) UNSIGNED     NOT NULL,
  `Username`     VARCHAR(15)         NOT NULL,
  `Password`     VARCHAR(40)         NOT NULL,
  `Email`        VARCHAR(100)        NOT NULL,
  `Activated`    TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `Confirmation` CHAR(40)            NOT NULL DEFAULT '',
  `RegDate`      INT(11) UNSIGNED    NOT NULL,
  `LastLogin`    INT(11) UNSIGNED    NOT NULL DEFAULT '0',
  `GroupID`      INT(2) UNSIGNED     NOT NULL DEFAULT '1'
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_users_settings` (
  `fuid`  VARCHAR(8)   NOT NULL,
  `name`  VARCHAR(128) NOT NULL DEFAULT '',
  `value` LONGTEXT     NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `nx_fitbit_water` (
  `user`   VARCHAR(8)  NOT NULL,
  `date`   VARCHAR(10) NOT NULL,
  `id`     INT(11)     NOT NULL,
  `liquid` DECIMAL(18, 12) DEFAULT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;


ALTER TABLE `nx_fitbit_activity`
ADD PRIMARY KEY (`user`, `date`);

ALTER TABLE `nx_fitbit_activity_log`
ADD PRIMARY KEY (`user`, `logId`, `activityId`, `startDate`, `startTime`);

ALTER TABLE `nx_fitbit_bages`
ADD PRIMARY KEY (`badgeType`, `value`);

ALTER TABLE `nx_fitbit_body`
ADD PRIMARY KEY (`user`, `date`);

ALTER TABLE `nx_fitbit_devices`
ADD PRIMARY KEY (`id`);

ALTER TABLE `nx_fitbit_goals_calories`
ADD PRIMARY KEY (`user`, `date`);

ALTER TABLE `nx_fitbit_heartAverage`
ADD PRIMARY KEY (`user`, `date`);

ALTER TABLE `nx_fitbit_keypoints`
ADD PRIMARY KEY (`category`, `value`);

ALTER TABLE `nx_fitbit_lnk_badge2usr`
ADD PRIMARY KEY (`user`, `badgeType`, `value`);

ALTER TABLE `nx_fitbit_lnk_dev2usr`
ADD UNIQUE KEY `user` (`user`, `device`);

ALTER TABLE `nx_fitbit_lnk_sleep2usr`
ADD PRIMARY KEY (`user`, `sleeplog`), ADD KEY `sleeplog` (`sleeplog`);

ALTER TABLE `nx_fitbit_logFood`
ADD PRIMARY KEY (`user`, `date`, `meal`);

ALTER TABLE `nx_fitbit_logSleep`
ADD PRIMARY KEY (`logId`);

ALTER TABLE `nx_fitbit_queue`
ADD PRIMARY KEY (`user`, `trigger`);

ALTER TABLE `nx_fitbit_runlog`
ADD PRIMARY KEY (`user`, `activity`);

ALTER TABLE `nx_fitbit_settings`
ADD UNIQUE KEY `var` (`var`);

ALTER TABLE `nx_fitbit_steps`
ADD PRIMARY KEY (`user`, `date`), ADD UNIQUE KEY `distance` (`user`, `date`, `distance`), ADD UNIQUE KEY `elevation` (`user`, `date`, `elevation`), ADD UNIQUE KEY `floors` (`user`, `date`, `floors`), ADD UNIQUE KEY `steps` (`user`, `date`, `steps`);

ALTER TABLE `nx_fitbit_steps_goals`
ADD PRIMARY KEY (`user`, `date`), ADD UNIQUE KEY `distance` (`user`, `date`, `distance`), ADD UNIQUE KEY `elevation` (`user`, `date`, `activeMinutes`), ADD UNIQUE KEY `floors` (`user`, `date`, `floors`), ADD UNIQUE KEY `steps` (`user`, `date`, `steps`);

ALTER TABLE `nx_fitbit_users`
ADD PRIMARY KEY (`fuid`), ADD UNIQUE KEY `drupalid` (`uid`);

ALTER TABLE `nx_fitbit_users_auth`
ADD PRIMARY KEY (`ID`);

ALTER TABLE `nx_fitbit_users_settings`
ADD PRIMARY KEY (`fuid`, `name`);

ALTER TABLE `nx_fitbit_water`
ADD PRIMARY KEY (`user`, `date`);


ALTER TABLE `nx_fitbit_users`
MODIFY `uid` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT =2;
ALTER TABLE `nx_fitbit_users_auth`
MODIFY `ID` INT(7) UNSIGNED NOT NULL AUTO_INCREMENT;

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

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;