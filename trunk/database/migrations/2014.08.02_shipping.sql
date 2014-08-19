ALTER TABLE `tbl_cart` ADD `method` VARCHAR(20) default NULL;
CREATE TABLE `tbl_customer_address` (
  `id` int(11) NOT NULL auto_increment,
  `full_name` varchar(60) default NULL,
  `address_line_1` varchar(60) default NULL,
  `address_line_2` varchar(60) default NULL,
  `city` varchar(60) default NULL,
  `region` varchar(60) default NULL,
  `zip` varchar(60) default NULL,
  `country_id` varchar(60) default NULL,
  `phone` varchar(60) default NULL,
  `customer_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;


DROP TABLE IF EXISTS `tbl_orders`;
CREATE TABLE `tbl_orders` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `created_at` timestamp default '0000-00-00 00:00:00',
  `updated_at` timestamp default now() on update now(),
  `customer_firstname` varchar(60) default NULL,
  `customer_lastname` varchar(60) default NULL,
  `customer_email` varchar(255) default NULL,
  `status` varchar(60) NOT NULL default '0',
  `comment` text,
  `customer_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE `tbl_order_address` (
  `id` int(11) NOT NULL auto_increment,
  `full_name` varchar(60) default NULL,
  `address_line_1` varchar(60) default NULL,
  `address_line_2` varchar(60) default NULL,
  `city` varchar(60) default NULL,
  `region` varchar(60) default NULL,
  `zip` varchar(60) default NULL,
  `country_id` varchar(60) default NULL,
  `phone` varchar(60) default NULL,
  `customerId` int(11) default NULL,
  `address_type` varchar(20) default NULL,
  `order_id` int(11) UNSIGNED default NULL,
  PRIMARY KEY  (`id`),
  CONSTRAINT `FK_ORDER_ID` FOREIGN KEY (`order_id`) REFERENCES `tbl_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;


CREATE  TABLE `tbl_order_items` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT ,
  `cart_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp default '0000-00-00 00:00:00',
  `updated_at` timestamp default now() on update now(),
  `product_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `qty` int(10) unsigned DEFAULT '0',
  `price` decimal(12,4) DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

