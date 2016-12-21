# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '3.5.0';

# Rename res_media_files to res_assets and standardize fieldnames
RENAME TABLE `res_media_files` TO `res_assets`;

ALTER TABLE `res_assets` CHANGE `str_title` `alt` VARCHAR(255)  CHARACTER SET latin1  COLLATE latin1_swedish_ci  NOT NULL  DEFAULT '';
ALTER TABLE `res_assets` CHANGE `str_type` `type` VARCHAR(10)  CHARACTER SET latin1  COLLATE latin1_swedish_ci  NOT NULL  DEFAULT '';
ALTER TABLE `res_assets` CHANGE `int_size` `size` INT(11)  NOT NULL;
ALTER TABLE `res_assets` CHANGE `int_img_width` `width` INT(11)  NOT NULL;
ALTER TABLE `res_assets` CHANGE `int_img_height` `height` INT(11)  NOT NULL;
ALTER TABLE `res_assets` CHANGE `dat_date` `date` DATE  NOT NULL;
ALTER TABLE `res_assets` CHANGE `file` `file` VARCHAR(255)  CHARACTER SET utf8  NOT NULL  DEFAULT '';
ALTER TABLE `res_assets` CHANGE `path` `path` VARCHAR(255)  CHARACTER SET utf8  NOT NULL  DEFAULT '';
ALTER TABLE `res_assets` CHANGE `type` `type` VARCHAR(10)  CHARACTER SET utf8  NOT NULL  DEFAULT '';
ALTER TABLE `res_assets` CHANGE `alt` `alt` VARCHAR(255)  CHARACTER SET utf8  NOT NULL  DEFAULT '';
ALTER TABLE `res_assets` ADD INDEX `file` (`file`);
ALTER TABLE `res_assets` ADD INDEX `path` (`path`);

UPDATE `cfg_media_info` SET `str_order` = 'name' WHERE `str_order` = 'file';
UPDATE `cfg_media_info` SET `str_order` = '_name' WHERE `str_order` = '_file';
UPDATE `cfg_media_info` SET `str_order` = 'rawdate' WHERE `str_order` = 'date';
UPDATE `cfg_media_info` SET `str_order` = '_rawdate' WHERE `str_order` = '_date';
