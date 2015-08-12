-- All numerical ids in the Fitbit API to be unsigned 64 bit integers
ALTER TABLE `nx_fitbit_activity_log` CHANGE `logId` `logId` BIGINT(20) NOT NULL, CHANGE `activityId` `activityId` BIGINT(20) NOT NULL, CHANGE `activityParentId` `activityParentId` BIGINT(20) NOT NULL;
ALTER TABLE `nx_fitbit_lnk_sleep2usr` CHANGE `sleeplog` `sleeplog` BIGINT(20) NOT NULL;
ALTER TABLE `nx_fitbit_logSleep` CHANGE `logId` `logId` BIGINT(20) NOT NULL;
ALTER TABLE `nx_fitbit_water` CHANGE `id` `id` BIGINT(20) NOT NULL;