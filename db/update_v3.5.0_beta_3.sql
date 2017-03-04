# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '3.5.0';

RENAME TABLE `cfg_configurations` TO `cfg_version`;
ALTER TABLE `cfg_version` DROP `txt_help`;
ALTER TABLE `cfg_version` CHANGE `str_revision` `str_version` VARCHAR(10)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '3.5.0';
