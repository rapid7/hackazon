ALTER TABLE tbl_orders ENGINE=InnoDB;
ALTER TABLE tbl_order_address ENGINE=InnoDB;
ALTER TABLE tbl_orders ENGINE=InnoDB;
ALTER TABLE tbl_order_address CHANGE `customerId` `customer_id` INT( 11 ) NULL DEFAULT NULL;
ALTER TABLE `tbl_orders` CHANGE `customerId` `customer_id` INT( 11 ) NULL DEFAULT NULL;
ALTER TABLE  `tbl_order_items` ADD `order_id` INT NOT NULL