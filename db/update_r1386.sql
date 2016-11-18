# Add cfg_ui and move all ui fields from cfg_table_info, cfg_field_info and cfg_media_info to cfg_ui

# Create UI Table
CREATE TABLE `cfg_ui` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(100) NOT NULL,
  `table` VARCHAR(100) NOT NULL,
  `field_field` VARCHAR(255) NOT NULL,
  `str_title_nl` VARCHAR(50) NOT NULL DEFAULT '',
  `str_title_en` VARCHAR(50) NOT NULL,
  `txt_help_nl` TEXT NOT NULL,
  `txt_help_en` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# Fill UI Table with old UI data
TRUNCATE TABLE `cfg_ui`;
INSERT INTO `cfg_ui` (`table`,`str_title_nl`,`txt_help_nl`) SELECT `table`, `str_ui_name` AS `str_title_nl`, `txt_help` AS `txt_help_nl` FROM `cfg_table_info`;
INSERT INTO `cfg_ui` (`path`,`str_title_nl`,`txt_help_nl`) SELECT `path`, `str_ui_name` AS `str_title_nl`, `txt_help` AS `txt_help_nl` FROM `cfg_media_info`;
INSERT INTO `cfg_ui` (`field_field`,`str_title_nl`,`txt_help_nl`) SELECT `field_field`, `str_ui_name` AS `str_title_nl`, `txt_help` AS `txt_help_nl` FROM `cfg_field_info`;

# Cleanup UI Table where values are empty
DELETE FROM `cfg_ui` WHERE `str_title_nl`='' AND `txt_help_nl`='' AND `str_title_en`='' AND `txt_help_en`='';

# Remove old UI fields from cfg_table_info and cfg_field_info
ALTER TABLE `cfg_media_info` DROP `str_ui_name`;
ALTER TABLE `cfg_media_info` DROP `txt_help`;
ALTER TABLE `cfg_field_info` DROP `str_ui_name`;
ALTER TABLE `cfg_field_info` DROP `txt_help`;
ALTER TABLE `cfg_table_info` DROP `str_ui_name`;
ALTER TABLE `cfg_table_info` DROP `txt_help`;
