CREATE DATABASE IF NOT EXISTS `gene_test`;
DROP TABLE IF EXISTS `gene_log_test`;

CREATE TABLE `gene_log_test` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `priority` varchar(100) collate utf8_unicode_ci NOT NULL,
  `message` text collate utf8_unicode_ci NOT NULL,
  `logdate` timestamp NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

