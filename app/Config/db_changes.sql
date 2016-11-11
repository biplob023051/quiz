ALTER TABLE `users` ADD `activation` VARCHAR(255) NULL AFTER `resettime`;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `field` varchar(255) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `settings` (`id`, `field`, `value`) VALUES
(1, 'alert_message', 'The site is going to be offline at {datetime}, time left {time}'),
(2, 'visible', ''),
(3, 'offline_message', 'The site is offline now for maintenance.'),
(4, 'offline_status', ''),
(5, 'maintenance_time', '06/30/2016 2:55 PM');

ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE  `questions` ADD  `case_sensitive` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '1 for case sensitive' AFTER  `max_allowed`


-- New works
ALTER TABLE `users` ADD `subjects` TEXT NULL AFTER `language`;
ALTER TABLE `quizzes` ADD `subjects` TEXT NULL AFTER `anonymous`;
ALTER TABLE `quizzes` ADD `classes` TEXT NULL AFTER `subjects`;

ALTER TABLE `quizzes` ADD `shared` TINYINT(1) NULL DEFAULT NULL COMMENT '1 for shared' AFTER `classes`;

ALTER TABLE `quizzes` ADD `is_approve` TINYINT(2) NULL DEFAULT NULL COMMENT '1 for approve, 2 for declined' AFTER `shared`;

ALTER TABLE `quizzes` ADD `comment` TEXT NULL AFTER `is_approve`;

-- Modification work
ALTER TABLE `users` ADD `imported_ids` TEXT NULL AFTER `activation`;