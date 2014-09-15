ALTER TABLE `tbl_roles`
ADD `removable` tinyint(1) unsigned NOT NULL DEFAULT '1',
COMMENT='';

UPDATE `tbl_roles`
SET `removable` = 0 WHERE `name` IN ('user', 'admin');