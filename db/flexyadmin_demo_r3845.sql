# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.5.38)
# Database: flexyadmin_demo
# Generation Time: 2016-11-16 06:27:33 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table cfg_admin_menu
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_admin_menu` WRITE;
/*!40000 ALTER TABLE `cfg_admin_menu` DISABLE KEYS */;

INSERT INTO `cfg_admin_menu` (`id`, `order`, `str_ui_name`, `b_visible`, `id_user_group`, `str_type`, `api`, `path`, `table`, `str_table_where`)
VALUES
	(1,0,'Home',1,3,'api','API_home','','',''),
	(2,1,'Logout',1,3,'api','API_logout','','',''),
	(3,2,'Help',1,3,'api','API_help','','',''),
	(4,4,'# all normal tables (if user has rights)',1,3,'all_tbl_tables','','','',''),
	(5,6,'# all media (if user has rights)',1,3,'all_media','','','',''),
	(6,10,'# all tools (if user has rights)',1,3,'tools','','','',''),
	(7,14,'# all config tables (if user has rights)',1,1,'all_cfg_tables','','','',''),
	(8,3,'',1,3,'seperator','','','',''),
	(9,7,'',1,3,'seperator','','','',''),
	(10,11,'',1,3,'seperator','','','',''),
	(11,8,'_stats_menu',1,3,'api','API_plugin_stats','','',''),
	(12,9,'',1,3,'seperator','','','',''),
	(16,12,'# all result tables (if there are any)',1,1,'all_res_tables','','','',''),
	(17,13,'',1,1,'seperator','','','',''),
	(18,5,'',1,3,'seperator','','','','');

/*!40000 ALTER TABLE `cfg_admin_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_configurations
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_configurations` WRITE;
/*!40000 ALTER TABLE `cfg_configurations` DISABLE KEYS */;

INSERT INTO `cfg_configurations` (`id`, `int_pagination`, `b_use_editor`, `str_class`, `str_valid_html`, `table`, `b_add_internal_links`, `str_buttons1`, `str_buttons2`, `str_buttons3`, `int_preview_width`, `int_preview_height`, `str_formats`, `str_styles`, `txt_help`, `str_revision`)
VALUES
	(1,20,1,'high','','tbl_links',1,'cut,copy,pastetext,pasteword,selectall,undo,bold,italic,bullist,formatselect,removeformat,link,unlink,image,embed','','','450','500','h2,h3','','','3845');

/*!40000 ALTER TABLE `cfg_configurations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_email
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cfg_email`;

CREATE TABLE `cfg_email` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_subject_nl` varchar(255) CHARACTER SET utf8 NOT NULL,
  `txt_email_nl` text CHARACTER SET utf8 NOT NULL,
  `str_subject_en` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `txt_email_en` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `cfg_email` WRITE;
/*!40000 ALTER TABLE `cfg_email` DISABLE KEYS */;

INSERT INTO `cfg_email` (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`)
VALUES
	(1,'test','Een test email van {site_title}','<p>Dit is een testmail, verzonden van {site_title} op {site_url}</p>\n<p>&nbsp;</p>\n<p>&nbsp;</p>\n<p>Naam {name}</p>\n<p>&nbsp;</p>\n<p>Bestaat niet {bestaat_niet}</p>\n<p>&nbsp;</p>','test','<p>TEST</p>'),
	(2,'login_admin_new_register','Nieuw account aangevraagd voor {site_title}','<h1>Een nieuw account is aangevraag door {identity} </h1>\n<p>Log in om de aanvraag te beoordelen.</p>\n','New account asked for {site_title}','<h1>A new account is being asked for by {identity} </h1>\n<p>Log in to deny or accept the registration.</p>'),
	(3,'login_accepted','Account voor {site_title} geaccepteerd','<h1>Account aanvraag voor {identity} is geaccepteerd.</h1>\n<p>U kunt nu inloggen.</p>','Account for {site_title} accepted','<h1>Account registration for {identity} is accepted.</h1>\n<p>You can login now.</p>'),
	(4,'login_activate','Activeer account voor {site_title}','<h1>Activeer de aanmelding voor {identity}</h1>\n<p>Klik op <a href=\"{site_url}/{activate_uri}?id={user_id}&amp;activation={activation}\">deze link</a> om je account te activeren.</p>','Activate your account for {site_title}','<h1>Activate account for {identity}</h1>\n<p>Please click <a href=\"{site_url}/{activate_uri}?id={user_id}&amp;activation={activation}\">this link</a> to activate your account.</p>'),
	(5,'login_deny','Account aanvraag voor {site_title} afgewezen','<h1>Afgewezen account voor {identity}</h1>\n<p>Uw aanvraag voor een account is afgewezen.</p>','Account for {site_title} denied','<h1>Denied account for {identity}</h1>\n<p>Your account is denied.</p>'),
	(6,'login_forgot_password','Nieuw wachtwoord voor {site_title}','<h1>Nieuw wachtwoord aanvragen voor {identity}</h1>\n<p>Klik hier om <a href=\"{site_url}{forgotten_password_uri}?code={forgotten_password_code}\">wachtwoord te resetten</a>.</p>','New password for {site_title}','<h1>New password request for {identity}</h1>\n<p>Click on <a href=\"{site_url}{forgotten_password_uri}?code={forgotten_password_code}\">to restet your password</a>.</p>'),
	(7,'login_new_password','Nieuwe inloggegevens voor {site_title}','<h1>Je nieuwe inlogggevens voor {site_title}:</h1>\n<p>Gebruiker: {identity}<br /> Wachtwoord: {password}</p>','New login for {site_title}','<h3>You got an account.</h3>\n<p>Login with these settings:</p>\n<p>Username : {identity}<br />Password : {password}</p>'),
	(8,'login_new_account','Welkom en inloggegevens voor {site_title}','<h1>Welkom bij {site_title}</h1>\n<p>Hieronder staan je inloggegevens.</p>\n<p>Gebruiker: {identity}<br /> Wachtwoord: {password}</p>','New login for {site_title}','<h1>Welcome at {site_title}</h1>\n<p>Login with these settings:</p>\n<p>Username : {identity}<br />Password : {password}</p>');

/*!40000 ALTER TABLE `cfg_email` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_field_info
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_field_info` WRITE;
/*!40000 ALTER TABLE `cfg_field_info` DISABLE KEYS */;

INSERT INTO `cfg_field_info` (`id`, `field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`)
VALUES
	(2,'tbl_menu.stx_description',0,1,' ','Extra',0,'',0,0,'','0',''),
	(3,'tbl_menu.str_keywords',0,1,' ','Extra',0,'',0,0,'','0',''),
	(4,'tbl_site.str_title',1,1,' ','',0,'',0,0,'','',''),
	(5,'tbl_site.str_author',1,1,' ','',0,'',0,0,'','',''),
	(6,'tbl_site.url_url',1,1,' ','',0,'',0,0,'','prep_url_mail',''),
	(7,'tbl_site.email_email',1,1,' ','',0,'',0,0,'','',''),
	(8,'tbl_site.stx_description',1,1,' ','',0,'',0,0,'','',''),
	(9,'tbl_site.stx_keywords',1,1,' ','',0,'',0,0,'','',''),
	(10,'tbl_site.str_google_analytics',1,1,' ','',0,'',0,0,'','valid_google_analytics',''),
	(11,'tbl_menu.str_module',1,1,' ','Extra',0,'|forms.contact|example',0,0,'','0',''),
	(12,'tbl_links.url_url',1,1,' ','',0,'',0,0,NULL,'prep_url_mail',''),
	(13,'tbl_links.url_url',1,1,' ','',0,'',0,0,NULL,'prep_url_mail',''),
	(14,'tbl_menu.str_title',1,1,' ','',0,'',0,0,'','required','');

/*!40000 ALTER TABLE `cfg_field_info` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_img_info
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_img_info` WRITE;
/*!40000 ALTER TABLE `cfg_img_info` DISABLE KEYS */;

INSERT INTO `cfg_img_info` (`id`, `path`, `int_min_width`, `int_min_height`, `b_resize_img`, `int_img_width`, `int_img_height`, `b_create_1`, `int_width_1`, `int_height_1`, `str_prefix_1`, `str_suffix_1`, `b_create_2`, `int_width_2`, `int_height_2`, `str_prefix_2`, `str_suffix_2`)
VALUES
	(1,'pictures',0,0,1,300,2000,1,100,1000,'_thumb_','',0,0,0,'','');

/*!40000 ALTER TABLE `cfg_img_info` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_media_info
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_media_info` WRITE;
/*!40000 ALTER TABLE `cfg_media_info` DISABLE KEYS */;

INSERT INTO `cfg_media_info` (`id`, `order`, `path`, `b_visible`, `str_types`, `b_encrypt_name`, `fields_media_fields`, `b_pagination`, `b_add_empty_choice`, `b_dragndrop`, `str_order`, `int_last_uploads`, `fields_check_if_used_in`, `str_autofill`, `fields_autofill_fields`, `b_in_media_list`, `b_in_img_list`, `b_in_link_list`, `b_user_restricted`, `b_serve_restricted`)
VALUES
	(1,0,'pictures',1,'jpg,jpeg,gif,png',0,'0',1,1,1,'_rawdate',5,'0','','0',0,1,0,0,0),
	(2,1,'downloads',1,'pdf,doc,docx,xls,xlsx,png,jpg',0,'0',1,0,0,'_rawdate',5,'','','0',0,0,1,0,0);

/*!40000 ALTER TABLE `cfg_media_info` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_sessions
# ------------------------------------------------------------

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



# Dump of table cfg_table_info
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_table_info` WRITE;
/*!40000 ALTER TABLE `cfg_table_info` DISABLE KEYS */;

INSERT INTO `cfg_table_info` (`id`, `order`, `table`, `b_visible`, `str_order_by`, `b_pagination`, `b_jump_to_today`, `str_fieldsets`, `str_abstract_fields`, `str_options_where`, `b_add_empty_choice`, `str_form_many_type`, `str_form_many_order`, `int_max_rows`, `b_grid_add_many`, `b_form_add_many`, `b_freeze_uris`)
VALUES
	(1,0,'tbl_site',1,'',0,0,'','','',1,'dropdown','last',1,0,1,0),
	(2,2,'tbl_links',1,'',0,0,'','str_title','',1,'dropdown','last',0,0,1,0),
	(3,1,'tbl_menu',1,'',0,0,'Extra','','',0,'dropdown','last',0,0,1,0);

/*!40000 ALTER TABLE `cfg_table_info` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_ui
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_ui` WRITE;
/*!40000 ALTER TABLE `cfg_ui` DISABLE KEYS */;

INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`)
VALUES
	(1,'','tbl_site','','','','<p>Algemene informatie van de site en informatie voor zoekmachines.</p>',''),
	(2,'','tbl_links','','','','<p>Een tabel met links die je in alle teksten van de site kunt gebruiken.</p>',''),
	(3,'','tbl_menu','','','','<p>Het menu van de site, met de onderliggende pagina\'s en teksten.</p>',''),
	(4,'pictures','','','Foto\'s','','<p>Upload of verwijder hier de foto\'s van je site.</p>',''),
	(5,'downloads','','','Downloads','','<p>Voeg hier bestanden toe die je in je tekst als download-link wilt gebruiken.</p>',''),
	(8,'','','tbl_site.str_title','','','<p>Vul hier de titel in van je site.</p><p>De titel is zichtbaar in de kop van de <a href=\"admin/help/faq\" target=\"_self\">browser</a> en in de zoekresultaten van Google.</p>',''),
	(9,'','','tbl_site.str_author','','','<p>Vul hier je naam in.</p><p>De naam van de auteur is onzichtbaar voor bezoekers van de site, maar vindbaar voor zoekmachines, zodat bezoekers ook via jouw naam op je site terechtkomen.</p>',''),
	(10,'','','tbl_site.url_url','','','<p>Vul hier het webadres van je site in, bijvoorbeeld: \"www.voorbeeldsite.nl\"</p>',''),
	(11,'','','tbl_site.email_email','','','<p>Vul hier je e-mailadres in.</p><p>Heb je formulieren op je site staan? Als bezoekers ze invullen en opzenden, ontvang je ze via dit e-mailadres.</p>',''),
	(12,'','','tbl_site.stx_description','','','<p>Vul hier een korte algemene omschrijving van je site in.</p><p>Die is onzichtbaar op de site, maar wordt gebruikt door zoekmachines.</p><p>Afhankelijk van de opzet van de site kun je voor elke pagina een eigen omschrijving maken. Die vervangt voor die pagina deze algemene omschrijving.</p>',''),
	(13,'','','tbl_site.stx_keywords','','','<p>Vul hier zoektermen in gescheiden door komma\'s.</p><p>Zoektermen worden door zoekmachines gebruikt om je site beter vindbaar te maken. Lees <a href=\"admin/help/tips_voor_een_goede_site\" target=\"_self\">hier meer over SEO</a>.<br /><br />Afhankelijk van de opzet van je site is het mogelijk om per pagina extra zoektermen toe te voegen.</p>',''),
	(14,'','','tbl_site.str_google_analytics','','','<p>FlexyAdmin biedt statistieken over de bezoekers van je site. Als je uitgebreider statistieken wilt, kun je bijvoorbeeld <a href=\"http://www.google.com/intl/nl/analytics/\" target=\"_blank\">Google Analytics</a> gebruiken. Voer hier de code daarvan in.</p>',''),
	(16,'','','tbl_menu.str_title','','','<p>Vul de titel van de pagina in.</p>',''),
	(17,'','','tbl_menu.self_parent','','','<p>Wil je dat de nieuwe pagina onder een al bestaande pagina uit het hoofdmenu komt te staan? Geef dan hier aan onder welke pagina. Als je niets kiest dan komt de pagina in het hoofdmenu.</p>',''),
	(18,'','','tbl_menu.txt_text','','','<p>Vul hier de <a href=\"admin/help/tekst_aanpassen\">tekst</a> van je pagina in.</p><p>Eventueel kun je hier ook <a href=\"admin/help/fotos\">foto\'s</a> of <a href=\"admin/help/youtube_googlemaps_etc\">YouTube</a> filmpjes tussen de tekst plaatsen.</p>',''),
	(19,'','','tbl_menu.str_module','','','<p>Kies hier eventueel een module.</p><p>Modules voegen extra inhoud toe aan je pagina: een contactformulier, een overzicht van alle links, een agenda of een speciaal voor jouw site geschreven module.</p>',''),
	(20,'','','tbl_menu.stx_description','','','<p>Vul hier een korte omschrijving van deze pagina in.</p><p>Die wordt gebruikt door zoekmachines als Google. Als je niets invult, wordt de algemene omschrijving gebruikt die je bij <strong><a href=\"admin/help/site\">Site</a></strong> hebt ingevuld.</p>',''),
	(21,'','','tbl_menu.str_keywords','','','<p>Vul hier zoektermen in voor deze pagina.</p><p>Ze worden toegevoegd aan de zoektermen die je bij <strong><a href=\"admin/help/site\">Site</a></strong> hebt ingevoerd.</p>','');

/*!40000 ALTER TABLE `cfg_ui` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_user_groups
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_user_groups` WRITE;
/*!40000 ALTER TABLE `cfg_user_groups` DISABLE KEYS */;

INSERT INTO `cfg_user_groups` (`id`, `name`, `description`, `rights`, `b_all_users`, `b_backup`, `b_tools`, `b_delete`, `b_add`, `b_edit`, `b_show`)
VALUES
	(1,'super_admin','Super Administrator','*',1,1,1,1,1,1,1),
	(2,'admin','Administrator','tbl_*|media_*|cfg_users',0,1,1,1,1,1,1),
	(3,'user','User','tbl_*|media_*',0,0,0,1,1,1,1),
	(4,'visitor','Visitor','tbl_*|media_*',0,0,0,0,0,0,0);

/*!40000 ALTER TABLE `cfg_user_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_users
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_users` WRITE;
/*!40000 ALTER TABLE `cfg_users` DISABLE KEYS */;

INSERT INTO `cfg_users` (`id`, `str_username`, `gpw_password`, `email_email`, `ip_address`, `salt`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `b_active`, `str_language`, `str_filemanager_view`)
VALUES
	(1,'admin','$2y$08$rKzbNchBsinmWva3UnPsZOMKbaTFdpOgjxNv.PYKn1BjWgrOznhZa','info@flexyadmin.com','','','','',0,'',0,1464008415,1,'nl','list'),
	(2,'user','$2y$08$.18vvqlz24ldRDJ4AcnPR.AVYFBGOv9YbnvEw/dLRfn88KBd2E/iG','jan@burp.nl','','','','0',0,'',0,1464008422,1,'nl','list');

/*!40000 ALTER TABLE `cfg_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table log_activity
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table log_login_attempts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `log_login_attempts`;

CREATE TABLE `log_login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table log_stats
# ------------------------------------------------------------

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



# Dump of table rel_users__groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rel_users__groups`;

CREATE TABLE `rel_users__groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) unsigned NOT NULL,
  `id_user_group` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rel_users__groups` WRITE;
/*!40000 ALTER TABLE `rel_users__groups` DISABLE KEYS */;

INSERT INTO `rel_users__groups` (`id`, `id_user`, `id_user_group`)
VALUES
	(2,2,3),
	(30,1,1);

/*!40000 ALTER TABLE `rel_users__groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table res_media_files
# ------------------------------------------------------------

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

LOCK TABLES `res_media_files` WRITE;
/*!40000 ALTER TABLE `res_media_files` DISABLE KEYS */;

INSERT INTO `res_media_files` (`id`, `b_exists`, `file`, `path`, `str_type`, `str_title`, `dat_date`, `int_size`, `int_img_width`, `int_img_height`)
VALUES
	(1,1,'test_04.jpg','pictures','jpg','test_04','2015-11-23',30,300,225),
	(2,1,'test_05.jpg','pictures','jpg','test_05','2015-11-23',26,300,225),
	(3,1,'CDlabel.pdf','downloads','pdf','CDlabel','2016-01-13',73,0,0),
	(4,1,'Signs.docx','downloads','docx','Signs','2016-01-13',18,0,0);

/*!40000 ALTER TABLE `res_media_files` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_links
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_links`;

CREATE TABLE `tbl_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `str_title` varchar(255) NOT NULL DEFAULT '',
  `url_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `tbl_links` WRITE;
/*!40000 ALTER TABLE `tbl_links` DISABLE KEYS */;

INSERT INTO `tbl_links` (`id`, `str_title`, `url_url`)
VALUES
	(1,'Jan den Besten - webontwerp en geluidsontwerp','http://www.jandenbesten.net'),
	(2,'FlexyAdmin','http://www.flexyadmin.com'),
	(7,'Email FlexyAdmin','mailto:info@flexyadmin.com');

/*!40000 ALTER TABLE `tbl_links` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_menu`;

CREATE TABLE `tbl_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` smallint(6) NOT NULL DEFAULT '0',
  `self_parent` int(11) NOT NULL DEFAULT '0',
  `uri` varchar(100) NOT NULL,
  `str_title` varchar(255) NOT NULL DEFAULT '',
  `txt_text` text NOT NULL,
  `str_module` varchar(30) NOT NULL,
  `stx_description` mediumtext NOT NULL,
  `str_keywords` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `tbl_menu` WRITE;
/*!40000 ALTER TABLE `tbl_menu` DISABLE KEYS */;

INSERT INTO `tbl_menu` (`id`, `order`, `self_parent`, `uri`, `str_title`, `txt_text`, `str_module`, `stx_description`, `str_keywords`)
VALUES
	(1,0,0,'gelukt','Gelukt!','<p>Als je dit ziet is het je gelukt om FlexyAdmin te installeren en werkend te krijgen. <br />Je hebt nu een standaard-installatie van een zeer eenvoudige basis-site.</p>\n<h2>Hoe verder</h2>\n<ul>\n<li>Pas de HTML aan in de map <em>site/views</em>. <em>site.php</em> is de basis view van je site en <em>page.php</em> de afzonderlijke pagina\'s.</li>\n<li>Pas de Stylesheets aan. Deze vindt je in de map <em>site/assets/css</em>.</li>\n<li>Handiger is om <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> te gebruiken.</li>\n</ul>\n<h2>LESS</h2>\n<p>FlexyAdmin ondersteund <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> in combinatie met een Gulp die het compileren verzorgd.</p>\n<ul>\n<li>Je vindt de <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> bestanden voor de standaard template in <em>site/assets/less-default.</em></li>\n<li>Om <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> te compileren tot CSS heeft FlexyAdmin een handige Gulpfile, zie hierna.<em><br /></em></li>\n</ul>\n<h2>Gulp</h2>\n<p>Als je gebruikt maakt van LESS heb je een compiler nodig om LESS om te zetten in CSS. FlexyAdmin maakt daarvoor gebruik van <a href=\"http://gulpjs.com/\" target=\"_blank\">Gulp</a>.<br />Gulp is een zogenaamde \'taskmanager\' en verzorgt automatisch een aantal taken. De bij FlexyAdmin geleverde Gulpfile verzorgt deze taken voor LESS en CSS:</p>\n<ul>\n<li>Compileren van LESS naar CSS</li>\n<li>Samenvoegen van alle CSS bestanden tot &eacute;&eacute;n CSS bestand</li>\n<li>Automatisch prefixen van CSS regels voor diverse browser (moz-, o-, webkit- e.d.)</li>\n<li>Rem units omzetten naar px units zodat browser die geen rem units kennen terugvallen op px (met name IE8)</li>\n<li>Minificeren van het CSS bestand.</li>\n</ul>\n<p>En deze taken voor Javascript:</p>\n<ul>\n<li>Javascript testen op veel voorkomende fouten met <a href=\"http://jshint.com/\" target=\"_blank\">JSHint</a></li>\n<li>Alle Javascript bestanden samenvoegen tot &eacute;&eacute;n bestand en deze minificeren.</li>\n</ul>\n<h2>Bower</h2>\n<p>Naast Gulp wordt FlexyAdmin ook geleverd met <a href=\"http://bower.io/\" target=\"_blank\">Bower</a>. Daarmee kun je je al je externe plugins handig installeren en updaten (zoals jQuery en Bootstrap).</p>\n<h2>gulpfile.js</h2>\n<p>Hoe je Gulp en Bower aan de praat kunt krijgen en welke gulp commando\'s er allemaal zijn lees je aan het begin van de gulpfile in de root: <em>gulpfile.js</em></p>\n<h2>Bootstrap</h2>\n<p>In plaats van het standaard minimale template kun je ook gebruik maken van <a href=\"http://getbootstrap.com/\">Bootstrap:</a></p>\n<ul>\n<li>Je vindt de Bootstrap bestanden in <em>site/assets/less-bootstrap</em></li>\n<li>Stel in <em>site/config/config.php:</em> <code>$config[\'framework\']=\'bootstrap\';</code></li>\n<li>Stel in <em>gulpfile.js: </em><code>var framework = \'bootstrap\';</code></li>\n<li>Bootstrap kun je alleen gebruiken in combinatie met LESS en Gulp.</li>\n</ul>','','',''),
	(2,1,0,'een_pagina','Een pagina','<h2>Lorem ipsum dolor sit amet</h2>\n<p>Consectetur adipiscing elit. Vivamus in augue ac justo posuere luctus sodales vel justo. Integer blandit, quam id porttitor consequat, lorem libero bibendum ipsum, non auctor sem ipsum eu mauris. <strong>Vestibulum condimentum,</strong> lectus sed aliquam rutrum, est velit pellentesque mauris, sed mattis sapien ante vitae enim. Quisque cursus facilisis molestie. Sed rhoncus lacus ac nunc interdum in laoreet mi rhoncus. Suspendisse ultrices fringilla felis, in porta mi pretium ut. Nunc nisl nulla, varius in lobortis a, dictum a purus. Sed consequat felis ut erat lobortis hendrerit. Donec bibendum lorem lorem. Fusce suscipit sapien id lorem mollis vel placerat nunc congue. Aenean non nunc tortor. <em>Curabitur rhoncus neque eget nulla adipiscing euismod.</em></p>\n<p><em><img title=\"test_03\" src=\"site/assets/pictures/test_03.jpg\" alt=\"test_03\" width=\"960\" height=\"720\" /></em></p>\n<h2>Duis tincidunt sollicitudin convallis</h2>\n<p>Quisque nibh tortor, blandit a mollis vitae, euismod non nulla. Duis dui erat, interdum sit amet porttitor a, porttitor nec augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed quis porta turpis. Suspendisse nec mi enim, ut fringilla tellus. Nunc sollicitudin justo at leo tempus eu fringilla nisl tempus. Sed id tellus non eros tristique vehicula. Quisque sollicitudin augue id velit euismod interdum. Proin lobortis ornare magna in facilisis. Nulla vestibulum ultricies dui ut fringilla. Duis eu ante in lorem pellentesque bibendum. Praesent id velit vel nulla ullamcorper adipiscing quis quis tellus. Integer nec augue quis felis dapibus imperdiet ac et nibh.</p>','','',''),
	(3,2,2,'subpagina_met_module_links','Subpagina','<p>Een subpagina</p>','','',''),
	(5,3,2,'nog_een_subpagina','Nog een subpagina','','example','',''),
	(4,4,0,'contact','Contact','<p>Hier een voorbeeld van een eenvoudig <a href=\"mailto:info@flexyadmin.com\">contactformulier</a>.</p>','forms.contact','','');

/*!40000 ALTER TABLE `tbl_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_site
# ------------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `tbl_site` WRITE;
/*!40000 ALTER TABLE `tbl_site` DISABLE KEYS */;

INSERT INTO `tbl_site` (`id`, `str_title`, `str_author`, `url_url`, `email_email`, `stx_description`, `stx_keywords`, `str_google_analytics`)
VALUES
	(1,'FlexyAdmin Demo','Jan den Besten','http://www.flexyadmin.com/','info@flexyadmin.com','','','');

/*!40000 ALTER TABLE `tbl_site` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
