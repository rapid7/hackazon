
INSERT INTO `tbl_wish_list` (`id`,`user_id`,`name`,`type`,`is_default`,`created`,`modified`) VALUES
(1,1, 'Test Wishlist 1', 'public', 1, '2014-09-16 20:22:25', NULL),
(2,1, 'Test Wishlist 2', 'public', 0, '2014-09-16 20:22:25', NULL);

INSERT INTO `tbl_wish_list_item` (`id`,`wish_list_id`,`product_id`,`created`) VALUES
(1, 1, 101, '2014-09-16 20:40:45'),
(2, 1, 64, '2014-09-16 20:40:45'),
(3, 1, 89, '2014-09-16 20:40:45'),
(4, 2, 167, '2014-09-16 20:40:45'),
(5, 2, 104, '2014-09-16 20:40:45');
