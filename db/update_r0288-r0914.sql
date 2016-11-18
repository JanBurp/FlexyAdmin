################### UPDATE r288 ############

# No need for a start_uri field, it will be found automatic (first in order)
ALTER TABLE `tbl_site` DROP `str_start_uri`;

# A class can be given to editor fields, for other sizes (normal|wide|big)
ALTER TABLE `cfg_editor` ADD `str_class` VARCHAR( 10 ) NOT NULL AFTER `b_use_editor` ;

# License key added, is needed to do updates
ALTER TABLE `cfg_configurations` ADD `key` VARCHAR( 255 ) NOT NULL AFTER `id` ;


#
# Rights are changed dramaticaly!
# Right/groups can be made, every user can have a combination of these groups
#

# Remove old system
ALTER TABLE `cfg_users`
  DROP `str_table_rights`,
  DROP `str_media_rights`;

# add right groups
DROP TABLE IF EXISTS `cfg_rights`;
CREATE TABLE IF NOT EXISTS `cfg_rights` (
  `id` int(11) NOT NULL auto_increment,
  `str_name` varchar(50) NOT NULL,
  `rights` varchar(255) NOT NULL,
  `b_delete` tinyint(1) NOT NULL,
  `b_add` tinyint(1) NOT NULL,
  `b_edit` tinyint(1) NOT NULL,
  `b_show` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;
INSERT INTO `cfg_rights` VALUES(1, 'super_admin', '*', 1, 1, 1, 1);
INSERT INTO `cfg_rights` VALUES(2, 'admin', 'tbl_*|media_*|cfg_users', 1, 1, 1, 1);
INSERT INTO `cfg_rights` VALUES(3, 'user', 'tbl_*|media_*', 1, 1, 1, 1);
INSERT INTO `cfg_rights` VALUES(4, 'visiter', 'tbl_*|media_*', 0, 0, 0, 1);

# add join table for user rigts
DROP TABLE IF EXISTS `rel_users__rights`;
CREATE TABLE IF NOT EXISTS `rel_users__rights` (
  `id` int(11) NOT NULL auto_increment,
  `id_users` int(11) NOT NULL,
  `id_rights` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;
INSERT INTO `rel_users__rights` VALUES(17, 1, 1);
INSERT INTO `rel_users__rights` VALUES(15, 2, 3);

# set some standard users (change the ids)
UPDATE `cfg_users` SET `id` = '1' WHERE `cfg_users`.`id` =14 LIMIT 1 ;
UPDATE `cfg_users` SET `id` = '2' WHERE `cfg_users`.`id` =15 LIMIT 1 ;


################### UPDATE r293 ############

# Add rights per user
# user field in a table sets row to a user id
# cfg_rights now has a field b_all_users
ALTER TABLE `cfg_rights` ADD `b_all_users` TINYINT( 1 ) NOT NULL AFTER `rights` ;

# Add rights per user for media files `cfg_media_files`
CREATE TABLE `cfg_media_files` (
  `id` int(11) NOT NULL auto_increment,
  `user` smallint(6) NOT NULL,
  `file` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


################### UPDATE r296 ############

# Add logout to choice
ALTER TABLE `cfg_configurations` ADD `b_logout_to_site` TINYINT( 1 ) NOT NULL;

################### UPDATE r303 ############

# options where
ALTER TABLE `cfg_table_info` ADD `str_options_where` VARCHAR( 255 ) NOT NULL AFTER `str_abstract_fields` ;

################### UPDATE r309 ############

# Extra option in cfg_media_list, make a file a download link
ALTER TABLE `cfg_media_info` ADD `b_in_link_list` TINYINT( 1 ) NOT NULL AFTER `b_in_img_list` ;

################### UPDATE r328 ############

# Help for media
ALTER TABLE `cfg_media_info` ADD `txt_help` BLOB NOT NULL ;

################### UPDATE r335 ############

# Add internal links to link list
ALTER TABLE `cfg_editor` ADD `b_add_internal_links` TINYINT( 1 ) NOT NULL AFTER `table` ;

################### UPDATE r343 ############

# Add Backup rights to rights table
ALTER TABLE `cfg_rights` ADD `b_backup` TINYINT( 1 ) NOT NULL AFTER `b_all_users` ;


################### UPDATE r349 ############

# Add choice of $_GET in frontend
ALTER TABLE `cfg_configurations` ADD `b_query_urls` TINYINT( 1 ) NOT NULL ;


################### UPDATE r356 ############

# Add possibility of freezing uris
ALTER TABLE `cfg_table_info` ADD `b_freeze_uris` TINYINT( 1 ) NOT NULL AFTER `str_order_by` ;

################### UPDATE r389 ############

# Add maximum items for a table (of submenu)
ALTER TABLE `cfg_table_info` ADD `int_max_rows` TINYINT NOT NULL AFTER `str_ui_name` ,
ADD `int_max_subrows` TINYINT NOT NULL AFTER `int_max_rows` ;


################### UPDATE r390 ############

# Rename cfg_media_info str_menu_name into str_ui_name
ALTER TABLE `cfg_media_info` CHANGE `str_menu_name` `str_ui_name` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL  


################### UPDATE r392 ############

# Add common help
ALTER TABLE `cfg_configurations` ADD `txt_help` TEXT NOT NULL ;

################### UPDATE r401 ############

# Change b_show_grid_with_joins` to `b_grid_add_many`
ALTER TABLE `cfg_table_info` CHANGE `b_show_grid_with_joins` `b_grid_add_many` TINYINT( 1 ) NOT NULL DEFAULT '0';
# Add field 'str_form_many_type'
ALTER TABLE `cfg_table_info` ADD `str_form_many_type` VARCHAR( 32 ) NOT NULL AFTER `b_grid_add_many` ;

################### UPDATE r442 ############

# Change field to field_... and fields to fields_...
ALTER TABLE `cfg_media_info` CHANGE `fields` `fields_media_fields` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `cfg_field_info` CHANGE `field` `field_field` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

# Add Autofill/Bulkupload settings to cfg_media_info
ALTER TABLE `cfg_media_info` ADD `fields_autofill_fields` VARCHAR( 255 ) NOT NULL AFTER `str_path` ;

################### UPDATE r443 ############

# Add rights for tools (search/replace, bulkupload)
ALTER TABLE `cfg_rights` ADD `b_tools` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `b_backup` ;

################### UPDATE r455 ############

# Add Validation rules parameters
ALTER TABLE `cfg_field_info` ADD `str_validation_parameters` VARCHAR( 20 ) NOT NULL AFTER `str_validation_rules` ;

################### UPDATE r456 ############

# Change str_path to path, special dropdown field.
ALTER TABLE `cfg_img_info` CHANGE `str_path` `path` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `cfg_media_info` CHANGE `str_path` `path` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

################### UPDATE r472 ############

# Change log_stats a bit
ALTER TABLE `log_stats` DROP `str_host`;
ALTER TABLE `log_stats` DROP `ip_ip`;
ALTER TABLE `log_stats` CHANGE `str_browser` `str_browser` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `log_stats` CHANGE `str_platform` `str_platform` VARCHAR( 25 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL  ;
UPDATE log_stats SET str_browser = 'Internet Explorer' WHERE str_browser LIKE 'Internet Expl%';
UPDATE log_stats SET str_platform = 'Windows Vista' WHERE str_platform = 'Windows Longhorn';
# Delete all images etc
DELETE FROM `log_stats` WHERE str_uri LIKE '%.%';


################### UPDATE r475 ############

# Add last uploads number
ALTER TABLE `cfg_media_info` ADD `int_last_uploads` int(2) NULL DEFAULT '5'  AFTER `str_types`;

################### UPDATE r478 ############

# User restriction for media's
ALTER TABLE `cfg_media_info` ADD `b_user_restricted` int(1) NULL DEFAULT '0'  AFTER `path`;

################### UPDATE r489 ############

# Adding cfg_admin_menu and standard content
-- structure
DROP TABLE IF EXISTS `cfg_admin_menu`;
CREATE TABLE IF NOT EXISTS `cfg_admin_menu` (
  `id` int(11) NOT NULL auto_increment,
  `order` smallint(6) default '0',
  `str_ui_name` varchar(50) default '',
  `b_visible` tinyint(1) default '1',
  `str_type` varchar(20) default '',
  `api` varchar(20) default '',
  `path` varchar(50) NOT NULL,
  `table` varchar(25) default NULL,
  `str_table_where` varchar(50) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;
-- data
INSERT INTO `cfg_admin_menu` VALUES(1, 0, 'Home', 1, 'api', 'API_home', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(2, 1, 'Logout', 1, 'api', 'API_logout', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(3, 2, 'Help', 0, 'api', 'API_help', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(4, 5, '# all normal tables (if user has rights)', 1, 'all_tbl_tables', '', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(5, 6, '# all media (if user has rights)', 1, 'all_media', '', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(6, 10, '# all tools (if user has rights)', 1, 'tools', '', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(7, 12, '# all config tables (if user has rights)', 1, 'all_cfg_tables', '', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(8, 3, '', 1, 'seperator', '', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(9, 7, '', 1, 'seperator', '', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(10, 11, '', 1, 'seperator', '', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(11, 8, '_stats_menu', 1, 'api', 'API_stats', '', '', '');
INSERT INTO `cfg_admin_menu` VALUES(12, 9, '', 1, 'seperator', '', '', '', '');

################### UPDATE r494 ############

# Add img_info dragndrop choice
ALTER TABLE `cfg_media_info` ADD `b_dragndrop` tinyint(1) NULL DEFAULT '1'  AFTER `fields_media_fields`;

################### UPDATE r602 ############

# Longer api calls in cfg_admin_menu
ALTER TABLE `cfg_admin_menu` CHANGE `api` `api` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

################### UPDATE r672 ############
# Ordering of drag n drop thumbs
ALTER TABLE `cfg_media_info` ADD `str_order` VARCHAR( 10 ) NOT NULL DEFAULT 'name' AFTER `b_dragndrop` ;
################### UPDATE r680 ############
# Add empty choice to foreign table
ALTER TABLE `cfg_table_info` ADD `b_add_empty_choice` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `str_order_by` ;
################### UPDATE r694 ############
# Autofill now possible for bulk & single uploadÅ
ALTER TABLE `cfg_media_info` ADD `str_autofill` varchar(20) NOT NULL DEFAULT 'bulk upload'  AFTER `b_user_restricted`;

################### UPDATE r703 ############
# Where options override at cfg_field
ALTER TABLE `cfg_field_info` ADD `str_options_where` varchar(255) NULL DEFAULT NULL  AFTER `b_multi_options`;


################### UPDATE r717 ############
# Show field optional
ALTER TABLE `cfg_field_info` ADD `str_show_in_form_where` varchar(255) NOT NULL DEFAULT ' '  AFTER `b_show_in_form`;


################### UPDATE r723 ############
# Add Revision nr to database
ALTER TABLE `cfg_configurations` ADD `str_revision` varchar(10) NOT NULL AFTER `txt_help`;


################### UPDATE r756 ############
# Add option to add an empty media field 
ALTER TABLE `cfg_media_info` ADD `b_add_empty_choice` tinyint(1) NOT NULL DEFAULT '1'  AFTER `fields_media_fields`;


################### UPDATE r762 ############
# Add option to set order of many option fields
ALTER TABLE `cfg_table_info` ADD `str_form_many_order` varchar(10) NOT NULL DEFAULT 'last'  AFTER `str_form_many_type`;

################### UPDATE r769 ############
# Add split language option to res_menu_result // cfg_auto_menu
# Unselect SQL if tables exist

# ALTER TABLE `cfg_auto_menu` ADD `str_parameters` varchar(20) NOT NULL DEFAULT ''  AFTER `field_group_by`;
#	ALTER TABLE `res_menu_result` CHANGE `id` `id` int(11) NOT NULL;

################### UPDATE r787 ############

# Add Google Analytics
ALTER TABLE `tbl_site` ADD `str_google_analytics` varchar(20) NOT NULL AFTER `stx_keywords`;

################### UPDATE r805 ############
# Add tinyMCE preview width & height
ALTER TABLE `cfg_editor` ADD `int_preview_width` varchar(4) NOT NULL DEFAULT '450'  AFTER `str_buttons3`;
ALTER TABLE `cfg_editor` ADD `int_preview_height` varchar(4) NOT NULL DEFAULT '500'  AFTER `int_preview_width`;

################### UPDATE r847 ############
# Add ordered multi options to cfg_field_info
ALTER TABLE `cfg_field_info` ADD `b_ordered_options` tinyint(1) NOT NULL AFTER `b_multi_options`;


################### UPDATE r914 ############
# Grid pagination
ALTER TABLE `cfg_table_info` ADD `int_pagination` tinyint NOT NULL AFTER `int_max_subrows`;
ALTER TABLE `cfg_table_info` CHANGE `int_pagination` `int_pagination` tinyint(4) NOT NULL DEFAULT '0';
ALTER TABLE `cfg_media_info` ADD `int_pagination` tinyint NOT NULL AFTER `str_ui_name`;
ALTER TABLE `cfg_media_info` CHANGE `int_pagination` `int_pagination` tinyint(4) NOT NULL DEFAULT '0';




