# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '3.5.0';

DROP TABLE `cfg_field_info`;
DROP TABLE `cfg_img_info`;
DROP TABLE `cfg_media_info`;
DROP TABLE `cfg_table_info`;
DROP TABLE `cfg_ui`;

ALTER TABLE `tbl_site` DROP `str_google_analytics`;

