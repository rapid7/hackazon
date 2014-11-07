ALTER TABLE `tbl_users`
ADD `credit_card` varchar(64) COLLATE 'utf8_general_ci' NULL,
ADD `credit_card_expires` varchar(32) COLLATE 'utf8_general_ci' NULL AFTER `credit_card`,
ADD `credit_card_cvv` int NULL AFTER `credit_card_expires`,
COMMENT=''; 