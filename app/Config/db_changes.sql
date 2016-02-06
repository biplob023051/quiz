ALTER TABLE `choices` ADD `weight` TINYINT(2) NULL AFTER `points`;
ALTER TABLE `questions` ADD `weight` SMALLINT(4) NULL AFTER `explanation`;

-- New Field
ALTER TABLE `question_types` ADD `type` TINYINT(1) NULL COMMENT 'Null for regular type, 1 for others' AFTER `manual_scoring`;
INSERT INTO `question_types` (`id`, `name`, `description`, `answer_field`, `multiple_choices`, `template_name`, `manual_scoring`, `type`) VALUES (NULL, 'Header', '', '', '0', 'header', '0', '1');
