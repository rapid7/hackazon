ALTER TABLE `tbl_product_options` COMMENT='' ENGINE='InnoDB';
ALTER TABLE `tbl_product_options_values` COMMENT='' ENGINE='InnoDB';
ALTER TABLE `tbl_products_opt_val_variants` COMMENT='' ENGINE='InnoDB';

ALTER TABLE `tbl_product_options_values` CHANGE COLUMN `VariantID` `ID` INT(11) NOT NULL; 
ALTER TABLE `tbl_product_options_values` ADD PRIMARY KEY (`ID`);

ALTER TABLE `tbl_product_options_val` CHANGE COLUMN `OptionID` `variantID` INT(11) NOT NULL;

ALTER TABLE `tbl_product_options_values` ADD INDEX (`productID`);
