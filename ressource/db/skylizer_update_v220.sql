
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `user_cli` (
  `eve_userid` int(11) NOT NULL,
  `eve_corpid` int(11) NOT NULL,
  `eve_tokenlifetime` datetime NOT NULL,
  `authcontainer` mediumtext,
  `in_use` int(1) NOT NULL DEFAULT '0',
  `token` mediumtext,
  PRIMARY KEY (`eve_userid`),
  KEY `idx_lifetime` (`eve_tokenlifetime`),
  KEY `ids_inuse` (`in_use`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `annotare`.`at_structure` 
ADD COLUMN `structure_id` BIGINT(20) NULL DEFAULT NULL COMMENT '' AFTER `target_system_id`,
ADD COLUMN `fuel_expires` DATETIME NULL DEFAULT NULL COMMENT '' AFTER `structure_id`,
ADD COLUMN `reinforce_hour` INT(2) NULL DEFAULT NULL COMMENT '' AFTER `fuel_expires`,
ADD COLUMN `reinforce_weekday` INT(2) NULL DEFAULT NULL COMMENT '' AFTER `reinforce_hour`,
ADD COLUMN `structure_state` VARCHAR(80) NULL DEFAULT NULL COMMENT '' AFTER `reinforce_weekday`,
ADD COLUMN `chunk_arrival_time` DATETIME NULL DEFAULT NULL COMMENT '' AFTER `structure_state`,
ADD COLUMN `extraction_start_time` DATETIME NULL DEFAULT NULL COMMENT '' AFTER `chunk_arrival_time`,
ADD COLUMN `natural_decay_time` DATETIME NULL DEFAULT NULL COMMENT '' AFTER `extraction_start_time`,
ADD COLUMN `state_timer_start` DATETIME NULL DEFAULT NULL COMMENT '' AFTER `structure_state`,
ADD COLUMN `state_timer_end` DATETIME NULL COMMENT '' AFTER `state_timer_start`;
ADD INDEX `idx_structure_id` (`structure_id` ASC)  COMMENT '';

SET FOREIGN_KEY_CHECKS = 1;
