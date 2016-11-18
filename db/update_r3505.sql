# log_login veranderen in log_activity met andere velden

ALTER TABLE `log_login` DROP `ip_login_ip`;
ALTER TABLE `log_login` CHANGE `tme_login_time` `tme_timestamp` TIMESTAMP  NOT NULL  DEFAULT CURRENT_TIMESTAMP  ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `log_login` ADD `stx_activity` LONGTEXT  NOT NULL  AFTER `str_changed_tables`;
ALTER TABLE `log_login` CHANGE `str_changed_tables` `str_description` VARCHAR(255)  CHARACTER SET utf8  NOT NULL  DEFAULT '';
ALTER TABLE `log_login` ADD `str_activity_type` VARCHAR(10)  NOT NULL  DEFAULT ''  AFTER `stx_activity`;
RENAME TABLE `log_login` TO `log_activity`;
ALTER TABLE `log_activity` CHANGE `id` `id` BIGINT  UNSIGNED  NOT NULL  AUTO_INCREMENT;
ALTER TABLE `log_activity` DROP `str_description`;
ALTER TABLE `log_activity` ADD `str_model` VARCHAR(255)  NOT NULL  AFTER `str_activity_type`;
ALTER TABLE `log_activity` ADD `str_key` VARCHAR(255)  NOT NULL  AFTER `str_model`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '3505';




