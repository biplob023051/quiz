ALTER TABLE `choices` ADD `weight` TINYINT(2) NULL AFTER `points`;
ALTER TABLE `questions` ADD `weight` SMALLINT(4) NULL AFTER `explanation`;

-- New Field
ALTER TABLE `question_types` ADD `type` TINYINT(1) NULL COMMENT 'Null for regular type, 1 for others' AFTER `manual_scoring`;
INSERT INTO `question_types` (`id`, `name`, `description`, `answer_field`, `multiple_choices`, `template_name`, `manual_scoring`, `type`) VALUES 
(6, 'Header', '', '', '0', 'header', '0', '1'),
(7, 'Youtube Video', '', '', 0, 'youtube_video', 0, 1),
(8, 'Web image', '', '', 0, 'image_url', 0, 1);

-- New changes on 23
ALTER TABLE `choices` CHANGE `text` `text` TINYTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
