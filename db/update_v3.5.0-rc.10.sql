# LOG ip adress
ALTER TABLE `log_activity` ADD `ip_address` VARCHAR(15)  NOT NULL  AFTER `id_user`;

# Change db revision
UPDATE `cfg_version` SET `str_version` = '3.5.0-rc.10';
