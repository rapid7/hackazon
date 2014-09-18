DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE `tbl_users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(64) NOT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `email` varchar(80) NOT NULL,
  `oauth_provider` varchar(10) DEFAULT NULL,
  `oauth_uid` text,
  `created_on` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT '1',
  `recover_passw` varchar(32) DEFAULT NULL,
  `rest_token` varchar(40) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `rest_token` (`rest_token`(8))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_brand`;
CREATE TABLE `tbl_brand` (
  `brandID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `comment` varchar(50) DEFAULT NULL,
  `Pict` varchar(255) DEFAULT NULL,
  `description` longtext,
  `brief` text,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `hurl` varchar(255) DEFAULT NULL,
  `canonical` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`brandID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_cart`;
CREATE TABLE `tbl_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `items_count` int(10) unsigned DEFAULT '0',
  `items_qty` int(10) unsigned DEFAULT '0',
  `total_price` decimal(12,4) DEFAULT '0.0000',
  `uid` varchar(255) DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT '0',
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_is_guest` smallint(5) unsigned DEFAULT '0',
  `payment_method` varchar(20) DEFAULT NULL,
  `shipping_method` varchar(20) DEFAULT NULL,
  `shipping_address_id` int(11) DEFAULT '0',
  `billing_address_id` int(11) DEFAULT '0',
  `last_step` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_cart_items`;
CREATE TABLE `tbl_cart_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `product_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `qty` int(10) unsigned DEFAULT '0',
  `price` decimal(12,4) DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  KEY `FK_CART_ID` (`cart_id`),
  CONSTRAINT `FK_CART_ID` FOREIGN KEY (`cart_id`) REFERENCES `tbl_cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_categories`;
CREATE TABLE `tbl_categories` (
  `categoryID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `products_count` int(11) DEFAULT NULL,
  `description` longtext,
  `picture` varchar(255) DEFAULT NULL,
  `products_count_admin` int(11) DEFAULT NULL,
  `about` text,
  `enabled` int(11) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `hurl` varchar(255) DEFAULT NULL,
  `canonical` varchar(255) DEFAULT NULL,
  `h1` varchar(255) DEFAULT NULL,
  `hidden` int(1) NOT NULL DEFAULT '0',
  `lpos` int(11) DEFAULT NULL,
  `rpos` int(11) DEFAULT NULL,
  `depth` int(11) DEFAULT NULL,
  PRIMARY KEY (`categoryID`),
  KEY `root_category` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `tbl_category_product`;
CREATE TABLE `tbl_category_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productID` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=395 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_contact_messages`;
CREATE TABLE `tbl_contact_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text,
  `customer_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_currency_types`;
CREATE TABLE `tbl_currency_types` (
  `CID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) DEFAULT NULL,
  `code` varchar(7) DEFAULT NULL,
  `currency_value` float DEFAULT NULL,
  `where2show` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `currency_iso_3` char(3) DEFAULT NULL,
  PRIMARY KEY (`CID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_customer_address`;
CREATE TABLE `tbl_customer_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(60) DEFAULT NULL,
  `address_line_1` varchar(60) DEFAULT NULL,
  `address_line_2` varchar(60) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `region` varchar(60) DEFAULT NULL,
  `zip` varchar(60) DEFAULT NULL,
  `country_id` varchar(60) DEFAULT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_customers`;
CREATE TABLE `tbl_customers` (
  `custID` int(11) NOT NULL AUTO_INCREMENT,
  `cust_password` varchar(255) DEFAULT NULL,
  `cust_email` varchar(130) DEFAULT NULL,
  `cust_firstname` varchar(30) DEFAULT NULL,
  `cust_lastname` varchar(30) DEFAULT NULL,
  `cust_country` varchar(30) DEFAULT NULL,
  `cust_zip` varchar(30) DEFAULT NULL,
  `cust_city` varchar(230) DEFAULT NULL,
  `cust_address` varchar(200) DEFAULT NULL,
  `cust_phone` varchar(30) DEFAULT NULL,
  `openID` varchar(20) DEFAULT NULL,
  `provider` varchar(30) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`custID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_enquiries`;
CREATE TABLE `tbl_enquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` mediumint(8) unsigned NOT NULL,
  `assigned_to` mediumint(8) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'new',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `assigned_to` (`assigned_to`),
  CONSTRAINT `tbl_enquiries_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `tbl_enquiries_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_enquiry_messages`;
CREATE TABLE `tbl_enquiry_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enquiry_id` int(11) NOT NULL,
  `author_id` mediumint(8) unsigned DEFAULT NULL,
  `message` text NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `enquiry_id` (`enquiry_id`),
  CONSTRAINT `tbl_enquiry_messages_ibfk_2` FOREIGN KEY (`enquiry_id`) REFERENCES `tbl_enquiries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_enquiry_messages_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_faq`;
CREATE TABLE `tbl_faq` (
  `faqID` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `answer` text,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`faqID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_files`;
CREATE TABLE `tbl_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tbl_files_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_manager`;
CREATE TABLE `tbl_manager` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `manager` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `access` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `online_type` int(11) DEFAULT NULL,
  `online_num` varchar(255) DEFAULT NULL,
  `online_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_news`;
CREATE TABLE `tbl_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `text` text,
  `brief` text,
  `Pict` varchar(50) DEFAULT NULL,
  `enable` int(11) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `hurl` varchar(255) DEFAULT NULL,
  `canonical` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tbl_orders`;
CREATE TABLE `tbl_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `customer_firstname` varchar(60) DEFAULT NULL,
  `customer_lastname` varchar(60) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `status` varchar(60) NOT NULL DEFAULT '0',
  `comment` text,
  `customer_id` int(11) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `shipping_method` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tbl_order_address`;
CREATE TABLE `tbl_order_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(60) DEFAULT NULL,
  `address_line_1` varchar(60) DEFAULT NULL,
  `address_line_2` varchar(60) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `region` varchar(60) DEFAULT NULL,
  `zip` varchar(60) DEFAULT NULL,
  `country_id` varchar(60) DEFAULT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `address_type` varchar(20) DEFAULT NULL,
  `order_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ORDER_ID` (`order_id`),
  CONSTRAINT `FK_ORDER_ID` FOREIGN KEY (`order_id`) REFERENCES `tbl_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tbl_order_items`;
CREATE TABLE `tbl_order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `product_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `qty` int(10) unsigned DEFAULT '0',
  `price` decimal(12,4) DEFAULT '0.0000',
  `order_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_order_status`;
CREATE TABLE `tbl_order_status` (
  `statusID` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(90) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`statusID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tbl_payment`;
CREATE TABLE `tbl_payment` (
  `payID` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`payID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_payoption`;
CREATE TABLE `tbl_payoption` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payID` int(11) DEFAULT NULL,
  `payoption` varchar(255) DEFAULT NULL,
  `payvalue` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_product_options`;
CREATE TABLE `tbl_product_options` (
  `optionID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  PRIMARY KEY (`optionID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_product_options_values`;
CREATE TABLE `tbl_product_options_values` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `productID` int(11) NOT NULL,
  `variantID` int(11) NOT NULL,
  `price_surplus` float NOT NULL DEFAULT '0',
  `default` int(11) NOT NULL DEFAULT '0',
  `picture` varchar(255) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `productID` (`productID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_products`;
CREATE TABLE `tbl_products` (
  `productID` int(11) NOT NULL AUTO_INCREMENT,
  `categoryID` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` longtext,
  `customers_rating` float NOT NULL,
  `Price` float DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `in_stock` int(11) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `customer_votes` int(11) NOT NULL,
  `items_sold` int(11) NOT NULL,
  `big_picture` varchar(255) DEFAULT NULL,
  `enabled` int(11) NOT NULL,
  `brief_description` longtext,
  `list_price` float DEFAULT NULL,
  `product_code` char(25) DEFAULT NULL,
  `hurl` varchar(255) DEFAULT NULL,
  `accompanyID` varchar(150) DEFAULT NULL,
  `brandID` int(11) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `canonical` varchar(255) DEFAULT NULL,
  `h1` varchar(255) DEFAULT NULL,
  `yml` int(1) DEFAULT '1',
  `min_qunatity` int(11) DEFAULT '1',
  `managerID` int(11) DEFAULT NULL,
  PRIMARY KEY (`productID`)
) ENGINE=InnoDB AUTO_INCREMENT=211 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_products_opt_val_variants`;
CREATE TABLE `tbl_products_opt_val_variants` (
  `variantID` int(11) NOT NULL AUTO_INCREMENT,
  `optionID` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  PRIMARY KEY (`variantID`),
  KEY `optionID` (`optionID`),
  CONSTRAINT `tbl_products_opt_val_variants_ibfk_1` FOREIGN KEY (`optionID`) REFERENCES `tbl_product_options` (`optionID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_review`;
CREATE TABLE `tbl_review` (
  `reviewID` int(11) NOT NULL AUTO_INCREMENT,
  `productID` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `review` text,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `moder` int(11) NOT NULL DEFAULT '0',
  `rating` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`reviewID`)
) ENGINE=InnoDB AUTO_INCREMENT=501 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_roles`;
CREATE TABLE `tbl_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `removable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_share`;
CREATE TABLE `tbl_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(25) DEFAULT NULL,
  `type_val` int(11) DEFAULT NULL,
  `value` float DEFAULT NULL,
  `code` varchar(150) DEFAULT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_special_offers`;
CREATE TABLE `tbl_special_offers` (
  `offerID` int(11) NOT NULL AUTO_INCREMENT,
  `productID` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`offerID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_tags`;
CREATE TABLE `tbl_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `tag` varchar(30) DEFAULT NULL,
  `hurl` varchar(255) DEFAULT NULL,
  `canonical` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_thumb`;
CREATE TABLE `tbl_thumb` (
  `thumbID` int(11) NOT NULL AUTO_INCREMENT,
  `productID` int(11) NOT NULL,
  `picture` varchar(150) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`thumbID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `tbl_users_roles`;
CREATE TABLE `tbl_users_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `tbl_users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `tbl_roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_votes`;
CREATE TABLE `tbl_votes` (
  `votesID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `enable` int(11) DEFAULT NULL,
  PRIMARY KEY (`votesID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_votes_content`;
CREATE TABLE `tbl_votes_content` (
  `votesID` int(11) DEFAULT NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(50) DEFAULT NULL,
  `result` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_wish_list`;
CREATE TABLE `tbl_wish_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `type` varchar(16) NOT NULL DEFAULT 'public',
  `is_default` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tbl_wish_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_wish_list_item`;
CREATE TABLE `tbl_wish_list_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wish_list_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wish_list_id_product_id` (`wish_list_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `tbl_wish_list_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`productID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_wish_list_item_ibfk_1` FOREIGN KEY (`wish_list_id`) REFERENCES `tbl_wish_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tbl_wishlist_followers`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;