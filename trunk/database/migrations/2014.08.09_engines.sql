ALTER TABLE tbl_orders ENGINE=InnoDB;
ALTER TABLE tbl_order_address ENGINE=InnoDB;
ALTER TABLE tbl_order_status ENGINE=InnoDB;
ALTER TABLE tbl_payment ENGINE=InnoDB;
ALTER TABLE tbl_payoption ENGINE=InnoDB;
ALTER TABLE tbl_order_address CHANGE `customerId` `customer_id` INT( 11 ) NULL DEFAULT NULL;
#ALTER TABLE `tbl_orders` CHANGE `customerId` `customer_id` INT( 11 ) NULL DEFAULT NULL;
