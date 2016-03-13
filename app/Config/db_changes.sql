ALTER TABLE `students` ADD `status` TINYINT(1) NULL DEFAULT NULL COMMENT 'Null for incomplete, 1 for complete' AFTER `class`;
ALTER TABLE `questions` ADD `max_allowed` TINYINT(2) NULL DEFAULT NULL AFTER `weight`;

UPDATE students SET `status`=1;