ALTER TABLE `users` ADD `location_id` INT NULL AFTER `active_flag`;

ALTER TABLE `users` ADD `pwd_reset_link` VARCHAR(100) NULL AFTER `location_id`, ADD `reset_link_date` DATE NULL AFTER `pwd_reset_link`;

ALTER TABLE `session` ADD `session_ip` VARCHAR(20) NULL AFTER `session_type`;