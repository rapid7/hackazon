ALTER TABLE `tbl_categories` ADD `lpos` INT(11) default NULL;
ALTER TABLE `tbl_categories` ADD `rpos` INT(11) default NULL;
ALTER TABLE `tbl_categories` ADD `depth` INT(11) default NULL;

ALTER TABLE `tbl_review` COMMENT='' ENGINE='InnoDB';
ALTER TABLE `tbl_categories` COMMENT='' ENGINE='InnoDB';
ALTER TABLE `tbl_category_product` COMMENT='' ENGINE='InnoDB';

