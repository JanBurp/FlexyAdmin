# Add minimal size for image uploading
ALTER TABLE `cfg_img_info` ADD `int_min_width` INT  NOT NULL  DEFAULT '0'  AFTER `path`;
ALTER TABLE `cfg_img_info` ADD `int_min_height` INT(11)  NOT NULL  DEFAULT '0'  AFTER `str_suffix_2`;
ALTER TABLE `cfg_img_info` MODIFY COLUMN `int_min_height` INT(11) NOT NULL DEFAULT '0' AFTER `int_min_width`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '1432';

