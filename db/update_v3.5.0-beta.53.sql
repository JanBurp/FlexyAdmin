# Keep version of system when user logged in
ALTER TABLE `cfg_users` ADD `str_last_version` VARCHAR(20)  NOT NULL  DEFAULT ''  AFTER `str_filemanager_view`;
ALTER TABLE `cfg_version` CHANGE `str_version` `str_version` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '3.5.0';

# Change db revision
UPDATE `cfg_version` SET `str_version` = '3.5.0-beta.53';
