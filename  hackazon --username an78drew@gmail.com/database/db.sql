CREATE TABLE IF NOT EXISTS `tbl_users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(64) NOT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `email` varchar(80) NOT NULL,
  `oauth_provider` varchar(10) DEFAULT NULL,
  `oauth_uid` text,
  `created_on` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT '1',
  `recover_passw` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE `tbl_brand` (
  `brandID` int(11) NOT NULL auto_increment,
  `name` varchar(150) default NULL,
  `comment` varchar(50) default NULL,
  `Pict` varchar(255) default NULL,
  `description` longtext,
  `brief` text,
  `meta_title` varchar(255) default NULL,
  `meta_keywords` varchar(255) default NULL,
  `meta_desc` varchar(255) default NULL,
  `hurl` varchar(255) default NULL,
  `canonical` varchar(255) default NULL,
  PRIMARY KEY  (`brandID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


CREATE TABLE `tbl_categories` (
  `categoryID` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `parent` int(11) default NULL,
  `products_count` int(11) default NULL,
  `description` longtext,
  `picture` varchar(255) default NULL,
  `products_count_admin` int(11) default NULL,
  `about` text,
  `enabled` int(11) default NULL,
  `meta_title` varchar(255) default NULL,
  `meta_keywords` varchar(255) default NULL,
  `meta_desc` varchar(255) default NULL,
  `hurl` varchar(255) default NULL,
  `canonical` varchar(255) default NULL,
  `h1` varchar(255) default NULL,
  `hidden` int(1) NOT NULL default '0',
  PRIMARY KEY  (`categoryID`),
  KEY `root_category` (`parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;


CREATE TABLE `tbl_category_product` (
  `id` int(11) NOT NULL auto_increment,
  `productID` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;


CREATE TABLE `tbl_currency_types` (
  `CID` int(11) NOT NULL auto_increment,
  `Name` varchar(30) default NULL,
  `code` varchar(7) default NULL,
  `currency_value` float default NULL,
  `where2show` int(11) default NULL,
  `sort_order` int(11) default '0',
  `currency_iso_3` char(3) default NULL,
  PRIMARY KEY  (`CID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


CREATE TABLE `tbl_customers` (
  `custID` int(11) NOT NULL auto_increment,
  `cust_password` varchar(255) default NULL,
  `cust_email` varchar(130) default NULL,
  `cust_firstname` varchar(30) default NULL,
  `cust_lastname` varchar(30) default NULL,
  `cust_country` varchar(30) default NULL,
  `cust_zip` varchar(30) default NULL,
  `cust_city` varchar(230) default NULL,
  `cust_address` varchar(200) default NULL,
  `cust_phone` varchar(30) default NULL,
  `openID` varchar(20) default NULL,
  `provider` varchar(30) default NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`custID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `tbl_manager` (
  `ID` int(11) NOT NULL auto_increment,
  `manager` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `access` int(11) default NULL,
  `email` varchar(255) default NULL,
  `online_type` int(11) default NULL,
  `online_num` varchar(255) default NULL,
  `online_name` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `tbl_news` (
  `id` int(11) NOT NULL auto_increment,
  `date` date NOT NULL,
  `title` varchar(255) default NULL,
  `text` text,
  `brief` text,
  `Pict` varchar(50) default NULL,
  `enable` int(11) default NULL,
  `meta_title` varchar(255) default NULL,
  `meta_keywords` varchar(255) default NULL,
  `meta_desc` varchar(255) default NULL,
  `hurl` varchar(255) default NULL,
  `canonical` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


CREATE TABLE `tbl_ordered_carts` (
  `id` int(11) NOT NULL auto_increment,
  `productID` varchar(20) NOT NULL,
  `orderID` int(11) NOT NULL,
  `name` char(255) default NULL,
  `Price` float default NULL,
  `Quantity` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `tbl_orders` (
  `orderID` int(11) NOT NULL auto_increment,
  `order_time` datetime default NULL,
  `cust_firstname` varchar(60) default NULL,
  `cust_lastname` varchar(60) default NULL,
  `cust_email` varchar(255) default NULL,
  `cust_country` varchar(30) default NULL,
  `cust_zip` varchar(30) default NULL,
  `cust_state` varchar(30) default NULL,
  `cust_city` varchar(70) default NULL,
  `cust_address` longtext,
  `cust_phone` varchar(30) default NULL,
  `status` int(2) NOT NULL default '0',
  `comment` text,
  `manager` int(11) default NULL,
  `custID` int(11) default NULL,
  PRIMARY KEY  (`orderID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `tbl_order_status` (
  `statusID` int(11) NOT NULL auto_increment,
  `status_name` varchar(90) default NULL,
  `sort_order` int(11) default NULL,
  PRIMARY KEY  (`statusID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `tbl_payment` (
  `payID` int(11) NOT NULL auto_increment,
  `type` varchar(255) default NULL,
  `enabled` tinyint(1) default NULL,
  PRIMARY KEY  (`payID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE `tbl_payoption` (
  `id` int(11) NOT NULL auto_increment,
  `payID` int(11) default NULL,
  `payoption` varchar(255) default NULL,
  `payvalue` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE `tbl_products` (
  `productID` int(11) NOT NULL auto_increment,
  `categoryID` int(11) default NULL,
  `name` varchar(255) default NULL,
  `description` longtext,
  `customers_rating` float NOT NULL,
  `Price` float default NULL,
  `picture` varchar(255) default NULL,
  `in_stock` int(11) default NULL,
  `thumbnail` varchar(255) default NULL,
  `customer_votes` int(11) NOT NULL,
  `items_sold` int(11) NOT NULL,
  `big_picture` varchar(255) default NULL,
  `enabled` int(11) NOT NULL,
  `brief_description` longtext,
  `list_price` float default NULL,
  `product_code` char(25) default NULL,
  `hurl` varchar(255) default NULL,
  `accompanyID` varchar(150) default NULL,
  `brandID` int(11) default NULL,
  `meta_title` varchar(255) default NULL,
  `meta_keywords` varchar(255) default NULL,
  `meta_desc` varchar(255) default NULL,
  `canonical` varchar(255) default NULL,
  `h1` varchar(255) default NULL,
  `yml` int(1) default '1',
  `min_qunatity` int(11) default '1',
  `managerID` int(11) default NULL,
  PRIMARY KEY  (`productID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

CREATE TABLE `tbl_products_opt_val_variants` (
  `variantID` int(11) NOT NULL auto_increment,
  `optionID` int(11) NOT NULL,
  `name` varchar(255) default NULL,
  `sort_order` int(11) default '0',
  PRIMARY KEY  (`variantID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE `tbl_product_options` (
  `optionID` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `sort_order` int(11) default '0',
  PRIMARY KEY  (`optionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `tbl_product_options_values` (
  `variantID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `optionID` int(11) NOT NULL,
  `price_surplus` float NOT NULL default '0',
  `default` int(11) NOT NULL default '0',
  `picture` varchar(255) default NULL,
  `count` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `tbl_review` (
  `reviewID` int(11) NOT NULL auto_increment,
  `productID` int(11) default NULL,
  `username` varchar(50) default NULL,
  `email` varchar(50) default NULL,
  `review` text,
  `date_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `moder` int(11) NOT NULL default '0',
  PRIMARY KEY  (`reviewID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `tbl_share` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(25) default NULL,
  `type_val` int(11) default NULL,
  `value` float default NULL,
  `code` varchar(150) default NULL,
  `default` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `tbl_special_offers` (
  `offerID` int(11) NOT NULL auto_increment,
  `productID` int(11) default NULL,
  `sort_order` int(11) default NULL,
  PRIMARY KEY  (`offerID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `tbl_tags` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) default NULL,
  `tag` varchar(30) default NULL,
  `hurl` varchar(255) default NULL,
  `canonical` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `tbl_thumb` (
  `thumbID` int(11) NOT NULL auto_increment,
  `productID` int(11) NOT NULL,
  `picture` varchar(150) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`thumbID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;


CREATE TABLE `tbl_votes` (
  `votesID` int(11) NOT NULL auto_increment,
  `title` varchar(50) default NULL,
  `enable` int(11) default NULL,
  PRIMARY KEY  (`votesID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `tbl_votes_content` (
  `votesID` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  `question` varchar(50) default NULL,
  `result` int(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
