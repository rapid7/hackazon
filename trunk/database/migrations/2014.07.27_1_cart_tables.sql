DROP TABLE IF EXISTS `tbl_ordered_carts`;
DROP TABLE IF EXISTS `tbl_cart_items`;
DROP TABLE IF EXISTS `tbl_cart`;
CREATE  TABLE `tbl_cart` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT ,
  `created_at` timestamp default '0000-00-00 00:00:00', 
  `updated_at` timestamp default now() on update now(),
  `items_count` int(10) unsigned DEFAULT '0',
  `items_qty` int(10) unsigned DEFAULT '0',
  `total_price` decimal(12,4) DEFAULT '0.0000',
  `uid` varchar(255) DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT '0',
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_is_guest` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE  TABLE `tbl_cart_items` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT ,
  `cart_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp default '0000-00-00 00:00:00', 
  `updated_at` timestamp default now() on update now(),
  `product_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `qty` int(10) unsigned DEFAULT '0',
  `price` decimal(12,4) DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_CART_ID` FOREIGN KEY (`cart_id`) REFERENCES `tbl_cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;