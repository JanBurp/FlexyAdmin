# Change last_login field to NOT NULL
ALTER TABLE `cfg_users` CHANGE `last_login` `last_login` INT(11)  UNSIGNED  NOT NULL;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2578';




