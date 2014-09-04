DELETE FROM tbl_category_product;

INSERT INTO tbl_category_product (productID, categoryID)
  SELECT productID, categoryID
  FROM tbl_products;