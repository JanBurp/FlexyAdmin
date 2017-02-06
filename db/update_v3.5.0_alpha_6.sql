# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '3.5.0';

ALTER TABLE `cfg_users` CHANGE `str_filemanager_view` `str_filemanager_view` VARCHAR(10)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'small';
UPDATE `cfg_users` SET `str_filemanager_view`='small' WHERE `str_filemanager_view`='';