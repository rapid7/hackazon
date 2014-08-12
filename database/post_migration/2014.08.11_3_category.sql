INSERT INTO `tbl_category_product` (`productID`,`CategoryID`) SELECT t.ProductID, t.CategoryID FROM tbl_products as t;
SET @tmp := (SELECT rebuild_nested_set_tree());