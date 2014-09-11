DELETE FROM hackazon.tbl_categories where categoryID not in
(select categoryID from tbl_category_product) and parent > 1;