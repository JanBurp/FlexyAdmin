# Update cfg_img_info: keep in database (res_media_files) or not (and some cleanup)
ALTER TABLE `cfg_media_info` ADD `b_in_database` TINYINT(1)  NOT NULL  DEFAULT '1'  AFTER `fields_autofill_fields`;
ALTER TABLE `cfg_media_info` CHANGE `b_in_media_list` `b_in_media_list` TINYINT(1)  NOT NULL  DEFAULT '0';
ALTER TABLE `cfg_media_info` CHANGE `b_in_img_list` `b_in_img_list` TINYINT(1)  NOT NULL  DEFAULT '0';

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2432';

