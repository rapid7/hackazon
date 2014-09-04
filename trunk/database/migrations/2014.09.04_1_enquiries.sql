CREATE TABLE `tbl_enquiries` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `created_by` mediumint(8) unsigned NOT NULL,
  `assigned_to` mediumint(8) unsigned NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'new',
  `created_on` datetime NULL,
  `updated_on` timestamp NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

ALTER TABLE `tbl_enquiries`
ADD FOREIGN KEY (`created_by`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_enquiries`
ADD FOREIGN KEY (`assigned_to`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE `tbl_enquiry_messages` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `author_id` mediumint(8) unsigned NULL,
  `message` text NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` timestamp NOT NULL,
  FOREIGN KEY (`author_id`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

ALTER TABLE `tbl_enquiry_messages`
ADD `enquiry_id` int(11) NOT NULL AFTER `id`,
ADD FOREIGN KEY (`enquiry_id`) REFERENCES `tbl_enquiries` (`id`) ON DELETE CASCADE,
COMMENT='';