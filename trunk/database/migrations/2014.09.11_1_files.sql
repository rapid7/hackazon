CREATE TABLE `tbl_files` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` mediumint(8) unsigned NULL,
  `path` varchar(255) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';