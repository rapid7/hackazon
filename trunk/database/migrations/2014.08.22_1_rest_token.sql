ALTER TABLE `tbl_users`
ADD `rest_token` varchar(40) COLLATE 'utf8_general_ci' NULL,
COMMENT='';

ALTER TABLE `tbl_users`
ADD INDEX `rest_token` (`rest_token`(8));