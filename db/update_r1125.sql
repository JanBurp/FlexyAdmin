# Database cleanup

ALTER TABLE `cfg_configurations` DROP `str_menu_table`;
ALTER TABLE `cfg_configurations` DROP `email_webmaster_email`;
ALTER TABLE `cfg_configurations` MODIFY COLUMN `txt_help` TEXT CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' AFTER `id`;


ALTER TABLE `cfg_media_info` DROP `str_type`;
ALTER TABLE `cfg_media_info` MODIFY COLUMN `path` VARCHAR(255) NOT NULL DEFAULT '' AFTER `str_ui_name`;
ALTER TABLE `cfg_media_info` MODIFY COLUMN `str_types` VARCHAR(100) NOT NULL DEFAULT '' AFTER `path`;
ALTER TABLE `cfg_media_info` MODIFY COLUMN `fields_media_fields` VARCHAR(100) NOT NULL DEFAULT '' AFTER `str_types`;
ALTER TABLE `cfg_media_info` MODIFY COLUMN `int_pagination` TINYINT(4) NOT NULL DEFAULT '0' AFTER `b_dragndrop`;

ALTER TABLE `cfg_table_info` MODIFY COLUMN `str_ui_name` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' AFTER `table`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `int_pagination` TINYINT(4) NOT NULL DEFAULT '0' AFTER `str_ui_name`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `str_abstract_fields` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' AFTER `b_single_row`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `str_order_by` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' AFTER `str_abstract_fields`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `b_grid_add_many` TINYINT(1) NOT NULL DEFAULT '0' AFTER `b_single_row`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `str_options_where` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' AFTER `str_order_by`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `str_abstract_fields` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' AFTER `str_order_by`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `str_form_many_type` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' AFTER `str_options_where`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `str_form_many_order` VARCHAR(10) NOT NULL DEFAULT 'last' AFTER `str_form_many_type`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `b_add_empty_choice` TINYINT(1) NOT NULL DEFAULT '1' AFTER `str_form_many_order`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `b_add_empty_choice` TINYINT(1) NOT NULL DEFAULT '1' AFTER `str_options_where`;

ALTER TABLE `cfg_field_info` DROP `str_overrule_prefix`;



# New user authentication

# Changes to cfg_rights and rename to cfg_user_groups

RENAME TABLE `cfg_rights` TO `cfg_user_groups`;
ALTER TABLE `cfg_user_groups` CHANGE `id` `id` MEDIUMINT(8)  UNSIGNED  NOT NULL  AUTO_INCREMENT;
ALTER TABLE `cfg_user_groups` CHANGE `str_name` `str_name` VARCHAR(20)  NOT NULL  DEFAULT '';
ALTER TABLE `cfg_user_groups` ADD `str_description` VARCHAR(50)  NOT NULL  AFTER `str_name`;
UPDATE `cfg_user_groups` SET `str_description` = 'Super Administrator' WHERE `id` = '1';
UPDATE `cfg_user_groups` SET `str_description` = 'Administrator' WHERE `id` = '2';
UPDATE `cfg_user_groups` SET `str_description` = 'User' WHERE `id` = '3';
UPDATE `cfg_user_groups` SET `str_description` = 'Visitor' WHERE `id` = '4';


# Changes to cfg_users: change old fields, add new ones

ALTER TABLE `cfg_users` CHANGE `id` `id` MEDIUMINT(8)  UNSIGNED  NOT NULL  AUTO_INCREMENT;
ALTER TABLE `cfg_users` CHANGE `str_user_name` `str_username` VARCHAR(50)  CHARACTER SET latin1  COLLATE latin1_general_ci  NOT NULL  DEFAULT '';
ALTER TABLE `cfg_users` CHANGE `str_username` `str_username` VARCHAR(20)  CHARACTER SET latin1  COLLATE latin1_general_ci  NOT NULL  DEFAULT '';
ALTER TABLE `cfg_users` ADD `id_user_group` MEDIUMINT(8)  UNSIGNED  NOT NULL  AFTER `str_username`;
ALTER TABLE `cfg_users` CHANGE `gpw_user_pwd` `gpw_password` VARCHAR(40)  CHARACTER SET latin1  COLLATE latin1_general_ci  NOT NULL  DEFAULT '';
ALTER TABLE `cfg_users` ADD `email_email` VARCHAR(100)  NOT NULL  DEFAULT ' '  AFTER `gpw_password`;
ALTER TABLE `cfg_users` MODIFY COLUMN `ip_user_ip` VARCHAR(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' AFTER `email_email`;
ALTER TABLE `cfg_users` CHANGE `ip_user_ip` `ip_address` VARCHAR(16)  CHARACTER SET latin1  COLLATE latin1_general_ci  NOT NULL  DEFAULT '';
ALTER TABLE `cfg_users` ADD `str_salt` VARCHAR(40)  NULL  DEFAULT NULL  AFTER `ip_address`;
ALTER TABLE `cfg_users` ADD `str_activation_code` VARCHAR(40)  NULL  DEFAULT NULL  AFTER `str_salt`;
ALTER TABLE `cfg_users` ADD `str_forgotten_password_code` VARCHAR(40)  NULL  DEFAULT NULL  AFTER `str_activation_code`;
ALTER TABLE `cfg_users` ADD `str_remember_code` VARCHAR(40)  NULL  DEFAULT NULL  AFTER `str_forgotten_password_code`;
ALTER TABLE `cfg_users` ADD `created_on` INT(11)  UNSIGNED  NOT NULL  AFTER `str_remember_code`;
ALTER TABLE `cfg_users` ADD `last_login` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `created_on`;
ALTER TABLE `cfg_users` ADD `b_active` TINYINT(1)  UNSIGNED  NULL  DEFAULT '1'  AFTER `last_login`;
ALTER TABLE `cfg_users` CHANGE `str_language` `str_language` CHAR(3)  CHARACTER SET latin1  COLLATE latin1_general_ci  NOT NULL  DEFAULT 'nl';
ALTER TABLE `cfg_users` DROP `b_strict_ip`;