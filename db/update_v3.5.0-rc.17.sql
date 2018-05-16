# LOG ip adress
ALTER TABLE `log_stats` ADD `ip_address` VARCHAR(15)  NOT NULL  DEFAULT ''  AFTER `tme_date_time`;

# Change db revision
UPDATE `cfg_version` SET `str_version` = '3.5.0-rc.17';
