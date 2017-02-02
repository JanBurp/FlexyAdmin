# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '3.5.0';

DROP TABLE `cfg_admin_menu`;

ALTER TABLE `cfg_configurations` DROP `int_pagination`;
ALTER TABLE `cfg_configurations` DROP `b_use_editor`;
ALTER TABLE `cfg_configurations` DROP `str_class`;
ALTER TABLE `cfg_configurations` DROP `str_valid_html`;
ALTER TABLE `cfg_configurations` DROP `table`;
ALTER TABLE `cfg_configurations` DROP `b_add_internal_links`;
ALTER TABLE `cfg_configurations` DROP `str_buttons1`;
ALTER TABLE `cfg_configurations` DROP `str_buttons2`;
ALTER TABLE `cfg_configurations` DROP `str_buttons3`;
ALTER TABLE `cfg_configurations` DROP `int_preview_width`;
ALTER TABLE `cfg_configurations` DROP `int_preview_height`;
ALTER TABLE `cfg_configurations` DROP `str_formats`;
ALTER TABLE `cfg_configurations` DROP `str_styles`;
ALTER TABLE `cfg_configurations` CHANGE `str_revision` `str_revision` VARCHAR(10)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '3.5.0';

