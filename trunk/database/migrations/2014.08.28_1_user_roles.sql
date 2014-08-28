CREATE TABLE `tbl_roles` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(32) NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

INSERT INTO `tbl_roles` (`name`) VALUES ('user');
INSERT INTO `tbl_roles` (`name`) VALUES ('admin');

CREATE TABLE `tbl_users_roles` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` mediumint(8) unsigned NOT NULL,
  `role_id` int(11) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `tbl_roles` (`id`) ON DELETE CASCADE
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';