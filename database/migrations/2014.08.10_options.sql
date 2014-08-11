ALTER TABLE `tbl_product_options` COMMENT='' ENGINE='InnoDB';
ALTER TABLE `tbl_product_options_values` COMMENT='' ENGINE='InnoDB';
ALTER TABLE `tbl_products_opt_val_variants` COMMENT='' ENGINE='InnoDB';

ALTER TABLE `tbl_product_options_values` CHANGE COLUMN `VariantID` `ID` INT(11) NOT NULL; 
ALTER TABLE `tbl_product_options_values` ADD PRIMARY KEY (`ID`);

ALTER TABLE `tbl_product_options_val` CHANGE COLUMN `OptionID` `variantID` INT(11) NOT NULL;

INSERT INTO `tbl_product_options` (optionID, name) VALUES ('', 'Color');
INSERT INTO `tbl_products_opt_val_variant` (variantID, optionID, name) VALUES ('', LAST_INSERT_ID(), 'Red'), ('', LAST_INSERT_ID(), 'Blue'), ('', LAST_INSERT_ID(), 'Green'), ('', LAST_INSERT_ID(), 'Yellow');

INSERT INTO `tbl_product_options` (optionID, name) VALUES ('', 'Brand');
INSERT INTO `tbl_products_opt_val_variant` (variantID, optionID, name) VALUES ('', LAST_INSERT_ID(), 'Brand1'), ('', LAST_INSERT_ID(), 'Brand2'), ('', LAST_INSERT_ID(), 'Brand3'), ('', LAST_INSERT_ID(), 'Brand4');

INSERT INTO `tbl_product_options` (optionID, name) VALUES ('', 'Quality');
INSERT INTO `tbl_products_opt_val_variant` (variantID, optionID, name) VALUES ('', LAST_INSERT_ID(), 'Brand New'), ('', LAST_INSERT_ID(), 'Used/Preowned'), ('', LAST_INSERT_ID(), 'Refurbished');

ALTER TABLE `tbl_product_options_values` CHANGE COLUMN `ID` `ID` INT(11) NOT NULL AUTO_INCREMENT; 

INSERT INTO `tbl_product_options_values` (ID, ProductID, variantID) VALUES ('', 7, 1);
INSERT INTO `tbl_product_options_values` (ID, ProductID, variantID) VALUES ('', 7, 7);
INSERT INTO `tbl_product_options_values` (ID, ProductID, variantID) VALUES ('', 7, 9);

INSERT INTO `tbl_product_options_values` (ID, ProductID, variantID) VALUES ('', 68, 2);
INSERT INTO `tbl_product_options_values` (ID, ProductID, variantID) VALUES ('', 68, 6);
INSERT INTO `tbl_product_options_values` (ID, ProductID, variantID) VALUES ('', 68, 11);

INSERT INTO `tbl_product_options_values` (ID, ProductID, variantID) VALUES ('', 72, 4);
INSERT INTO `tbl_product_options_values` (ID, ProductID, variantID) VALUES ('', 72, 5);
INSERT INTO `tbl_product_options_values` (ID, ProductID, variantID) VALUES ('', 72, 10);

ALTER TABLE `tbl_product_options_values` ADD INDEX (`ProductID`);
ALTER TABLE `tbl_product_options_values` ADD INDEX (`ProductID`);