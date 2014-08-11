ALTER TABLE `tbl_orders` ADD `payment_method` VARCHAR(20) default NULL;
ALTER TABLE `tbl_orders` ADD `shipping_method` VARCHAR(20) default NULL;

CREATE TABLE `tbl_wishlist_followers` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` mediumint(8) unsigned NOT NULL,
 `follower_id` mediumint(8) unsigned NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `followers` (`user_id`,`follower_id`),
 KEY `user_id` (`user_id`),
 KEY `tbl_follower_id_ibfk_2` (`follower_id`),
 CONSTRAINT `tbl_user_id_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
 CONSTRAINT `tbl_follower_id_ibfk_2` FOREIGN KEY (`follower_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8