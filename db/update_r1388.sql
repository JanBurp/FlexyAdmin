# Global pagination settings

# Add pagination to cfg_configurations
ALTER TABLE `cfg_configurations` CHANGE `id` `id` INT(2)  NOT NULL  AUTO_INCREMENT;
ALTER TABLE `cfg_configurations` ADD `int_pagination` SMALLINT(3)  NOT NULL  DEFAULT '20'  AFTER `id`;

# change local pagination settings to boolean
ALTER TABLE `cfg_table_info` CHANGE `int_pagination` `b_pagination` TINYINT(1)  NOT NULL  DEFAULT '1';
ALTER TABLE `cfg_media_info` CHANGE `int_pagination` `b_pagination` TINYINT(1)  NOT NULL  DEFAULT '1';
ALTER TABLE `cfg_media_info` MODIFY COLUMN `b_pagination` TINYINT(1) NOT NULL DEFAULT '1' AFTER `path`;


