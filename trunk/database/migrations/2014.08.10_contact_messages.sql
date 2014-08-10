CREATE  TABLE `tbl_contact_messages` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT ,
  `created_at` timestamp default now(),
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text,
  `customer_id` int(10),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;