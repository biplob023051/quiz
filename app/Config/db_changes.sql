ALTER TABLE `choices` ADD `weight` TINYINT(2) NULL AFTER `points`;
ALTER TABLE `questions` ADD `weight` SMALLINT(4) NULL AFTER `explanation`;