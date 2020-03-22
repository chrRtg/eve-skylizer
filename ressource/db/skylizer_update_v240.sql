SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `at_structure_services` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `structure_id` INT NOT NULL,
  `service` VARCHAR(120) NOT NULL,
  `state` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_structure_id` (`structure_id` ASC),
  INDEX `idx_service` (`service` ASC),
  INDEX `idx_state` (`state` ASC));


SET FOREIGN_KEY_CHECKS = 1;
