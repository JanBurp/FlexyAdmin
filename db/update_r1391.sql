# Cleanup some cfg AND added options for fieldsets in forms

# Change postfix to suffix
ALTER TABLE `cfg_img_info` CHANGE `str_postfix_1` `str_suffix_1` VARCHAR(10)  NOT NULL  DEFAULT '';
ALTER TABLE `cfg_img_info` CHANGE `str_postfix_2` `str_suffix_2` VARCHAR(10)  NOT NULL  DEFAULT '';

# Alter order of cfg_fields
ALTER TABLE `cfg_media_info` MODIFY COLUMN `str_types` VARCHAR(100) NOT NULL AFTER `path`;
ALTER TABLE `cfg_media_info` MODIFY COLUMN `fields_media_fields` VARCHAR(100) NOT NULL AFTER `str_types`;
ALTER TABLE `cfg_media_info` MODIFY COLUMN `int_last_uploads` INT(2) DEFAULT '5' AFTER `str_order`;
ALTER TABLE `cfg_media_info` MODIFY COLUMN `b_user_restricted` INT(1) DEFAULT '0' AFTER `b_in_link_list`;

ALTER TABLE `cfg_table_info` MODIFY COLUMN `int_max_rows` TINYINT(4) NOT NULL AFTER `b_pagination`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `b_freeze_uris` TINYINT(1) NOT NULL AFTER `int_max_rows`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `str_order_by` VARCHAR(50) NOT NULL AFTER `table`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `b_freeze_uris` TINYINT(1) NOT NULL AFTER `str_form_many_order`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `b_grid_add_many` TINYINT(1) NOT NULL DEFAULT '0' AFTER `str_form_many_order`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `int_max_rows` TINYINT(4) NOT NULL AFTER `str_form_many_order`;

# add fieldsets to cfg_table_info and cfg_field_info
ALTER TABLE `cfg_table_info` ADD `str_fieldsets` VARCHAR(255)  NOT NULL  AFTER `str_form_many_order`;
ALTER TABLE `cfg_field_info` ADD `str_fieldset` VARCHAR(100)  NOT NULL  AFTER `str_show_in_form_where`;
ALTER TABLE `cfg_table_info` MODIFY COLUMN `str_fieldsets` VARCHAR(255) NOT NULL AFTER `b_pagination`;

