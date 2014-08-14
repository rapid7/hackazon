ALTER TABLE `tbl_users`
ADD `first_name` varchar(64) COLLATE 'utf8_general_ci' NULL AFTER `password`,
ADD `last_name` varchar(64) COLLATE 'utf8_general_ci' NULL AFTER `first_name`,
COMMENT='';
