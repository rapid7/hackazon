INSERT INTO `tbl_orders` (`id`, `created_at`, `updated_at`, `customer_firstname`, `customer_lastname`, `customer_email`, `status`, `comment`, `customer_id`, `payment_method`, `shipping_method`) VALUES
  (7,	'2014-09-15 17:41:25',	'2014-09-15 20:41:25',	'admin',	NULL,	'admin@hackazon.com',	'complete',	NULL,	2,	'wire transfer',	'mail'),
  (8,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	'test_user',	NULL,	'test_user@example.com',	'complete',	NULL,	1,	'paypal',	'express'),
  (9,	'2014-09-15 17:44:54',	'2014-09-15 20:44:54',	'test_user',	NULL,	'test_user@example.com',	'complete',	NULL,	1,	'wire transfer',	'mail'),
  (10,	'2014-09-15 17:45:16',	'2014-09-15 20:45:16',	'test_user',	NULL,	'test_user@example.com',	'complete',	NULL,	1,	'wire transfer',	'mail');

INSERT INTO `tbl_order_address` (`id`, `full_name`, `address_line_1`, `address_line_2`, `city`, `region`, `zip`, `country_id`, `phone`, `customer_id`, `address_type`, `order_id`) VALUES
  (13,	'Vasya Petrov',	'Star street, 666',	'',	'Inkograd',	'Buryatia',	'666666',	'RU',	'',	2,	'shipping',	7),
  (14,	'Vasya Petrov',	'Star street, 666',	'',	'Inkograd',	'Buryatia',	'666666',	'RU',	'',	2,	'billing',	7),
  (15,	'Nikita',	'Minnaya, 10',	'',	'Moskow',	'Moscow',	'123456',	'RU',	'',	1,	'shipping',	8),
  (16,	'Nikita',	'Minnaya, 10',	'',	'Moskow',	'Moscow',	'123456',	'RU',	'',	1,	'billing',	8),
  (17,	'Nikita',	'Minnaya, 10',	'',	'Moskow',	'Moscow',	'123456',	'RU',	'',	1,	'shipping',	9),
  (18,	'Nikita',	'Minnaya, 10',	'',	'Moskow',	'Moscow',	'123456',	'RU',	'',	1,	'billing',	9),
  (19,	'Nikita',	'Minnaya, 10',	'',	'Moskow',	'Moscow',	'123456',	'RU',	'',	1,	'shipping',	10),
  (20,	'Nikita',	'Minnaya, 10',	'',	'Moskow',	'Moscow',	'123456',	'RU',	'',	1,	'billing',	10);

INSERT INTO `tbl_order_items` (`id`, `cart_id`, `created_at`, `updated_at`, `product_id`, `name`, `qty`, `price`, `order_id`) VALUES
  (19,	2,	'2014-09-15 17:41:25',	'2014-09-15 20:41:25',	1,	'\r\nMartha Stewart Crafts Garland, Pink Pom Pom Small\r\n',	3,	9.0000,	7),
  (20,	2,	'2014-09-15 17:41:25',	'2014-09-15 20:41:25',	2,	'\r\nMartha Stewart Crafts Modern Festive Pennant Garland, 12ft\r\n',	1,	5.6000,	7),
  (21,	2,	'2014-09-15 17:41:25',	'2014-09-15 20:41:25',	186,	'\r\nCrazy Taxi\r\n',	1,	4.9900,	7),
  (22,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	1,	'\r\nMartha Stewart Crafts Garland, Pink Pom Pom Small\r\n',	2,	9.0000,	8),
  (23,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	81,	'\r\nNative Forest Organic Classic Coconut Milk, 13.5-Ounce Cans (Pack of 12)\r\n',	3,	30.0000,	8),
  (24,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	97,	'\r\nFrench Toast Girls Ribbon Jumper\r\n',	1,	15.0000,	8),
  (25,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	101,	'\r\nDiesel Men\'s Sleenker Skinny-Leg Jean 0608D\r\n',	1,	238.0000,	8),
  (26,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	74,	'\r\nGold Bond Men\'s Everyday Essentials Lotion, 14.5 Ounce\r\n',	1,	8.0000,	8),
  (27,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	173,	'\r\nNFL Clean Up Adjustable Hat, One Size Fits All Fits All\r\n',	1,	15.0000,	8),
  (28,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	202,	'\r\nAmerican Pickers\r\n',	1,	0.9900,	8),
  (29,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	153,	'\r\nMustache Party Food and Cupcake Picks - 25 ct\r\n',	1,	5.0000,	8),
  (30,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	146,	'\r\nNEST Fragrances NEST08-BM Bamboo Scented Reed Diffuser\r\n',	1,	38.0000,	8),
  (31,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	28,	'\r\nDuracell DRPP300 Powerpack 300-Watt Jump Starter and Emergency Power Source\r\n',	3,	102.5900,	8),
  (32,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	156,	'\r\nHigh Cotton Doormat, Open Policy Wine\r\n',	1,	24.0000,	8),
  (33,	3,	'2014-09-15 17:44:33',	'2014-09-15 20:44:33',	163,	'\r\nUnder Armour Men\'s HeatGear® Sonic Compression Short Sleeve\r\n',	1,	25.0000,	8),
  (34,	4,	'2014-09-15 17:44:54',	'2014-09-15 20:44:54',	130,	'\r\nBrother Printer MFC7360N Monochrome Printer with Scanner, Copier & Fax and built in Networking\r\n',	1,	130.0000,	9),
  (35,	5,	'2014-09-15 17:45:16',	'2014-09-15 20:45:16',	81,	'\r\nNative Forest Organic Classic Coconut Milk, 13.5-Ounce Cans (Pack of 12)\r\n',	1,	30.0000,	10),
  (36,	5,	'2014-09-15 17:45:16',	'2014-09-15 20:45:16',	32,	'\r\nBlack & Decker PPRH5B Professional Power Station\r\n',	3,	113.6700,	10),
  (37,	5,	'2014-09-15 17:45:16',	'2014-09-15 20:45:16',	196,	'\r\nTest Fire TV\r\n',	1,	99.0000,	10);