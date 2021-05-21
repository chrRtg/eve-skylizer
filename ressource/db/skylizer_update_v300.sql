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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE `at_mining_observer` (
  `emo_id` int(11) NOT NULL AUTO_INCREMENT,
  `structure_id` bigint(20) NOT NULL,
  `observer_type` varchar(80) NOT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`emo_id`),
  KEY `idx_struct` (`structure_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE `at_mining_period` (
  `amp_id` int(11) NOT NULL AUTO_INCREMENT,
  `structure_id` bigint(20) NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime DEFAULT NULL,
  PRIMARY KEY (`amp_id`),
  UNIQUE KEY `idx_combined` (`structure_id`,`date_start`,`date_end`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=latin1;



SET FOREIGN_KEY_CHECKS = 1;