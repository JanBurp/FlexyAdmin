# Update ip addresse (CI 2.1.2) for IPv6
ALTER TABLE `cfg_sessions` CHANGE `ip_address` `ip_address` VARCHAR(45)  NOT NULL  DEFAULT '0';
ALTER TABLE `cfg_users` CHANGE `ip_address` `ip_address` VARCHAR(45)  NOT NULL  DEFAULT '';
ALTER TABLE `log_login` CHANGE `ip_login_ip` `ip_login_ip` VARCHAR(45)  NOT NULL  DEFAULT '';

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '1610';

