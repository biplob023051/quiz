ALTER TABLE `students` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `class`;
ALTER TABLE `questions` ADD `max_allowed` TINYINT(2) NULL DEFAULT NULL AFTER `weight`;