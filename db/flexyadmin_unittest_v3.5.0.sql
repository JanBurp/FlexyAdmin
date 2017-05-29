# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: flexyadmin_test
# Generation Time: 2017-05-29 04:17:28 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


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

LOCK TABLES `cfg_sessions` WRITE;
/*!40000 ALTER TABLE `cfg_sessions` DISABLE KEYS */;

INSERT INTO `cfg_sessions` (`id`, `ip_address`, `timestamp`, `data`)
VALUES
	('9b47fb5d7dd9ee340ac81ef77dc59360904333ce','::1',1488592856,X'5F5F63695F6C6173745F726567656E65726174657C693A313438383539323738343B6964656E746974797C733A353A2261646D696E223B7374725F757365726E616D657C733A353A2261646D696E223B656D61696C5F656D61696C7C733A31393A22696E666F40666C65787961646D696E2E636F6D223B757365725F69647C733A313A2231223B6F6C645F6C6173745F6C6F67696E7C733A31303A2231343838353932383536223B617574685F746F6B656E7C733A3133333A2265794A30655841694F694A4B563151694C434A68624763694F694A49557A49314E694A392E65794A3163325679626D46745A534936496D466B62576C75496977696347467A63336476636D51694F694A685A47317062694A392E46594E6474413733753933384C4B6E526453774E594A4D3637697568484F736D55385A50426A4D4A534D30223B6C616E67756167657C733A323A226E6C223B66696C65766965777C733A353A22736D616C6C223B');

/*!40000 ALTER TABLE `cfg_sessions` ENABLE KEYS */;
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
  `str_filemanager_view` varchar(10) NOT NULL DEFAULT 'small',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_users` WRITE;
/*!40000 ALTER TABLE `cfg_users` DISABLE KEYS */;

INSERT INTO `cfg_users` (`id`, `str_username`, `gpw_password`, `email_email`, `ip_address`, `salt`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `b_active`, `str_language`, `str_filemanager_view`)
VALUES
	(1,'admin','$2y$08$rKzbNchBsinmWva3UnPsZOMKbaTFdpOgjxNv.PYKn1BjWgrOznhZa','info@flexyadmin.com','','','','',0,'',0,1496031434,1,'nl','small'),
	(2,'user','$2y$08$.18vvqlz24ldRDJ4AcnPR.AVYFBGOv9YbnvEw/dLRfn88KBd2E/iG','jan@burp.nl','','','','0',0,'',0,1496031433,1,'nl','small'),
	(3,'test','$2y$08$OfDssFUdFL3mqwzlg4mFJeDrmwCRrzc.9sEQj0uVbM7MRxTpX/pZC','test@flexyadmin.com','',NULL,NULL,NULL,0,NULL,0,1496031431,1,'nl','small');

/*!40000 ALTER TABLE `cfg_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_version
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cfg_version`;

CREATE TABLE `cfg_version` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `str_version` varchar(10) NOT NULL DEFAULT '3.5.0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_version` WRITE;
/*!40000 ALTER TABLE `cfg_version` DISABLE KEYS */;

INSERT INTO `cfg_version` (`id`, `str_version`)
VALUES
	(1,'3.5.0');

/*!40000 ALTER TABLE `cfg_version` ENABLE KEYS */;
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

LOCK TABLES `log_activity` WRITE;
/*!40000 ALTER TABLE `log_activity` DISABLE KEYS */;

INSERT INTO `log_activity` (`id`, `id_user`, `tme_timestamp`, `stx_activity`, `str_activity_type`, `str_model`, `str_key`)
VALUES
	(1,1,'2017-03-04 02:59:48','login','auth','',''),
	(2,1,'2017-03-04 03:00:16','INSERT INTO `tbl_blog` (`str_title`) VALUES (\'6CnuRgGK\')','database','tbl_blog','1'),
	(3,1,'2017-03-04 03:00:16','INSERT INTO `tbl_blog` (`str_title`) VALUES (\'j3dc0Vpr\')','database','tbl_blog','2'),
	(4,1,'2017-03-04 03:00:16','INSERT INTO `tbl_blog` (`str_title`) VALUES (\'NKckmhYt\')','database','tbl_blog','3'),
	(5,1,'2017-03-04 03:00:16','INSERT INTO `tbl_blog` (`str_title`) VALUES (\'hFBEaPet\')','database','tbl_blog','4'),
	(6,1,'2017-03-04 03:00:16','INSERT INTO `tbl_blog` (`str_title`) VALUES (\'gkCLnxr7\')','database','tbl_blog','5'),
	(7,1,'2017-03-04 03:00:16','INSERT INTO `tbl_blog` (`str_title`) VALUES (\'LBuNkAES\')','database','tbl_blog','6'),
	(8,1,'2017-03-04 03:00:16','INSERT INTO `tbl_blog` (`str_title`) VALUES (\'7A91bMxd\')','database','tbl_blog','7'),
	(9,1,'2017-03-04 03:00:16','INSERT INTO `tbl_blog` (`str_title`) VALUES (\'4BQ8hvju\')','database','tbl_blog','8'),
	(10,1,'2017-03-04 03:00:16','INSERT INTO `tbl_blog` (`str_title`) VALUES (\'1C9z2m4S\')','database','tbl_blog','9'),
	(11,1,'2017-03-04 03:00:16','INSERT INTO `tbl_blog` (`str_title`) VALUES (\'2ErCxj51\')','database','tbl_blog','10'),
	(12,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `str_title` = \'Massa et consequat scelerisque nunc \'\nWHERE `id` = 1','database','tbl_blog','1'),
	(13,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `str_title` = \'Tempor \'\nWHERE `id` = 9','database','tbl_blog','9'),
	(14,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `str_title` = \'Faucibus sit porttitor quisque posuere \'\nWHERE `id` = 8','database','tbl_blog','8'),
	(15,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `str_title` = \'Pulvinar \'\nWHERE `id` = 7','database','tbl_blog','7'),
	(16,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `str_title` = \'Euismod augue placerat sodales \'\nWHERE `id` = 6','database','tbl_blog','6'),
	(17,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `str_title` = \'Phasellus ullamcorper dolor aenean suspendisse \'\nWHERE `id` = 5','database','tbl_blog','5'),
	(18,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `str_title` = \'Fringilla \'\nWHERE `id` = 4','database','tbl_blog','4'),
	(19,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `str_title` = \'Donec volutpat dictumst \'\nWHERE `id` = 3','database','tbl_blog','3'),
	(20,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `str_title` = \'Vehicula iaculis quis \'\nWHERE `id` = 2','database','tbl_blog','2'),
	(21,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `str_title` = \'Suspendisse \'\nWHERE `id` = 10','database','tbl_blog','10'),
	(22,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `dat_date` = \'2020-3-11\'\nWHERE `id` = 1','database','tbl_blog','1'),
	(23,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `dat_date` = \'2014-6-9\'\nWHERE `id` = 9','database','tbl_blog','9'),
	(24,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `dat_date` = \'2015-11-3\'\nWHERE `id` = 8','database','tbl_blog','8'),
	(25,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `dat_date` = \'2016-10-19\'\nWHERE `id` = 7','database','tbl_blog','7'),
	(26,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `dat_date` = \'2014-10-12\'\nWHERE `id` = 6','database','tbl_blog','6'),
	(27,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `dat_date` = \'2015-8-15\'\nWHERE `id` = 5','database','tbl_blog','5'),
	(28,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `dat_date` = \'2014-3-16\'\nWHERE `id` = 4','database','tbl_blog','4'),
	(29,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `dat_date` = \'2015-3-3\'\nWHERE `id` = 3','database','tbl_blog','3'),
	(30,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `dat_date` = \'2016-2-3\'\nWHERE `id` = 2','database','tbl_blog','2'),
	(31,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `dat_date` = \'2018-2-8\'\nWHERE `id` = 10','database','tbl_blog','10'),
	(32,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `txt_text` = \'<p>\\nOdio gravida ipsum molestie consectetur risus ornare mi dolor magna, nam tellus fusce viverra mattis ultricies fringilla vivamus. \\nCras lectus pellentesque elementum diam dapibus, aliquam id ultricies conubia pulvinar, pretium donec justo phasellus. \\nConubia viverra sed ipsum luctus inceptos consectetur aptent habitant, a vel justo congue platea fermentum est, molestie placerat eget ut sociosqu lacinia aliquam. \\nNec enim convallis odio cubilia dictumst pretium massa condimentum etiam iaculis, sodales suscipit ipsum mauris curabitur imperdiet donec lectus tempus orci nisl, dictum tristique justo vestibulum euismod aenean a himenaeos sollicitudin. \\nIpsum neque sociosqu aliquam eros sem fringilla quis semper, cubilia molestie aenean dictumst gravida aenean diam, himenaeos litora varius dui fames nullam ac. \\n</p>\\n<h2>Etiam</h2><p>\\nVenenatis vestibulum tempor faucibus augue cursus amet taciti nibh pulvinar, accumsan ac velit bibendum condimentum nisl adipiscing. \\nVestibulum eget non quisque malesuada diam malesuada himenaeos id amet, pulvinar porttitor dictumst platea molestie facilisis nullam nibh a interdum, fames bibendum hac velit ac habitant bibendum scelerisque. \\nMauris habitasse dictum volutpat fringilla sodales posuere ullamcorper tincidunt eu vestibulum, quisque gravida volutpat aenean conubia interdum quisque tortor consectetur suspendisse praesent, porttitor convallis hendrerit nam litora faucibus quam vehicula egestas. \\nCongue senectus eu tortor tempor lectus fermentum taciti, nibh ullamcorper risus congue proin velit ligula sit, potenti proin viverra metus tellus ultricies. \\n</p>\\n<h1>Etiam nisl</h1><p>\\nAccumsan commodo tempor nam pulvinar nisi integer tristique cursus, suspendisse nam aptent malesuada turpis etiam vitae placerat, augue torquent nisl aliquam dui platea pretium. \\nEgestas porttitor morbi feugiat sem velit sociosqu nulla, facilisis tincidunt ut phasellus elementum rhoncus etiam, aliquam curae dapibus leo semper mattis. \\nGravida turpis ornare suspendisse gravida sapien tempor commodo viverra eu scelerisque, aenean mauris elit curabitur est ad nisi tempor ut, elit morbi lobortis lacus rutrum scelerisque gravida erat ut. \\nTaciti ultrices augue mollis sagittis auctor enim mollis, velit eros odio conubia erat mollis habitasse, ad molestie nisl venenatis eros sem. \\n</p>\\n<p>\\nPraesent phasellus ipsum in accumsan molestie vivamus dapibus justo, tempus faucibus fringilla bibendum nam facilisis nec. \\nSodales dictumst hac consequat nostra rutrum lectus potenti sodales nec, hendrerit mauris quam sodales donec ullamcorper adipiscing ornare sagittis ante, cras bibendum eget sociosqu ultrices risus ipsum mollis. \\nEleifend facilisis dui curabitur cursus, id semper hendrerit aptent mauris, tortor fusce mattis. \\nPosuere dolor urna vivamus lectus donec auctor curabitur potenti rhoncus vehicula donec ut, hendrerit sodales quisque curabitur netus tortor platea volutpat amet laoreet sed, felis interdum dui vulputate enim etiam aliquet porta ornare donec aptent. \\n</p>\'\nWHERE `id` = 1','database','tbl_blog','1'),
	(33,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `txt_text` = \'<p>\\nTortor ullamcorper pulvinar massa dictumst rhoncus eget, pulvinar massa elit augue tristique, sodales nibh praesent est lobortis. \\nAptent inceptos libero conubia donec dolor consequat habitant egestas, potenti vehicula donec aptent turpis amet aptent class, vulputate litora orci justo ut luctus magna. \\nAliquet congue et tempor magna porttitor curae a nostra quisque, ligula morbi fames maecenas nisi cras posuere placerat, cubilia primis semper adipiscing velit pretium iaculis netus. \\nMolestie aenean consequat litora tellus eros vestibulum volutpat orci magna, senectus rutrum ultrices litora ut nullam etiam dapibus, cubilia augue vulputate ornare elementum hendrerit odio pulvinar. \\n</p>\\n<p>\\nPorttitor amet purus ipsum lacinia lectus metus ultricies fusce vivamus sodales gravida, cubilia eget at litora sodales commodo dapibus erat aliquam. \\nImperdiet aliquam et ut arcu ullamcorper egestas nulla taciti, dictumst amet phasellus sollicitudin felis aliquam massa, magna semper maecenas fames pretium malesuada sit. \\nSuscipit himenaeos molestie lectus pharetra imperdiet ultrices class risus, nulla non consectetur quis inceptos nibh integer, molestie nisl lorem tellus praesent amet sollicitudin. \\nPlatea dictumst nisi aliquam luctus ipsum nam laoreet quam, sociosqu ut sociosqu urna elit neque tristique, hendrerit sagittis eleifend semper mollis dictum placerat. \\n</p>\\n<p>\\nConsectetur hendrerit faucibus inceptos nec, nisl bibendum cubilia, lacinia venenatis praesent. \\nFeugiat iaculis molestie eleifend inceptos, felis sapien auctor. \\n</p>\'\nWHERE `id` = 10','database','tbl_blog','10'),
	(34,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `txt_text` = \'<p>\\nNunc pharetra primis aliquet fringilla erat dui sapien porttitor sodales consequat porttitor et morbi, risus vestibulum quisque aenean curae aliquam conubia elementum morbi tempor vel. \\nFelis proin justo tellus nisi blandit vulputate, ut etiam blandit tortor metus, per aliquam ultrices primis in. \\nDonec nullam est litora pharetra donec volutpat id ullamcorper varius potenti venenatis magna, sagittis et adipiscing pharetra feugiat in laoreet habitant rutrum aliquam est. \\nId faucibus senectus ornare ultrices nec nisl commodo, ultrices diam odio nostra malesuada platea. \\n</p>\'\nWHERE `id` = 7','database','tbl_blog','7'),
	(35,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `txt_text` = \'<h1>Varius congue</h1><p>\\nVestibulum iaculis per dictum egestas praesent quis litora proin, hac tellus proin risus dictum cubilia libero lectus pulvinar, leo sagittis in curabitur condimentum maecenas curabitur. \\nEtiam gravida dictumst aliquet varius congue torquent, posuere cubilia quisque consectetur sollicitudin vitae, potenti neque justo aliquam platea. \\nRutrum laoreet lorem mattis porttitor porta nisi imperdiet pharetra ligula, augue leo neque metus libero aliquam justo imperdiet feugiat, donec ac lorem dapibus cursus nulla aliquam auctor. \\nSemper netus ornare mattis maecenas sagittis odio vivamus ultrices commodo per, aptent nulla congue est vestibulum quam ligula class varius, malesuada diam sit non rutrum feugiat curabitur convallis hendrerit. \\n</p>\\n<p>\\nBibendum quam dictumst sem primis fames posuere blandit, elementum suscipit vel placerat sagittis sit est, risus hendrerit quisque leo donec himenaeos. \\nAenean duis risus sed nulla, platea metus nisi mi, at morbi aptent. \\nVivamus cursus volutpat fermentum elementum donec sed lacinia sodales fermentum egestas ipsum mattis convallis, curabitur consectetur aenean aliquam dapibus congue conubia quam commodo tristique scelerisque. \\nAd id sit bibendum rutrum duis ut cursus integer, laoreet vulputate viverra ac elementum sociosqu phasellus purus dictumst, malesuada fusce praesent volutpat turpis purus ligula. \\nFelis massa malesuada potenti litora proin ante interdum, fringilla suspendisse urna volutpat libero litora volutpat, nulla maecenas adipiscing netus ornare duis. \\n</p>\\n<h2>Aliquam imperdiet</h2><p>\\nPulvinar venenatis pharetra fringilla justo lacinia per ac id mollis luctus odio consectetur libero, cras imperdiet a conubia congue eget conubia felis quisque cubilia cras feugiat. \\nLuctus diam at sollicitudin facilisis nisl auctor fusce ultrices, blandit mauris euismod massa odio ligula torquent adipiscing viverra, accumsan donec iaculis elit quam aenean mollis. \\nFelis nostra dolor habitasse maecenas vitae porta in libero volutpat platea condimentum pulvinar, iaculis conubia lectus ullamcorper potenti ligula augue lorem arcu sapien. \\nScelerisque suspendisse ac odio himenaeos lacus aenean laoreet morbi, euismod malesuada mollis malesuada leo imperdiet habitant nibh nec, consequat convallis nam habitasse integer ad platea. \\n</p>\\n<h2>Class ornare</h2><p>\\nPulvinar himenaeos primis suscipit mattis turpis id sed primis, ante donec sodales congue mauris nunc at, curabitur cursus in felis tempus nec est. \\nTempus semper cubilia pretium ullamcorper orci fusce blandit himenaeos gravida primis per faucibus tortor, in sed mattis ut curabitur rutrum placerat suscipit auctor tellus purus urna. \\nPlatea rhoncus conubia accumsan donec fusce dolor fermentum fringilla, elementum odio nibh ad porta pharetra adipiscing interdum metus, nunc libero turpis vitae morbi hac diam. \\nDui arcu porta donec gravida ullamcorper eu vivamus potenti a taciti accumsan, mattis velit rhoncus iaculis platea orci suscipit fermentum ligula curabitur, a aenean varius nisl lectus nulla pharetra hac vestibulum sodales. \\n</p>\\n<h1>Pharetra</h1><p>\\nTellus bibendum ut eleifend, nullam. \\n</p>\'\nWHERE `id` = 2','database','tbl_blog','2'),
	(36,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `txt_text` = \'<h2>Platea etiam</h2><p>\\nTortor volutpat eleifend libero semper sem, tellus molestie tempus torquent integer laoreet, egestas tortor ad quis. \\nSem interdum fringilla auctor in et lacinia hac est habitant non, quisque potenti cubilia aptent lorem nullam posuere quis. \\nAptent et semper velit gravida sociosqu per, lacus facilisis nostra cras scelerisque ultricies vulputate, torquent aenean enim quis feugiat. \\nPorta ante mi non mi et aliquam felis commodo ipsum feugiat, inceptos euismod aliquam consectetur sit nostra cras vel mattis, porta mi maecenas aliquam nullam duis est suscipit torquent. \\nFaucibus tellus hac tincidunt bibendum malesuada interdum quisque sociosqu venenatis, sapien fames nisi quam diam amet pharetra nisl, consequat erat interdum torquent tellus sociosqu lobortis potenti. \\n</p>\\n<h2>Nam</h2><p>\\nPlatea in est turpis faucibus torquent amet in porttitor egestas, cras vel vivamus morbi in tempus per mi ultrices congue, cursus congue est ante nec habitasse tempus gravida. \\nAnte urna curabitur et metus sapien purus vehicula rutrum etiam, torquent habitasse orci velit mi diam vestibulum quisque. \\nViverra nisi nulla adipiscing congue dolor etiam nostra vivamus cursus vivamus, fringilla habitant bibendum nec senectus sociosqu elit in vel placerat ad, at lacus bibendum justo cras malesuada interdum etiam maecenas. \\nRhoncus justo faucibus suscipit lobortis platea vivamus mollis amet quisque praesent, torquent sodales class eget scelerisque enim dapibus habitant etiam, tellus fringilla accumsan enim aenean sed eu molestie ac. \\n</p>\\n<h2>Viverra viverra sociosqu</h2><p>\\nSuspendisse aptent porttitor platea egestas nisi sollicitudin orci pellentesque in cubilia, phasellus accumsan enim nullam elit tristique bibendum lorem massa interdum, hendrerit malesuada fringilla turpis proin vivamus etiam augue ad. \\nAt consequat arcu quis mollis sollicitudin tempor platea et, hac rutrum imperdiet venenatis morbi etiam neque, potenti ut mauris curae nisi sem ultrices. \\nAliquam consequat auctor sit augue libero conubia aliquet, curabitur metus iaculis ac nam auctor, rutrum sollicitudin elementum tincidunt varius aenean. \\nAliquet venenatis vel urna sagittis et facilisis hendrerit vivamus sollicitudin pulvinar cursus, at nec nostra felis conubia rhoncus viverra sollicitudin quisque ullamcorper, nisl quisque platea tellus pulvinar amet interdum turpis viverra ut. \\n</p>\\n<h2>Cras in nec</h2><p>\\nEget curabitur nunc auctor vulputate auctor in sit ultricies tellus magna, accumsan nostra donec habitant mauris felis ipsum arcu donec posuere neque, orci blandit vulputate auctor vehicula ultrices taciti accumsan libero. \\nLigula accumsan porta morbi cras consectetur platea vitae enim, velit hendrerit convallis potenti cras justo proin, in phasellus et iaculis porttitor senectus bibendum. \\nConvallis mauris sollicitudin tristique orci vivamus tincidunt auctor placerat in nulla, non ultrices risus consequat felis sagittis libero est venenatis, conubia tempor condimentum proin primis iaculis felis sem curabitur. \\nEgestas integer per id convallis aliquam vitae aenean erat porta, class gravida quisque interdum nisi donec primis pulvinar lorem, libero cursus diam nisl bibendum dapibus blandit vitae. \\n</p>\\n<h2>Accumsan massa aptent</h2><p>\\nMorbi vestibulum integer laoreet cursus suscipit massa aenean orci ad quam imperdiet felis, metus sapien et libero leo maecenas ad sodales tristique integer. \\n</p>\'\nWHERE `id` = 8','database','tbl_blog','8'),
	(37,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `txt_text` = \'<h1>Luctus</h1><p>\\nInteger quis mollis mauris adipiscing ultrices taciti sociosqu rutrum, semper pellentesque vel donec vivamus est sodales aptent ut, feugiat nibh conubia vehicula platea vivamus hendrerit. \\nSenectus ut nostra hendrerit ullamcorper dictum neque ante interdum, sagittis aliquam fringilla etiam placerat tempus sagittis himenaeos quam, curabitur nam dui praesent conubia molestie class. \\nHabitasse vehicula platea a lobortis dapibus tristique, consequat augue aptent nunc purus scelerisque, mattis purus maecenas consequat nam. \\nAmet aenean conubia porta potenti sem scelerisque risus ante adipiscing tempus, congue malesuada imperdiet blandit donec platea convallis libero tellus aliquam sapien, in sapien etiam senectus a lectus pellentesque senectus at. \\n</p>\\n<h1>Vitae netus</h1><p>\\nNon vestibulum morbi tincidunt aliquet laoreet vestibulum sollicitudin, laoreet leo nisi integer volutpat lorem placerat cubilia, mauris conubia mattis nulla luctus fames. \\nNeque venenatis litora nisi ullamcorper class amet scelerisque pretium, leo maecenas suspendisse porttitor vitae proin vivamus fusce netus, nam scelerisque egestas imperdiet tincidunt adipiscing malesuada. \\nVelit phasellus ultrices erat duis massa feugiat maecenas morbi inceptos ut, habitasse lorem praesent habitant viverra semper eleifend malesuada urna, est et neque eu nibh lorem posuere platea sodales. \\nEleifend sed condimentum libero diam ligula tortor vehicula diam vitae dui ac, ullamcorper nibh ultrices curabitur fusce aenean odio at ipsum laoreet, auctor orci vulputate viverra arcu habitant curae proin non eleifend. \\n</p>\\n<h2>Scelerisque fusce tincidunt</h2><p>\\nEuismod dictumst enim amet euismod etiam placerat sit id vehicula proin, nisi eros hac phasellus elit neque quisque ac cubilia. \\nPellentesque quis nisi accumsan lectus ultricies eu malesuada vulputate ante, lacinia accumsan duis nisl congue dolor bibendum torquent curae morbi, pulvinar donec torquent mi fusce turpis egestas eu. \\nUllamcorper platea condimentum ut est porta lacinia molestie, phasellus porta eleifend potenti ultrices auctor fringilla, libero tortor lacus tellus accumsan tristique. \\nTortor risus dictum per duis id egestas porta mattis quam leo, aenean at pulvinar sagittis tempus tellus class neque vulputate, hendrerit purus maecenas aenean etiam orci duis eleifend integer. \\n</p>\\n<h2>Cras morbi</h2><p>\\nCommodo conubia sit mattis non auctor imperdiet suspendisse dapibus dictumst ullamcorper morbi, euismod lobortis sodales vestibulum donec mollis fusce cras est mi, morbi fames elementum tincidunt imperdiet netus libero suscipit est enim. \\nQuam metus hendrerit ornare et posuere hac morbi nostra netus ultrices enim fames, euismod nibh tortor gravida praesent nostra amet curabitur risus venenatis enim, vel rutrum nullam semper hendrerit porttitor malesuada faucibus malesuada proin primis. \\nVitae porttitor egestas class euismod nec platea vel quisque duis, sagittis vel accumsan porta proin congue dolor nunc condimentum per, lobortis neque donec proin cubilia scelerisque morbi malesuada. \\n</p>\\n<h2>Mauris conubia</h2><p>\\nSapien venenatis nisi nunc est sed tempor semper porta ante augue nisl viverra, platea potenti vulputate rutrum eu quisque ut nibh fringilla sit auctor suscipit vulputate, gravida vivamus primis feugiat ut magna aliquam sed vel condimentum habitant. \\nVitae feugiat tortor mollis ut, netus sit pretium. \\n</p>\'\nWHERE `id` = 5','database','tbl_blog','5'),
	(38,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `txt_text` = \'<h2>Nunc vestibulum vestibulum</h2><p>\\nPer tellus luctus consectetur ornare ligula porttitor consectetur leo integer orci class enim imperdiet, orci fames ut torquent dui imperdiet aenean velit purus massa maecenas. \\nQuisque eleifend erat et mi nulla, aenean vivamus quisque consectetur a, malesuada porta ornare velit. \\nBlandit integer amet tempor feugiat magna vestibulum nunc duis turpis vitae massa blandit netus volutpat litora lorem, taciti nibh aliquet tempus sagittis cursus non sodales euismod arcu consectetur vestibulum faucibus egestas. \\nLitora platea ipsum et aptent mollis ut sapien interdum, dapibus elementum sit erat quam per maecenas velit, vel etiam consequat donec etiam sapien donec. \\n</p>\\n<p>\\nMalesuada quam diam a consequat faucibus quisque fringilla dui quam, dapibus imperdiet felis primis conubia suspendisse egestas interdum luctus massa, interdum dapibus dictumst mollis platea est a fames. \\nAliquet tristique viverra torquent sit dictum bibendum vitae rhoncus iaculis curae, posuere ante vivamus malesuada maecenas quis volutpat interdum eu. \\nAt conubia vitae sed auctor pulvinar convallis cubilia rhoncus, sed vulputate ante pellentesque malesuada nec vehicula elit, quam dictum accumsan curabitur ad lobortis hac. \\nAliquam cubilia commodo ut elit malesuada in, lacinia lectus imperdiet tellus nisi, pretium est porta pulvinar nam. \\n</p>\\n<h2>Eget nisi</h2><p>\\nCongue dolor ultrices consectetur velit in per euismod, elit aptent lobortis nostra tincidunt ornare etiam torquent, luctus ipsum turpis felis sem nam. \\nInteger netus integer hac sodales tempor donec ad, fermentum ligula mattis congue velit eros euismod, sapien est malesuada faucibus potenti et. \\nSuscipit imperdiet nec mi nullam donec massa semper cubilia ut tellus proin tortor sapien, pulvinar eleifend sociosqu auctor platea fermentum aliquet etiam semper quam vel accumsan. \\nHabitant blandit enim risus ultricies nunc adipiscing aliquet venenatis elementum metus, curabitur sagittis tortor placerat himenaeos ornare urna quisque leo rutrum praesent, ligula tempor dui tortor molestie massa sociosqu iaculis taciti. \\n</p>\\n<h1>Rhoncus elit</h1><p>\\nEuismod magna suspendisse sagittis eu malesuada tellus odio cras, ac class suscipit laoreet pretium porttitor libero elementum, eget potenti at sit volutpat duis praesent. \\nEget aliquam tincidunt suspendisse dui velit lacinia donec, quisque felis aliquam cras turpis mollis nisi, proin viverra ultrices ante aliquam quisque. \\nTorquent fermentum phasellus tellus, suscipit pharetra. \\n</p>\'\nWHERE `id` = 3','database','tbl_blog','3'),
	(39,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `txt_text` = \'<h2>Fermentum purus sagittis</h2><p>\\nPraesent magna nullam aenean pharetra semper enim sit volutpat ullamcorper per, mauris platea elementum eros phasellus himenaeos augue duis at ipsum lacus, imperdiet aliquam conubia torquent lacus turpis orci vulputate lacinia. \\nTellus phasellus senectus molestie est torquent mattis fusce, vehicula suscipit phasellus ac venenatis curae fermentum, aptent mi vehicula faucibus nunc nullam. \\nNisl elementum amet blandit eros sagittis justo dolor, hac sapien molestie integer quisque dictumst, vivamus sodales suscipit magna posuere nec. \\nVel aliquet potenti faucibus donec lobortis non donec accumsan et purus, primis fusce mollis sed massa purus lectus tincidunt. \\n</p>\\n<h2>Erat fusce semper</h2><p>\\nMi primis nullam phasellus etiam justo inceptos cras netus aliquam, donec senectus faucibus mi curabitur nostra ut libero, sed habitasse leo blandit consequat mauris potenti nisl. \\nPlatea donec dapibus maecenas orci ad proin pharetra potenti etiam fusce leo ornare, primis metus in molestie convallis nulla facilisis adipiscing lacus faucibus libero ipsum, donec euismod elementum ullamcorper senectus ullamcorper aliquam enim fringilla aliquam lorem. \\nDapibus posuere vel habitant malesuada class tempus senectus volutpat vehicula etiam libero, nec hendrerit dui maecenas ipsum nunc a fringilla laoreet. \\nProin sed laoreet consectetur morbi porta habitasse tortor, ut faucibus quis nulla volutpat fusce integer, viverra orci lobortis ultrices eleifend nisi. \\n</p>\\n<h2>Justo tellus</h2><p>\\nEuismod nulla quam lorem iaculis conubia semper viverra molestie maecenas tortor ipsum, justo mattis ut fringilla ut nam lobortis cras litora netus. \\nPorta erat non semper enim dui vitae tortor leo cras euismod lorem nibh, imperdiet ornare per adipiscing aptent fusce massa fusce nisl dictumst. \\nArcu magna eu fermentum proin accumsan ipsum tortor cras malesuada massa praesent, in donec etiam platea a congue vestibulum quisque morbi sit, pharetra tellus senectus habitant aliquet lorem ultricies ante mattis rutrum. \\nSollicitudin ac pellentesque tellus enim class conubia consequat ante placerat congue, pellentesque malesuada lectus justo aenean netus imperdiet mattis dui. \\n</p>\\n<h2>Non</h2><p>\\nCommodo condimentum aptent primis vehicula adipiscing himenaeos torquent scelerisque rutrum ad ullamcorper nam placerat tellus nec, ut porttitor sociosqu morbi duis iaculis proin iaculis quam tristique quisque at sociosqu. \\nScelerisque fermentum pretium sagittis purus pellentesque in arcu, faucibus volutpat fames tincidunt donec mollis, turpis habitasse himenaeos malesuada consequat lobortis. \\nElementum hendrerit egestas aliquet pellentesque interdum varius imperdiet sapien adipiscing vitae hac sollicitudin pulvinar, varius viverra egestas vel torquent suspendisse lacus ullamcorper etiam torquent himenaeos. \\nVitae bibendum torquent habitasse duis primis erat tempor, volutpat donec elit tempus himenaeos aptent etiam, metus fermentum massa proin non et. \\n</p>\\n<h2>Vitae</h2><p>\\nSit feugiat dui arcu sollicitudin nulla eros blandit, curabitur fames posuere porta vitae blandit non, mauris in purus per lectus urna. \\nTincidunt eu id dictumst leo platea tortor donec, semper vel erat vel ac est. \\n</p>\'\nWHERE `id` = 6','database','tbl_blog','6'),
	(40,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `txt_text` = \'<p>\\nNam malesuada ullamcorper gravida auctor amet pulvinar donec himenaeos volutpat, non nulla ad tortor eget tempor massa eget. \\nDiam orci congue ante metus enim orci rutrum mi, morbi vitae erat cras praesent lobortis diam duis, augue nam vitae elit habitasse commodo etiam. \\nAdipiscing cursus ad urna auctor diam odio gravida habitant vel nam, sollicitudin neque fusce at luctus dapibus nostra sem in ultricies, venenatis at nibh himenaeos est vulputate interdum at condimentum. \\nCongue facilisis purus cubilia pretium nibh sagittis vel rhoncus gravida aenean, sapien turpis ut ligula praesent quisque habitant eros ornare molestie, rhoncus duis per fames nulla laoreet pellentesque malesuada nec. \\n</p>\\n<p>\\nEu ullamcorper arcu vitae felis platea ligula, ipsum gravida suspendisse praesent molestie ultricies, quam aliquam ad purus habitant. \\nNetus sem aliquam consectetur enim aenean phasellus convallis volutpat orci nullam, libero gravida torquent donec per ut sit viverra donec. \\nPharetra bibendum hendrerit vitae ipsum ligula accumsan nulla in etiam, tristique potenti lorem lobortis nullam risus euismod dapibus, molestie suscipit hendrerit fringilla phasellus pellentesque aenean tempus. \\nDictumst in mollis etiam diam libero nunc condimentum sem velit lobortis, pulvinar purus condimentum purus fusce platea donec cubilia. \\nDiam aliquam morbi metus pulvinar, iaculis cursus dapibus, nisi augue fringilla. \\n</p>\\n<p>\\nCursus praesent tristique cubilia blandit dictumst aliquam litora non aenean, senectus molestie lacus mauris luctus fringilla per in nibh, magna ac pretium rutrum lacinia vulputate ornare magna. \\nVehicula scelerisque diam auctor tristique sit sollicitudin nostra congue hendrerit feugiat potenti, duis fames potenti dictumst fames quam et turpis ligula habitant. \\nMagna sapien sit pretium dictumst eget semper elit aenean id, metus praesent nunc odio vehicula nam gravida placerat turpis nec, porta quisque felis phasellus imperdiet tincidunt justo diam. \\nAd nisl justo aenean neque malesuada, nostra eleifend himenaeos torquent sed sagittis, semper ad magna viverra. \\n</p>\\n<h1>Senectus aliquam</h1><p>\\nMagna aliquet venenatis imperdiet potenti curabitur sit egestas inceptos, massa tristique fringilla sagittis mattis velit donec congue, ante cursus duis adipiscing cubilia dictum tempor. \\nQuisque ligula dui sed etiam hendrerit vehicula ornare pretium diam non, id dictum phasellus molestie ligula pellentesque pharetra luctus est dapibus, curabitur fringilla cubilia lectus fusce rutrum donec quis conubia. \\nVitae suscipit imperdiet donec integer enim habitant aptent curae cubilia enim, adipiscing potenti eleifend vivamus augue semper pretium ipsum dolor, lacinia curabitur etiam ante scelerisque suspendisse dolor non ultricies. \\nVenenatis diam sagittis mi aliquam in dui id sollicitudin id venenatis, ullamcorper magna dictumst gravida varius senectus vestibulum donec mi curabitur, accumsan donec nullam orci neque arcu magna purus phasellus. \\n</p>\\n<p>\\nOdio nisi conubia interdum venenatis, cras elit luctus. \\n</p>\'\nWHERE `id` = 9','database','tbl_blog','9'),
	(41,1,'2017-03-04 03:00:16','UPDATE `tbl_blog` SET `txt_text` = \'<p>\\nMolestie augue turpis lobortis torquent donec turpis faucibus, hendrerit vulputate euismod aliquet lacus quam volutpat etiam, nunc sollicitudin leo risus semper gravida. \\nId massa aliquam fermentum nibh congue ut ac aliquam facilisis placerat nunc maecenas, aenean vestibulum urna rhoncus pulvinar ultrices augue posuere habitasse aliquam urna. \\nEget curabitur tortor orci laoreet ac habitasse hac tristique maecenas, phasellus ut metus hendrerit etiam nunc rhoncus tellus dui ultricies, feugiat euismod iaculis netus felis class molestie vel. \\nPraesent quis malesuada suspendisse quam urna aliquam morbi fringilla, malesuada semper lobortis curae torquent mollis lectus, suscipit at class taciti etiam elementum nisl. \\n</p>\\n<h1>Eros molestie</h1><p>\\nUrna rutrum duis aenean egestas blandit arcu, blandit per diam aptent at, suspendisse blandit consectetur sed tristique. \\nUt libero imperdiet suspendisse metus ultrices nostra sociosqu, donec pharetra eros ipsum per semper, porta metus fringilla eros consectetur justo. \\nVehicula orci porta lorem tellus eget aptent dictumst viverra aliquam, habitasse imperdiet consequat nulla ullamcorper ante euismod lectus, himenaeos massa auctor per facilisis posuere primis eros. \\nVel ipsum vehicula lectus venenatis pharetra ligula class aenean himenaeos, est ligula tempus nam dui metus nostra dictum eros mauris, elit aenean dapibus tincidunt dictum nullam duis leo. \\n</p>\\n<h2>Egestas arcu</h2><p>\\nDictumst purus mi enim vestibulum ipsum donec volutpat sit congue, sodales torquent lacinia cras inceptos dapibus curabitur varius turpis, ullamcorper et porttitor vitae nunc mollis nec conubia. \\nHimenaeos curabitur nostra integer nisi orci dui sapien lectus, eleifend at phasellus himenaeos tempor nibh placerat, praesent scelerisque nunc vitae etiam at auctor. \\nAliquam senectus cras nec sapien eros feugiat urna, commodo nam dapibus felis pulvinar molestie et, gravida vulputate ut amet gravida fusce. \\nRutrum dapibus ultrices netus platea lacus potenti vitae platea, risus ullamcorper donec class quisque morbi. \\n</p>\'\nWHERE `id` = 4','database','tbl_blog','4'),
	(42,1,'2017-03-04 03:00:22','login','auth','',''),
	(43,1,'2017-03-04 03:00:22','login','auth','',''),
	(44,1,'2017-03-04 03:00:30','UPDATE `tbl_blog` SET `uri` = \'massa_et_consequat_scelerisque_nunc\'\nWHERE `id` = 1','database','tbl_blog','1'),
	(45,1,'2017-03-04 03:00:30','UPDATE `tbl_blog` SET `uri` = \'suspendisse\'\nWHERE `id` = 10','database','tbl_blog','10'),
	(46,1,'2017-03-04 03:00:30','UPDATE `tbl_blog` SET `uri` = \'pulvinar\'\nWHERE `id` = 7','database','tbl_blog','7'),
	(47,1,'2017-03-04 03:00:30','UPDATE `tbl_blog` SET `uri` = \'vehicula_iaculis_quis\'\nWHERE `id` = 2','database','tbl_blog','2'),
	(48,1,'2017-03-04 03:00:30','UPDATE `tbl_blog` SET `uri` = \'faucibus_sit_porttitor_quisque_posuere\'\nWHERE `id` = 8','database','tbl_blog','8'),
	(49,1,'2017-03-04 03:00:30','UPDATE `tbl_blog` SET `uri` = \'phasellus_ullamcorper_dolor_aenean_suspendisse\'\nWHERE `id` = 5','database','tbl_blog','5'),
	(50,1,'2017-03-04 03:00:30','UPDATE `tbl_blog` SET `uri` = \'donec_volutpat_dictumst\'\nWHERE `id` = 3','database','tbl_blog','3'),
	(51,1,'2017-03-04 03:00:30','UPDATE `tbl_blog` SET `uri` = \'euismod_augue_placerat_sodales\'\nWHERE `id` = 6','database','tbl_blog','6'),
	(52,1,'2017-03-04 03:00:30','UPDATE `tbl_blog` SET `uri` = \'tempor\'\nWHERE `id` = 9','database','tbl_blog','9'),
	(53,1,'2017-03-04 03:00:30','UPDATE `tbl_blog` SET `uri` = \'fringilla\'\nWHERE `id` = 4','database','tbl_blog','4'),
	(54,1,'2017-03-04 03:00:35','login','auth','',''),
	(55,1,'2017-03-04 03:00:35','login','auth','',''),
	(56,1,'2017-03-04 03:00:43','login','auth','',''),
	(57,1,'2017-03-04 03:00:43','login','auth','',''),
	(58,1,'2017-03-04 03:00:46','login','auth','',''),
	(59,1,'2017-03-04 03:00:46','login','auth','',''),
	(60,1,'2017-03-04 03:00:51','login','auth','',''),
	(61,1,'2017-03-04 03:00:51','login','auth','',''),
	(62,1,'2017-03-04 03:00:51','INSERT INTO `tbl_menu` (`order`, `self_parent`, `uri`, `str_title`, `txt_text`, `medias_fotos`, `b_visible`, `str_module`, `stx_description`, `str_keywords`) VALUES (\'5\', \'0\', \'blog\', \'Blog\', \'\', \'\', \'1\', \'\', \'\', \'\')','database','tbl_menu','6'),
	(63,1,'2017-03-04 03:00:52','login','auth','',''),
	(64,1,'2017-03-04 03:00:52','login','auth','',''),
	(65,1,'2017-03-04 03:00:56','login','auth','',''),
	(66,1,'2017-03-04 03:00:56','login','auth','',''),
	(67,1,'2017-03-04 03:00:56','UPDATE `tbl_menu` SET `order` = 1\nWHERE `id` = 6','database','tbl_menu','6'),
	(68,1,'2017-03-04 03:00:56','UPDATE `tbl_menu` SET `self_parent` = \'0\'\nWHERE `id` = 6','database','tbl_menu','6'),
	(69,1,'2017-03-04 03:00:56','INSERT INTO `log_activity` (`id_user`, `str_activity_type`, `stx_activity`, `str_model`, `str_key`) VALUES (\'1\', \'database\', \'UPDATE `tbl_menu` SET `order` = 1\\nWHERE `id` = 6\', \'tbl_menu\', \'6\');\n\nUPDATE `tbl_menu` SET `order`=`order`+1 WHERE `order`>=\'1\' AND `order`<\'5\' AND `id` NOT IN(6);\n\nINSERT INTO `log_activity` (`id_user`, `str_activity_type`, `stx_activity`, `str_model`, `str_key`) VALUES (\'1\', \'database\', \'UPDATE `tbl_menu` SET `self_parent` = \\\'0\\\'\\nWHERE `id` = 6\', \'tbl_menu\', \'6\');\n\n','database','tbl_menu','6');

/*!40000 ALTER TABLE `log_activity` ENABLE KEYS */;
UNLOCK TABLES;


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

LOCK TABLES `log_login_attempts` WRITE;
/*!40000 ALTER TABLE `log_login_attempts` DISABLE KEYS */;

INSERT INTO `log_login_attempts` (`id`, `ip_address`, `login`, `time`)
VALUES
	(3,'127.0.0.1','E0mniBtO',1496031431),
	(4,'127.0.0.1','zDm7nGdR',1496031431),
	(5,'127.0.0.1','OR \"\"=\"\"',1496031431),
	(6,'127.0.0.1','1; DROP TABLE cfg_users',1496031431),
	(7,'127.0.0.1','1 or 1=1',1496031431),
	(8,'127.0.0.1','1\' or \'1\' = \'1\'))/*',1496031431),
	(9,'127.0.0.1','1\' or \'1\' = \'1\')) LIMIT 1/*',1496031431),
	(10,'127.0.0.1','1 AND 1=1',1496031431),
	(11,'127.0.0.1','1 ORDER BY 10--',1496031431),
	(14,'127.0.0.1','OR \"\"=\"\"',1496031431),
	(15,'127.0.0.1','1; DROP TABLE cfg_users',1496031431),
	(16,'127.0.0.1','1 or 1=1',1496031432),
	(17,'127.0.0.1','1\' or \'1\' = \'1\'))/*',1496031432),
	(18,'127.0.0.1','1\' or \'1\' = \'1\')) LIMIT 1/*',1496031432),
	(19,'127.0.0.1','1 AND 1=1',1496031432),
	(20,'127.0.0.1','1 ORDER BY 10--',1496031432);

/*!40000 ALTER TABLE `log_login_attempts` ENABLE KEYS */;
UNLOCK TABLES;


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
	(1608,658,0,'2017-01-08 11:06:21'),
	(1609,660,0,'2017-01-14 15:28:19'),
	(1610,660,0,'2017-01-14 15:28:19'),
	(1611,660,0,'2017-01-14 15:28:19'),
	(1612,660,0,'2017-01-14 15:28:19'),
	(1613,660,0,'2017-01-14 15:28:19'),
	(1614,662,0,'2017-01-30 21:12:30'),
	(1615,662,0,'2017-01-30 21:12:30'),
	(1616,662,0,'2017-01-30 21:12:30'),
	(1617,662,0,'2017-01-30 21:12:30'),
	(1618,664,0,'2017-01-31 21:42:15'),
	(1619,664,0,'2017-01-31 21:42:15'),
	(1620,664,0,'2017-01-31 21:42:15'),
	(1621,664,0,'2017-01-31 21:42:15'),
	(1622,664,0,'2017-01-31 21:42:15'),
	(1623,664,0,'2017-01-31 21:42:15'),
	(1624,666,18,'2017-05-29 06:17:14'),
	(1625,666,2,'2017-05-29 06:17:14'),
	(1626,666,7,'2017-05-29 06:17:14'),
	(1627,666,10,'2017-05-29 06:17:14'),
	(1628,666,8,'2017-05-29 06:17:14');

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

LOCK TABLES `res_assets` WRITE;
/*!40000 ALTER TABLE `res_assets` DISABLE KEYS */;

INSERT INTO `res_assets` (`id`, `b_exists`, `file`, `path`, `type`, `alt`, `date`, `size`, `width`, `height`)
VALUES
	(1,1,'cdlabel.pdf','downloads','pdf','CDlabel','2017-01-14',73,0,0),
	(2,1,'cdlabel_20161209032502.pdf','downloads','pdf','CDlabel_20161209032502','2017-01-14',73,0,0),
	(3,1,'intomyarms.doc','downloads','doc','IntoMyArms','2017-01-14',12,0,0),
	(4,1,'test_01.jpg','pictures','jpg','3WLH2Rbt','2017-01-13',60,300,400),
	(5,1,'test_02.jpg','pictures','jpg','JoEPLCpA','2017-01-13',33,300,225),
	(6,1,'test_11.jpg','pictures','jpg','xms9zYjW','2017-01-14',34,300,225);

/*!40000 ALTER TABLE `res_assets` ENABLE KEYS */;
UNLOCK TABLES;


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


# Dump of table tbl_blog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_blog`;

CREATE TABLE `tbl_blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(100) NOT NULL,
  `str_title` varchar(255) NOT NULL DEFAULT '',
  `dat_date` date NOT NULL,
  `txt_text` text NOT NULL,
  `b_visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `tbl_blog` WRITE;
/*!40000 ALTER TABLE `tbl_blog` DISABLE KEYS */;

INSERT INTO `tbl_blog` (`id`, `uri`, `str_title`, `dat_date`, `txt_text`, `b_visible`)
VALUES
	(1,'massa_et_consequat_scelerisque_nunc','Massa et consequat scelerisque nunc ','2020-03-11','<p>\nOdio gravida ipsum molestie consectetur risus ornare mi dolor magna, nam tellus fusce viverra mattis ultricies fringilla vivamus. \nCras lectus pellentesque elementum diam dapibus, aliquam id ultricies conubia pulvinar, pretium donec justo phasellus. \nConubia viverra sed ipsum luctus inceptos consectetur aptent habitant, a vel justo congue platea fermentum est, molestie placerat eget ut sociosqu lacinia aliquam. \nNec enim convallis odio cubilia dictumst pretium massa condimentum etiam iaculis, sodales suscipit ipsum mauris curabitur imperdiet donec lectus tempus orci nisl, dictum tristique justo vestibulum euismod aenean a himenaeos sollicitudin. \nIpsum neque sociosqu aliquam eros sem fringilla quis semper, cubilia molestie aenean dictumst gravida aenean diam, himenaeos litora varius dui fames nullam ac. \n</p>\n<h2>Etiam</h2><p>\nVenenatis vestibulum tempor faucibus augue cursus amet taciti nibh pulvinar, accumsan ac velit bibendum condimentum nisl adipiscing. \nVestibulum eget non quisque malesuada diam malesuada himenaeos id amet, pulvinar porttitor dictumst platea molestie facilisis nullam nibh a interdum, fames bibendum hac velit ac habitant bibendum scelerisque. \nMauris habitasse dictum volutpat fringilla sodales posuere ullamcorper tincidunt eu vestibulum, quisque gravida volutpat aenean conubia interdum quisque tortor consectetur suspendisse praesent, porttitor convallis hendrerit nam litora faucibus quam vehicula egestas. \nCongue senectus eu tortor tempor lectus fermentum taciti, nibh ullamcorper risus congue proin velit ligula sit, potenti proin viverra metus tellus ultricies. \n</p>\n<h1>Etiam nisl</h1><p>\nAccumsan commodo tempor nam pulvinar nisi integer tristique cursus, suspendisse nam aptent malesuada turpis etiam vitae placerat, augue torquent nisl aliquam dui platea pretium. \nEgestas porttitor morbi feugiat sem velit sociosqu nulla, facilisis tincidunt ut phasellus elementum rhoncus etiam, aliquam curae dapibus leo semper mattis. \nGravida turpis ornare suspendisse gravida sapien tempor commodo viverra eu scelerisque, aenean mauris elit curabitur est ad nisi tempor ut, elit morbi lobortis lacus rutrum scelerisque gravida erat ut. \nTaciti ultrices augue mollis sagittis auctor enim mollis, velit eros odio conubia erat mollis habitasse, ad molestie nisl venenatis eros sem. \n</p>\n<p>\nPraesent phasellus ipsum in accumsan molestie vivamus dapibus justo, tempus faucibus fringilla bibendum nam facilisis nec. \nSodales dictumst hac consequat nostra rutrum lectus potenti sodales nec, hendrerit mauris quam sodales donec ullamcorper adipiscing ornare sagittis ante, cras bibendum eget sociosqu ultrices risus ipsum mollis. \nEleifend facilisis dui curabitur cursus, id semper hendrerit aptent mauris, tortor fusce mattis. \nPosuere dolor urna vivamus lectus donec auctor curabitur potenti rhoncus vehicula donec ut, hendrerit sodales quisque curabitur netus tortor platea volutpat amet laoreet sed, felis interdum dui vulputate enim etiam aliquet porta ornare donec aptent. \n</p>',1),
	(2,'vehicula_iaculis_quis','Vehicula iaculis quis ','2016-02-03','<h1>Varius congue</h1><p>\nVestibulum iaculis per dictum egestas praesent quis litora proin, hac tellus proin risus dictum cubilia libero lectus pulvinar, leo sagittis in curabitur condimentum maecenas curabitur. \nEtiam gravida dictumst aliquet varius congue torquent, posuere cubilia quisque consectetur sollicitudin vitae, potenti neque justo aliquam platea. \nRutrum laoreet lorem mattis porttitor porta nisi imperdiet pharetra ligula, augue leo neque metus libero aliquam justo imperdiet feugiat, donec ac lorem dapibus cursus nulla aliquam auctor. \nSemper netus ornare mattis maecenas sagittis odio vivamus ultrices commodo per, aptent nulla congue est vestibulum quam ligula class varius, malesuada diam sit non rutrum feugiat curabitur convallis hendrerit. \n</p>\n<p>\nBibendum quam dictumst sem primis fames posuere blandit, elementum suscipit vel placerat sagittis sit est, risus hendrerit quisque leo donec himenaeos. \nAenean duis risus sed nulla, platea metus nisi mi, at morbi aptent. \nVivamus cursus volutpat fermentum elementum donec sed lacinia sodales fermentum egestas ipsum mattis convallis, curabitur consectetur aenean aliquam dapibus congue conubia quam commodo tristique scelerisque. \nAd id sit bibendum rutrum duis ut cursus integer, laoreet vulputate viverra ac elementum sociosqu phasellus purus dictumst, malesuada fusce praesent volutpat turpis purus ligula. \nFelis massa malesuada potenti litora proin ante interdum, fringilla suspendisse urna volutpat libero litora volutpat, nulla maecenas adipiscing netus ornare duis. \n</p>\n<h2>Aliquam imperdiet</h2><p>\nPulvinar venenatis pharetra fringilla justo lacinia per ac id mollis luctus odio consectetur libero, cras imperdiet a conubia congue eget conubia felis quisque cubilia cras feugiat. \nLuctus diam at sollicitudin facilisis nisl auctor fusce ultrices, blandit mauris euismod massa odio ligula torquent adipiscing viverra, accumsan donec iaculis elit quam aenean mollis. \nFelis nostra dolor habitasse maecenas vitae porta in libero volutpat platea condimentum pulvinar, iaculis conubia lectus ullamcorper potenti ligula augue lorem arcu sapien. \nScelerisque suspendisse ac odio himenaeos lacus aenean laoreet morbi, euismod malesuada mollis malesuada leo imperdiet habitant nibh nec, consequat convallis nam habitasse integer ad platea. \n</p>\n<h2>Class ornare</h2><p>\nPulvinar himenaeos primis suscipit mattis turpis id sed primis, ante donec sodales congue mauris nunc at, curabitur cursus in felis tempus nec est. \nTempus semper cubilia pretium ullamcorper orci fusce blandit himenaeos gravida primis per faucibus tortor, in sed mattis ut curabitur rutrum placerat suscipit auctor tellus purus urna. \nPlatea rhoncus conubia accumsan donec fusce dolor fermentum fringilla, elementum odio nibh ad porta pharetra adipiscing interdum metus, nunc libero turpis vitae morbi hac diam. \nDui arcu porta donec gravida ullamcorper eu vivamus potenti a taciti accumsan, mattis velit rhoncus iaculis platea orci suscipit fermentum ligula curabitur, a aenean varius nisl lectus nulla pharetra hac vestibulum sodales. \n</p>\n<h1>Pharetra</h1><p>\nTellus bibendum ut eleifend, nullam. \n</p>',1),
	(3,'donec_volutpat_dictumst','Donec volutpat dictumst ','2015-03-03','<h2>Nunc vestibulum vestibulum</h2><p>\nPer tellus luctus consectetur ornare ligula porttitor consectetur leo integer orci class enim imperdiet, orci fames ut torquent dui imperdiet aenean velit purus massa maecenas. \nQuisque eleifend erat et mi nulla, aenean vivamus quisque consectetur a, malesuada porta ornare velit. \nBlandit integer amet tempor feugiat magna vestibulum nunc duis turpis vitae massa blandit netus volutpat litora lorem, taciti nibh aliquet tempus sagittis cursus non sodales euismod arcu consectetur vestibulum faucibus egestas. \nLitora platea ipsum et aptent mollis ut sapien interdum, dapibus elementum sit erat quam per maecenas velit, vel etiam consequat donec etiam sapien donec. \n</p>\n<p>\nMalesuada quam diam a consequat faucibus quisque fringilla dui quam, dapibus imperdiet felis primis conubia suspendisse egestas interdum luctus massa, interdum dapibus dictumst mollis platea est a fames. \nAliquet tristique viverra torquent sit dictum bibendum vitae rhoncus iaculis curae, posuere ante vivamus malesuada maecenas quis volutpat interdum eu. \nAt conubia vitae sed auctor pulvinar convallis cubilia rhoncus, sed vulputate ante pellentesque malesuada nec vehicula elit, quam dictum accumsan curabitur ad lobortis hac. \nAliquam cubilia commodo ut elit malesuada in, lacinia lectus imperdiet tellus nisi, pretium est porta pulvinar nam. \n</p>\n<h2>Eget nisi</h2><p>\nCongue dolor ultrices consectetur velit in per euismod, elit aptent lobortis nostra tincidunt ornare etiam torquent, luctus ipsum turpis felis sem nam. \nInteger netus integer hac sodales tempor donec ad, fermentum ligula mattis congue velit eros euismod, sapien est malesuada faucibus potenti et. \nSuscipit imperdiet nec mi nullam donec massa semper cubilia ut tellus proin tortor sapien, pulvinar eleifend sociosqu auctor platea fermentum aliquet etiam semper quam vel accumsan. \nHabitant blandit enim risus ultricies nunc adipiscing aliquet venenatis elementum metus, curabitur sagittis tortor placerat himenaeos ornare urna quisque leo rutrum praesent, ligula tempor dui tortor molestie massa sociosqu iaculis taciti. \n</p>\n<h1>Rhoncus elit</h1><p>\nEuismod magna suspendisse sagittis eu malesuada tellus odio cras, ac class suscipit laoreet pretium porttitor libero elementum, eget potenti at sit volutpat duis praesent. \nEget aliquam tincidunt suspendisse dui velit lacinia donec, quisque felis aliquam cras turpis mollis nisi, proin viverra ultrices ante aliquam quisque. \nTorquent fermentum phasellus tellus, suscipit pharetra. \n</p>',1),
	(4,'fringilla','Fringilla ','2014-03-16','<p>\nMolestie augue turpis lobortis torquent donec turpis faucibus, hendrerit vulputate euismod aliquet lacus quam volutpat etiam, nunc sollicitudin leo risus semper gravida. \nId massa aliquam fermentum nibh congue ut ac aliquam facilisis placerat nunc maecenas, aenean vestibulum urna rhoncus pulvinar ultrices augue posuere habitasse aliquam urna. \nEget curabitur tortor orci laoreet ac habitasse hac tristique maecenas, phasellus ut metus hendrerit etiam nunc rhoncus tellus dui ultricies, feugiat euismod iaculis netus felis class molestie vel. \nPraesent quis malesuada suspendisse quam urna aliquam morbi fringilla, malesuada semper lobortis curae torquent mollis lectus, suscipit at class taciti etiam elementum nisl. \n</p>\n<h1>Eros molestie</h1><p>\nUrna rutrum duis aenean egestas blandit arcu, blandit per diam aptent at, suspendisse blandit consectetur sed tristique. \nUt libero imperdiet suspendisse metus ultrices nostra sociosqu, donec pharetra eros ipsum per semper, porta metus fringilla eros consectetur justo. \nVehicula orci porta lorem tellus eget aptent dictumst viverra aliquam, habitasse imperdiet consequat nulla ullamcorper ante euismod lectus, himenaeos massa auctor per facilisis posuere primis eros. \nVel ipsum vehicula lectus venenatis pharetra ligula class aenean himenaeos, est ligula tempus nam dui metus nostra dictum eros mauris, elit aenean dapibus tincidunt dictum nullam duis leo. \n</p>\n<h2>Egestas arcu</h2><p>\nDictumst purus mi enim vestibulum ipsum donec volutpat sit congue, sodales torquent lacinia cras inceptos dapibus curabitur varius turpis, ullamcorper et porttitor vitae nunc mollis nec conubia. \nHimenaeos curabitur nostra integer nisi orci dui sapien lectus, eleifend at phasellus himenaeos tempor nibh placerat, praesent scelerisque nunc vitae etiam at auctor. \nAliquam senectus cras nec sapien eros feugiat urna, commodo nam dapibus felis pulvinar molestie et, gravida vulputate ut amet gravida fusce. \nRutrum dapibus ultrices netus platea lacus potenti vitae platea, risus ullamcorper donec class quisque morbi. \n</p>',1),
	(5,'phasellus_ullamcorper_dolor_aenean_suspendisse','Phasellus ullamcorper dolor aenean suspendisse ','2015-08-15','<h1>Luctus</h1><p>\nInteger quis mollis mauris adipiscing ultrices taciti sociosqu rutrum, semper pellentesque vel donec vivamus est sodales aptent ut, feugiat nibh conubia vehicula platea vivamus hendrerit. \nSenectus ut nostra hendrerit ullamcorper dictum neque ante interdum, sagittis aliquam fringilla etiam placerat tempus sagittis himenaeos quam, curabitur nam dui praesent conubia molestie class. \nHabitasse vehicula platea a lobortis dapibus tristique, consequat augue aptent nunc purus scelerisque, mattis purus maecenas consequat nam. \nAmet aenean conubia porta potenti sem scelerisque risus ante adipiscing tempus, congue malesuada imperdiet blandit donec platea convallis libero tellus aliquam sapien, in sapien etiam senectus a lectus pellentesque senectus at. \n</p>\n<h1>Vitae netus</h1><p>\nNon vestibulum morbi tincidunt aliquet laoreet vestibulum sollicitudin, laoreet leo nisi integer volutpat lorem placerat cubilia, mauris conubia mattis nulla luctus fames. \nNeque venenatis litora nisi ullamcorper class amet scelerisque pretium, leo maecenas suspendisse porttitor vitae proin vivamus fusce netus, nam scelerisque egestas imperdiet tincidunt adipiscing malesuada. \nVelit phasellus ultrices erat duis massa feugiat maecenas morbi inceptos ut, habitasse lorem praesent habitant viverra semper eleifend malesuada urna, est et neque eu nibh lorem posuere platea sodales. \nEleifend sed condimentum libero diam ligula tortor vehicula diam vitae dui ac, ullamcorper nibh ultrices curabitur fusce aenean odio at ipsum laoreet, auctor orci vulputate viverra arcu habitant curae proin non eleifend. \n</p>\n<h2>Scelerisque fusce tincidunt</h2><p>\nEuismod dictumst enim amet euismod etiam placerat sit id vehicula proin, nisi eros hac phasellus elit neque quisque ac cubilia. \nPellentesque quis nisi accumsan lectus ultricies eu malesuada vulputate ante, lacinia accumsan duis nisl congue dolor bibendum torquent curae morbi, pulvinar donec torquent mi fusce turpis egestas eu. \nUllamcorper platea condimentum ut est porta lacinia molestie, phasellus porta eleifend potenti ultrices auctor fringilla, libero tortor lacus tellus accumsan tristique. \nTortor risus dictum per duis id egestas porta mattis quam leo, aenean at pulvinar sagittis tempus tellus class neque vulputate, hendrerit purus maecenas aenean etiam orci duis eleifend integer. \n</p>\n<h2>Cras morbi</h2><p>\nCommodo conubia sit mattis non auctor imperdiet suspendisse dapibus dictumst ullamcorper morbi, euismod lobortis sodales vestibulum donec mollis fusce cras est mi, morbi fames elementum tincidunt imperdiet netus libero suscipit est enim. \nQuam metus hendrerit ornare et posuere hac morbi nostra netus ultrices enim fames, euismod nibh tortor gravida praesent nostra amet curabitur risus venenatis enim, vel rutrum nullam semper hendrerit porttitor malesuada faucibus malesuada proin primis. \nVitae porttitor egestas class euismod nec platea vel quisque duis, sagittis vel accumsan porta proin congue dolor nunc condimentum per, lobortis neque donec proin cubilia scelerisque morbi malesuada. \n</p>\n<h2>Mauris conubia</h2><p>\nSapien venenatis nisi nunc est sed tempor semper porta ante augue nisl viverra, platea potenti vulputate rutrum eu quisque ut nibh fringilla sit auctor suscipit vulputate, gravida vivamus primis feugiat ut magna aliquam sed vel condimentum habitant. \nVitae feugiat tortor mollis ut, netus sit pretium. \n</p>',1),
	(6,'euismod_augue_placerat_sodales','Euismod augue placerat sodales ','2014-10-12','<h2>Fermentum purus sagittis</h2><p>\nPraesent magna nullam aenean pharetra semper enim sit volutpat ullamcorper per, mauris platea elementum eros phasellus himenaeos augue duis at ipsum lacus, imperdiet aliquam conubia torquent lacus turpis orci vulputate lacinia. \nTellus phasellus senectus molestie est torquent mattis fusce, vehicula suscipit phasellus ac venenatis curae fermentum, aptent mi vehicula faucibus nunc nullam. \nNisl elementum amet blandit eros sagittis justo dolor, hac sapien molestie integer quisque dictumst, vivamus sodales suscipit magna posuere nec. \nVel aliquet potenti faucibus donec lobortis non donec accumsan et purus, primis fusce mollis sed massa purus lectus tincidunt. \n</p>\n<h2>Erat fusce semper</h2><p>\nMi primis nullam phasellus etiam justo inceptos cras netus aliquam, donec senectus faucibus mi curabitur nostra ut libero, sed habitasse leo blandit consequat mauris potenti nisl. \nPlatea donec dapibus maecenas orci ad proin pharetra potenti etiam fusce leo ornare, primis metus in molestie convallis nulla facilisis adipiscing lacus faucibus libero ipsum, donec euismod elementum ullamcorper senectus ullamcorper aliquam enim fringilla aliquam lorem. \nDapibus posuere vel habitant malesuada class tempus senectus volutpat vehicula etiam libero, nec hendrerit dui maecenas ipsum nunc a fringilla laoreet. \nProin sed laoreet consectetur morbi porta habitasse tortor, ut faucibus quis nulla volutpat fusce integer, viverra orci lobortis ultrices eleifend nisi. \n</p>\n<h2>Justo tellus</h2><p>\nEuismod nulla quam lorem iaculis conubia semper viverra molestie maecenas tortor ipsum, justo mattis ut fringilla ut nam lobortis cras litora netus. \nPorta erat non semper enim dui vitae tortor leo cras euismod lorem nibh, imperdiet ornare per adipiscing aptent fusce massa fusce nisl dictumst. \nArcu magna eu fermentum proin accumsan ipsum tortor cras malesuada massa praesent, in donec etiam platea a congue vestibulum quisque morbi sit, pharetra tellus senectus habitant aliquet lorem ultricies ante mattis rutrum. \nSollicitudin ac pellentesque tellus enim class conubia consequat ante placerat congue, pellentesque malesuada lectus justo aenean netus imperdiet mattis dui. \n</p>\n<h2>Non</h2><p>\nCommodo condimentum aptent primis vehicula adipiscing himenaeos torquent scelerisque rutrum ad ullamcorper nam placerat tellus nec, ut porttitor sociosqu morbi duis iaculis proin iaculis quam tristique quisque at sociosqu. \nScelerisque fermentum pretium sagittis purus pellentesque in arcu, faucibus volutpat fames tincidunt donec mollis, turpis habitasse himenaeos malesuada consequat lobortis. \nElementum hendrerit egestas aliquet pellentesque interdum varius imperdiet sapien adipiscing vitae hac sollicitudin pulvinar, varius viverra egestas vel torquent suspendisse lacus ullamcorper etiam torquent himenaeos. \nVitae bibendum torquent habitasse duis primis erat tempor, volutpat donec elit tempus himenaeos aptent etiam, metus fermentum massa proin non et. \n</p>\n<h2>Vitae</h2><p>\nSit feugiat dui arcu sollicitudin nulla eros blandit, curabitur fames posuere porta vitae blandit non, mauris in purus per lectus urna. \nTincidunt eu id dictumst leo platea tortor donec, semper vel erat vel ac est. \n</p>',1),
	(7,'pulvinar','Pulvinar ','2016-10-19','<p>\nNunc pharetra primis aliquet fringilla erat dui sapien porttitor sodales consequat porttitor et morbi, risus vestibulum quisque aenean curae aliquam conubia elementum morbi tempor vel. \nFelis proin justo tellus nisi blandit vulputate, ut etiam blandit tortor metus, per aliquam ultrices primis in. \nDonec nullam est litora pharetra donec volutpat id ullamcorper varius potenti venenatis magna, sagittis et adipiscing pharetra feugiat in laoreet habitant rutrum aliquam est. \nId faucibus senectus ornare ultrices nec nisl commodo, ultrices diam odio nostra malesuada platea. \n</p>',1),
	(8,'faucibus_sit_porttitor_quisque_posuere','Faucibus sit porttitor quisque posuere ','2015-11-03','<h2>Platea etiam</h2><p>\nTortor volutpat eleifend libero semper sem, tellus molestie tempus torquent integer laoreet, egestas tortor ad quis. \nSem interdum fringilla auctor in et lacinia hac est habitant non, quisque potenti cubilia aptent lorem nullam posuere quis. \nAptent et semper velit gravida sociosqu per, lacus facilisis nostra cras scelerisque ultricies vulputate, torquent aenean enim quis feugiat. \nPorta ante mi non mi et aliquam felis commodo ipsum feugiat, inceptos euismod aliquam consectetur sit nostra cras vel mattis, porta mi maecenas aliquam nullam duis est suscipit torquent. \nFaucibus tellus hac tincidunt bibendum malesuada interdum quisque sociosqu venenatis, sapien fames nisi quam diam amet pharetra nisl, consequat erat interdum torquent tellus sociosqu lobortis potenti. \n</p>\n<h2>Nam</h2><p>\nPlatea in est turpis faucibus torquent amet in porttitor egestas, cras vel vivamus morbi in tempus per mi ultrices congue, cursus congue est ante nec habitasse tempus gravida. \nAnte urna curabitur et metus sapien purus vehicula rutrum etiam, torquent habitasse orci velit mi diam vestibulum quisque. \nViverra nisi nulla adipiscing congue dolor etiam nostra vivamus cursus vivamus, fringilla habitant bibendum nec senectus sociosqu elit in vel placerat ad, at lacus bibendum justo cras malesuada interdum etiam maecenas. \nRhoncus justo faucibus suscipit lobortis platea vivamus mollis amet quisque praesent, torquent sodales class eget scelerisque enim dapibus habitant etiam, tellus fringilla accumsan enim aenean sed eu molestie ac. \n</p>\n<h2>Viverra viverra sociosqu</h2><p>\nSuspendisse aptent porttitor platea egestas nisi sollicitudin orci pellentesque in cubilia, phasellus accumsan enim nullam elit tristique bibendum lorem massa interdum, hendrerit malesuada fringilla turpis proin vivamus etiam augue ad. \nAt consequat arcu quis mollis sollicitudin tempor platea et, hac rutrum imperdiet venenatis morbi etiam neque, potenti ut mauris curae nisi sem ultrices. \nAliquam consequat auctor sit augue libero conubia aliquet, curabitur metus iaculis ac nam auctor, rutrum sollicitudin elementum tincidunt varius aenean. \nAliquet venenatis vel urna sagittis et facilisis hendrerit vivamus sollicitudin pulvinar cursus, at nec nostra felis conubia rhoncus viverra sollicitudin quisque ullamcorper, nisl quisque platea tellus pulvinar amet interdum turpis viverra ut. \n</p>\n<h2>Cras in nec</h2><p>\nEget curabitur nunc auctor vulputate auctor in sit ultricies tellus magna, accumsan nostra donec habitant mauris felis ipsum arcu donec posuere neque, orci blandit vulputate auctor vehicula ultrices taciti accumsan libero. \nLigula accumsan porta morbi cras consectetur platea vitae enim, velit hendrerit convallis potenti cras justo proin, in phasellus et iaculis porttitor senectus bibendum. \nConvallis mauris sollicitudin tristique orci vivamus tincidunt auctor placerat in nulla, non ultrices risus consequat felis sagittis libero est venenatis, conubia tempor condimentum proin primis iaculis felis sem curabitur. \nEgestas integer per id convallis aliquam vitae aenean erat porta, class gravida quisque interdum nisi donec primis pulvinar lorem, libero cursus diam nisl bibendum dapibus blandit vitae. \n</p>\n<h2>Accumsan massa aptent</h2><p>\nMorbi vestibulum integer laoreet cursus suscipit massa aenean orci ad quam imperdiet felis, metus sapien et libero leo maecenas ad sodales tristique integer. \n</p>',1),
	(9,'tempor','Tempor ','2014-06-09','<p>\nNam malesuada ullamcorper gravida auctor amet pulvinar donec himenaeos volutpat, non nulla ad tortor eget tempor massa eget. \nDiam orci congue ante metus enim orci rutrum mi, morbi vitae erat cras praesent lobortis diam duis, augue nam vitae elit habitasse commodo etiam. \nAdipiscing cursus ad urna auctor diam odio gravida habitant vel nam, sollicitudin neque fusce at luctus dapibus nostra sem in ultricies, venenatis at nibh himenaeos est vulputate interdum at condimentum. \nCongue facilisis purus cubilia pretium nibh sagittis vel rhoncus gravida aenean, sapien turpis ut ligula praesent quisque habitant eros ornare molestie, rhoncus duis per fames nulla laoreet pellentesque malesuada nec. \n</p>\n<p>\nEu ullamcorper arcu vitae felis platea ligula, ipsum gravida suspendisse praesent molestie ultricies, quam aliquam ad purus habitant. \nNetus sem aliquam consectetur enim aenean phasellus convallis volutpat orci nullam, libero gravida torquent donec per ut sit viverra donec. \nPharetra bibendum hendrerit vitae ipsum ligula accumsan nulla in etiam, tristique potenti lorem lobortis nullam risus euismod dapibus, molestie suscipit hendrerit fringilla phasellus pellentesque aenean tempus. \nDictumst in mollis etiam diam libero nunc condimentum sem velit lobortis, pulvinar purus condimentum purus fusce platea donec cubilia. \nDiam aliquam morbi metus pulvinar, iaculis cursus dapibus, nisi augue fringilla. \n</p>\n<p>\nCursus praesent tristique cubilia blandit dictumst aliquam litora non aenean, senectus molestie lacus mauris luctus fringilla per in nibh, magna ac pretium rutrum lacinia vulputate ornare magna. \nVehicula scelerisque diam auctor tristique sit sollicitudin nostra congue hendrerit feugiat potenti, duis fames potenti dictumst fames quam et turpis ligula habitant. \nMagna sapien sit pretium dictumst eget semper elit aenean id, metus praesent nunc odio vehicula nam gravida placerat turpis nec, porta quisque felis phasellus imperdiet tincidunt justo diam. \nAd nisl justo aenean neque malesuada, nostra eleifend himenaeos torquent sed sagittis, semper ad magna viverra. \n</p>\n<h1>Senectus aliquam</h1><p>\nMagna aliquet venenatis imperdiet potenti curabitur sit egestas inceptos, massa tristique fringilla sagittis mattis velit donec congue, ante cursus duis adipiscing cubilia dictum tempor. \nQuisque ligula dui sed etiam hendrerit vehicula ornare pretium diam non, id dictum phasellus molestie ligula pellentesque pharetra luctus est dapibus, curabitur fringilla cubilia lectus fusce rutrum donec quis conubia. \nVitae suscipit imperdiet donec integer enim habitant aptent curae cubilia enim, adipiscing potenti eleifend vivamus augue semper pretium ipsum dolor, lacinia curabitur etiam ante scelerisque suspendisse dolor non ultricies. \nVenenatis diam sagittis mi aliquam in dui id sollicitudin id venenatis, ullamcorper magna dictumst gravida varius senectus vestibulum donec mi curabitur, accumsan donec nullam orci neque arcu magna purus phasellus. \n</p>\n<p>\nOdio nisi conubia interdum venenatis, cras elit luctus. \n</p>',1),
	(10,'suspendisse','Suspendisse ','2018-02-08','<p>\nTortor ullamcorper pulvinar massa dictumst rhoncus eget, pulvinar massa elit augue tristique, sodales nibh praesent est lobortis. \nAptent inceptos libero conubia donec dolor consequat habitant egestas, potenti vehicula donec aptent turpis amet aptent class, vulputate litora orci justo ut luctus magna. \nAliquet congue et tempor magna porttitor curae a nostra quisque, ligula morbi fames maecenas nisi cras posuere placerat, cubilia primis semper adipiscing velit pretium iaculis netus. \nMolestie aenean consequat litora tellus eros vestibulum volutpat orci magna, senectus rutrum ultrices litora ut nullam etiam dapibus, cubilia augue vulputate ornare elementum hendrerit odio pulvinar. \n</p>\n<p>\nPorttitor amet purus ipsum lacinia lectus metus ultricies fusce vivamus sodales gravida, cubilia eget at litora sodales commodo dapibus erat aliquam. \nImperdiet aliquam et ut arcu ullamcorper egestas nulla taciti, dictumst amet phasellus sollicitudin felis aliquam massa, magna semper maecenas fames pretium malesuada sit. \nSuscipit himenaeos molestie lectus pharetra imperdiet ultrices class risus, nulla non consectetur quis inceptos nibh integer, molestie nisl lorem tellus praesent amet sollicitudin. \nPlatea dictumst nisi aliquam luctus ipsum nam laoreet quam, sociosqu ut sociosqu urna elit neque tristique, hendrerit sagittis eleifend semper mollis dictum placerat. \n</p>\n<p>\nConsectetur hendrerit faucibus inceptos nec, nisl bibendum cubilia, lacinia venenatis praesent. \nFeugiat iaculis molestie eleifend inceptos, felis sapien auctor. \n</p>',1);

/*!40000 ALTER TABLE `tbl_blog` ENABLE KEYS */;
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
	(409,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(410,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(411,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(412,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(413,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(414,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(415,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(416,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(417,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(418,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(419,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(420,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(421,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(422,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(423,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(424,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(425,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(426,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(427,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(428,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(429,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(430,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(431,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(432,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(433,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(434,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(435,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(436,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(437,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(438,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(439,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(440,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(441,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(442,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(443,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(444,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(445,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(446,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(447,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(448,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(449,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(450,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(451,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(452,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(453,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(454,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(455,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(456,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(457,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(458,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(459,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(460,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(461,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(462,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(463,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(464,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(465,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(466,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(467,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(468,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(469,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(470,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(471,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(472,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(473,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(474,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(475,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(476,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(477,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(478,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(479,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(480,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(481,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(482,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(483,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(484,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(485,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(486,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(487,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(488,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(489,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(490,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(491,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(492,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(493,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(494,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(495,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(496,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(497,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(498,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(499,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(500,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(501,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(502,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(503,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(504,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(505,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(506,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(507,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(508,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(509,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(510,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(511,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(512,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(513,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(514,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(515,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(516,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(517,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(518,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(519,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(520,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(521,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(522,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(523,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(524,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(525,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(526,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(527,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(528,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(529,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(530,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(531,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(532,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(533,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(534,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(535,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(536,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(537,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(538,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(539,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(540,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(541,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(542,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(543,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(544,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(545,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(546,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(547,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(548,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(549,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(550,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(551,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(552,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(553,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(554,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(555,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(556,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(557,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(558,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(559,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(560,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(561,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(562,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(563,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(564,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(565,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(566,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(567,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(568,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(569,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(570,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(571,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(572,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(573,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(574,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(575,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(576,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(577,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(578,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(579,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(580,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(581,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(582,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(583,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(584,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(585,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(586,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(587,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(588,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(589,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(590,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(591,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(592,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(593,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(594,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(595,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(596,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(597,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(598,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(599,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(600,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(601,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(602,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(603,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(604,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(605,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(606,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(607,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(608,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(609,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(610,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(611,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(612,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(613,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(614,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(615,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(616,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(617,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(618,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(619,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(620,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(621,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(622,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(623,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(624,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(625,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(626,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(627,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(628,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(629,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(630,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(631,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(632,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(633,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(634,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(635,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(636,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(637,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(638,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(639,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(640,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(641,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(642,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(643,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(644,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(645,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(646,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(647,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(648,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(649,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(650,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(651,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(652,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(653,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(654,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(655,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(656,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(657,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(658,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(659,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(660,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(661,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(662,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(663,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(664,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(665,'INSERT 64Kc5RAX','UPDATE 08NqQcgi','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0),
	(666,'_INSERT N24ij3TB','_UPDATE NLZC12K6','0000-00-00','00:00:00','0000-00-00 00:00:00','2017-05-29 06:17:14',0);

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
  `tme_last_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_changed` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `tbl_kinderen` WRITE;
/*!40000 ALTER TABLE `tbl_kinderen` DISABLE KEYS */;

INSERT INTO `tbl_kinderen` (`id`, `str_first_name`, `str_middle_name`, `str_last_name`, `id_adressen`, `id_groepen`, `tme_last_changed`, `user_changed`)
VALUES
	(2,'Adam','','Aalts',7,32,'2017-05-29 06:17:18',2),
	(3,'Aafje','','Aarden',4,31,'2017-05-29 06:17:18',4),
	(6,'Albert','','Adriaansen',4,29,'2017-05-29 06:17:18',4),
	(8,'Aaron','van','Alenburg',9,36,'2017-05-29 06:17:18',2),
	(9,'Abbe','van','Amstel',7,31,'2017-05-29 06:17:18',3),
	(10,'Abdul','','Ansems',14,36,'2017-05-29 06:17:18',2),
	(12,'Abel','','Appelman',4,32,'2017-05-29 06:17:18',3),
	(13,'Ada','van','Arkel',13,32,'2017-05-29 06:17:18',3),
	(16,'Adriane','','Arts',13,33,'2017-05-29 06:17:18',3),
	(17,'Alwin','','Aschman',8,29,'2017-05-29 06:17:18',2),
	(18,'Alissa','van','Asten',13,39,'2017-05-29 06:17:18',2),
	(19,'Amir','','Armin',1,36,'2017-05-29 06:17:18',1),
	(21,'Alfred','','Albus',12,30,'2017-05-29 06:17:18',4),
	(23,'Agnes','','Aeije',5,36,'2017-05-29 06:17:18',2),
	(26,'Aida','','Adelaar',11,29,'2017-05-29 06:17:18',4),
	(27,'Andreas','','Asperger',6,32,'2017-05-29 06:17:18',3),
	(28,'Aisley','van','Asissi',7,33,'2017-05-29 06:17:18',3),
	(29,'Aldo','','Akkerman',12,29,'2017-05-29 06:17:18',3),
	(30,'Alexander','','Averdijk',3,32,'2017-05-29 06:17:18',4),
	(31,'Andries','','Andermans',1,36,'2017-05-29 06:17:18',1),
	(32,'Bart','van','Baalen',5,33,'2017-05-29 06:17:18',3),
	(33,'Bas','','Bartels',13,33,'2017-05-29 06:17:18',1),
	(35,'Beau','','Barents',13,36,'2017-05-29 06:17:18',1),
	(36,'Beatrijs','van','Beeck',1,40,'2017-05-29 06:17:18',2),
	(37,'Berend','','Beckham',11,39,'2017-05-29 06:17:18',3),
	(38,'Bert','van','Beieren',13,29,'2017-05-29 06:17:18',4),
	(39,'Bobby','','Bosch',9,29,'2017-05-29 06:17:18',3),
	(40,'Bo','van den','Berg',1,32,'2017-05-29 06:17:18',4),
	(41,'Boy','den','Buytelaar',12,29,'2017-05-29 06:17:18',2),
	(42,'Brian','','Blaak',14,36,'2017-05-29 06:17:18',1),
	(43,'Bonnie','','Bezemer',11,30,'2017-05-29 06:17:18',3),
	(44,'Bram','','Bouhuizen',7,30,'2017-05-29 06:17:18',1),
	(45,'Boyd','de','Bont',4,33,'2017-05-29 06:17:18',2),
	(46,'Bregje','','Brandt',14,31,'2017-05-29 06:17:18',2),
	(48,'Brigitte','de','Bruijn',2,32,'2017-05-29 06:17:18',4),
	(49,'Britt','','Brouwer',4,40,'2017-05-29 06:17:18',1),
	(50,'Bregje','van','Buuren',3,33,'2017-05-29 06:17:18',3),
	(51,'Bruno','','Buijs',7,40,'2017-05-29 06:17:18',1),
	(53,'Busra','','Blonk',14,40,'2017-05-29 06:17:18',1),
	(54,'Boudewijn','','Bolkesteijn',9,31,'2017-05-29 06:17:18',1),
	(55,'Caspar','','Claesner',6,36,'2017-05-29 06:17:18',2),
	(56,'Caoa','','Cammel',13,32,'2017-05-29 06:17:18',4),
	(57,'Callen','','Cordet',1,36,'2017-05-29 06:17:18',1),
	(58,'Cecile','','Coolen',12,36,'2017-05-29 06:17:18',3),
	(59,'Chelso','','Coenen',2,33,'2017-05-29 06:17:18',1),
	(60,'Cedric','van','Clootwijck',9,31,'2017-05-29 06:17:18',3),
	(61,'Christiaan','','Corstiaens',6,33,'2017-05-29 06:17:18',4),
	(62,'Ciska','','Courtier',10,30,'2017-05-29 06:17:18',1),
	(64,'Claire','','Cosman',13,31,'2017-05-29 06:17:18',3),
	(65,'Coen','van','Cant',14,33,'2017-05-29 06:17:18',3),
	(66,'Constantijn','','Cornelissen',6,30,'2017-05-29 06:17:18',1),
	(67,'Daan','','Dekker',10,33,'2017-05-29 06:17:18',4),
	(68,'Dagmar','','Dijkman',12,29,'2017-05-29 06:17:18',1),
	(69,'Dafne','','Dirksen',12,40,'2017-05-29 06:17:18',2),
	(70,'Dago','van','Dokkum',14,33,'2017-05-29 06:17:18',4),
	(71,'Damian','','Dorsman',6,29,'2017-05-29 06:17:18',4),
	(73,'Daniëlle','','Dries',6,31,'2017-05-29 06:17:18',1),
	(74,'Dick','van','Duyvenvoorde',9,30,'2017-05-29 06:17:18',1),
	(75,'Dirk','','Dubois',14,40,'2017-05-29 06:17:18',3),
	(76,'Djara','van','Dillen',14,33,'2017-05-29 06:17:18',2),
	(77,'Dianne','van','Dijk',11,40,'2017-05-29 06:17:18',4),
	(78,'Dinand','','Doornhem',11,31,'2017-05-29 06:17:18',3),
	(80,'Dineke','van','Dommelen',4,29,'2017-05-29 06:17:18',4),
	(81,'Ditmar','','Domela',7,29,'2017-05-29 06:17:18',4),
	(82,'Dolf','van','Dam',10,30,'2017-05-29 06:17:18',1),
	(83,'Dominick','','Dubois',12,29,'2017-05-29 06:17:18',4),
	(84,'Donald','','Duik',1,32,'2017-05-29 06:17:18',2),
	(86,'Driek','','Doesburg',13,36,'2017-05-29 06:17:18',3),
	(87,'Dorien','','Draaisma',6,29,'2017-05-29 06:17:18',3),
	(88,'Driek','','Doesburg',14,32,'2017-05-29 06:17:18',1),
	(89,'Dries','','Dekkers',9,40,'2017-05-29 06:17:18',2),
	(90,'Dunya','','Doorhof',5,40,'2017-05-29 06:17:18',4),
	(92,'Ede','van','Eck',11,40,'2017-05-29 06:17:18',3),
	(93,'Edith','','Eelman',7,31,'2017-05-29 06:17:18',2),
	(94,'Edwin','','Etter',9,33,'2017-05-29 06:17:18',3),
	(95,'Eefke','','Elberts',14,40,'2017-05-29 06:17:18',2),
	(96,'Eelco','','Eisenaar',6,32,'2017-05-29 06:17:18',1),
	(97,'Egbert','van','Emmelen',5,39,'2017-05-29 06:17:18',4),
	(98,'Eline','','Erhout',13,36,'2017-05-29 06:17:18',2),
	(99,'Elisabeth','','Engels',9,40,'2017-05-29 06:17:18',2),
	(103,'Elissa','van','Elzas',5,39,'2017-05-29 06:17:18',1),
	(104,'Els','','Evertsen',9,29,'2017-05-29 06:17:18',4),
	(105,'Eva','van','Evelingen',5,30,'2017-05-29 06:17:18',2),
	(107,'Emanuel','','Estey',13,40,'2017-05-29 06:17:18',1),
	(108,'Emiel','','Eijkelboom',3,36,'2017-05-29 06:17:18',3),
	(110,'Epke','van','Essen',11,36,'2017-05-29 06:17:18',4),
	(111,'Ernst','','Everts',8,39,'2017-05-29 06:17:18',1),
	(112,'Erwin','','Ehre',12,39,'2017-05-29 06:17:18',2),
	(113,'Esmée','van','Egisheim',3,29,'2017-05-29 06:17:18',3),
	(114,'Esmeralda','van','Es',6,32,'2017-05-29 06:17:18',2),
	(115,'Eugenie','van den','Euvel',14,31,'2017-05-29 06:17:18',3),
	(116,'Evy','','Eisenhouwer',12,30,'2017-05-29 06:17:18',1);

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
	(2,2,0,'een_pagina','Een pagina','','test_01.jpg',1,'','',''),
  (3,3,2,'subpagina','Subpagina','<p>Een subpagina</p>','test_02.jpg|test_01.jpg',1,'','',''),
  (5,4,2,'een_pagina','Nog een subpagina','<p>Met plaatje:</p><p><img src=\"_media/pictures/test_01.jpg\" alt=\"test_01\" /></p>','',1,'example','',''),
  (4,5,0,'contact','Contact','<p>Hier een voorbeeld van een eenvoudig <a href=\"mailto:info@flexyadmin.com\">contactformulier</a>.</p>','',1,'forms.contact','',''),
	(6,1,0,'blog','Blog','','',1,'blog','','');

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `tbl_site` WRITE;
/*!40000 ALTER TABLE `tbl_site` DISABLE KEYS */;

INSERT INTO `tbl_site` (`id`, `str_title`, `str_author`, `url_url`, `email_email`, `stx_description`, `stx_keywords`)
VALUES
	(1,'FlexyAdmin','Jan den Besten','http://www.flexyadmin.com/','info@flexyadmin.com','','');

/*!40000 ALTER TABLE `tbl_site` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
