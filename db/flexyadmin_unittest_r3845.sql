#
# FlexyAdmin DB-Export 2016-11-18
#
# DATA TABLES: cfg_admin_menu, cfg_configurations, cfg_email, cfg_field_info, cfg_img_info, cfg_media_info, cfg_table_info, cfg_ui, cfg_user_groups, cfg_users, rel_crud__crud2, rel_groepen__adressen, rel_users__groups, res_media_files, tbl_adressen, tbl_crud, tbl_crud2, tbl_groepen, tbl_kinderen, tbl_links, tbl_menu, tbl_site
# STRUCTURE TABLES: cfg_sessions, log_activity, log_stats, log_login_attempts
#


#
# TABLE STRUCTURE FOR: cfg_admin_menu
#

DROP TABLE IF EXISTS `cfg_admin_menu`;

CREATE TABLE `cfg_admin_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` smallint(6) DEFAULT '0',
  `str_ui_name` varchar(50) DEFAULT NULL,
  `b_visible` tinyint(1) DEFAULT '1',
  `id_user_group` int(11) NOT NULL DEFAULT '3',
  `str_type` varchar(20) DEFAULT NULL,
  `api` varchar(50) DEFAULT NULL,
  `path` varchar(50) NOT NULL,
  `table` varchar(25) DEFAULT NULL,
  `str_table_where` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('1', '0', 'Home', '1', '3', 'api', 'API_home', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('2', '1', 'Logout', '1', '3', 'api', 'API_logout', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('3', '2', 'Help', '1', '3', 'api', 'API_help', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('4', '4', '# all normal tables (if user has rights)', '1', '3', 'all_tbl_tables', '', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('5', '5', '# all media (if user has rights)', '1', '3', 'all_media', '', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('6', '9', '# all tools (if user has rights)', '1', '3', 'tools', '', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('7', '13', '# all config tables (if user has rights)', '1', '1', 'all_cfg_tables', '', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('8', '3', '', '1', '3', 'seperator', '', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('9', '6', '', '1', '3', 'seperator', '', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('10', '10', '', '1', '3', 'seperator', '', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('11', '7', '_stats_menu', '1', '3', 'api', 'API_plugin_stats', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('12', '8', '', '1', '3', 'seperator', '', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('16', '11', '# all result tables (if there are any)', '1', '1', 'all_res_tables', '', '', '', '');
INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`) VALUES ('17', '12', '', '1', '1', 'seperator', '', '', '', '');


#
# TABLE STRUCTURE FOR: cfg_configurations
#

DROP TABLE IF EXISTS `cfg_configurations`;

CREATE TABLE `cfg_configurations` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `int_pagination` smallint(3) NOT NULL DEFAULT '20',
  `b_use_editor` tinyint(1) NOT NULL DEFAULT '0',
  `str_class` varchar(10) NOT NULL,
  `str_valid_html` varchar(255) NOT NULL,
  `table` varchar(50) NOT NULL,
  `b_add_internal_links` tinyint(1) NOT NULL DEFAULT '0',
  `str_buttons1` varchar(255) NOT NULL,
  `str_buttons2` varchar(255) NOT NULL,
  `str_buttons3` varchar(255) NOT NULL,
  `int_preview_width` varchar(4) NOT NULL DEFAULT '450',
  `int_preview_height` varchar(4) NOT NULL DEFAULT '500',
  `str_formats` varchar(255) NOT NULL,
  `str_styles` varchar(100) NOT NULL,
  `txt_help` text,
  `str_revision` varchar(10) NOT NULL DEFAULT '3067',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `cfg_configurations` (`id`, `int_pagination`, `b_use_editor`, `str_class`, `str_valid_html`, `table`, `b_add_internal_links`, `str_buttons1`, `str_buttons2`, `str_buttons3`, `int_preview_width`, `int_preview_height`, `str_formats`, `str_styles`, `txt_help`, `str_revision`) VALUES ('1', '20', '1', 'normal', '', 'tbl_links', '1', 'cut,copy,pastetext,pasteword,selectall,undo,bold,italic,bullist,formatselect,removeformat,link,unlink,image,embed', '', '', '450', '500', 'h2,h3', '', '', '3845');


#
# TABLE STRUCTURE FOR: cfg_email
#

DROP TABLE IF EXISTS `cfg_email`;

CREATE TABLE `cfg_email` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_subject_nl` varchar(255) CHARACTER SET utf8 NOT NULL,
  `txt_email_nl` text CHARACTER SET utf8 NOT NULL,
  `str_subject_en` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `txt_email_en` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

INSERT INTO `cfg_email` (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES ('1', 'test', 'Een test email van {site_title}', '<p>Dit is een testmail, verzonden van {site_title} op {site_url}</p>\n<p>&nbsp;</p>\n<p>&nbsp;</p>\n<p>Naam {name}</p>\n<p>&nbsp;</p>\n<p>Bestaat niet {bestaat_niet}</p>\n<p>&nbsp;</p>', 'test', '<p>TEST</p>');
INSERT INTO `cfg_email` (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES ('2', 'login_admin_new_register', 'Nieuw account aangevraagd voor {site_title}', '<h1>Een nieuw account is aangevraag door {identity} </h1>\n<p>Log in om de aanvraag te beoordelen.</p>\n', 'New account asked for {site_title}', '<h1>A new account is being asked for by {identity} </h1>\n<p>Log in to deny or accept the registration.</p>');
INSERT INTO `cfg_email` (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES ('3', 'login_accepted', 'Account voor {site_title} geaccepteerd', '<h1>Account aanvraag voor {identity} is geaccepteerd.</h1>\n<p>U kunt nu inloggen.</p>', 'Account for {site_title} accepted', '<h1>Account registration for {identity} is accepted.</h1>\n<p>You can login now.</p>');
INSERT INTO `cfg_email` (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES ('4', 'login_activate', 'Activeer account voor {site_title}', '<h1>Activeer de aanmelding voor {identity}</h1>\n<p>Klik op <a href=\"{site_url}/{activate_uri}?id={user_id}&amp;activation={activation}\">deze link</a> om je account te activeren.</p>', 'Activate your account for {site_title}', '<h1>Activate account for {identity}</h1>\n<p>Please click <a href=\"{site_url}/{activate_uri}?id={user_id}&amp;activation={activation}\">this link</a> to activate your account.</p>');
INSERT INTO `cfg_email` (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES ('5', 'login_deny', 'Account aanvraag voor {site_title} afgewezen', '<h1>Afgewezen account voor {identity}</h1>\n<p>Uw aanvraag voor een account is afgewezen.</p>', 'Account for {site_title} denied', '<h1>Denied account for {identity}</h1>\n<p>Your account is denied.</p>');
INSERT INTO `cfg_email` (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES ('6', 'login_forgot_password', 'Nieuw wachtwoord voor {site_title}', '<h1>Nieuw wachtwoord aanvragen voor {identity}</h1>\n<p>Klik hier om <a href=\"{site_url}{forgotten_password_uri}?code={forgotten_password_code}\">wachtwoord te resetten</a>.</p>', 'New password for {site_title}', '<h1>New password request for {identity}</h1>\n<p>Click on <a href=\"{site_url}{forgotten_password_uri}?code={forgotten_password_code}\">to restet your password</a>.</p>');
INSERT INTO `cfg_email` (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES ('7', 'login_new_password', 'Nieuwe inloggegevens voor {site_title}', '<h1>Je nieuwe inlogggevens voor {site_title}:</h1>\n<p>Gebruiker: {identity}<br /> Wachtwoord: {password}</p>', 'New login for {site_title}', '<h3>You got an account.</h3>\n<p>Login with these settings:</p>\n<p>Username : {identity}<br />Password : {password}</p>');
INSERT INTO `cfg_email` (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES ('8', 'login_new_account', 'Welkom en inloggegevens voor {site_title}', '<h1>Welkom bij {site_title}</h1>\n<p>Hieronder staan je inloggegevens.</p>\n<p>Gebruiker: {identity}<br /> Wachtwoord: {password}</p>', 'New login for {site_title}', '<h1>Welcome at {site_title}</h1>\n<p>Login with these settings:</p>\n<p>Username : {identity}<br />Password : {password}</p>');


#
# TABLE STRUCTURE FOR: cfg_field_info
#

DROP TABLE IF EXISTS `cfg_field_info`;

CREATE TABLE `cfg_field_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_field` varchar(255) NOT NULL,
  `b_show_in_grid` tinyint(1) NOT NULL DEFAULT '1',
  `b_show_in_form` tinyint(1) NOT NULL DEFAULT '1',
  `str_show_in_form_where` varchar(255) NOT NULL DEFAULT ' ',
  `str_fieldset` varchar(100) NOT NULL,
  `b_editable_in_grid` tinyint(1) NOT NULL DEFAULT '0',
  `str_options` varchar(255) NOT NULL,
  `b_multi_options` tinyint(1) NOT NULL DEFAULT '0',
  `b_ordered_options` tinyint(1) NOT NULL,
  `str_options_where` varchar(255) DEFAULT NULL,
  `str_validation_rules` varchar(255) NOT NULL,
  `str_validation_parameters` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('2', 'tbl_menu.stx_description', '0', '1', ' ', 'Extra', '0', '', '0', '0', '', '0', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('3', 'tbl_menu.str_keywords', '0', '1', ' ', 'Extra', '0', '', '0', '0', '', '0', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('4', 'tbl_site.str_title', '1', '1', ' ', '', '0', '', '0', '0', '', '', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('5', 'tbl_site.str_author', '1', '1', ' ', '', '0', '', '0', '0', '', '', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('6', 'tbl_site.url_url', '1', '1', ' ', '', '0', '', '0', '0', '', 'prep_url_mail', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('7', 'tbl_site.email_email', '1', '1', ' ', '', '0', '', '0', '0', '', '', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('8', 'tbl_site.stx_description', '1', '1', ' ', '', '0', '', '0', '0', '', '', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('9', 'tbl_site.stx_keywords', '1', '1', ' ', '', '0', '', '0', '0', '', '', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('10', 'tbl_site.str_google_analytics', '1', '1', ' ', '', '0', '', '0', '0', '', 'valid_google_analytics', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('11', 'tbl_menu.str_module', '1', '1', ' ', 'Extra', '0', '|forms.contact|example', '0', '0', '', '0', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('12', 'tbl_links.url_url', '1', '1', ' ', '', '0', '', '0', '0', NULL, 'prep_url_mail', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('13', 'tbl_links.url_url', '1', '1', ' ', '', '0', '', '0', '0', NULL, 'prep_url_mail', '');
INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('14', 'tbl_menu.str_title', '1', '1', ' ', '', '0', '', '0', '0', '', 'required', '');


#
# TABLE STRUCTURE FOR: cfg_img_info
#

DROP TABLE IF EXISTS `cfg_img_info`;

CREATE TABLE `cfg_img_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `int_min_width` int(11) NOT NULL DEFAULT '0',
  `int_min_height` int(11) NOT NULL DEFAULT '0',
  `b_resize_img` tinyint(1) NOT NULL DEFAULT '0',
  `int_img_width` int(11) NOT NULL DEFAULT '0',
  `int_img_height` int(11) NOT NULL DEFAULT '0',
  `b_create_1` tinyint(1) NOT NULL DEFAULT '0',
  `int_width_1` int(6) NOT NULL DEFAULT '0',
  `int_height_1` int(6) NOT NULL DEFAULT '0',
  `str_prefix_1` varchar(10) NOT NULL,
  `str_suffix_1` varchar(10) NOT NULL DEFAULT '',
  `b_create_2` tinyint(1) NOT NULL DEFAULT '0',
  `int_width_2` int(6) NOT NULL DEFAULT '0',
  `int_height_2` int(6) NOT NULL DEFAULT '0',
  `str_prefix_2` varchar(10) NOT NULL,
  `str_suffix_2` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `cfg_img_info` (`id`, `path`, `int_min_width`, `int_min_height`, `b_resize_img`, `int_img_width`, `int_img_height`, `b_create_1`, `int_width_1`, `int_height_1`, `str_prefix_1`, `str_suffix_1`, `b_create_2`, `int_width_2`, `int_height_2`, `str_prefix_2`, `str_suffix_2`) VALUES ('1', 'pictures', '0', '0', '1', '300', '1000', '1', '100', '1000', '_thumb_', '', '0', '0', '0', '', '');


#
# TABLE STRUCTURE FOR: cfg_media_info
#

DROP TABLE IF EXISTS `cfg_media_info`;

CREATE TABLE `cfg_media_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` smallint(6) NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `b_visible` tinyint(1) NOT NULL DEFAULT '1',
  `str_types` varchar(100) NOT NULL,
  `b_encrypt_name` tinyint(1) NOT NULL DEFAULT '0',
  `fields_media_fields` varchar(100) NOT NULL,
  `b_pagination` tinyint(1) NOT NULL DEFAULT '1',
  `b_add_empty_choice` tinyint(1) NOT NULL DEFAULT '1',
  `b_dragndrop` tinyint(1) DEFAULT '1',
  `str_order` varchar(10) NOT NULL DEFAULT 'name',
  `int_last_uploads` int(2) DEFAULT '5',
  `fields_check_if_used_in` varchar(50) NOT NULL,
  `str_autofill` varchar(20) NOT NULL DEFAULT 'bulk upload',
  `fields_autofill_fields` varchar(255) NOT NULL,
  `b_in_media_list` tinyint(1) NOT NULL DEFAULT '0',
  `b_in_img_list` tinyint(1) NOT NULL DEFAULT '0',
  `b_in_link_list` tinyint(1) NOT NULL DEFAULT '0',
  `b_user_restricted` tinyint(1) NOT NULL DEFAULT '0',
  `b_serve_restricted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `cfg_media_info` (`id`, `order`, `path`, `b_visible`, `str_types`, `b_encrypt_name`, `fields_media_fields`, `b_pagination`, `b_add_empty_choice`, `b_dragndrop`, `str_order`, `int_last_uploads`, `fields_check_if_used_in`, `str_autofill`, `fields_autofill_fields`, `b_in_media_list`, `b_in_img_list`, `b_in_link_list`, `b_user_restricted`, `b_serve_restricted`) VALUES ('1', '0', 'pictures', '1', 'jpg,jpeg,gif,png', '0', 'tbl_groepen.media_tekening|tbl_menu.medias_fotos', '1', '1', '1', 'name', '5', '0', '', '0', '0', '1', '0', '0', '0');
INSERT INTO `cfg_media_info` (`id`, `order`, `path`, `b_visible`, `str_types`, `b_encrypt_name`, `fields_media_fields`, `b_pagination`, `b_add_empty_choice`, `b_dragndrop`, `str_order`, `int_last_uploads`, `fields_check_if_used_in`, `str_autofill`, `fields_autofill_fields`, `b_in_media_list`, `b_in_img_list`, `b_in_link_list`, `b_user_restricted`, `b_serve_restricted`) VALUES ('2', '1', 'downloads', '1', 'pdf,doc,docx,xls,xlsx,png,jpg', '0', '0', '1', '0', '0', 'name', '5', '', '', '0', '0', '0', '1', '0', '0');


#
# TABLE STRUCTURE FOR: cfg_table_info
#

DROP TABLE IF EXISTS `cfg_table_info`;

CREATE TABLE `cfg_table_info` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `order` smallint(6) NOT NULL DEFAULT '0',
  `table` varchar(100) NOT NULL DEFAULT '',
  `b_visible` tinyint(1) NOT NULL DEFAULT '1',
  `str_order_by` varchar(50) NOT NULL,
  `b_pagination` tinyint(1) NOT NULL DEFAULT '1',
  `b_jump_to_today` tinyint(1) NOT NULL DEFAULT '0',
  `str_fieldsets` varchar(255) NOT NULL,
  `str_abstract_fields` varchar(255) NOT NULL DEFAULT '',
  `str_options_where` varchar(255) NOT NULL DEFAULT '',
  `b_add_empty_choice` tinyint(1) NOT NULL DEFAULT '1',
  `str_form_many_type` varchar(32) NOT NULL DEFAULT '',
  `str_form_many_order` varchar(10) NOT NULL DEFAULT 'last',
  `int_max_rows` tinyint(4) NOT NULL,
  `b_grid_add_many` tinyint(1) NOT NULL DEFAULT '0',
  `b_form_add_many` tinyint(1) NOT NULL DEFAULT '1',
  `b_freeze_uris` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `cfg_table_info` (`id`, `order`, `table`, `b_visible`, `str_order_by`, `b_pagination`, `b_jump_to_today`, `str_fieldsets`, `str_abstract_fields`, `str_options_where`, `b_add_empty_choice`, `str_form_many_type`, `str_form_many_order`, `int_max_rows`, `b_grid_add_many`, `b_form_add_many`, `b_freeze_uris`) VALUES ('1', '0', 'tbl_site', '1', '', '0', '0', '', '', '', '1', 'dropdown', 'last', '1', '0', '1', '0');
INSERT INTO `cfg_table_info` (`id`, `order`, `table`, `b_visible`, `str_order_by`, `b_pagination`, `b_jump_to_today`, `str_fieldsets`, `str_abstract_fields`, `str_options_where`, `b_add_empty_choice`, `str_form_many_type`, `str_form_many_order`, `int_max_rows`, `b_grid_add_many`, `b_form_add_many`, `b_freeze_uris`) VALUES ('2', '2', 'tbl_links', '1', '', '0', '0', '', 'str_title', '', '1', 'dropdown', 'last', '0', '0', '1', '0');
INSERT INTO `cfg_table_info` (`id`, `order`, `table`, `b_visible`, `str_order_by`, `b_pagination`, `b_jump_to_today`, `str_fieldsets`, `str_abstract_fields`, `str_options_where`, `b_add_empty_choice`, `str_form_many_type`, `str_form_many_order`, `int_max_rows`, `b_grid_add_many`, `b_form_add_many`, `b_freeze_uris`) VALUES ('3', '1', 'tbl_menu', '1', '', '0', '0', 'Extra', '', '', '0', 'dropdown', 'last', '0', '0', '1', '0');
INSERT INTO `cfg_table_info` (`id`, `order`, `table`, `b_visible`, `str_order_by`, `b_pagination`, `b_jump_to_today`, `str_fieldsets`, `str_abstract_fields`, `str_options_where`, `b_add_empty_choice`, `str_form_many_type`, `str_form_many_order`, `int_max_rows`, `b_grid_add_many`, `b_form_add_many`, `b_freeze_uris`) VALUES ('4', '3', 'tbl_groepen', '1', '', '1', '0', '', '', '', '1', 'dropdown', 'last', '0', '1', '1', '0');


#
# TABLE STRUCTURE FOR: cfg_ui
#

DROP TABLE IF EXISTS `cfg_ui`;

CREATE TABLE `cfg_ui` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(100) NOT NULL,
  `table` varchar(100) NOT NULL,
  `field_field` varchar(255) NOT NULL,
  `str_title_nl` varchar(50) NOT NULL DEFAULT '',
  `str_title_en` varchar(50) NOT NULL,
  `txt_help_nl` text NOT NULL,
  `txt_help_en` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('1', '', 'tbl_site', '', '', '', '<p>Algemene informatie van de site en informatie voor zoekmachines.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('2', '', 'tbl_links', '', '', '', '<p>Een tabel met links die je in alle teksten van de site kunt gebruiken.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('3', '', 'tbl_menu', '', '', '', '<p>Het menu van de site, met de onderliggende pagina\'s en teksten.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('4', 'pictures', '', '', 'Foto\'s', '', '<p>Upload of verwijder hier de foto\'s van je site.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('5', 'downloads', '', '', 'Downloads', '', '<p>Voeg hier bestanden toe die je in je tekst als download-link wilt gebruiken.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('8', '', '', 'tbl_site.str_title', '', '', '<p>Vul hier de titel in van je site.</p><p>De titel is zichtbaar in de kop van de <a href=\"admin/help/faq\" target=\"_self\">browser</a> en in de zoekresultaten van Google.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('9', '', '', 'tbl_site.str_author', '', '', '<p>Vul hier je naam in.</p><p>De naam van de auteur is onzichtbaar voor bezoekers van de site, maar vindbaar voor zoekmachines, zodat bezoekers ook via jouw naam op je site terechtkomen.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('10', '', '', 'tbl_site.url_url', '', '', '<p>Vul hier het webadres van je site in, bijvoorbeeld: \"www.voorbeeldsite.nl\"</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('11', '', '', 'tbl_site.email_email', '', '', '<p>Vul hier je e-mailadres in.</p><p>Heb je formulieren op je site staan? Als bezoekers ze invullen en opzenden, ontvang je ze via dit e-mailadres.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('12', '', '', 'tbl_site.stx_description', '', '', '<p>Vul hier een korte algemene omschrijving van je site in.</p><p>Die is onzichtbaar op de site, maar wordt gebruikt door zoekmachines.</p><p>Afhankelijk van de opzet van de site kun je voor elke pagina een eigen omschrijving maken. Die vervangt voor die pagina deze algemene omschrijving.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('13', '', '', 'tbl_site.stx_keywords', '', '', '<p>Vul hier zoektermen in gescheiden door komma\'s.</p><p>Zoektermen worden door zoekmachines gebruikt om je site beter vindbaar te maken. Lees <a href=\"admin/help/tips_voor_een_goede_site\" target=\"_self\">hier meer over SEO</a>.<br /><br />Afhankelijk van de opzet van je site is het mogelijk om per pagina extra zoektermen toe te voegen.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('14', '', '', 'tbl_site.str_google_analytics', '', '', '<p>FlexyAdmin biedt statistieken over de bezoekers van je site. Als je uitgebreider statistieken wilt, kun je bijvoorbeeld <a href=\"http://www.google.com/intl/nl/analytics/\" target=\"_blank\">Google Analytics</a> gebruiken. Voer hier de code daarvan in.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('16', '', '', 'tbl_menu.str_title', '', '', '<p>Vul de titel van de pagina in.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('17', '', '', 'tbl_menu.self_parent', '', '', '<p>Wil je dat de nieuwe pagina onder een al bestaande pagina uit het hoofdmenu komt te staan? Geef dan hier aan onder welke pagina. Als je niets kiest dan komt de pagina in het hoofdmenu.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('18', '', '', 'tbl_menu.txt_text', '', '', '<p>Vul hier de <a href=\"admin/help/tekst_aanpassen\">tekst</a> van je pagina in.</p><p>Eventueel kun je hier ook <a href=\"admin/help/fotos\">foto\'s</a> of <a href=\"admin/help/youtube_googlemaps_etc\">YouTube</a> filmpjes tussen de tekst plaatsen.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('19', '', '', 'tbl_menu.str_module', '', '', '<p>Kies hier eventueel een module.</p><p>Modules voegen extra inhoud toe aan je pagina: een contactformulier, een overzicht van alle links, een agenda of een speciaal voor jouw site geschreven module.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('20', '', '', 'tbl_menu.stx_description', '', '', '<p>Vul hier een korte omschrijving van deze pagina in.</p><p>Die wordt gebruikt door zoekmachines als Google. Als je niets invult, wordt de algemene omschrijving gebruikt die je bij <strong><a href=\"admin/help/site\">Site</a></strong> hebt ingevuld.</p>', '');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('21', '', '', 'tbl_menu.str_keywords', '', '', '<p>Vul hier zoektermen in voor deze pagina.</p><p>Ze worden toegevoegd aan de zoektermen die je bij <strong><a href=\"admin/help/site\">Site</a></strong> hebt ingevoerd.</p>', '');


#
# TABLE STRUCTURE FOR: cfg_user_groups
#

DROP TABLE IF EXISTS `cfg_user_groups`;

CREATE TABLE `cfg_user_groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(50) NOT NULL DEFAULT '',
  `rights` varchar(1000) NOT NULL,
  `b_all_users` tinyint(1) NOT NULL DEFAULT '0',
  `b_backup` tinyint(1) NOT NULL DEFAULT '0',
  `b_tools` tinyint(1) NOT NULL DEFAULT '0',
  `b_delete` tinyint(1) NOT NULL DEFAULT '0',
  `b_add` tinyint(1) NOT NULL DEFAULT '0',
  `b_edit` tinyint(1) NOT NULL DEFAULT '0',
  `b_show` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `cfg_user_groups` (`id`, `name`, `description`, `rights`, `b_all_users`, `b_backup`, `b_tools`, `b_delete`, `b_add`, `b_edit`, `b_show`) VALUES ('1', 'super_admin', 'Super Administrator', '*', '1', '1', '1', '1', '1', '1', '1');
INSERT INTO `cfg_user_groups` (`id`, `name`, `description`, `rights`, `b_all_users`, `b_backup`, `b_tools`, `b_delete`, `b_add`, `b_edit`, `b_show`) VALUES ('2', 'admin', 'Administrator', 'tbl_*|media_*|cfg_users', '0', '1', '1', '1', '1', '1', '1');
INSERT INTO `cfg_user_groups` (`id`, `name`, `description`, `rights`, `b_all_users`, `b_backup`, `b_tools`, `b_delete`, `b_add`, `b_edit`, `b_show`) VALUES ('3', 'user', 'User', 'tbl_*|media_*', '0', '0', '0', '1', '1', '1', '1');
INSERT INTO `cfg_user_groups` (`id`, `name`, `description`, `rights`, `b_all_users`, `b_backup`, `b_tools`, `b_delete`, `b_add`, `b_edit`, `b_show`) VALUES ('4', 'visitor', 'Visitor', 'tbl_*|media_*', '0', '0', '0', '0', '0', '0', '0');


#
# TABLE STRUCTURE FOR: cfg_users
#

DROP TABLE IF EXISTS `cfg_users`;

CREATE TABLE `cfg_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_username` varchar(100) NOT NULL DEFAULT '',
  `gpw_password` varchar(255) NOT NULL DEFAULT '',
  `email_email` varchar(100) NOT NULL DEFAULT ' ',
  `ip_address` varchar(45) NOT NULL DEFAULT '',
  `salt` varchar(255) DEFAULT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(10) unsigned NOT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned NOT NULL,
  `b_active` tinyint(1) unsigned DEFAULT '1',
  `str_language` char(3) NOT NULL DEFAULT 'nl',
  `str_filemanager_view` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `cfg_users` (`id`, `str_username`, `gpw_password`, `email_email`, `ip_address`, `salt`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `b_active`, `str_language`, `str_filemanager_view`) VALUES ('1', 'admin', '$2y$08$rKzbNchBsinmWva3UnPsZOMKbaTFdpOgjxNv.PYKn1BjWgrOznhZa', 'info@flexyadmin.com', '', '', '', '', '0', '', '0', '1479468453', '1', 'nl', 'list');
INSERT INTO `cfg_users` (`id`, `str_username`, `gpw_password`, `email_email`, `ip_address`, `salt`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `b_active`, `str_language`, `str_filemanager_view`) VALUES ('2', 'user', '$2y$08$.18vvqlz24ldRDJ4AcnPR.AVYFBGOv9YbnvEw/dLRfn88KBd2E/iG', 'jan@burp.nl', '', '', '', '0', '0', '', '0', '1479468443', '1', 'nl', 'list');
INSERT INTO `cfg_users` (`id`, `str_username`, `gpw_password`, `email_email`, `ip_address`, `salt`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `b_active`, `str_language`, `str_filemanager_view`) VALUES ('3', 'test', '$2y$08$OfDssFUdFL3mqwzlg4mFJeDrmwCRrzc.9sEQj0uVbM7MRxTpX/pZC', 'test@flexyadmin.com', '', NULL, NULL, NULL, '0', NULL, '0', '1479468443', '1', 'nl', '');


#
# TABLE STRUCTURE FOR: rel_crud__crud2
#

DROP TABLE IF EXISTS `rel_crud__crud2`;

CREATE TABLE `rel_crud__crud2` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `id_crud` int(10) unsigned NOT NULL,
  `id_crud2` int(10) unsigned NOT NULL,
  `tme_last_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1588 DEFAULT CHARSET=utf8;

INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1026', '401', '8', '2015-10-04 07:48:20');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1027', '401', '1', '2015-10-04 07:48:20');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1028', '401', '20', '2015-10-04 07:48:20');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1029', '401', '9', '2015-10-04 07:48:20');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1030', '403', '10', '2015-10-04 07:48:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1031', '403', '18', '2015-10-04 07:48:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1032', '403', '3', '2015-10-04 07:48:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1033', '405', '16', '2015-10-04 07:51:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1034', '405', '13', '2015-10-04 07:51:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1035', '405', '20', '2015-10-04 07:51:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1036', '405', '9', '2015-10-04 07:51:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1037', '407', '8', '2015-10-04 07:52:26');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1038', '407', '7', '2015-10-04 07:52:26');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1039', '407', '18', '2015-10-04 07:52:26');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1040', '407', '20', '2015-10-04 07:52:26');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1041', '409', '20', '2015-10-04 07:54:09');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1042', '409', '10', '2015-10-04 07:54:09');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1043', '409', '3', '2015-10-04 07:54:09');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1044', '409', '18', '2015-10-04 07:54:09');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1045', '409', '20', '2015-10-04 07:54:09');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1046', '409', '9', '2015-10-04 07:54:09');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1047', '411', '9', '2015-10-04 07:54:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1048', '411', '15', '2015-10-04 07:54:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1049', '411', '18', '2015-10-04 07:54:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1050', '411', '1', '2015-10-04 07:54:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1051', '411', '10', '2015-10-04 07:54:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1052', '411', '13', '2015-10-04 07:54:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1053', '413', '10', '2015-10-04 07:54:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1054', '413', '2', '2015-10-04 07:54:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1055', '413', '2', '2015-10-04 07:54:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1056', '413', '8', '2015-10-04 07:54:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1057', '413', '6', '2015-10-04 07:54:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1058', '413', '13', '2015-10-04 07:54:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1059', '415', '19', '2015-10-04 07:54:58');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1060', '415', '7', '2015-10-04 07:54:58');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1061', '415', '3', '2015-10-04 07:54:58');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1062', '417', '2', '2015-10-04 08:12:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1063', '417', '10', '2015-10-04 08:12:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1064', '417', '2', '2015-10-04 08:12:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1065', '417', '16', '2015-10-04 08:12:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1066', '419', '4', '2015-10-04 08:20:58');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1067', '419', '5', '2015-10-04 08:20:58');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1068', '419', '7', '2015-10-04 08:20:58');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1069', '419', '4', '2015-10-04 08:20:58');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1070', '421', '13', '2015-10-04 08:23:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1071', '421', '16', '2015-10-04 08:23:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1072', '421', '20', '2015-10-04 08:23:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1073', '421', '11', '2015-10-04 08:23:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1074', '421', '17', '2015-10-04 08:23:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1075', '421', '2', '2015-10-04 08:23:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1076', '423', '19', '2015-10-04 08:24:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1077', '423', '1', '2015-10-04 08:24:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1078', '423', '8', '2015-10-04 08:24:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1079', '425', '5', '2015-10-04 08:26:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1080', '425', '18', '2015-10-04 08:26:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1081', '425', '1', '2015-10-04 08:26:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1082', '427', '9', '2015-10-04 08:26:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1083', '427', '20', '2015-10-04 08:26:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1084', '427', '8', '2015-10-04 08:26:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1085', '427', '12', '2015-10-04 08:26:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1086', '427', '4', '2015-10-04 08:26:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1087', '427', '14', '2015-10-04 08:26:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1088', '429', '8', '2015-10-04 08:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1089', '429', '3', '2015-10-04 08:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1090', '429', '9', '2015-10-04 08:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1091', '429', '14', '2015-10-04 08:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1092', '429', '1', '2015-10-04 08:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1093', '429', '11', '2015-10-04 08:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1094', '431', '18', '2015-10-04 08:30:40');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1095', '431', '9', '2015-10-04 08:30:40');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1096', '431', '9', '2015-10-04 08:30:40');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1097', '431', '15', '2015-10-04 08:30:40');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1098', '431', '16', '2015-10-04 08:30:40');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1099', '433', '6', '2015-10-04 08:32:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1100', '433', '3', '2015-10-04 08:32:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1101', '433', '3', '2015-10-04 08:32:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1102', '435', '11', '2015-10-04 08:33:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1103', '435', '15', '2015-10-04 08:33:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1104', '435', '4', '2015-10-04 08:33:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1105', '435', '13', '2015-10-04 08:33:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1106', '435', '9', '2015-10-04 08:33:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1107', '435', '7', '2015-10-04 08:33:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1108', '437', '18', '2015-10-04 08:33:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1109', '437', '14', '2015-10-04 08:33:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1110', '437', '7', '2015-10-04 08:33:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1111', '437', '3', '2015-10-04 08:33:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1112', '437', '18', '2015-10-04 08:33:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1113', '437', '18', '2015-10-04 08:33:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1114', '439', '11', '2015-10-04 08:34:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1115', '439', '15', '2015-10-04 08:34:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1116', '439', '17', '2015-10-04 08:34:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1117', '439', '5', '2015-10-04 08:34:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1118', '439', '4', '2015-10-04 08:34:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1119', '439', '20', '2015-10-04 08:34:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1120', '441', '15', '2015-10-04 08:34:49');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1121', '441', '12', '2015-10-04 08:34:49');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1122', '441', '15', '2015-10-04 08:34:49');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1123', '441', '18', '2015-10-04 08:34:49');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1124', '441', '18', '2015-10-04 08:34:49');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1125', '443', '7', '2015-10-04 08:35:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1126', '443', '12', '2015-10-04 08:35:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1127', '443', '12', '2015-10-04 08:35:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1128', '443', '17', '2015-10-04 08:35:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1129', '443', '15', '2015-10-04 08:35:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1130', '445', '1', '2015-10-04 08:37:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1131', '445', '19', '2015-10-04 08:37:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1132', '445', '4', '2015-10-04 08:37:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1133', '445', '16', '2015-10-04 08:37:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1134', '445', '4', '2015-10-04 08:37:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1135', '447', '13', '2015-10-04 08:38:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1136', '447', '8', '2015-10-04 08:38:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1137', '447', '11', '2015-10-04 08:38:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1138', '447', '5', '2015-10-04 08:38:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1139', '447', '6', '2015-10-04 08:38:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1140', '447', '16', '2015-10-04 08:38:44');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1141', '449', '16', '2015-10-04 08:38:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1142', '449', '11', '2015-10-04 08:38:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1143', '449', '19', '2015-10-04 08:38:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1144', '449', '7', '2015-10-04 08:38:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1145', '449', '20', '2015-10-04 08:38:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1146', '449', '11', '2015-10-04 08:38:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1147', '451', '2', '2015-10-04 08:40:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1148', '451', '1', '2015-10-04 08:40:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1149', '451', '10', '2015-10-04 08:40:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1150', '451', '18', '2015-10-04 08:40:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1151', '451', '19', '2015-10-04 08:40:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1152', '453', '20', '2015-10-04 08:41:16');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1153', '453', '5', '2015-10-04 08:41:16');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1154', '453', '19', '2015-10-04 08:41:16');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1155', '453', '6', '2015-10-04 08:41:16');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1156', '453', '10', '2015-10-04 08:41:16');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1157', '455', '19', '2015-10-04 08:42:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1158', '455', '1', '2015-10-04 08:42:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1159', '455', '18', '2015-10-04 08:42:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1160', '455', '13', '2015-10-04 08:42:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1161', '455', '10', '2015-10-04 08:42:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1162', '455', '7', '2015-10-04 08:42:07');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1163', '457', '13', '2015-10-04 08:42:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1164', '457', '13', '2015-10-04 08:42:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1165', '457', '13', '2015-10-04 08:42:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1166', '457', '2', '2015-10-04 08:42:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1167', '457', '1', '2015-10-04 08:42:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1168', '457', '12', '2015-10-04 08:42:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1169', '459', '6', '2015-10-04 08:42:50');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1170', '459', '5', '2015-10-04 08:42:50');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1171', '459', '20', '2015-10-04 08:42:50');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1172', '461', '8', '2015-10-04 08:43:21');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1173', '461', '1', '2015-10-04 08:43:21');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1174', '461', '5', '2015-10-04 08:43:21');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1175', '463', '16', '2015-10-04 08:43:32');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1176', '463', '5', '2015-10-04 08:43:32');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1177', '463', '14', '2015-10-04 08:43:32');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1178', '463', '4', '2015-10-04 08:43:32');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1179', '465', '4', '2015-10-04 11:15:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1180', '465', '3', '2015-10-04 11:15:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1181', '465', '9', '2015-10-04 11:15:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1182', '465', '7', '2015-10-04 11:15:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1183', '465', '13', '2015-10-04 11:15:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1184', '465', '20', '2015-10-04 11:15:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1185', '467', '10', '2015-10-04 11:22:26');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1186', '467', '4', '2015-10-04 11:22:26');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1187', '467', '16', '2015-10-04 11:22:26');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1188', '469', '10', '2015-10-04 11:24:17');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1189', '469', '6', '2015-10-04 11:24:17');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1190', '469', '8', '2015-10-04 11:24:17');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1191', '469', '12', '2015-10-04 11:24:17');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1192', '471', '14', '2015-10-04 11:25:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1193', '471', '5', '2015-10-04 11:25:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1194', '471', '18', '2015-10-04 11:25:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1195', '471', '3', '2015-10-04 11:25:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1196', '471', '18', '2015-10-04 11:25:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1197', '473', '20', '2015-10-04 11:26:03');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1198', '473', '5', '2015-10-04 11:26:03');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1199', '473', '7', '2015-10-04 11:26:03');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1200', '473', '11', '2015-10-04 11:26:03');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1201', '475', '13', '2015-10-04 11:26:41');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1202', '475', '19', '2015-10-04 11:26:41');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1203', '475', '2', '2015-10-04 11:26:41');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1204', '475', '16', '2015-10-04 11:26:41');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1205', '477', '14', '2015-10-04 11:27:27');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1206', '477', '11', '2015-10-04 11:27:27');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1207', '477', '8', '2015-10-04 11:27:27');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1208', '477', '11', '2015-10-04 11:27:27');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1209', '477', '20', '2015-10-04 11:27:27');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1210', '479', '12', '2015-10-04 11:30:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1211', '479', '12', '2015-10-04 11:30:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1212', '479', '18', '2015-10-04 11:30:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1213', '479', '11', '2015-10-04 11:30:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1214', '481', '7', '2015-10-04 11:31:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1215', '481', '20', '2015-10-04 11:31:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1216', '481', '11', '2015-10-04 11:31:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1217', '481', '20', '2015-10-04 11:31:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1218', '483', '6', '2015-10-04 11:32:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1219', '483', '4', '2015-10-04 11:32:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1220', '483', '17', '2015-10-04 11:32:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1221', '483', '4', '2015-10-04 11:32:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1222', '483', '8', '2015-10-04 11:32:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1223', '483', '16', '2015-10-04 11:32:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1224', '485', '17', '2015-10-04 11:33:13');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1225', '485', '14', '2015-10-04 11:33:13');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1226', '485', '6', '2015-10-04 11:33:13');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1227', '485', '5', '2015-10-04 11:33:13');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1228', '487', '4', '2015-10-04 11:36:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1229', '487', '12', '2015-10-04 11:36:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1230', '487', '11', '2015-10-04 11:36:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1231', '489', '9', '2015-10-04 11:36:40');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1232', '489', '16', '2015-10-04 11:36:40');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1233', '489', '2', '2015-10-04 11:36:40');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1234', '489', '15', '2015-10-04 11:36:40');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1235', '491', '2', '2015-10-04 11:42:09');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1236', '491', '10', '2015-10-04 11:42:09');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1237', '491', '4', '2015-10-04 11:42:09');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1238', '491', '10', '2015-10-04 11:42:09');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1239', '493', '20', '2015-10-04 11:52:29');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1240', '493', '20', '2015-10-04 11:52:29');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1241', '493', '18', '2015-10-04 11:52:29');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1242', '495', '8', '2015-10-04 11:53:42');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1243', '495', '17', '2015-10-04 11:53:42');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1244', '495', '14', '2015-10-04 11:53:42');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1245', '495', '6', '2015-10-04 11:53:42');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1246', '495', '8', '2015-10-04 11:53:42');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1247', '495', '20', '2015-10-04 11:53:42');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1248', '497', '18', '2015-10-04 12:02:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1249', '497', '19', '2015-10-04 12:02:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1250', '497', '5', '2015-10-04 12:02:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1251', '497', '3', '2015-10-04 12:02:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1252', '497', '4', '2015-10-04 12:02:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1253', '497', '11', '2015-10-04 12:02:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1254', '499', '2', '2015-10-04 12:05:24');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1255', '499', '10', '2015-10-04 12:05:24');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1256', '499', '19', '2015-10-04 12:05:24');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1257', '499', '2', '2015-10-04 12:05:24');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1258', '501', '18', '2015-10-04 12:05:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1259', '501', '1', '2015-10-04 12:05:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1260', '501', '14', '2015-10-04 12:05:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1261', '501', '16', '2015-10-04 12:05:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1262', '501', '1', '2015-10-04 12:05:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1263', '503', '7', '2015-10-04 12:05:48');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1264', '503', '6', '2015-10-04 12:05:48');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1265', '503', '6', '2015-10-04 12:05:48');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1266', '503', '8', '2015-10-04 12:05:48');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1267', '505', '17', '2015-10-04 12:06:00');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1268', '505', '11', '2015-10-04 12:06:00');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1269', '505', '13', '2015-10-04 12:06:00');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1270', '505', '9', '2015-10-04 12:06:00');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1271', '507', '5', '2015-10-04 12:07:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1272', '507', '10', '2015-10-04 12:07:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1273', '507', '2', '2015-10-04 12:07:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1274', '507', '18', '2015-10-04 12:07:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1275', '507', '8', '2015-10-04 12:07:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1276', '507', '7', '2015-10-04 12:07:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1277', '509', '16', '2015-10-04 12:09:41');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1278', '509', '15', '2015-10-04 12:09:41');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1279', '509', '5', '2015-10-04 12:09:41');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1280', '511', '2', '2015-10-04 12:10:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1281', '511', '6', '2015-10-04 12:10:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1282', '511', '20', '2015-10-04 12:10:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1283', '511', '18', '2015-10-04 12:10:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1284', '511', '3', '2015-10-04 12:10:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1285', '513', '2', '2015-10-04 12:10:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1286', '513', '19', '2015-10-04 12:10:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1287', '513', '2', '2015-10-04 12:10:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1288', '513', '16', '2015-10-04 12:10:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1289', '513', '14', '2015-10-04 12:10:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1290', '513', '16', '2015-10-04 12:10:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1291', '515', '9', '2015-10-04 12:11:39');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1292', '515', '15', '2015-10-04 12:11:39');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1293', '515', '5', '2015-10-04 12:11:39');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1294', '515', '16', '2015-10-04 12:11:39');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1295', '515', '20', '2015-10-04 12:11:39');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1296', '517', '7', '2015-10-04 12:11:51');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1297', '517', '2', '2015-10-04 12:11:51');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1298', '517', '19', '2015-10-04 12:11:51');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1299', '517', '14', '2015-10-04 12:11:51');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1300', '519', '3', '2015-10-04 12:12:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1301', '519', '1', '2015-10-04 12:12:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1302', '519', '3', '2015-10-04 12:12:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1303', '519', '14', '2015-10-04 12:12:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1304', '519', '2', '2015-10-04 12:12:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1305', '519', '2', '2015-10-04 12:12:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1306', '521', '17', '2015-10-04 12:13:16');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1307', '521', '18', '2015-10-04 12:13:16');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1308', '521', '12', '2015-10-04 12:13:16');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1309', '523', '2', '2015-10-04 12:13:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1310', '523', '2', '2015-10-04 12:13:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1311', '523', '4', '2015-10-04 12:13:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1312', '523', '11', '2015-10-04 12:13:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1313', '525', '8', '2015-10-04 12:13:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1314', '525', '6', '2015-10-04 12:13:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1315', '525', '11', '2015-10-04 12:13:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1316', '525', '7', '2015-10-04 12:13:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1317', '525', '14', '2015-10-04 12:13:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1318', '527', '5', '2015-10-04 12:14:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1319', '527', '15', '2015-10-04 12:14:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1320', '527', '5', '2015-10-04 12:14:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1321', '527', '14', '2015-10-04 12:14:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1322', '529', '16', '2015-10-04 12:22:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1323', '529', '8', '2015-10-04 12:22:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1324', '529', '15', '2015-10-04 12:22:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1325', '529', '4', '2015-10-04 12:22:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1326', '529', '18', '2015-10-04 12:22:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1327', '529', '11', '2015-10-04 12:22:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1328', '531', '1', '2015-10-04 12:28:22');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1329', '531', '20', '2015-10-04 12:28:22');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1330', '531', '3', '2015-10-04 12:28:22');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1331', '531', '20', '2015-10-04 12:28:22');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1332', '533', '17', '2015-10-04 13:03:34');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1333', '533', '19', '2015-10-04 13:03:34');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1334', '533', '5', '2015-10-04 13:03:34');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1335', '533', '12', '2015-10-04 13:03:34');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1336', '533', '7', '2015-10-04 13:03:34');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1337', '533', '9', '2015-10-04 13:03:34');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1338', '535', '5', '2015-10-04 13:04:08');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1339', '535', '1', '2015-10-04 13:04:08');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1340', '535', '2', '2015-10-04 13:04:08');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1341', '535', '11', '2015-10-04 13:04:08');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1342', '535', '3', '2015-10-04 13:04:08');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1343', '537', '17', '2015-10-04 13:04:50');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1344', '537', '15', '2015-10-04 13:04:50');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1345', '537', '13', '2015-10-04 13:04:50');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1346', '539', '19', '2015-10-04 13:05:04');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1347', '539', '17', '2015-10-04 13:05:04');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1348', '539', '4', '2015-10-04 13:05:04');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1349', '539', '18', '2015-10-04 13:05:04');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1350', '541', '13', '2015-10-04 13:05:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1351', '541', '13', '2015-10-04 13:05:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1352', '541', '1', '2015-10-04 13:05:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1353', '541', '14', '2015-10-04 13:05:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1354', '541', '16', '2015-10-04 13:05:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1355', '541', '16', '2015-10-04 13:05:43');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1356', '543', '1', '2015-10-04 13:05:51');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1357', '543', '5', '2015-10-04 13:05:51');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1358', '543', '9', '2015-10-04 13:05:51');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1359', '543', '17', '2015-10-04 13:05:51');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1360', '545', '3', '2015-10-04 13:06:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1361', '545', '18', '2015-10-04 13:06:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1362', '545', '17', '2015-10-04 13:06:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1363', '545', '17', '2015-10-04 13:06:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1364', '545', '16', '2015-10-04 13:06:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1365', '547', '7', '2015-10-04 13:07:46');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1366', '547', '3', '2015-10-04 13:07:46');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1367', '547', '14', '2015-10-04 13:07:46');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1368', '547', '8', '2015-10-04 13:07:46');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1369', '549', '2', '2015-10-04 13:20:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1370', '549', '19', '2015-10-04 13:20:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1371', '549', '20', '2015-10-04 13:20:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1372', '549', '9', '2015-10-04 13:20:47');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1373', '551', '12', '2015-10-04 13:21:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1374', '551', '8', '2015-10-04 13:21:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1375', '551', '16', '2015-10-04 13:21:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1376', '551', '13', '2015-10-04 13:21:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1377', '551', '6', '2015-10-04 13:21:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1378', '553', '15', '2015-10-04 20:27:17');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1379', '553', '3', '2015-10-04 20:27:17');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1380', '553', '16', '2015-10-04 20:27:17');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1381', '553', '19', '2015-10-04 20:27:17');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1382', '553', '6', '2015-10-04 20:27:17');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1383', '553', '6', '2015-10-04 20:27:17');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1384', '555', '17', '2015-10-04 22:03:57');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1385', '555', '17', '2015-10-04 22:03:57');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1386', '555', '16', '2015-10-04 22:03:57');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1391', '359', '1', '2015-10-04 22:15:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1392', '359', '3', '2015-10-04 22:15:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1393', '359', '4', '2015-10-04 22:15:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1394', '557', '19', '2015-10-04 22:16:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1395', '557', '10', '2015-10-04 22:16:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1396', '557', '12', '2015-10-04 22:16:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1397', '557', '18', '2015-10-04 22:16:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1398', '559', '20', '2015-10-04 22:16:20');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1399', '559', '1', '2015-10-04 22:16:20');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1400', '559', '13', '2015-10-04 22:16:20');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1401', '559', '11', '2015-10-04 22:16:20');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1402', '559', '19', '2015-10-04 22:16:20');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1403', '561', '7', '2015-10-04 22:18:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1404', '561', '2', '2015-10-04 22:18:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1405', '561', '7', '2015-10-04 22:18:01');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1406', '563', '6', '2015-10-05 06:43:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1407', '563', '19', '2015-10-05 06:43:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1408', '563', '11', '2015-10-05 06:43:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1409', '563', '10', '2015-10-05 06:43:35');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1410', '565', '9', '2015-10-05 07:03:12');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1411', '565', '13', '2015-10-05 07:03:12');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1412', '565', '19', '2015-10-05 07:03:12');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1413', '565', '13', '2015-10-05 07:03:12');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1414', '567', '13', '2015-10-05 07:03:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1415', '567', '14', '2015-10-05 07:03:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1416', '567', '10', '2015-10-05 07:03:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1417', '567', '17', '2015-10-05 07:03:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1418', '567', '16', '2015-10-05 07:03:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1419', '567', '3', '2015-10-05 07:03:59');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1420', '569', '10', '2015-10-05 07:04:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1421', '569', '10', '2015-10-05 07:04:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1422', '569', '7', '2015-10-05 07:04:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1423', '569', '10', '2015-10-05 07:04:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1424', '569', '14', '2015-10-05 07:04:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1425', '571', '15', '2015-10-05 07:20:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1426', '571', '10', '2015-10-05 07:20:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1427', '571', '6', '2015-10-05 07:20:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1428', '573', '4', '2015-10-05 07:20:23');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1429', '573', '12', '2015-10-05 07:20:23');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1430', '573', '3', '2015-10-05 07:20:23');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1431', '573', '2', '2015-10-05 07:20:23');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1432', '573', '17', '2015-10-05 07:20:23');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1433', '575', '3', '2015-10-05 08:38:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1434', '575', '12', '2015-10-05 08:38:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1435', '575', '20', '2015-10-05 08:38:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1436', '577', '17', '2015-10-05 08:38:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1437', '577', '7', '2015-10-05 08:38:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1438', '577', '16', '2015-10-05 08:38:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1439', '577', '13', '2015-10-05 08:38:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1440', '577', '13', '2015-10-05 08:38:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1441', '577', '6', '2015-10-05 08:38:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1442', '588', '2', '2015-10-05 08:50:52');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1443', '588', '16', '2015-10-05 08:50:52');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1444', '588', '4', '2015-10-05 08:50:52');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1445', '588', '18', '2015-10-05 08:50:52');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1446', '588', '7', '2015-10-05 08:50:52');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1447', '588', '2', '2015-10-05 08:50:52');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1448', '590', '8', '2015-10-05 08:51:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1449', '590', '16', '2015-10-05 08:51:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1450', '590', '1', '2015-10-05 08:51:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1451', '590', '7', '2015-10-05 08:51:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1452', '592', '20', '2015-10-05 08:51:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1453', '592', '12', '2015-10-05 08:51:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1454', '592', '2', '2015-10-05 08:51:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1455', '592', '1', '2015-10-05 08:51:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1456', '592', '10', '2015-10-05 08:51:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1457', '592', '9', '2015-10-05 08:51:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1458', '594', '3', '2015-10-05 08:51:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1459', '594', '4', '2015-10-05 08:51:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1460', '594', '19', '2015-10-05 08:51:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1461', '594', '15', '2015-10-05 08:51:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1462', '596', '8', '2015-10-05 08:56:27');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1463', '596', '10', '2015-10-05 08:56:27');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1464', '596', '10', '2015-10-05 08:56:27');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1465', '598', '13', '2015-10-05 08:57:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1466', '598', '15', '2015-10-05 08:57:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1467', '598', '2', '2015-10-05 08:57:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1468', '598', '1', '2015-10-05 08:57:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1469', '598', '11', '2015-10-05 08:57:06');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1470', '600', '11', '2015-10-05 08:57:52');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1471', '600', '3', '2015-10-05 08:57:52');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1472', '600', '19', '2015-10-05 08:57:52');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1473', '600', '3', '2015-10-05 08:57:52');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1474', '602', '14', '2016-05-23 14:25:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1475', '602', '4', '2016-05-23 14:25:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1476', '602', '12', '2016-05-23 14:25:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1477', '602', '7', '2016-05-23 14:25:56');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1478', '604', '15', '2016-05-23 14:26:34');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1479', '604', '9', '2016-05-23 14:26:34');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1480', '604', '17', '2016-05-23 14:26:34');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1481', '606', '15', '2016-05-23 14:26:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1482', '606', '4', '2016-05-23 14:26:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1483', '606', '7', '2016-05-23 14:26:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1484', '606', '16', '2016-05-23 14:26:53');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1485', '608', '1', '2016-05-23 14:28:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1486', '608', '9', '2016-05-23 14:28:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1487', '608', '10', '2016-05-23 14:28:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1488', '608', '11', '2016-05-23 14:28:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1489', '608', '19', '2016-05-23 14:28:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1490', '608', '4', '2016-05-23 14:28:11');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1491', '610', '7', '2016-05-23 14:29:45');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1492', '610', '4', '2016-05-23 14:29:45');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1493', '610', '7', '2016-05-23 14:29:45');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1494', '610', '3', '2016-05-23 14:29:45');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1495', '612', '12', '2016-05-23 14:30:12');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1496', '612', '8', '2016-05-23 14:30:12');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1497', '612', '1', '2016-05-23 14:30:12');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1498', '612', '14', '2016-05-23 14:30:12');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1499', '612', '20', '2016-05-23 14:30:12');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1500', '614', '20', '2016-05-23 14:31:45');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1501', '614', '12', '2016-05-23 14:31:45');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1502', '614', '9', '2016-05-23 14:31:45');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1503', '616', '20', '2016-05-23 14:32:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1504', '616', '2', '2016-05-23 14:32:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1505', '616', '13', '2016-05-23 14:32:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1506', '616', '4', '2016-05-23 14:32:30');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1507', '618', '19', '2016-05-23 14:32:49');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1508', '618', '16', '2016-05-23 14:32:49');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1509', '618', '12', '2016-05-23 14:32:49');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1510', '618', '10', '2016-05-23 14:32:49');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1511', '620', '3', '2016-05-23 14:33:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1512', '620', '13', '2016-05-23 14:33:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1513', '620', '11', '2016-05-23 14:33:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1514', '620', '19', '2016-05-23 14:33:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1515', '620', '13', '2016-05-23 14:33:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1516', '620', '16', '2016-05-23 14:33:15');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1517', '622', '12', '2016-05-23 14:33:48');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1518', '622', '10', '2016-05-23 14:33:48');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1519', '622', '4', '2016-05-23 14:33:48');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1520', '622', '20', '2016-05-23 14:33:48');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1521', '624', '9', '2016-05-23 14:34:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1522', '624', '7', '2016-05-23 14:34:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1523', '624', '14', '2016-05-23 14:34:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1524', '624', '20', '2016-05-23 14:34:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1525', '624', '14', '2016-05-23 14:34:10');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1526', '626', '18', '2016-05-23 14:35:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1527', '626', '1', '2016-05-23 14:35:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1528', '626', '11', '2016-05-23 14:35:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1529', '626', '2', '2016-05-23 14:35:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1530', '626', '7', '2016-05-23 14:35:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1531', '626', '9', '2016-05-23 14:35:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1532', '628', '15', '2016-05-25 12:09:00');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1533', '628', '3', '2016-05-25 12:09:00');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1534', '628', '2', '2016-05-25 12:09:00');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1535', '628', '3', '2016-05-25 12:09:00');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1536', '628', '16', '2016-05-25 12:09:00');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1537', '628', '5', '2016-05-25 12:09:00');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1538', '630', '15', '2016-05-25 12:09:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1539', '630', '17', '2016-05-25 12:09:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1540', '630', '7', '2016-05-25 12:09:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1541', '630', '10', '2016-05-25 12:09:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1542', '630', '7', '2016-05-25 12:09:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1543', '630', '1', '2016-05-25 12:09:31');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1544', '632', '19', '2016-05-25 12:10:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1545', '632', '15', '2016-05-25 12:10:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1546', '632', '9', '2016-05-25 12:10:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1547', '632', '15', '2016-05-25 12:10:19');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1548', '634', '16', '2016-05-25 13:12:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1549', '634', '9', '2016-05-25 13:12:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1550', '634', '1', '2016-05-25 13:12:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1551', '634', '1', '2016-05-25 13:12:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1552', '634', '8', '2016-05-25 13:12:18');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1553', '636', '1', '2016-05-25 14:14:54');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1554', '636', '19', '2016-05-25 14:14:54');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1555', '636', '3', '2016-05-25 14:14:54');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1556', '638', '6', '2016-05-25 14:31:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1557', '638', '16', '2016-05-25 14:31:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1558', '638', '7', '2016-05-25 14:31:37');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1559', '640', '5', '2016-05-25 14:43:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1560', '640', '17', '2016-05-25 14:43:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1561', '640', '10', '2016-05-25 14:43:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1562', '640', '4', '2016-05-25 14:43:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1563', '640', '19', '2016-05-25 14:43:36');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1564', '642', '13', '2016-05-25 15:38:13');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1565', '642', '12', '2016-05-25 15:38:13');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1566', '642', '20', '2016-05-25 15:38:13');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1567', '642', '10', '2016-05-25 15:38:13');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1568', '642', '14', '2016-05-25 15:38:13');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1569', '644', '6', '2016-05-25 15:38:22');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1570', '644', '3', '2016-05-25 15:38:22');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1571', '644', '15', '2016-05-25 15:38:22');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1572', '644', '6', '2016-05-25 15:38:22');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1573', '646', '18', '2016-05-25 15:45:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1574', '646', '1', '2016-05-25 15:45:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1575', '646', '15', '2016-05-25 15:45:14');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1576', '648', '19', '2016-11-16 07:22:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1577', '648', '15', '2016-11-16 07:22:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1578', '648', '9', '2016-11-16 07:22:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1579', '648', '10', '2016-11-16 07:22:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1580', '648', '6', '2016-11-16 07:22:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1581', '648', '10', '2016-11-16 07:22:28');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1582', '650', '1', '2016-11-18 12:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1583', '650', '16', '2016-11-18 12:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1584', '650', '8', '2016-11-18 12:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1585', '650', '18', '2016-11-18 12:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1586', '650', '4', '2016-11-18 12:27:25');
INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`) VALUES ('1587', '650', '8', '2016-11-18 12:27:25');


#
# TABLE STRUCTURE FOR: rel_groepen__adressen
#

DROP TABLE IF EXISTS `rel_groepen__adressen`;

CREATE TABLE `rel_groepen__adressen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_groepen` int(11) NOT NULL,
  `id_adressen` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=399 DEFAULT CHARSET=latin1;

INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('347', '30', '9');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('348', '39', '10');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('349', '36', '3');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('350', '30', '14');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('351', '32', '3');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('352', '29', '4');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('353', '32', '10');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('354', '30', '7');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('355', '31', '10');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('356', '36', '9');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('357', '33', '14');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('358', '39', '8');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('359', '32', '6');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('360', '39', '3');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('361', '31', '3');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('362', '36', '1');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('363', '36', '13');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('364', '31', '4');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('365', '40', '7');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('366', '31', '10');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('367', '31', '7');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('368', '30', '12');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('369', '29', '9');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('370', '33', '11');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('371', '36', '4');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('372', '40', '11');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('373', '31', '13');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('374', '39', '1');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('375', '33', '1');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('376', '29', '11');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('377', '32', '1');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('378', '32', '12');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('379', '33', '8');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('380', '31', '9');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('381', '30', '11');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('382', '36', '3');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('383', '40', '8');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('384', '32', '5');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('385', '39', '3');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('386', '36', '9');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('387', '30', '3');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('388', '39', '3');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('389', '32', '5');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('390', '40', '6');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('391', '33', '8');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('392', '31', '11');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('393', '30', '13');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('394', '39', '9');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('395', '33', '11');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('396', '30', '6');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('397', '29', '8');
INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`) VALUES ('398', '32', '1');


#
# TABLE STRUCTURE FOR: rel_users__groups
#

DROP TABLE IF EXISTS `rel_users__groups`;

CREATE TABLE `rel_users__groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) unsigned NOT NULL,
  `id_user_group` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

INSERT INTO `rel_users__groups` (`id`, `id_user`, `id_user_group`) VALUES ('1', '1', '1');
INSERT INTO `rel_users__groups` (`id`, `id_user`, `id_user_group`) VALUES ('2', '2', '3');
INSERT INTO `rel_users__groups` (`id`, `id_user`, `id_user_group`) VALUES ('72', '3', '2');


#
# TABLE STRUCTURE FOR: res_media_files
#

DROP TABLE IF EXISTS `res_media_files`;

CREATE TABLE `res_media_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `b_exists` tinyint(1) NOT NULL DEFAULT '1',
  `file` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL,
  `str_type` varchar(10) NOT NULL DEFAULT '',
  `str_title` varchar(255) NOT NULL,
  `dat_date` date NOT NULL,
  `int_size` int(11) NOT NULL,
  `int_img_width` int(11) NOT NULL,
  `int_img_height` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

#
# TABLE STRUCTURE FOR: tbl_adressen
#

DROP TABLE IF EXISTS `tbl_adressen`;

CREATE TABLE `tbl_adressen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_address` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_zipcode` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_city` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('1', 'Schooolstraat 1', '1234AB', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('2', 'Lesbank 12', '1234MN', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('3', 'Taalsteeg 20', '1234QR', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('4', 'Rekenpark 42', '1234IJ', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('5', 'Bibliotheeklaan 36', '1234GH', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('6', 'Schoonschrijfdreef 18', '1234OP', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('7', 'Overblijf 16', '1234KL', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('8', 'Proefwerk 10', '1234DK', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('9', 'Dicteedreef 123', '1234CD', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('10', 'Spiekspui 7', '1234EF', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('11', 'Lessenaar 22', '1234ST', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('12', 'Prikbordlaan 32', '1234UV', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('13', 'Alumnidijk 100', '1234WX', 'Schoooldorp');
INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`) VALUES ('14', 'Knikkerplein 21', '1234YZ', 'Schoooldorp');


#
# TABLE STRUCTURE FOR: tbl_crud
#

DROP TABLE IF EXISTS `tbl_crud`;

CREATE TABLE `tbl_crud` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_insert` varchar(100) NOT NULL DEFAULT '',
  `str_update` varchar(100) NOT NULL,
  `tme_last_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_changed` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=651 DEFAULT CHARSET=utf8;

INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('359', 'TEST', 'TEST', '2015-10-04 22:04:58', '1');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('401', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('402', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('403', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('404', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('405', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('406', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('407', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('408', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('409', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('410', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('411', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('412', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('413', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('414', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('415', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('416', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('417', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('418', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('419', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('420', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('421', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('422', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('423', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('424', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('425', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('426', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('427', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('428', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('429', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('430', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('431', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('432', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('433', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('434', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('435', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('436', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('437', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('438', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('439', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('440', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('441', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('442', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('443', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('444', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('445', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('446', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('447', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('448', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('449', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('450', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('451', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('452', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('453', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('454', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('455', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('456', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('457', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('458', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('459', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('460', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('461', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('462', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('463', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('464', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('465', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('466', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('467', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('468', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('469', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('470', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('471', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('472', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('473', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('474', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('475', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('476', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('477', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('478', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('479', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('480', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('481', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('482', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('483', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('484', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('485', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('486', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('487', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('488', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('489', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('490', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('491', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('492', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('493', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('494', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('495', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('496', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('497', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('498', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('499', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('500', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('501', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('502', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('503', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('504', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('505', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('506', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('507', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('508', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('509', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('510', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('511', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('512', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('513', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('514', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('515', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('516', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('517', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('518', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('519', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('520', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('521', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('522', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('523', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('524', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('525', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('526', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('527', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('528', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('529', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('530', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('531', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('532', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('533', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('534', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('535', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('536', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('537', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('538', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('539', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('540', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('541', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('542', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('543', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('544', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('545', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('546', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('547', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('548', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('549', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('550', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('551', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('552', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('553', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('554', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('555', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('556', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('557', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('558', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('559', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('560', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('561', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('562', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('563', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('564', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('565', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('566', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('567', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('568', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('569', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('570', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('571', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('572', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('573', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('574', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('575', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('576', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('577', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('578', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('579', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('580', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('581', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('582', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('583', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('584', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('585', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('586', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('587', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('588', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('589', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('590', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('591', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('592', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('593', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('594', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('595', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('596', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('597', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('598', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('599', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('600', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('601', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('602', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('603', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('604', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('605', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('606', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('607', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('608', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('609', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('610', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('611', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('612', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('613', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('614', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('615', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('616', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('617', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('618', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('619', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('620', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('621', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('622', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('623', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('624', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('625', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('626', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('627', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('628', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('629', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('630', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('631', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('632', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('633', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('634', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('635', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('636', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('637', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('638', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('639', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('640', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('641', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('642', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('643', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('644', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('645', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('646', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('647', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('648', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('649', 'INSERT xZuJCOaI', 'UPDATE 1J5FAH6r', '2016-11-18 12:27:25', '0');
INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `tme_last_changed`, `user_changed`) VALUES ('650', '_INSERT sJyq3oGQ', '_UPDATE TdmprxAw', '2016-11-18 12:27:25', '0');


#
# TABLE STRUCTURE FOR: tbl_crud2
#

DROP TABLE IF EXISTS `tbl_crud2`;

CREATE TABLE `tbl_crud2` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_other` varchar(100) NOT NULL DEFAULT '',
  `tme_last_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_changed` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('1', 'Varius tempus condimentum adipiscing fermentum ', '0000-00-00 00:00:00', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('2', 'Aliquam lobortis elit ', '2015-05-18 05:03:50', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('3', 'Commodo ut eros cursus ', '2015-01-17 07:10:02', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('4', 'TEST', '2015-10-04 13:20:25', '1');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('5', 'Fermentum arcu ', '2015-03-31 22:32:16', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('6', 'Curabitur platea quam hendrerit primis ', '2016-05-19 16:37:54', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('7', 'Venenatis felis ', '2015-11-10 15:06:22', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('8', 'Ac ', '2015-05-14 00:38:36', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('9', 'Congue inceptos ', '2015-04-16 06:22:17', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('10', 'In aenean nam ', '2015-07-17 05:31:04', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('11', 'Risus habitasse duis lorem dictum ', '2015-03-14 02:55:02', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('12', 'Metus ', '2016-01-29 07:39:02', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('13', 'Rutrum mauris himenaeos mauris augue ', '2016-02-12 02:10:01', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('14', 'Aliquam ', '2016-03-10 05:30:41', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('15', 'TEST', '2015-10-04 13:20:38', '1');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('16', 'Nulla ', '2015-06-01 13:32:56', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('17', 'Nisl eleifend netus dictum ', '2016-07-30 12:51:36', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('18', 'Cursus dapibus ', '2016-07-24 22:39:55', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('19', 'Erat vitae commodo quam ', '2016-05-06 06:36:41', '0');
INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`) VALUES ('20', 'Curabitur varius proin adipiscing gravida ', '2016-02-14 05:10:23', '0');


#
# TABLE STRUCTURE FOR: tbl_groepen
#

DROP TABLE IF EXISTS `tbl_groepen`;

CREATE TABLE `tbl_groepen` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `uri` varchar(50) NOT NULL,
  `order` smallint(4) NOT NULL,
  `str_title` varchar(50) NOT NULL DEFAULT '',
  `str_soort` varchar(5) NOT NULL DEFAULT 'groep',
  `media_tekening` varchar(128) NOT NULL,
  `rgb_kleur` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

INSERT INTO `tbl_groepen` (`id`, `uri`, `order`, `str_title`, `str_soort`, `media_tekening`, `rgb_kleur`) VALUES ('29', 'groep_2015-2016_d', '3', 'D', 'groep', '', '');
INSERT INTO `tbl_groepen` (`id`, `uri`, `order`, `str_title`, `str_soort`, `media_tekening`, `rgb_kleur`) VALUES ('30', 'groep_2015-2016_handvaardigheid', '6', 'Handvaardigheid', 'vak', '', '');
INSERT INTO `tbl_groepen` (`id`, `uri`, `order`, `str_title`, `str_soort`, `media_tekening`, `rgb_kleur`) VALUES ('31', 'groep_2015-2016_gym', '5', 'Gym', 'vak', '', '');
INSERT INTO `tbl_groepen` (`id`, `uri`, `order`, `str_title`, `str_soort`, `media_tekening`, `rgb_kleur`) VALUES ('32', 'groep_2015-2016_c', '2', 'C', 'groep', '', '');
INSERT INTO `tbl_groepen` (`id`, `uri`, `order`, `str_title`, `str_soort`, `media_tekening`, `rgb_kleur`) VALUES ('33', 'groep_2015-2016_b', '1', 'B', 'groep', '', '');
INSERT INTO `tbl_groepen` (`id`, `uri`, `order`, `str_title`, `str_soort`, `media_tekening`, `rgb_kleur`) VALUES ('36', 'groep_2015-2016_a', '0', 'A', 'groep', '', '');
INSERT INTO `tbl_groepen` (`id`, `uri`, `order`, `str_title`, `str_soort`, `media_tekening`, `rgb_kleur`) VALUES ('39', 'groep_2015-2016_e', '4', 'E', 'groep', '', '');
INSERT INTO `tbl_groepen` (`id`, `uri`, `order`, `str_title`, `str_soort`, `media_tekening`, `rgb_kleur`) VALUES ('40', 'groep_2015-2016_muziek', '7', 'Muziek', 'vak', '', '');


#
# TABLE STRUCTURE FOR: tbl_kinderen
#

DROP TABLE IF EXISTS `tbl_kinderen`;

CREATE TABLE `tbl_kinderen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_first_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_middle_name` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_last_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `id_adressen` int(11) NOT NULL,
  `id_groepen` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('2', 'Adam', '', 'Aalts', '7', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('3', 'Aafje', '', 'Aarden', '4', '31');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('6', 'Albert', '', 'Adriaansen', '4', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('8', 'Aaron', 'van', 'Alenburg', '9', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('9', 'Abbe', 'van', 'Amstel', '7', '31');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('10', 'Abdul', '', 'Ansems', '14', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('12', 'Abel', '', 'Appelman', '4', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('13', 'Ada', 'van', 'Arkel', '13', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('16', 'Adriane', '', 'Arts', '13', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('17', 'Alwin', '', 'Aschman', '8', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('18', 'Alissa', 'van', 'Asten', '13', '39');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('19', 'Amir', '', 'Armin', '1', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('21', 'Alfred', '', 'Albus', '12', '30');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('23', 'Agnes', '', 'Aeije', '5', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('26', 'Aida', '', 'Adelaar', '11', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('27', 'Andreas', '', 'Asperger', '6', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('28', 'Aisley', 'van', 'Asissi', '7', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('29', 'Aldo', '', 'Akkerman', '12', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('30', 'Alexander', '', 'Averdijk', '3', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('31', 'Andries', '', 'Andermans', '1', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('32', 'Bart', 'van', 'Baalen', '5', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('33', 'Bas', '', 'Bartels', '13', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('35', 'Beau', '', 'Barents', '13', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('36', 'Beatrijs', 'van', 'Beeck', '1', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('37', 'Berend', '', 'Beckham', '11', '39');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('38', 'Bert', 'van', 'Beieren', '13', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('39', 'Bobby', '', 'Bosch', '9', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('40', 'Bo', 'van den', 'Berg', '1', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('41', 'Boy', 'den', 'Buytelaar', '12', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('42', 'Brian', '', 'Blaak', '14', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('43', 'Bonnie', '', 'Bezemer', '11', '30');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('44', 'Bram', '', 'Bouhuizen', '7', '30');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('45', 'Boyd', 'de', 'Bont', '4', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('46', 'Bregje', '', 'Brandt', '14', '31');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('48', 'Brigitte', 'de', 'Bruijn', '2', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('49', 'Britt', '', 'Brouwer', '4', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('50', 'Bregje', 'van', 'Buuren', '3', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('51', 'Bruno', '', 'Buijs', '7', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('53', 'Busra', '', 'Blonk', '14', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('54', 'Boudewijn', '', 'Bolkesteijn', '9', '31');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('55', 'Caspar', '', 'Claesner', '6', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('56', 'Caoa', '', 'Cammel', '13', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('57', 'Callen', '', 'Cordet', '1', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('58', 'Cecile', '', 'Coolen', '12', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('59', 'Chelso', '', 'Coenen', '2', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('60', 'Cedric', 'van', 'Clootwijck', '9', '31');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('61', 'Christiaan', '', 'Corstiaens', '6', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('62', 'Ciska', '', 'Courtier', '10', '30');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('64', 'Claire', '', 'Cosman', '13', '31');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('65', 'Coen', 'van', 'Cant', '14', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('66', 'Constantijn', '', 'Cornelissen', '6', '30');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('67', 'Daan', '', 'Dekker', '10', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('68', 'Dagmar', '', 'Dijkman', '12', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('69', 'Dafne', '', 'Dirksen', '12', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('70', 'Dago', 'van', 'Dokkum', '14', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('71', 'Damian', '', 'Dorsman', '6', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('73', 'Daniëlle', '', 'Dries', '6', '31');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('74', 'Dick', 'van', 'Duyvenvoorde', '9', '30');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('75', 'Dirk', '', 'Dubois', '14', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('76', 'Djara', 'van', 'Dillen', '14', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('77', 'Dianne', 'van', 'Dijk', '11', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('78', 'Dinand', '', 'Doornhem', '11', '31');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('80', 'Dineke', 'van', 'Dommelen', '4', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('81', 'Ditmar', '', 'Domela', '7', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('82', 'Dolf', 'van', 'Dam', '10', '30');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('83', 'Dominick', '', 'Dubois', '12', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('84', 'Donald', '', 'Duik', '1', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('86', 'Driek', '', 'Doesburg', '13', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('87', 'Dorien', '', 'Draaisma', '6', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('88', 'Driek', '', 'Doesburg', '14', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('89', 'Dries', '', 'Dekkers', '9', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('90', 'Dunya', '', 'Doorhof', '5', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('92', 'Ede', 'van', 'Eck', '11', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('93', 'Edith', '', 'Eelman', '7', '31');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('94', 'Edwin', '', 'Etter', '9', '33');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('95', 'Eefke', '', 'Elberts', '14', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('96', 'Eelco', '', 'Eisenaar', '6', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('97', 'Egbert', 'van', 'Emmelen', '5', '39');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('98', 'Eline', '', 'Erhout', '13', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('99', 'Elisabeth', '', 'Engels', '9', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('103', 'Elissa', 'van', 'Elzas', '5', '39');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('104', 'Els', '', 'Evertsen', '9', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('105', 'Eva', 'van', 'Evelingen', '5', '30');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('107', 'Emanuel', '', 'Estey', '13', '40');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('108', 'Emiel', '', 'Eijkelboom', '3', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('110', 'Epke', 'van', 'Essen', '11', '36');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('111', 'Ernst', '', 'Everts', '8', '39');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('112', 'Erwin', '', 'Ehre', '12', '39');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('113', 'Esmée', 'van', 'Egisheim', '3', '29');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('114', 'Esmeralda', 'van', 'Es', '6', '32');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('115', 'Eugenie', 'van den', 'Euvel', '14', '31');
INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`) VALUES ('116', 'Evy', '', 'Eisenhouwer', '12', '30');


#
# TABLE STRUCTURE FOR: tbl_links
#

DROP TABLE IF EXISTS `tbl_links`;

CREATE TABLE `tbl_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `str_title` varchar(255) NOT NULL DEFAULT '',
  `url_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

INSERT INTO `tbl_links` (`id`, `str_title`, `url_url`) VALUES ('1', 'Jan den Besten - webontwerp en geluidsontwerp', 'http://www.jandenbesten.net');
INSERT INTO `tbl_links` (`id`, `str_title`, `url_url`) VALUES ('2', 'FlexyAdmin', 'http://www.flexyadmin.com');
INSERT INTO `tbl_links` (`id`, `str_title`, `url_url`) VALUES ('7', 'Email FlexyAdmin', 'mailto:info@flexyadmin.com');


#
# TABLE STRUCTURE FOR: tbl_menu
#

DROP TABLE IF EXISTS `tbl_menu`;

CREATE TABLE `tbl_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` smallint(6) NOT NULL DEFAULT '0',
  `self_parent` int(11) NOT NULL DEFAULT '0',
  `uri` varchar(100) NOT NULL,
  `str_title` varchar(255) NOT NULL DEFAULT '',
  `txt_text` text NOT NULL,
  `medias_fotos` varchar(1000) NOT NULL,
  `b_visible` tinyint(1) NOT NULL DEFAULT '1',
  `str_module` varchar(30) NOT NULL,
  `stx_description` mediumtext NOT NULL,
  `str_keywords` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `tbl_menu` (`id`, `order`, `self_parent`, `uri`, `str_title`, `txt_text`, `medias_fotos`, `b_visible`, `str_module`, `stx_description`, `str_keywords`) VALUES ('1', '0', '0', 'gelukt', 'Gelukt!', '<p>Als je dit ziet is het je gelukt om FlexyAdmin te installeren en werkend te krijgen. <br />Je hebt nu een standaard-installatie van een zeer eenvoudige basis-site.</p>\n<h2>Hoe verder</h2>\n<ul>\n<li>Pas de HTML aan in de map <em>site/views</em>. <em>site.php</em> is de basis view van je site en <em>page.php</em> de afzonderlijke pagina\'s.</li>\n<li>Pas de Stylesheets aan. Deze vindt je in de map <em>site/assets/css</em>.</li>\n<li>Handiger is om <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> te gebruiken.</li>\n</ul>\n<h2>LESS</h2>\n<p>FlexyAdmin ondersteund <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> in combinatie met een Gulp die het compileren verzorgd.</p>\n<ul>\n<li>Je vindt de <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> bestanden voor de standaard template in <em>site/assets/less-default.</em></li>\n<li>Om <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> te compileren tot CSS heeft FlexyAdmin een handige Gulpfile, zie hierna.<em><br /></em></li>\n</ul>\n<h2>Gulp</h2>\n<p>Als je gebruikt maakt van LESS heb je een compiler nodig om LESS om te zetten in CSS. FlexyAdmin maakt daarvoor gebruik van <a href=\"http://gulpjs.com/\" target=\"_blank\">Gulp</a>.<br />Gulp is een zogenaamde \'taskmanager\' en verzorgt automatisch een aantal taken. De bij FlexyAdmin geleverde Gulpfile verzorgt deze taken voor LESS en CSS:</p>\n<ul>\n<li>Compileren van LESS naar CSS</li>\n<li>Samenvoegen van alle CSS bestanden tot &eacute;&eacute;n CSS bestand</li>\n<li>Automatisch prefixen van CSS regels voor diverse browser (moz-, o-, webkit- e.d.)</li>\n<li>Rem units omzetten naar px units zodat browser die geen rem units kennen terugvallen op px (met name IE8)</li>\n<li>Minificeren van het CSS bestand.</li>\n</ul>\n<p>En deze taken voor Javascript:</p>\n<ul>\n<li>Javascript testen op veel voorkomende fouten met <a href=\"http://jshint.com/\" target=\"_blank\">JSHint</a></li>\n<li>Alle Javascript bestanden samenvoegen tot &eacute;&eacute;n bestand en deze minificeren.</li>\n</ul>\n<h2>Bower</h2>\n<p>Naast Gulp wordt FlexyAdmin ook geleverd met <a href=\"http://bower.io/\" target=\"_blank\">Bower</a>. Daarmee kun je je al je externe plugins handig installeren en updaten (zoals jQuery en Bootstrap).</p>\n<h2>gulpfile.js</h2>\n<p>Hoe je Gulp en Bower aan de praat kunt krijgen en welke gulp commando\'s er allemaal zijn lees je aan het begin van de gulpfile in de root: <em>gulpfile.js</em></p>\n<h2>Bootstrap</h2>\n<p>In plaats van het standaard minimale template kun je ook gebruik maken van <a href=\"http://getbootstrap.com/\">Bootstrap:</a></p>\n<ul>\n<li>Je vindt de Bootstrap bestanden in <em>site/assets/less-bootstrap</em></li>\n<li>Stel in <em>site/config/config.php:</em> <code>$config[\'framework\']=\'bootstrap\';</code></li>\n<li>Stel in <em>gulpfile.js: </em><code>var framework = \'bootstrap\';</code></li>\n<li>Bootstrap kun je alleen gebruiken in combinatie met LESS en Gulp.</li>\n</ul>', '', '1', '', '', '');
INSERT INTO `tbl_menu` (`id`, `order`, `self_parent`, `uri`, `str_title`, `txt_text`, `medias_fotos`, `b_visible`, `str_module`, `stx_description`, `str_keywords`) VALUES ('2', '1', '0', 'een_pagina', 'Een pagina', '', '', '1', '', '', '');
INSERT INTO `tbl_menu` (`id`, `order`, `self_parent`, `uri`, `str_title`, `txt_text`, `medias_fotos`, `b_visible`, `str_module`, `stx_description`, `str_keywords`) VALUES ('3', '2', '2', 'subpagina', 'Subpagina', '<p>Een subpagina</p>', '', '1', '', '', '');
INSERT INTO `tbl_menu` (`id`, `order`, `self_parent`, `uri`, `str_title`, `txt_text`, `medias_fotos`, `b_visible`, `str_module`, `stx_description`, `str_keywords`) VALUES ('5', '3', '2', 'een_pagina', 'Nog een subpagina', '', '', '1', 'example', '', '');
INSERT INTO `tbl_menu` (`id`, `order`, `self_parent`, `uri`, `str_title`, `txt_text`, `medias_fotos`, `b_visible`, `str_module`, `stx_description`, `str_keywords`) VALUES ('4', '4', '0', 'contact', 'Contact', '<p>Hier een voorbeeld van een eenvoudig <a href=\"mailto:info@flexyadmin.com\">contactformulier</a>.</p>', '', '1', 'forms.contact', '', '');


#
# TABLE STRUCTURE FOR: tbl_site
#

DROP TABLE IF EXISTS `tbl_site`;

CREATE TABLE `tbl_site` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `str_title` varchar(255) NOT NULL,
  `str_author` varchar(255) NOT NULL,
  `url_url` varchar(255) NOT NULL,
  `email_email` varchar(255) NOT NULL,
  `stx_description` text NOT NULL,
  `stx_keywords` text NOT NULL,
  `str_google_analytics` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `tbl_site` (`id`, `str_title`, `str_author`, `url_url`, `email_email`, `stx_description`, `stx_keywords`, `str_google_analytics`) VALUES ('1', 'FlexyAdmin', 'Jan den Besten', 'http://www.flexyadmin.com/', 'info@flexyadmin.com', '', '', '');


#
# TABLE STRUCTURE FOR: cfg_sessions
#

DROP TABLE IF EXISTS `cfg_sessions`;

CREATE TABLE `cfg_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cfg_sessions_id_ip` (`id`,`ip_address`),
  KEY `cfg_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# TABLE STRUCTURE FOR: log_activity
#

DROP TABLE IF EXISTS `log_activity`;

CREATE TABLE `log_activity` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `tme_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `stx_activity` longtext NOT NULL,
  `str_activity_type` varchar(10) NOT NULL DEFAULT '',
  `str_model` varchar(255) NOT NULL,
  `str_key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

#
# TABLE STRUCTURE FOR: log_stats
#

DROP TABLE IF EXISTS `log_stats`;

CREATE TABLE `log_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tme_date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `str_uri` varchar(100) NOT NULL,
  `str_browser` varchar(20) NOT NULL,
  `str_version` varchar(8) NOT NULL,
  `str_platform` varchar(25) NOT NULL,
  `str_referrer` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# TABLE STRUCTURE FOR: log_login_attempts
#

DROP TABLE IF EXISTS `log_login_attempts`;

CREATE TABLE `log_login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8;

