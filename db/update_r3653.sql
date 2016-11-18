# LET OP: Alle gebruikers moeten een nieuwe inlog krijgen & 'super_admin' heeft een tijdelijk nieuw wachtwoord 'admin'!! (IonAuth)

CREATE TABLE `rel_users__groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) unsigned NOT NULL,
  `id_user_group` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `log_login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pas cfg_users aans
ALTER TABLE `cfg_users` CHANGE `str_salt` `salt` VARCHAR(40)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT NULL;
ALTER TABLE `cfg_users` CHANGE `str_activation_code` `activation_code` VARCHAR(40)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT NULL;
ALTER TABLE `cfg_users` CHANGE `str_forgotten_password_code` `forgotten_password_code` VARCHAR(40)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT NULL;
ALTER TABLE `cfg_users` CHANGE `str_remember_code` `remember_code` VARCHAR(40)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT NULL;
ALTER TABLE `cfg_users` CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT;
ALTER TABLE `cfg_users` CHANGE `str_username` `str_username` VARCHAR(100)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '';
ALTER TABLE `cfg_users` CHANGE `gpw_password` `gpw_password` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '';
ALTER TABLE `cfg_users` CHANGE `salt` `salt` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT NULL;
ALTER TABLE `cfg_users` ADD `forgotten_password_time` INT  UNSIGNED  NOT NULL  AFTER `forgotten_password_code`;

-- Pas cfg_user_groups aans
ALTER TABLE `cfg_user_groups` CHANGE `str_name` `name` VARCHAR(20)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '';
ALTER TABLE `cfg_user_groups` CHANGE `str_description` `description` VARCHAR(50)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '';


# Wachtwoord van admin is gereset naar 'admin' -> andere users moeten een nieuwe krijgen
UPDATE `cfg_users` SET `gpw_password` = '$2y$08$rKzbNchBsinmWva3UnPsZOMKbaTFdpOgjxNv.PYKn1BjWgrOznhZa' WHERE `id` = '1';

# Copier user_groups van cfg_users -> rel_users_groups
INSERT INTO `rel_users__groups` (`id_user`,`id_user_group`)
SELECT `id` AS `id_user`, `id_user_group` FROM `cfg_users`;

# Verwijder verwijzing van cfg_users
ALTER TABLE `cfg_users` DROP `id_user_group`;




# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '3653';
