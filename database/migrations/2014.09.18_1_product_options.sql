ALTER TABLE `tbl_products_opt_val_variants`
ADD FOREIGN KEY (`optionID`) REFERENCES `tbl_product_options` (`optionID`) ON DELETE CASCADE ON UPDATE CASCADE;