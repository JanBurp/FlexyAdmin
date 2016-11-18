# Remove cfg_editor and move all fields to cfg_configurations

ALTER TABLE `cfg_editor` ADD `txt_help` TEXT;
UPDATE `cfg_editor` SET `txt_help`= (SELECT `txt_help` FROM `cfg_configurations` LIMIT 1);
ALTER TABLE `cfg_editor` ADD `str_revision` VARCHAR(10)  NOT NULL  DEFAULT '1385'  AFTER `txt_help`;
DROP TABLE `cfg_configurations`;
RENAME TABLE `cfg_editor` TO `cfg_configurations`;
