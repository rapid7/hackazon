CREATE TABLE `tbl_coupons` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `coupon` varchar(32) NOT NULL,
  `discount` int NOT NULL DEFAULT '0'
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

ALTER TABLE `tbl_coupons`
ADD UNIQUE `coupon` (`coupon`);

ALTER TABLE `tbl_orders`
ADD `coupon_id` int(11) NULL,
ADD FOREIGN KEY (`coupon_id`) REFERENCES `tbl_coupons` (`id`) ON DELETE RESTRICT,
COMMENT='';

ALTER TABLE `tbl_orders`
ADD `discount` int(11) NOT NULL DEFAULT '0',
COMMENT='';