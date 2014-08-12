INSERT INTO `tbl_categories` (categoryID, name, parent) VALUES ('', 'ROOT', null);
UPDATE `tbl_categories` SET parent = LAST_INSERT_ID() WHERE parent=0;
