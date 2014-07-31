ALTER TABLE `tbl_users` COMMENT='' ENGINE='InnoDB';
ALTER TABLE `tbl_products` COMMENT='' ENGINE='InnoDB';

CREATE TABLE `tbl_wish_list` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` mediumint(8) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `type` varchar(16) NOT NULL DEFAULT 'public',
  `is_default` tinyint unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

ALTER TABLE `tbl_wish_list`
ADD FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


CREATE TABLE `tbl_wish_list_item` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `wish_list_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created` datetime NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

ALTER TABLE `tbl_wish_list_item`
ADD UNIQUE `wish_list_id_product_id` (`wish_list_id`, `product_id`);

ALTER TABLE `tbl_wish_list_item`
ADD FOREIGN KEY (`wish_list_id`) REFERENCES `tbl_wish_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_wish_list_item`
ADD FOREIGN KEY (`id`) REFERENCES `tbl_products` (`productID`) ON DELETE CASCADE ON UPDATE CASCADE;