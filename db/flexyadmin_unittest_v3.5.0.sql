# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.33)
# Database: flexyadmin_test
# Generation Time: 2017-01-08 10:20:48 +0000
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
	(5,5,'# all media (if user has rights)',1,3,'all_media','','','',''),
	(6,9,'# all tools (if user has rights)',1,3,'tools','','','',''),
	(7,13,'# all config tables (if user has rights)',1,1,'all_cfg_tables','','','',''),
	(8,3,'',1,3,'seperator','','','',''),
	(9,6,'',1,3,'seperator','','','',''),
	(10,10,'',1,3,'seperator','','','',''),
	(11,7,'_stats_menu',1,3,'api','API_plugin_stats','','',''),
	(12,8,'',1,3,'seperator','','','',''),
	(16,11,'# all result tables (if there are any)',1,1,'all_res_tables','','','',''),
	(17,12,'',1,1,'seperator','','','','');

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
	(1,20,1,'normal','','tbl_links',1,'cut,copy,pastetext,pasteword,selectall,undo,bold,italic,bullist,formatselect,removeformat,link,unlink,image,embed','','','450','500','h2,h3','','','3.5.0');

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
	(14,'tbl_menu.str_title',1,1,' ','',0,'',0,0,'','required',''),
	(15,'tbl_groepen.str_soort',1,1,' ','',0,'groep|vak',0,0,'','','');

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
	(1,'pictures',0,0,1,300,1000,1,100,1000,'_thumb_','',0,0,0,'','');

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
	(1,0,'pictures',1,'jpg,jpeg,gif,png',0,'tbl_groepen.media_tekening|tbl_menu.medias_fotos',1,1,1,'name',5,'0','','0',0,1,0,0,0),
	(2,1,'downloads',1,'pdf,doc,docx,xls,xlsx,png,jpg',0,'0',1,0,0,'name',5,'','','0',0,0,1,0,0);

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
	(3,1,'tbl_menu',1,'',0,0,'Extra','','',0,'dropdown','last',0,0,1,0),
	(4,3,'tbl_groepen',1,'',1,0,'','','',1,'dropdown','last',0,1,1,0);

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
	(1,'admin','$2y$08$rKzbNchBsinmWva3UnPsZOMKbaTFdpOgjxNv.PYKn1BjWgrOznhZa','info@flexyadmin.com','','','','',0,'',0,1483870482,1,'nl','list'),
	(2,'user','$2y$08$.18vvqlz24ldRDJ4AcnPR.AVYFBGOv9YbnvEw/dLRfn88KBd2E/iG','jan@burp.nl','','','','0',0,'',0,1483869980,1,'nl','list'),
	(3,'test','$2y$08$OfDssFUdFL3mqwzlg4mFJeDrmwCRrzc.9sEQj0uVbM7MRxTpX/pZC','test@flexyadmin.com','',NULL,NULL,NULL,0,NULL,0,1483869980,1,'nl','');

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



# Dump of table rel_crud__crud2
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rel_crud__crud2`;

CREATE TABLE `rel_crud__crud2` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `id_crud` int(10) unsigned NOT NULL,
  `id_crud2` int(10) unsigned NOT NULL,
  `tme_last_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rel_crud__crud2` WRITE;
/*!40000 ALTER TABLE `rel_crud__crud2` DISABLE KEYS */;

INSERT INTO `rel_crud__crud2` (`id`, `id_crud`, `id_crud2`, `tme_last_changed`)
VALUES
	(1033,405,16,'2015-10-04 07:51:37'),
	(1034,405,13,'2015-10-04 07:51:37'),
	(1035,405,20,'2015-10-04 07:51:37'),
	(1036,405,9,'2015-10-04 07:51:37'),
	(1037,407,8,'2015-10-04 07:52:26'),
	(1038,407,7,'2015-10-04 07:52:26'),
	(1039,407,18,'2015-10-04 07:52:26'),
	(1040,407,20,'2015-10-04 07:52:26'),
	(1041,409,20,'2015-10-04 07:54:09'),
	(1042,409,10,'2015-10-04 07:54:09'),
	(1043,409,3,'2015-10-04 07:54:09'),
	(1044,409,18,'2015-10-04 07:54:09'),
	(1045,409,20,'2015-10-04 07:54:09'),
	(1046,409,9,'2015-10-04 07:54:09'),
	(1047,411,9,'2015-10-04 07:54:30'),
	(1048,411,15,'2015-10-04 07:54:30'),
	(1049,411,18,'2015-10-04 07:54:30'),
	(1050,411,1,'2015-10-04 07:54:30'),
	(1051,411,10,'2015-10-04 07:54:30'),
	(1052,411,13,'2015-10-04 07:54:30'),
	(1053,413,10,'2015-10-04 07:54:44'),
	(1054,413,2,'2015-10-04 07:54:44'),
	(1055,413,2,'2015-10-04 07:54:44'),
	(1056,413,8,'2015-10-04 07:54:44'),
	(1057,413,6,'2015-10-04 07:54:44'),
	(1058,413,13,'2015-10-04 07:54:44'),
	(1059,415,19,'2015-10-04 07:54:58'),
	(1060,415,7,'2015-10-04 07:54:58'),
	(1061,415,3,'2015-10-04 07:54:58'),
	(1062,417,2,'2015-10-04 08:12:31'),
	(1063,417,10,'2015-10-04 08:12:31'),
	(1064,417,2,'2015-10-04 08:12:31'),
	(1065,417,16,'2015-10-04 08:12:31'),
	(1066,419,4,'2015-10-04 08:20:58'),
	(1067,419,5,'2015-10-04 08:20:58'),
	(1068,419,7,'2015-10-04 08:20:58'),
	(1069,419,4,'2015-10-04 08:20:58'),
	(1070,421,13,'2015-10-04 08:23:19'),
	(1071,421,16,'2015-10-04 08:23:19'),
	(1072,421,20,'2015-10-04 08:23:19'),
	(1073,421,11,'2015-10-04 08:23:19'),
	(1074,421,17,'2015-10-04 08:23:19'),
	(1075,421,2,'2015-10-04 08:23:19'),
	(1076,423,19,'2015-10-04 08:24:18'),
	(1077,423,1,'2015-10-04 08:24:18'),
	(1078,423,8,'2015-10-04 08:24:18'),
	(1079,425,5,'2015-10-04 08:26:28'),
	(1080,425,18,'2015-10-04 08:26:28'),
	(1081,425,1,'2015-10-04 08:26:28'),
	(1082,427,9,'2015-10-04 08:26:59'),
	(1083,427,20,'2015-10-04 08:26:59'),
	(1084,427,8,'2015-10-04 08:26:59'),
	(1085,427,12,'2015-10-04 08:26:59'),
	(1086,427,4,'2015-10-04 08:26:59'),
	(1087,427,14,'2015-10-04 08:26:59'),
	(1088,429,8,'2015-10-04 08:27:25'),
	(1089,429,3,'2015-10-04 08:27:25'),
	(1090,429,9,'2015-10-04 08:27:25'),
	(1091,429,14,'2015-10-04 08:27:25'),
	(1092,429,1,'2015-10-04 08:27:25'),
	(1093,429,11,'2015-10-04 08:27:25'),
	(1094,431,18,'2015-10-04 08:30:40'),
	(1095,431,9,'2015-10-04 08:30:40'),
	(1096,431,9,'2015-10-04 08:30:40'),
	(1097,431,15,'2015-10-04 08:30:40'),
	(1098,431,16,'2015-10-04 08:30:40'),
	(1099,433,6,'2015-10-04 08:32:37'),
	(1100,433,3,'2015-10-04 08:32:37'),
	(1101,433,3,'2015-10-04 08:32:37'),
	(1102,435,11,'2015-10-04 08:33:01'),
	(1103,435,15,'2015-10-04 08:33:01'),
	(1104,435,4,'2015-10-04 08:33:01'),
	(1105,435,13,'2015-10-04 08:33:01'),
	(1106,435,9,'2015-10-04 08:33:01'),
	(1107,435,7,'2015-10-04 08:33:01'),
	(1108,437,18,'2015-10-04 08:33:06'),
	(1109,437,14,'2015-10-04 08:33:06'),
	(1110,437,7,'2015-10-04 08:33:06'),
	(1111,437,3,'2015-10-04 08:33:06'),
	(1112,437,18,'2015-10-04 08:33:06'),
	(1113,437,18,'2015-10-04 08:33:06'),
	(1114,439,11,'2015-10-04 08:34:06'),
	(1115,439,15,'2015-10-04 08:34:06'),
	(1116,439,17,'2015-10-04 08:34:06'),
	(1117,439,5,'2015-10-04 08:34:06'),
	(1118,439,4,'2015-10-04 08:34:06'),
	(1119,439,20,'2015-10-04 08:34:06'),
	(1120,441,15,'2015-10-04 08:34:49'),
	(1121,441,12,'2015-10-04 08:34:49'),
	(1122,441,15,'2015-10-04 08:34:49'),
	(1123,441,18,'2015-10-04 08:34:49'),
	(1124,441,18,'2015-10-04 08:34:49'),
	(1125,443,7,'2015-10-04 08:35:30'),
	(1126,443,12,'2015-10-04 08:35:30'),
	(1127,443,12,'2015-10-04 08:35:30'),
	(1128,443,17,'2015-10-04 08:35:30'),
	(1129,443,15,'2015-10-04 08:35:30'),
	(1130,445,1,'2015-10-04 08:37:07'),
	(1131,445,19,'2015-10-04 08:37:07'),
	(1132,445,4,'2015-10-04 08:37:07'),
	(1133,445,16,'2015-10-04 08:37:07'),
	(1134,445,4,'2015-10-04 08:37:07'),
	(1135,447,13,'2015-10-04 08:38:44'),
	(1136,447,8,'2015-10-04 08:38:44'),
	(1137,447,11,'2015-10-04 08:38:44'),
	(1138,447,5,'2015-10-04 08:38:44'),
	(1139,447,6,'2015-10-04 08:38:44'),
	(1140,447,16,'2015-10-04 08:38:44'),
	(1141,449,16,'2015-10-04 08:38:56'),
	(1142,449,11,'2015-10-04 08:38:56'),
	(1143,449,19,'2015-10-04 08:38:56'),
	(1144,449,7,'2015-10-04 08:38:56'),
	(1145,449,20,'2015-10-04 08:38:56'),
	(1146,449,11,'2015-10-04 08:38:56'),
	(1147,451,2,'2015-10-04 08:40:10'),
	(1148,451,1,'2015-10-04 08:40:10'),
	(1149,451,10,'2015-10-04 08:40:10'),
	(1150,451,18,'2015-10-04 08:40:10'),
	(1151,451,19,'2015-10-04 08:40:10'),
	(1152,453,20,'2015-10-04 08:41:16'),
	(1153,453,5,'2015-10-04 08:41:16'),
	(1154,453,19,'2015-10-04 08:41:16'),
	(1155,453,6,'2015-10-04 08:41:16'),
	(1156,453,10,'2015-10-04 08:41:16'),
	(1157,455,19,'2015-10-04 08:42:07'),
	(1158,455,1,'2015-10-04 08:42:07'),
	(1159,455,18,'2015-10-04 08:42:07'),
	(1160,455,13,'2015-10-04 08:42:07'),
	(1161,455,10,'2015-10-04 08:42:07'),
	(1162,455,7,'2015-10-04 08:42:07'),
	(1163,457,13,'2015-10-04 08:42:11'),
	(1164,457,13,'2015-10-04 08:42:11'),
	(1165,457,13,'2015-10-04 08:42:11'),
	(1166,457,2,'2015-10-04 08:42:11'),
	(1167,457,1,'2015-10-04 08:42:11'),
	(1168,457,12,'2015-10-04 08:42:11'),
	(1169,459,6,'2015-10-04 08:42:50'),
	(1170,459,5,'2015-10-04 08:42:50'),
	(1171,459,20,'2015-10-04 08:42:50'),
	(1172,461,8,'2015-10-04 08:43:21'),
	(1173,461,1,'2015-10-04 08:43:21'),
	(1174,461,5,'2015-10-04 08:43:21'),
	(1175,463,16,'2015-10-04 08:43:32'),
	(1176,463,5,'2015-10-04 08:43:32'),
	(1177,463,14,'2015-10-04 08:43:32'),
	(1178,463,4,'2015-10-04 08:43:32'),
	(1179,465,4,'2015-10-04 11:15:01'),
	(1180,465,3,'2015-10-04 11:15:01'),
	(1181,465,9,'2015-10-04 11:15:01'),
	(1182,465,7,'2015-10-04 11:15:01'),
	(1183,465,13,'2015-10-04 11:15:01'),
	(1184,465,20,'2015-10-04 11:15:01'),
	(1185,467,10,'2015-10-04 11:22:26'),
	(1186,467,4,'2015-10-04 11:22:26'),
	(1187,467,16,'2015-10-04 11:22:26'),
	(1188,469,10,'2015-10-04 11:24:17'),
	(1189,469,6,'2015-10-04 11:24:17'),
	(1190,469,8,'2015-10-04 11:24:17'),
	(1191,469,12,'2015-10-04 11:24:17'),
	(1192,471,14,'2015-10-04 11:25:19'),
	(1193,471,5,'2015-10-04 11:25:19'),
	(1194,471,18,'2015-10-04 11:25:19'),
	(1195,471,3,'2015-10-04 11:25:19'),
	(1196,471,18,'2015-10-04 11:25:19'),
	(1197,473,20,'2015-10-04 11:26:03'),
	(1198,473,5,'2015-10-04 11:26:03'),
	(1199,473,7,'2015-10-04 11:26:03'),
	(1200,473,11,'2015-10-04 11:26:03'),
	(1201,475,13,'2015-10-04 11:26:41'),
	(1202,475,19,'2015-10-04 11:26:41'),
	(1203,475,2,'2015-10-04 11:26:41'),
	(1204,475,16,'2015-10-04 11:26:41'),
	(1205,477,14,'2015-10-04 11:27:27'),
	(1206,477,11,'2015-10-04 11:27:27'),
	(1207,477,8,'2015-10-04 11:27:27'),
	(1208,477,11,'2015-10-04 11:27:27'),
	(1209,477,20,'2015-10-04 11:27:27'),
	(1210,479,12,'2015-10-04 11:30:43'),
	(1211,479,12,'2015-10-04 11:30:43'),
	(1212,479,18,'2015-10-04 11:30:43'),
	(1213,479,11,'2015-10-04 11:30:43'),
	(1214,481,7,'2015-10-04 11:31:35'),
	(1215,481,20,'2015-10-04 11:31:35'),
	(1216,481,11,'2015-10-04 11:31:35'),
	(1217,481,20,'2015-10-04 11:31:35'),
	(1218,483,6,'2015-10-04 11:32:53'),
	(1219,483,4,'2015-10-04 11:32:53'),
	(1220,483,17,'2015-10-04 11:32:53'),
	(1221,483,4,'2015-10-04 11:32:53'),
	(1222,483,8,'2015-10-04 11:32:53'),
	(1223,483,16,'2015-10-04 11:32:53'),
	(1224,485,17,'2015-10-04 11:33:13'),
	(1225,485,14,'2015-10-04 11:33:13'),
	(1226,485,6,'2015-10-04 11:33:13'),
	(1227,485,5,'2015-10-04 11:33:13'),
	(1228,487,4,'2015-10-04 11:36:18'),
	(1229,487,12,'2015-10-04 11:36:18'),
	(1230,487,11,'2015-10-04 11:36:18'),
	(1231,489,9,'2015-10-04 11:36:40'),
	(1232,489,16,'2015-10-04 11:36:40'),
	(1233,489,2,'2015-10-04 11:36:40'),
	(1234,489,15,'2015-10-04 11:36:40'),
	(1235,491,2,'2015-10-04 11:42:09'),
	(1236,491,10,'2015-10-04 11:42:09'),
	(1237,491,4,'2015-10-04 11:42:09'),
	(1238,491,10,'2015-10-04 11:42:09'),
	(1239,493,20,'2015-10-04 11:52:29'),
	(1240,493,20,'2015-10-04 11:52:29'),
	(1241,493,18,'2015-10-04 11:52:29'),
	(1242,495,8,'2015-10-04 11:53:42'),
	(1243,495,17,'2015-10-04 11:53:42'),
	(1244,495,14,'2015-10-04 11:53:42'),
	(1245,495,6,'2015-10-04 11:53:42'),
	(1246,495,8,'2015-10-04 11:53:42'),
	(1247,495,20,'2015-10-04 11:53:42'),
	(1248,497,18,'2015-10-04 12:02:43'),
	(1249,497,19,'2015-10-04 12:02:43'),
	(1250,497,5,'2015-10-04 12:02:43'),
	(1251,497,3,'2015-10-04 12:02:43'),
	(1252,497,4,'2015-10-04 12:02:43'),
	(1253,497,11,'2015-10-04 12:02:43'),
	(1254,499,2,'2015-10-04 12:05:24'),
	(1255,499,10,'2015-10-04 12:05:24'),
	(1256,499,19,'2015-10-04 12:05:24'),
	(1257,499,2,'2015-10-04 12:05:24'),
	(1258,501,18,'2015-10-04 12:05:30'),
	(1259,501,1,'2015-10-04 12:05:30'),
	(1260,501,14,'2015-10-04 12:05:30'),
	(1261,501,16,'2015-10-04 12:05:30'),
	(1262,501,1,'2015-10-04 12:05:30'),
	(1263,503,7,'2015-10-04 12:05:48'),
	(1264,503,6,'2015-10-04 12:05:48'),
	(1265,503,6,'2015-10-04 12:05:48'),
	(1266,503,8,'2015-10-04 12:05:48'),
	(1267,505,17,'2015-10-04 12:06:00'),
	(1268,505,11,'2015-10-04 12:06:00'),
	(1269,505,13,'2015-10-04 12:06:00'),
	(1270,505,9,'2015-10-04 12:06:00'),
	(1271,507,5,'2015-10-04 12:07:15'),
	(1272,507,10,'2015-10-04 12:07:15'),
	(1273,507,2,'2015-10-04 12:07:15'),
	(1274,507,18,'2015-10-04 12:07:15'),
	(1275,507,8,'2015-10-04 12:07:15'),
	(1276,507,7,'2015-10-04 12:07:15'),
	(1277,509,16,'2015-10-04 12:09:41'),
	(1278,509,15,'2015-10-04 12:09:41'),
	(1279,509,5,'2015-10-04 12:09:41'),
	(1280,511,2,'2015-10-04 12:10:01'),
	(1281,511,6,'2015-10-04 12:10:01'),
	(1282,511,20,'2015-10-04 12:10:01'),
	(1283,511,18,'2015-10-04 12:10:01'),
	(1284,511,3,'2015-10-04 12:10:01'),
	(1285,513,2,'2015-10-04 12:10:47'),
	(1286,513,19,'2015-10-04 12:10:47'),
	(1287,513,2,'2015-10-04 12:10:47'),
	(1288,513,16,'2015-10-04 12:10:47'),
	(1289,513,14,'2015-10-04 12:10:47'),
	(1290,513,16,'2015-10-04 12:10:47'),
	(1291,515,9,'2015-10-04 12:11:39'),
	(1292,515,15,'2015-10-04 12:11:39'),
	(1293,515,5,'2015-10-04 12:11:39'),
	(1294,515,16,'2015-10-04 12:11:39'),
	(1295,515,20,'2015-10-04 12:11:39'),
	(1296,517,7,'2015-10-04 12:11:51'),
	(1297,517,2,'2015-10-04 12:11:51'),
	(1298,517,19,'2015-10-04 12:11:51'),
	(1299,517,14,'2015-10-04 12:11:51'),
	(1300,519,3,'2015-10-04 12:12:36'),
	(1301,519,1,'2015-10-04 12:12:36'),
	(1302,519,3,'2015-10-04 12:12:36'),
	(1303,519,14,'2015-10-04 12:12:36'),
	(1304,519,2,'2015-10-04 12:12:36'),
	(1305,519,2,'2015-10-04 12:12:36'),
	(1306,521,17,'2015-10-04 12:13:16'),
	(1307,521,18,'2015-10-04 12:13:16'),
	(1308,521,12,'2015-10-04 12:13:16'),
	(1309,523,2,'2015-10-04 12:13:35'),
	(1310,523,2,'2015-10-04 12:13:35'),
	(1311,523,4,'2015-10-04 12:13:35'),
	(1312,523,11,'2015-10-04 12:13:35'),
	(1313,525,8,'2015-10-04 12:13:53'),
	(1314,525,6,'2015-10-04 12:13:53'),
	(1315,525,11,'2015-10-04 12:13:53'),
	(1316,525,7,'2015-10-04 12:13:53'),
	(1317,525,14,'2015-10-04 12:13:53'),
	(1318,527,5,'2015-10-04 12:14:10'),
	(1319,527,15,'2015-10-04 12:14:10'),
	(1320,527,5,'2015-10-04 12:14:10'),
	(1321,527,14,'2015-10-04 12:14:10'),
	(1322,529,16,'2015-10-04 12:22:19'),
	(1323,529,8,'2015-10-04 12:22:19'),
	(1324,529,15,'2015-10-04 12:22:19'),
	(1325,529,4,'2015-10-04 12:22:19'),
	(1326,529,18,'2015-10-04 12:22:19'),
	(1327,529,11,'2015-10-04 12:22:19'),
	(1328,531,1,'2015-10-04 12:28:22'),
	(1329,531,20,'2015-10-04 12:28:22'),
	(1330,531,3,'2015-10-04 12:28:22'),
	(1331,531,20,'2015-10-04 12:28:22'),
	(1332,533,17,'2015-10-04 13:03:34'),
	(1333,533,19,'2015-10-04 13:03:34'),
	(1334,533,5,'2015-10-04 13:03:34'),
	(1335,533,12,'2015-10-04 13:03:34'),
	(1336,533,7,'2015-10-04 13:03:34'),
	(1337,533,9,'2015-10-04 13:03:34'),
	(1338,535,5,'2015-10-04 13:04:08'),
	(1339,535,1,'2015-10-04 13:04:08'),
	(1340,535,2,'2015-10-04 13:04:08'),
	(1341,535,11,'2015-10-04 13:04:08'),
	(1342,535,3,'2015-10-04 13:04:08'),
	(1343,537,17,'2015-10-04 13:04:50'),
	(1344,537,15,'2015-10-04 13:04:50'),
	(1345,537,13,'2015-10-04 13:04:50'),
	(1346,539,19,'2015-10-04 13:05:04'),
	(1347,539,17,'2015-10-04 13:05:04'),
	(1348,539,4,'2015-10-04 13:05:04'),
	(1349,539,18,'2015-10-04 13:05:04'),
	(1350,541,13,'2015-10-04 13:05:43'),
	(1351,541,13,'2015-10-04 13:05:43'),
	(1352,541,1,'2015-10-04 13:05:43'),
	(1353,541,14,'2015-10-04 13:05:43'),
	(1354,541,16,'2015-10-04 13:05:43'),
	(1355,541,16,'2015-10-04 13:05:43'),
	(1356,543,1,'2015-10-04 13:05:51'),
	(1357,543,5,'2015-10-04 13:05:51'),
	(1358,543,9,'2015-10-04 13:05:51'),
	(1359,543,17,'2015-10-04 13:05:51'),
	(1360,545,3,'2015-10-04 13:06:11'),
	(1361,545,18,'2015-10-04 13:06:11'),
	(1362,545,17,'2015-10-04 13:06:11'),
	(1363,545,17,'2015-10-04 13:06:11'),
	(1364,545,16,'2015-10-04 13:06:11'),
	(1365,547,7,'2015-10-04 13:07:46'),
	(1366,547,3,'2015-10-04 13:07:46'),
	(1367,547,14,'2015-10-04 13:07:46'),
	(1368,547,8,'2015-10-04 13:07:46'),
	(1369,549,2,'2015-10-04 13:20:47'),
	(1370,549,19,'2015-10-04 13:20:47'),
	(1371,549,20,'2015-10-04 13:20:47'),
	(1372,549,9,'2015-10-04 13:20:47'),
	(1373,551,12,'2015-10-04 13:21:14'),
	(1374,551,8,'2015-10-04 13:21:14'),
	(1375,551,16,'2015-10-04 13:21:14'),
	(1376,551,13,'2015-10-04 13:21:14'),
	(1377,551,6,'2015-10-04 13:21:14'),
	(1378,553,15,'2015-10-04 20:27:17'),
	(1379,553,3,'2015-10-04 20:27:17'),
	(1380,553,16,'2015-10-04 20:27:17'),
	(1381,553,19,'2015-10-04 20:27:17'),
	(1382,553,6,'2015-10-04 20:27:17'),
	(1383,553,6,'2015-10-04 20:27:17'),
	(1384,555,17,'2015-10-04 22:03:57'),
	(1385,555,17,'2015-10-04 22:03:57'),
	(1386,555,16,'2015-10-04 22:03:57'),
	(1391,359,1,'2015-10-04 22:15:56'),
	(1392,359,3,'2015-10-04 22:15:56'),
	(1393,359,4,'2015-10-04 22:15:56'),
	(1394,557,19,'2015-10-04 22:16:10'),
	(1395,557,10,'2015-10-04 22:16:10'),
	(1396,557,12,'2015-10-04 22:16:10'),
	(1397,557,18,'2015-10-04 22:16:10'),
	(1398,559,20,'2015-10-04 22:16:20'),
	(1399,559,1,'2015-10-04 22:16:20'),
	(1400,559,13,'2015-10-04 22:16:20'),
	(1401,559,11,'2015-10-04 22:16:20'),
	(1402,559,19,'2015-10-04 22:16:20'),
	(1403,561,7,'2015-10-04 22:18:01'),
	(1404,561,2,'2015-10-04 22:18:01'),
	(1405,561,7,'2015-10-04 22:18:01'),
	(1406,563,6,'2015-10-05 06:43:35'),
	(1407,563,19,'2015-10-05 06:43:35'),
	(1408,563,11,'2015-10-05 06:43:35'),
	(1409,563,10,'2015-10-05 06:43:35'),
	(1410,565,9,'2015-10-05 07:03:12'),
	(1411,565,13,'2015-10-05 07:03:12'),
	(1412,565,19,'2015-10-05 07:03:12'),
	(1413,565,13,'2015-10-05 07:03:12'),
	(1414,567,13,'2015-10-05 07:03:59'),
	(1415,567,14,'2015-10-05 07:03:59'),
	(1416,567,10,'2015-10-05 07:03:59'),
	(1417,567,17,'2015-10-05 07:03:59'),
	(1418,567,16,'2015-10-05 07:03:59'),
	(1419,567,3,'2015-10-05 07:03:59'),
	(1420,569,10,'2015-10-05 07:04:18'),
	(1421,569,10,'2015-10-05 07:04:18'),
	(1422,569,7,'2015-10-05 07:04:18'),
	(1423,569,10,'2015-10-05 07:04:18'),
	(1424,569,14,'2015-10-05 07:04:18'),
	(1425,571,15,'2015-10-05 07:20:06'),
	(1426,571,10,'2015-10-05 07:20:06'),
	(1427,571,6,'2015-10-05 07:20:06'),
	(1428,573,4,'2015-10-05 07:20:23'),
	(1429,573,12,'2015-10-05 07:20:23'),
	(1430,573,3,'2015-10-05 07:20:23'),
	(1431,573,2,'2015-10-05 07:20:23'),
	(1432,573,17,'2015-10-05 07:20:23'),
	(1433,575,3,'2015-10-05 08:38:18'),
	(1434,575,12,'2015-10-05 08:38:18'),
	(1435,575,20,'2015-10-05 08:38:18'),
	(1436,577,17,'2015-10-05 08:38:31'),
	(1437,577,7,'2015-10-05 08:38:31'),
	(1438,577,16,'2015-10-05 08:38:31'),
	(1439,577,13,'2015-10-05 08:38:31'),
	(1440,577,13,'2015-10-05 08:38:31'),
	(1441,577,6,'2015-10-05 08:38:31'),
	(1442,588,2,'2015-10-05 08:50:52'),
	(1443,588,16,'2015-10-05 08:50:52'),
	(1444,588,4,'2015-10-05 08:50:52'),
	(1445,588,18,'2015-10-05 08:50:52'),
	(1446,588,7,'2015-10-05 08:50:52'),
	(1447,588,2,'2015-10-05 08:50:52'),
	(1448,590,8,'2015-10-05 08:51:10'),
	(1449,590,16,'2015-10-05 08:51:10'),
	(1450,590,1,'2015-10-05 08:51:10'),
	(1451,590,7,'2015-10-05 08:51:10'),
	(1452,592,20,'2015-10-05 08:51:14'),
	(1453,592,12,'2015-10-05 08:51:14'),
	(1454,592,2,'2015-10-05 08:51:14'),
	(1455,592,1,'2015-10-05 08:51:14'),
	(1456,592,10,'2015-10-05 08:51:14'),
	(1457,592,9,'2015-10-05 08:51:14'),
	(1458,594,3,'2015-10-05 08:51:28'),
	(1459,594,4,'2015-10-05 08:51:28'),
	(1460,594,19,'2015-10-05 08:51:28'),
	(1461,594,15,'2015-10-05 08:51:28'),
	(1462,596,8,'2015-10-05 08:56:27'),
	(1463,596,10,'2015-10-05 08:56:27'),
	(1464,596,10,'2015-10-05 08:56:27'),
	(1465,598,13,'2015-10-05 08:57:06'),
	(1466,598,15,'2015-10-05 08:57:06'),
	(1467,598,2,'2015-10-05 08:57:06'),
	(1468,598,1,'2015-10-05 08:57:06'),
	(1469,598,11,'2015-10-05 08:57:06'),
	(1470,600,11,'2015-10-05 08:57:52'),
	(1471,600,3,'2015-10-05 08:57:52'),
	(1472,600,19,'2015-10-05 08:57:52'),
	(1473,600,3,'2015-10-05 08:57:52'),
	(1474,602,14,'2016-05-23 14:25:56'),
	(1475,602,4,'2016-05-23 14:25:56'),
	(1476,602,12,'2016-05-23 14:25:56'),
	(1477,602,7,'2016-05-23 14:25:56'),
	(1478,604,15,'2016-05-23 14:26:34'),
	(1479,604,9,'2016-05-23 14:26:34'),
	(1480,604,17,'2016-05-23 14:26:34'),
	(1481,606,15,'2016-05-23 14:26:53'),
	(1482,606,4,'2016-05-23 14:26:53'),
	(1483,606,7,'2016-05-23 14:26:53'),
	(1484,606,16,'2016-05-23 14:26:53'),
	(1485,608,1,'2016-05-23 14:28:11'),
	(1486,608,9,'2016-05-23 14:28:11'),
	(1487,608,10,'2016-05-23 14:28:11'),
	(1488,608,11,'2016-05-23 14:28:11'),
	(1489,608,19,'2016-05-23 14:28:11'),
	(1490,608,4,'2016-05-23 14:28:11'),
	(1491,610,7,'2016-05-23 14:29:45'),
	(1492,610,4,'2016-05-23 14:29:45'),
	(1493,610,7,'2016-05-23 14:29:45'),
	(1494,610,3,'2016-05-23 14:29:45'),
	(1495,612,12,'2016-05-23 14:30:12'),
	(1496,612,8,'2016-05-23 14:30:12'),
	(1497,612,1,'2016-05-23 14:30:12'),
	(1498,612,14,'2016-05-23 14:30:12'),
	(1499,612,20,'2016-05-23 14:30:12'),
	(1500,614,20,'2016-05-23 14:31:45'),
	(1501,614,12,'2016-05-23 14:31:45'),
	(1502,614,9,'2016-05-23 14:31:45'),
	(1503,616,20,'2016-05-23 14:32:30'),
	(1504,616,2,'2016-05-23 14:32:30'),
	(1505,616,13,'2016-05-23 14:32:30'),
	(1506,616,4,'2016-05-23 14:32:30'),
	(1507,618,19,'2016-05-23 14:32:49'),
	(1508,618,16,'2016-05-23 14:32:49'),
	(1509,618,12,'2016-05-23 14:32:49'),
	(1510,618,10,'2016-05-23 14:32:49'),
	(1511,620,3,'2016-05-23 14:33:15'),
	(1512,620,13,'2016-05-23 14:33:15'),
	(1513,620,11,'2016-05-23 14:33:15'),
	(1514,620,19,'2016-05-23 14:33:15'),
	(1515,620,13,'2016-05-23 14:33:15'),
	(1516,620,16,'2016-05-23 14:33:15'),
	(1517,622,12,'2016-05-23 14:33:48'),
	(1518,622,10,'2016-05-23 14:33:48'),
	(1519,622,4,'2016-05-23 14:33:48'),
	(1520,622,20,'2016-05-23 14:33:48'),
	(1521,624,9,'2016-05-23 14:34:10'),
	(1522,624,7,'2016-05-23 14:34:10'),
	(1523,624,14,'2016-05-23 14:34:10'),
	(1524,624,20,'2016-05-23 14:34:10'),
	(1525,624,14,'2016-05-23 14:34:10'),
	(1526,626,18,'2016-05-23 14:35:37'),
	(1527,626,1,'2016-05-23 14:35:37'),
	(1528,626,11,'2016-05-23 14:35:37'),
	(1529,626,2,'2016-05-23 14:35:37'),
	(1530,626,7,'2016-05-23 14:35:37'),
	(1531,626,9,'2016-05-23 14:35:37'),
	(1532,628,15,'2016-05-25 12:09:00'),
	(1533,628,3,'2016-05-25 12:09:00'),
	(1534,628,2,'2016-05-25 12:09:00'),
	(1535,628,3,'2016-05-25 12:09:00'),
	(1536,628,16,'2016-05-25 12:09:00'),
	(1537,628,5,'2016-05-25 12:09:00'),
	(1538,630,15,'2016-05-25 12:09:31'),
	(1539,630,17,'2016-05-25 12:09:31'),
	(1540,630,7,'2016-05-25 12:09:31'),
	(1541,630,10,'2016-05-25 12:09:31'),
	(1542,630,7,'2016-05-25 12:09:31'),
	(1543,630,1,'2016-05-25 12:09:31'),
	(1544,632,19,'2016-05-25 12:10:19'),
	(1545,632,15,'2016-05-25 12:10:19'),
	(1546,632,9,'2016-05-25 12:10:19'),
	(1547,632,15,'2016-05-25 12:10:19'),
	(1548,634,16,'2016-05-25 13:12:18'),
	(1549,634,9,'2016-05-25 13:12:18'),
	(1550,634,1,'2016-05-25 13:12:18'),
	(1551,634,1,'2016-05-25 13:12:18'),
	(1552,634,8,'2016-05-25 13:12:18'),
	(1553,636,1,'2016-05-25 14:14:54'),
	(1554,636,19,'2016-05-25 14:14:54'),
	(1555,636,3,'2016-05-25 14:14:54'),
	(1556,638,6,'2016-05-25 14:31:37'),
	(1557,638,16,'2016-05-25 14:31:37'),
	(1558,638,7,'2016-05-25 14:31:37'),
	(1559,640,5,'2016-05-25 14:43:36'),
	(1560,640,17,'2016-05-25 14:43:36'),
	(1561,640,10,'2016-05-25 14:43:36'),
	(1562,640,4,'2016-05-25 14:43:36'),
	(1563,640,19,'2016-05-25 14:43:36'),
	(1564,642,13,'2016-05-25 15:38:13'),
	(1565,642,12,'2016-05-25 15:38:13'),
	(1566,642,20,'2016-05-25 15:38:13'),
	(1567,642,10,'2016-05-25 15:38:13'),
	(1568,642,14,'2016-05-25 15:38:13'),
	(1569,644,6,'2016-05-25 15:38:22'),
	(1570,644,3,'2016-05-25 15:38:22'),
	(1571,644,15,'2016-05-25 15:38:22'),
	(1572,644,6,'2016-05-25 15:38:22'),
	(1573,646,18,'2016-05-25 15:45:14'),
	(1574,646,1,'2016-05-25 15:45:14'),
	(1575,646,15,'2016-05-25 15:45:14'),
	(1576,648,19,'2016-11-16 07:22:28'),
	(1577,648,15,'2016-11-16 07:22:28'),
	(1578,648,9,'2016-11-16 07:22:28'),
	(1579,648,10,'2016-11-16 07:22:28'),
	(1580,648,6,'2016-11-16 07:22:28'),
	(1581,648,10,'2016-11-16 07:22:28'),
	(1582,650,1,'2016-11-18 12:27:25'),
	(1583,650,16,'2016-11-18 12:27:25'),
	(1584,650,8,'2016-11-18 12:27:25'),
	(1585,650,18,'2016-11-18 12:27:25'),
	(1586,650,4,'2016-11-18 12:27:25'),
	(1587,650,8,'2016-11-18 12:27:25'),
	(1588,652,18,'2016-12-15 10:27:29'),
	(1589,652,10,'2016-12-15 10:27:29'),
	(1590,652,5,'2016-12-15 10:27:29'),
	(1591,652,16,'2016-12-15 10:27:29'),
	(1592,652,3,'2016-12-15 10:27:29'),
	(1593,652,9,'2016-12-15 10:27:29'),
	(1594,654,13,'2016-12-15 10:56:55'),
	(1595,654,4,'2016-12-15 10:56:55'),
	(1596,654,12,'2016-12-15 10:56:55'),
	(1597,656,9,'2016-12-15 11:13:51'),
	(1598,656,13,'2016-12-15 11:13:51'),
	(1599,656,13,'2016-12-15 11:13:51'),
	(1600,656,2,'2016-12-15 11:13:51'),
	(1601,656,2,'2016-12-15 11:13:51'),
	(1602,656,6,'2016-12-15 11:13:51'),
	(1603,658,8,'2017-01-08 11:06:21'),
	(1604,658,0,'2017-01-08 11:06:21'),
	(1605,658,0,'2017-01-08 11:06:21'),
	(1606,658,0,'2017-01-08 11:06:21'),
	(1607,658,0,'2017-01-08 11:06:21'),
	(1608,658,0,'2017-01-08 11:06:21');

/*!40000 ALTER TABLE `rel_crud__crud2` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rel_groepen__adressen
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rel_groepen__adressen`;

CREATE TABLE `rel_groepen__adressen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_groepen` int(11) NOT NULL,
  `id_adressen` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `rel_groepen__adressen` WRITE;
/*!40000 ALTER TABLE `rel_groepen__adressen` DISABLE KEYS */;

INSERT INTO `rel_groepen__adressen` (`id`, `id_groepen`, `id_adressen`)
VALUES
	(347,30,9),
	(348,39,10),
	(349,36,3),
	(350,30,14),
	(351,32,3),
	(352,29,4),
	(353,32,1),
	(354,30,7),
	(355,31,10),
	(356,36,9),
	(357,33,14),
	(358,39,8),
	(359,32,5),
	(360,39,3),
	(361,31,3),
	(362,36,4),
	(363,36,13),
	(364,31,4),
	(365,40,7),
	(366,31,10),
	(367,31,7),
	(368,30,12),
	(369,29,9),
	(370,33,11),
	(371,36,1),
	(372,40,11),
	(373,31,13),
	(374,39,1),
	(375,33,8),
	(376,29,11),
	(377,32,12),
	(378,32,6),
	(379,33,1),
	(380,31,9),
	(381,30,11),
	(383,40,8),
	(384,32,10),
	(385,39,3),
	(387,30,3),
	(388,39,3),
	(390,40,6),
	(392,31,11),
	(393,30,13),
	(394,39,9),
	(396,30,6),
	(397,29,8);

/*!40000 ALTER TABLE `rel_groepen__adressen` ENABLE KEYS */;
UNLOCK TABLES;


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
	(1,1,1),
	(2,2,3),
	(72,3,2);

/*!40000 ALTER TABLE `rel_users__groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table res_assets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `res_assets`;

CREATE TABLE `res_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `b_exists` tinyint(1) NOT NULL DEFAULT '1',
  `file` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `path` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `type` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `alt` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `date` date NOT NULL,
  `size` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `file` (`file`),
  KEY `path` (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table tbl_adressen
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_adressen`;

CREATE TABLE `tbl_adressen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_address` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_zipcode` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_city` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `tbl_adressen` WRITE;
/*!40000 ALTER TABLE `tbl_adressen` DISABLE KEYS */;

INSERT INTO `tbl_adressen` (`id`, `str_address`, `str_zipcode`, `str_city`)
VALUES
	(1,'Schooolstraat 1','1234AB','Schoooldorp'),
	(2,'Lesbank 12','1234MN','Schoooldorp'),
	(3,'Taalsteeg 20','1234QR','Schoooldorp'),
	(4,'Rekenpark 42','1234IJ','Schoooldorp'),
	(5,'Bibliotheeklaan 36','1234GH','Schoooldorp'),
	(6,'Schoonschrijfdreef 18','1234OP','Schoooldorp'),
	(7,'Overblijf 16','1234KL','Schoooldorp'),
	(8,'Proefwerk 10','1234DK','Schoooldorp'),
	(9,'Dicteedreef 123','1234CD','Schoooldorp'),
	(10,'Spiekspui 7','1234EF','Schoooldorp'),
	(11,'Lessenaar 22','1234ST','Schoooldorp'),
	(12,'Prikbordlaan 32','1234UV','Schoooldorp'),
	(13,'Alumnidijk 100','1234WX','Schoooldorp'),
	(14,'Knikkerplein 21','1234YZ','Schoooldorp');

/*!40000 ALTER TABLE `tbl_adressen` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_crud
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_crud`;

CREATE TABLE `tbl_crud` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_insert` varchar(100) NOT NULL DEFAULT '',
  `str_update` varchar(100) NOT NULL,
  `dat_date` date NOT NULL,
  `time_time` time NOT NULL,
  `tme_datetime` datetime NOT NULL,
  `tme_last_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_changed` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tbl_crud` WRITE;
/*!40000 ALTER TABLE `tbl_crud` DISABLE KEYS */;

INSERT INTO `tbl_crud` (`id`, `str_insert`, `str_update`, `dat_date`, `time_time`, `tme_datetime`, `tme_last_changed`, `user_changed`)
VALUES
	(359,'TEST','TEST','0000-00-00','00:00:00','0000-00-00 00:00:00','2015-10-04 22:04:58',1),
	(405,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(406,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(407,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(408,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(409,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(410,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(411,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(412,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(413,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(414,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(415,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(416,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(417,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(418,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(419,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(420,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(421,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(422,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(423,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(424,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(425,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(426,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(427,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(428,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(429,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(430,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(431,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(432,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(433,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(434,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(435,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(436,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(437,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(438,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(439,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(440,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(441,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(442,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(443,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(444,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(445,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(446,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(447,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(448,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(449,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(450,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(451,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(452,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(453,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(454,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(455,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(456,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(457,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(458,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(459,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(460,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(461,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(462,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(463,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(464,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(465,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(466,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(467,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(468,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(469,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(470,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(471,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(472,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(473,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(474,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(475,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(476,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(477,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(478,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(479,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(480,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(481,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(482,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(483,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(484,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(485,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(486,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(487,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(488,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(489,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(490,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(491,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(492,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(493,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(494,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(495,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(496,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(497,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(498,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(499,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(500,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(501,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(502,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(503,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(504,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(505,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(506,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(507,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(508,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(509,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(510,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(511,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(512,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(513,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(514,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(515,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(516,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(517,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(518,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(519,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(520,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(521,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(522,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(523,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(524,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(525,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(526,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(527,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(528,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(529,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(530,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(531,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(532,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(533,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(534,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(535,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(536,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(537,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(538,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(539,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(540,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(541,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(542,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(543,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(544,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(545,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(546,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(547,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(548,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(549,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(550,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(551,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(552,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(553,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(554,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(555,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(556,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(557,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(558,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(559,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(560,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(561,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(562,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(563,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(564,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(565,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(566,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(567,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(568,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(569,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(570,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(571,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(572,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(573,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(574,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(575,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(576,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(577,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(578,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(579,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(580,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(581,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(582,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(583,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(584,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(585,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(586,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(587,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(588,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(589,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(590,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(591,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(592,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(593,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(594,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(595,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(596,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(597,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(598,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(599,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(600,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(601,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(602,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(603,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(604,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(605,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(606,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(607,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(608,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(609,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(610,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(611,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(612,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(613,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(614,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(615,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(616,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(617,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(618,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(619,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(620,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(621,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(622,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(623,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(624,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(625,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(626,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(627,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(628,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(629,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(630,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(631,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(632,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(633,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(634,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(635,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(636,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(637,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(638,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(639,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(640,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(641,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(642,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(643,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(644,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(645,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(646,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(647,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(648,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(649,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(650,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(651,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(652,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(653,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(654,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(655,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(656,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(657,'INSERT VHwZMjCu','UPDATE LVqKj1Bz','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0),
	(658,'_INSERT lR7xFY1b','_UPDATE EnVeziyC','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-01-08 11:06:21',0);

/*!40000 ALTER TABLE `tbl_crud` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_crud2
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_crud2`;

CREATE TABLE `tbl_crud2` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_other` varchar(100) NOT NULL DEFAULT '',
  `tme_last_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_changed` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tbl_crud2` WRITE;
/*!40000 ALTER TABLE `tbl_crud2` DISABLE KEYS */;

INSERT INTO `tbl_crud2` (`id`, `str_other`, `tme_last_changed`, `user_changed`)
VALUES
	(1,'Varius tempus condimentum adipiscing fermentum ','0000-00-00 00:00:00',0),
	(2,'Aliquam lobortis elit ','2015-05-18 05:03:50',0),
	(3,'Commodo ut eros cursus ','2015-01-17 07:10:02',0),
	(4,'TEST','2015-10-04 13:20:25',1),
	(5,'Fermentum arcu ','2015-03-31 22:32:16',0),
	(6,'Curabitur platea quam hendrerit primis ','2016-05-19 16:37:54',0),
	(7,'Venenatis felis ','2015-11-10 15:06:22',0),
	(8,'Ac ','2015-05-14 00:38:36',0),
	(9,'Congue inceptos ','2015-04-16 06:22:17',0),
	(10,'In aenean nam ','2015-07-17 05:31:04',0),
	(11,'Risus habitasse duis lorem dictum ','2015-03-14 02:55:02',0),
	(12,'Metus ','2016-01-29 07:39:02',0),
	(13,'Rutrum mauris himenaeos mauris augue ','2016-02-12 02:10:01',0),
	(14,'Aliquam ','2016-03-10 05:30:41',0),
	(15,'TEST','2015-10-04 13:20:38',1),
	(16,'Nulla ','2015-06-01 13:32:56',0),
	(17,'Nisl eleifend netus dictum ','2016-07-30 12:51:36',0),
	(18,'Cursus dapibus ','2016-07-24 22:39:55',0),
	(19,'Erat vitae commodo quam ','2016-05-06 06:36:41',0),
	(20,'Curabitur varius proin adipiscing gravida ','2016-02-14 05:10:23',0);

/*!40000 ALTER TABLE `tbl_crud2` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_groepen
# ------------------------------------------------------------

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tbl_groepen` WRITE;
/*!40000 ALTER TABLE `tbl_groepen` DISABLE KEYS */;

INSERT INTO `tbl_groepen` (`id`, `uri`, `order`, `str_title`, `str_soort`, `media_tekening`, `rgb_kleur`)
VALUES
	(29,'groep_2015-2016_d',3,'D','groep','',''),
	(30,'groep_2015-2016_handvaardigheid',6,'Handvaardigheid','vak','',''),
	(31,'groep_2015-2016_gym',5,'Gym','vak','',''),
	(32,'groep_2015-2016_c',2,'C','groep','','#00FF00'),
	(33,'groep_2015-2016_b',1,'B','groep','','#FF0000'),
	(36,'groep_2015-2016_a',0,'A','groep','','#FFCC00'),
	(39,'groep_2015-2016_e',4,'E','groep','',''),
	(40,'groep_2015-2016_muziek',7,'Muziek','vak','','');

/*!40000 ALTER TABLE `tbl_groepen` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_kinderen
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_kinderen`;

CREATE TABLE `tbl_kinderen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_first_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_middle_name` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_last_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `id_adressen` int(11) NOT NULL,
  `id_groepen` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `tbl_kinderen` WRITE;
/*!40000 ALTER TABLE `tbl_kinderen` DISABLE KEYS */;

INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`)
VALUES
	(2,'Adam','','Aalts',7,32),
	(3,'Aafje','','Aarden',4,31),
	(6,'Albert','','Adriaansen',4,29),
	(8,'Aaron','van','Alenburg',9,36),
	(9,'Abbe','van','Amstel',7,31),
	(10,'Abdul','','Ansems',14,36),
	(12,'Abel','','Appelman',4,32),
	(13,'Ada','van','Arkel',13,32),
	(16,'Adriane','','Arts',13,33),
	(17,'Alwin','','Aschman',8,29),
	(18,'Alissa','van','Asten',13,39),
	(19,'Amir','','Armin',1,36),
	(21,'Alfred','','Albus',12,30),
	(23,'Agnes','','Aeije',5,36),
	(26,'Aida','','Adelaar',11,29),
	(27,'Andreas','','Asperger',6,32),
	(28,'Aisley','van','Asissi',7,33),
	(29,'Aldo','','Akkerman',12,29),
	(30,'Alexander','','Averdijk',3,32),
	(31,'Andries','','Andermans',1,36),
	(32,'Bart','van','Baalen',5,33),
	(33,'Bas','','Bartels',13,33),
	(35,'Beau','','Barents',13,36),
	(36,'Beatrijs','van','Beeck',1,40),
	(37,'Berend','','Beckham',11,39),
	(38,'Bert','van','Beieren',13,29),
	(39,'Bobby','','Bosch',9,29),
	(40,'Bo','van den','Berg',1,32),
	(41,'Boy','den','Buytelaar',12,29),
	(42,'Brian','','Blaak',14,36),
	(43,'Bonnie','','Bezemer',11,30),
	(44,'Bram','','Bouhuizen',7,30),
	(45,'Boyd','de','Bont',4,33),
	(46,'Bregje','','Brandt',14,31),
	(48,'Brigitte','de','Bruijn',2,32),
	(49,'Britt','','Brouwer',4,40),
	(50,'Bregje','van','Buuren',3,33),
	(51,'Bruno','','Buijs',7,40),
	(53,'Busra','','Blonk',14,40),
	(54,'Boudewijn','','Bolkesteijn',9,31),
	(55,'Caspar','','Claesner',6,36),
	(56,'Caoa','','Cammel',13,32),
	(57,'Callen','','Cordet',1,36),
	(58,'Cecile','','Coolen',12,36),
	(59,'Chelso','','Coenen',2,33),
	(60,'Cedric','van','Clootwijck',9,31),
	(61,'Christiaan','','Corstiaens',6,33),
	(62,'Ciska','','Courtier',10,30),
	(64,'Claire','','Cosman',13,31),
	(65,'Coen','van','Cant',14,33),
	(66,'Constantijn','','Cornelissen',6,30),
	(67,'Daan','','Dekker',10,33),
	(68,'Dagmar','','Dijkman',12,29),
	(69,'Dafne','','Dirksen',12,40),
	(70,'Dago','van','Dokkum',14,33),
	(71,'Damian','','Dorsman',6,29),
	(73,'Daniëlle','','Dries',6,31),
	(74,'Dick','van','Duyvenvoorde',9,30),
	(75,'Dirk','','Dubois',14,40),
	(76,'Djara','van','Dillen',14,33),
	(77,'Dianne','van','Dijk',11,40),
	(78,'Dinand','','Doornhem',11,31),
	(80,'Dineke','van','Dommelen',4,29),
	(81,'Ditmar','','Domela',7,29),
	(82,'Dolf','van','Dam',10,30),
	(83,'Dominick','','Dubois',12,29),
	(84,'Donald','','Duik',1,32),
	(86,'Driek','','Doesburg',13,36),
	(87,'Dorien','','Draaisma',6,29),
	(88,'Driek','','Doesburg',14,32),
	(89,'Dries','','Dekkers',9,40),
	(90,'Dunya','','Doorhof',5,40),
	(92,'Ede','van','Eck',11,40),
	(93,'Edith','','Eelman',7,31),
	(94,'Edwin','','Etter',9,33),
	(95,'Eefke','','Elberts',14,40),
	(96,'Eelco','','Eisenaar',6,32),
	(97,'Egbert','van','Emmelen',5,39),
	(98,'Eline','','Erhout',13,36),
	(99,'Elisabeth','','Engels',9,40),
	(103,'Elissa','van','Elzas',5,39),
	(104,'Els','','Evertsen',9,29),
	(105,'Eva','van','Evelingen',5,30),
	(107,'Emanuel','','Estey',13,40),
	(108,'Emiel','','Eijkelboom',3,36),
	(110,'Epke','van','Essen',11,36),
	(111,'Ernst','','Everts',8,39),
	(112,'Erwin','','Ehre',12,39),
	(113,'Esmée','van','Egisheim',3,29),
	(114,'Esmeralda','van','Es',6,32),
	(115,'Eugenie','van den','Euvel',14,31),
	(116,'Evy','','Eisenhouwer',12,30);

/*!40000 ALTER TABLE `tbl_kinderen` ENABLE KEYS */;
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
  `medias_fotos` varchar(1000) NOT NULL,
  `b_visible` tinyint(1) NOT NULL DEFAULT '1',
  `str_module` varchar(30) NOT NULL,
  `stx_description` mediumtext NOT NULL,
  `str_keywords` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `tbl_menu` WRITE;
/*!40000 ALTER TABLE `tbl_menu` DISABLE KEYS */;

INSERT INTO `tbl_menu` (`id`, `order`, `self_parent`, `uri`, `str_title`, `txt_text`, `medias_fotos`, `b_visible`, `str_module`, `stx_description`, `str_keywords`)
VALUES
	(1,0,0,'gelukt','Gelukt!','<p>Als je dit ziet is het je gelukt om FlexyAdmin te installeren en werkend te krijgen. <br />Je hebt nu een standaard-installatie van een zeer eenvoudige basis-site.</p>\n<h2>Hoe verder</h2>\n<ul>\n<li>Pas de HTML aan in de map <em>site/views</em>. <em>site.php</em> is de basis view van je site en <em>page.php</em> de afzonderlijke pagina\'s.</li>\n<li>Pas de Stylesheets aan. Deze vindt je in de map <em>site/assets/css</em>.</li>\n<li>Handiger is om <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> te gebruiken.</li>\n</ul>\n<h2>LESS</h2>\n<p>FlexyAdmin ondersteund <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> in combinatie met een Gulp die het compileren verzorgd.</p>\n<ul>\n<li>Je vindt de <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> bestanden voor de standaard template in <em>site/assets/less-default.</em></li>\n<li>Om <a href=\"http://lesscss.org/\" target=\"_blank\">LESS</a> te compileren tot CSS heeft FlexyAdmin een handige Gulpfile, zie hierna.<em><br /></em></li>\n</ul>\n<h2>Gulp</h2>\n<p>Als je gebruikt maakt van LESS heb je een compiler nodig om LESS om te zetten in CSS. FlexyAdmin maakt daarvoor gebruik van <a href=\"http://gulpjs.com/\" target=\"_blank\">Gulp</a>.<br />Gulp is een zogenaamde \'taskmanager\' en verzorgt automatisch een aantal taken. De bij FlexyAdmin geleverde Gulpfile verzorgt deze taken voor LESS en CSS:</p>\n<ul>\n<li>Compileren van LESS naar CSS</li>\n<li>Samenvoegen van alle CSS bestanden tot &eacute;&eacute;n CSS bestand</li>\n<li>Automatisch prefixen van CSS regels voor diverse browser (moz-, o-, webkit- e.d.)</li>\n<li>Rem units omzetten naar px units zodat browser die geen rem units kennen terugvallen op px (met name IE8)</li>\n<li>Minificeren van het CSS bestand.</li>\n</ul>\n<p>En deze taken voor Javascript:</p>\n<ul>\n<li>Javascript testen op veel voorkomende fouten met <a href=\"http://jshint.com/\" target=\"_blank\">JSHint</a></li>\n<li>Alle Javascript bestanden samenvoegen tot &eacute;&eacute;n bestand en deze minificeren.</li>\n</ul>\n<h2>Bower</h2>\n<p>Naast Gulp wordt FlexyAdmin ook geleverd met <a href=\"http://bower.io/\" target=\"_blank\">Bower</a>. Daarmee kun je je al je externe plugins handig installeren en updaten (zoals jQuery en Bootstrap).</p>\n<h2>gulpfile.js</h2>\n<p>Hoe je Gulp en Bower aan de praat kunt krijgen en welke gulp commando\'s er allemaal zijn lees je aan het begin van de gulpfile in de root: <em>gulpfile.js</em></p>\n<h2>Bootstrap</h2>\n<p>In plaats van het standaard minimale template kun je ook gebruik maken van <a href=\"http://getbootstrap.com/\">Bootstrap:</a></p>\n<ul>\n<li>Je vindt de Bootstrap bestanden in <em>site/assets/less-bootstrap</em></li>\n<li>Stel in <em>site/config/config.php:</em> <code>$config[\'framework\']=\'bootstrap\';</code></li>\n<li>Stel in <em>gulpfile.js: </em><code>var framework = \'bootstrap\';</code></li>\n<li>Bootstrap kun je alleen gebruiken in combinatie met LESS en Gulp.</li>\n</ul>','',1,'','',''),
	(2,1,0,'een_pagina','Een pagina','','',1,'','',''),
	(3,2,2,'subpagina','Subpagina','<p>Een subpagina</p>','',1,'','',''),
	(5,3,2,'een_pagina','Nog een subpagina','','',1,'example','',''),
	(4,4,0,'contact','Contact','<p>Hier een voorbeeld van een eenvoudig <a href=\"mailto:info@flexyadmin.com\">contactformulier</a>.</p>','',1,'forms.contact','','');

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
	(1,'FlexyAdmin','Jan den Besten','http://www.flexyadmin.com/','info@flexyadmin.com','','','');

/*!40000 ALTER TABLE `tbl_site` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
