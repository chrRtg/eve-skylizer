SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `at_mining_ledger` (
  `ml_id` INT NOT NULL AUTO_INCREMENT,
  `structure_id` BIGINT(20) NOT NULL,
  `eve_userid` INT NOT NULL,
  `eve_corpid` INT NOT NULL,
  `goo_quantity` INT NOT NULL,
  `eve_invtypes_typeid` INT NOT NULL,
  `last_updated` DATETIME NOT NULL,
  PRIMARY KEY (`ml_id`),
  UNIQUE INDEX `idx_uniq` (`structure_id` ASC, `eve_userid` ASC, `last_updated` ASC, `eve_invtypes_typeid` ASC),
  INDEX `idx_struct` (`structure_id` ASC),
  INDEX `idx_user` (`eve_userid` ASC),
  INDEX `idx_goo` (`eve_invtypes_typeid` ASC),
  INDEX `idx_date` (`last_updated` ASC));

CREATE TABLE `at_mining_observer` (
  `emo_id` INT NOT NULL AUTO_INCREMENT,
  `structure_id` BIGINT(20) NOT NULL,
  `observer_type` VARCHAR(80) NOT NULL,
  `last_updated` DATETIME NULL,
  PRIMARY KEY (`emo_id`),
  INDEX `idx_struct` (`structure_id` ASC));

CREATE TABLE `skylizer`.`at_mining_period` (
  `amp_id` INT NOT NULL AUTO_INCREMENT,
  `structure_id` BIGINT(20) NOT NULL,
  `date_start` DATETIME NOT NULL,
  `date_end` DATETIME NULL,
  PRIMARY KEY (`amp_id`),
  UNIQUE INDEX `idx_uniq` (`structure_id` ASC, `date_start` ASC, `date_end` ASC));

SET FOREIGN_KEY_CHECKS = 1;