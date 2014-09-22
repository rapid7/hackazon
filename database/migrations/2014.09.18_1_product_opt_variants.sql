ALTER TABLE `tbl_product_options_values`
ADD FOREIGN KEY (`productID`) REFERENCES `tbl_products` (`productID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_product_options_values`
ADD FOREIGN KEY (`variantID`) REFERENCES `tbl_products_opt_val_variants` (`variantID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_product_options_values`
ADD UNIQUE `productID_variantID` (`productID`, `variantID`);