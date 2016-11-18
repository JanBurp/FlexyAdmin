# Update cfg_media_info for checking if file is used somewhere
ALTER TABLE `cfg_media_info` ADD `fields_check_if_used_in` VARCHAR(50)  NOT NULL  AFTER `int_last_uploads`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '1804';

