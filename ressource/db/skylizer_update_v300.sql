SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `at_mining_ledger` (
  `ml_id` int(11) NOT NULL AUTO_INCREMENT,
  `structure_id` bigint(20) NOT NULL,
  `eve_userid` int(11) NOT NULL,
  `eve_corpid` int(11) NOT NULL,
  `goo_quantity` int(11) NOT NULL,
  `eve_invtypes_typeid` int(11) NOT NULL,
  `celestial_id` int(11) NOT NULL,
  `structure_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `basePrice` decimal(19,4) NOT NULL,
  `refinedPrice` decimal(19,4) NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`ml_id`),
  UNIQUE KEY `idx_uniq` (`structure_id`,`eve_userid`,`last_updated`,`eve_invtypes_typeid`),
  KEY `idx_struct` (`structure_id`),
  KEY `idx_user` (`eve_userid`),
  KEY `idx_goo` (`eve_invtypes_typeid`),
  KEY `idx_date` (`last_updated`),
  KEY `idx_celestial_id` (`celestial_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10793 DEFAULT CHARSET=utf8;



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