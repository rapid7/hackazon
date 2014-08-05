ALTER TABLE `tbl_cart` DROP `method`;
ALTER TABLE `tbl_cart` ADD `payment_method` VARCHAR(20) default NULL;
ALTER TABLE `tbl_cart` ADD `shipping_method` VARCHAR(20) default NULL;
ALTER TABLE `tbl_cart` ADD `shipping_address_id` int(11) default 0;
ALTER TABLE `tbl_cart` ADD `billing_address_id` int(11) default 0;
ALTER TABLE `tbl_cart` ADD `last_step` tinyint(1) default 0;
