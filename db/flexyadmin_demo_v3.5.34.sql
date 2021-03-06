# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.7.25)
# Database: flexyadmin_demo
# Generation Time: 2019-06-14 15:12:19 +0000
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
	(6,'login_forgot_password','Nieuw wachtwoord voor {site_title}','<h1>Nieuw wachtwoord aanvragen voor {identity}</h1>\n<p>&nbsp;</p>\n<p>Klik hier om <a href=\"{site_url}{forgotten_password_uri}?code={forgotten_password_code}\">wachtwoord te resetten</a>.</p>\n<p>Je krijgt na het klikken op de link een nieuwe email met daarin je nieuwe wachtwoord.</p>','New password for {site_title}','<h1>New password request for {identity}</h1>\n<p>Click on <a href=\"{site_url}{forgotten_password_uri}?code={forgotten_password_code}\">to restet your password</a>.</p>'),
	(7,'login_new_password','Nieuwe inloggegevens voor {site_title}','<h1>Je nieuwe inloggevens voor {site_title}:</h1>\n<p>Gebruiker: {identity}<br />Wachtwoord: {password}</p>\n<p>Let op dat je bij het wachtwoord alle tekens meeneemt, ook eventuele punten aan het einde.</p>','New login for {site_title}','<h3>You got an account.</h3>\n<p>Login with these settings:</p>\n<p>Username : {identity}<br />Password : {password}</p>'),
	(8,'login_new_account','Welkom en inloggegevens voor {site_title}','<h1>Welkom bij {site_title}</h1>\n<p>Hieronder staan je inloggegevens.</p>\n<p>Gebruiker: {identity}<br />Wachtwoord: {password}</p>\n<p>Let op dat je bij het wachtwoord alle tekens meeneemt, ook eventuele punten aan het einde.</p>','New login for {site_title}','<h1>Welcome at {site_title}</h1>\n<p>Login with these settings:</p>\n<p>Username : {identity}<br />Password : {password}</p>');

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
  `str_last_version` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_users` WRITE;
/*!40000 ALTER TABLE `cfg_users` DISABLE KEYS */;

INSERT INTO `cfg_users` (`id`, `str_username`, `gpw_password`, `email_email`, `ip_address`, `salt`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `b_active`, `str_language`, `str_filemanager_view`, `str_last_version`)
VALUES
	(1,'admin','$2y$08$rKzbNchBsinmWva3UnPsZOMKbaTFdpOgjxNv.PYKn1BjWgrOznhZa','info@flexyadmin.com','','','','',0,'',0,1500548010,1,'nl','small',''),
	(2,'user','$2y$08$.18vvqlz24ldRDJ4AcnPR.AVYFBGOv9YbnvEw/dLRfn88KBd2E/iG','jan@burp.nl','','','','0',0,'',0,1500545043,1,'nl','small',''),
	(3,'test','$2y$08$OfDssFUdFL3mqwzlg4mFJeDrmwCRrzc.9sEQj0uVbM7MRxTpX/pZC','test@flexyadmin.com','',NULL,NULL,NULL,0,NULL,0,1500545041,1,'nl','small','');

/*!40000 ALTER TABLE `cfg_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cfg_version
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cfg_version`;

CREATE TABLE `cfg_version` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `str_version` varchar(20) NOT NULL DEFAULT '3.5.0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `cfg_version` WRITE;
/*!40000 ALTER TABLE `cfg_version` DISABLE KEYS */;

INSERT INTO `cfg_version` (`id`, `str_version`)
VALUES
	(1,'3.5.34');

/*!40000 ALTER TABLE `cfg_version` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table log_activity
# ------------------------------------------------------------

DROP TABLE IF EXISTS `log_activity`;

CREATE TABLE `log_activity` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL,
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
  `ip_address` varchar(15) NOT NULL DEFAULT '',
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
	(4,1,'test_01.jpg','pictures','jpg','kU2XGziV','2017-01-13',60,300,400),
	(5,1,'test_02.jpg','pictures','jpg','ut5q3G2Z','2017-01-13',33,300,225),
	(6,1,'test_03.jpg','pictures','jpg','cfCqUwoD','2017-01-14',34,300,225);

/*!40000 ALTER TABLE `res_assets` ENABLE KEYS */;
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
	(4,'fringilla','Fringilla ','2014-03-16','<p>\nMolestie augue turpis lobortis torquent donec turpis faucibus, hendrerit vulputate euismod aliquet lacus quam volutpat etiam, nunc sollicitudin leo risus semper gravida. \nId massa aliquam fermentum nibh congue ut ac aliquam facilisis placerat nunc maecenas, aenean vestibulum urna rhoncus pulvinar ultrices augue posuere habitasse aliquam urna. \nEget curabitur tortor orci laoreet ac habitasse hac tristique maecenas, phasellus ut metus hendrerit etiam nunc rhoncus tellus dui ultricies, feugiat euismod iaculis netus felis class molestie vel. \nPraesent quis malesuada suspendisse quam urna aliquam morbi fringilla, malesuada semper lobortis curae torquent mollis lectus, suscipit at class taciti etiam elementum nisl. \n</p>\n<h1>Eros molestie</h1><p>\nUrna rutrum duis aenean egestas blandit arcu, blandit per diam aptent at, suspendisse blandit consectetur sed tristique. \nUt libero imperdiet suspendisse metus ultrices nostra sociosqu, donec pharetra eros ipsum per semper, porta metus fringilla eros consectetur justo. \nVehicula orci porta lorem tellus eget aptent dictumst viverra aliquam, habitasse imperdiet consequat nulla ullamcorper ante euismod lectus, himenaeos massa auctor per facilisis posuere primis eros. \nVel ipsum vehicula lectus venenatis pharetra ligula class aenean himenaeos, est ligula tempus nam dui metus nostra dictum eros mauris, elit aenean dapibus tincidunt dictum nullam duis leo. \n</p>\n<h2>Egestas arcu</h2><p>\nDictumst purus mi enim vestibulum ipsum donec volutpat sit congue, sodales torquent lacinia cras inceptos dapibus curabitur varius turpis, ullamcorper et porttitor vitae nunc mollis nec conubia. \nHimenaeos curabitur nostra integer nisi orci dui sapien lectus, eleifend at phasellus himenaeos tempor nibh placerat, praesent scelerisque nunc vitae etiam at auctor. \nAliquam senectus cras nec sapien eros feugiat urna, commodo nam dapibus felis pulvinar molestie et, gravida vulputate ut amet gravida fusce. \nRutrum dapibus ultrices netus platea lacus potenti vitae platea, risus ullamcorper donec class quisque morbi. \n</p>',1),
	(6,'euismod_augue_placerat_sodales','Euismod augue placerat sodales ','2014-10-12','<h2>Fermentum purus sagittis</h2><p>\nPraesent magna nullam aenean pharetra semper enim sit volutpat ullamcorper per, mauris platea elementum eros phasellus himenaeos augue duis at ipsum lacus, imperdiet aliquam conubia torquent lacus turpis orci vulputate lacinia. \nTellus phasellus senectus molestie est torquent mattis fusce, vehicula suscipit phasellus ac venenatis curae fermentum, aptent mi vehicula faucibus nunc nullam. \nNisl elementum amet blandit eros sagittis justo dolor, hac sapien molestie integer quisque dictumst, vivamus sodales suscipit magna posuere nec. \nVel aliquet potenti faucibus donec lobortis non donec accumsan et purus, primis fusce mollis sed massa purus lectus tincidunt. \n</p>\n<h2>Erat fusce semper</h2><p>\nMi primis nullam phasellus etiam justo inceptos cras netus aliquam, donec senectus faucibus mi curabitur nostra ut libero, sed habitasse leo blandit consequat mauris potenti nisl. \nPlatea donec dapibus maecenas orci ad proin pharetra potenti etiam fusce leo ornare, primis metus in molestie convallis nulla facilisis adipiscing lacus faucibus libero ipsum, donec euismod elementum ullamcorper senectus ullamcorper aliquam enim fringilla aliquam lorem. \nDapibus posuere vel habitant malesuada class tempus senectus volutpat vehicula etiam libero, nec hendrerit dui maecenas ipsum nunc a fringilla laoreet. \nProin sed laoreet consectetur morbi porta habitasse tortor, ut faucibus quis nulla volutpat fusce integer, viverra orci lobortis ultrices eleifend nisi. \n</p>\n<h2>Justo tellus</h2><p>\nEuismod nulla quam lorem iaculis conubia semper viverra molestie maecenas tortor ipsum, justo mattis ut fringilla ut nam lobortis cras litora netus. \nPorta erat non semper enim dui vitae tortor leo cras euismod lorem nibh, imperdiet ornare per adipiscing aptent fusce massa fusce nisl dictumst. \nArcu magna eu fermentum proin accumsan ipsum tortor cras malesuada massa praesent, in donec etiam platea a congue vestibulum quisque morbi sit, pharetra tellus senectus habitant aliquet lorem ultricies ante mattis rutrum. \nSollicitudin ac pellentesque tellus enim class conubia consequat ante placerat congue, pellentesque malesuada lectus justo aenean netus imperdiet mattis dui. \n</p>\n<h2>Non</h2><p>\nCommodo condimentum aptent primis vehicula adipiscing himenaeos torquent scelerisque rutrum ad ullamcorper nam placerat tellus nec, ut porttitor sociosqu morbi duis iaculis proin iaculis quam tristique quisque at sociosqu. \nScelerisque fermentum pretium sagittis purus pellentesque in arcu, faucibus volutpat fames tincidunt donec mollis, turpis habitasse himenaeos malesuada consequat lobortis. \nElementum hendrerit egestas aliquet pellentesque interdum varius imperdiet sapien adipiscing vitae hac sollicitudin pulvinar, varius viverra egestas vel torquent suspendisse lacus ullamcorper etiam torquent himenaeos. \nVitae bibendum torquent habitasse duis primis erat tempor, volutpat donec elit tempus himenaeos aptent etiam, metus fermentum massa proin non et. \n</p>\n<h2>Vitae</h2><p>\nSit feugiat dui arcu sollicitudin nulla eros blandit, curabitur fames posuere porta vitae blandit non, mauris in purus per lectus urna. \nTincidunt eu id dictumst leo platea tortor donec, semper vel erat vel ac est. \n</p>',1),
	(9,'tempor','Tempor ','2014-06-09','<p>\nNam malesuada ullamcorper gravida auctor amet pulvinar donec himenaeos volutpat, non nulla ad tortor eget tempor massa eget. \nDiam orci congue ante metus enim orci rutrum mi, morbi vitae erat cras praesent lobortis diam duis, augue nam vitae elit habitasse commodo etiam. \nAdipiscing cursus ad urna auctor diam odio gravida habitant vel nam, sollicitudin neque fusce at luctus dapibus nostra sem in ultricies, venenatis at nibh himenaeos est vulputate interdum at condimentum. \nCongue facilisis purus cubilia pretium nibh sagittis vel rhoncus gravida aenean, sapien turpis ut ligula praesent quisque habitant eros ornare molestie, rhoncus duis per fames nulla laoreet pellentesque malesuada nec. \n</p>\n<p>\nEu ullamcorper arcu vitae felis platea ligula, ipsum gravida suspendisse praesent molestie ultricies, quam aliquam ad purus habitant. \nNetus sem aliquam consectetur enim aenean phasellus convallis volutpat orci nullam, libero gravida torquent donec per ut sit viverra donec. \nPharetra bibendum hendrerit vitae ipsum ligula accumsan nulla in etiam, tristique potenti lorem lobortis nullam risus euismod dapibus, molestie suscipit hendrerit fringilla phasellus pellentesque aenean tempus. \nDictumst in mollis etiam diam libero nunc condimentum sem velit lobortis, pulvinar purus condimentum purus fusce platea donec cubilia. \nDiam aliquam morbi metus pulvinar, iaculis cursus dapibus, nisi augue fringilla. \n</p>\n<p>\nCursus praesent tristique cubilia blandit dictumst aliquam litora non aenean, senectus molestie lacus mauris luctus fringilla per in nibh, magna ac pretium rutrum lacinia vulputate ornare magna. \nVehicula scelerisque diam auctor tristique sit sollicitudin nostra congue hendrerit feugiat potenti, duis fames potenti dictumst fames quam et turpis ligula habitant. \nMagna sapien sit pretium dictumst eget semper elit aenean id, metus praesent nunc odio vehicula nam gravida placerat turpis nec, porta quisque felis phasellus imperdiet tincidunt justo diam. \nAd nisl justo aenean neque malesuada, nostra eleifend himenaeos torquent sed sagittis, semper ad magna viverra. \n</p>\n<h1>Senectus aliquam</h1><p>\nMagna aliquet venenatis imperdiet potenti curabitur sit egestas inceptos, massa tristique fringilla sagittis mattis velit donec congue, ante cursus duis adipiscing cubilia dictum tempor. \nQuisque ligula dui sed etiam hendrerit vehicula ornare pretium diam non, id dictum phasellus molestie ligula pellentesque pharetra luctus est dapibus, curabitur fringilla cubilia lectus fusce rutrum donec quis conubia. \nVitae suscipit imperdiet donec integer enim habitant aptent curae cubilia enim, adipiscing potenti eleifend vivamus augue semper pretium ipsum dolor, lacinia curabitur etiam ante scelerisque suspendisse dolor non ultricies. \nVenenatis diam sagittis mi aliquam in dui id sollicitudin id venenatis, ullamcorper magna dictumst gravida varius senectus vestibulum donec mi curabitur, accumsan donec nullam orci neque arcu magna purus phasellus. \n</p>\n<p>\nOdio nisi conubia interdum venenatis, cras elit luctus. \n</p>',1),
	(10,'suspendisse','Suspendisse ','2018-02-08','<p>\nTortor ullamcorper pulvinar massa dictumst rhoncus eget, pulvinar massa elit augue tristique, sodales nibh praesent est lobortis. \nAptent inceptos libero conubia donec dolor consequat habitant egestas, potenti vehicula donec aptent turpis amet aptent class, vulputate litora orci justo ut luctus magna. \nAliquet congue et tempor magna porttitor curae a nostra quisque, ligula morbi fames maecenas nisi cras posuere placerat, cubilia primis semper adipiscing velit pretium iaculis netus. \nMolestie aenean consequat litora tellus eros vestibulum volutpat orci magna, senectus rutrum ultrices litora ut nullam etiam dapibus, cubilia augue vulputate ornare elementum hendrerit odio pulvinar. \n</p>\n<p>\nPorttitor amet purus ipsum lacinia lectus metus ultricies fusce vivamus sodales gravida, cubilia eget at litora sodales commodo dapibus erat aliquam. \nImperdiet aliquam et ut arcu ullamcorper egestas nulla taciti, dictumst amet phasellus sollicitudin felis aliquam massa, magna semper maecenas fames pretium malesuada sit. \nSuscipit himenaeos molestie lectus pharetra imperdiet ultrices class risus, nulla non consectetur quis inceptos nibh integer, molestie nisl lorem tellus praesent amet sollicitudin. \nPlatea dictumst nisi aliquam luctus ipsum nam laoreet quam, sociosqu ut sociosqu urna elit neque tristique, hendrerit sagittis eleifend semper mollis dictum placerat. \n</p>\n<p>\nConsectetur hendrerit faucibus inceptos nec, nisl bibendum cubilia, lacinia venenatis praesent. \nFeugiat iaculis molestie eleifend inceptos, felis sapien auctor. \n</p>',1);

/*!40000 ALTER TABLE `tbl_blog` ENABLE KEYS */;
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
	(2,'FlexyAdmin','http://www.flexyadmin.com');

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
	(1,0,0,'gelukt','Gelukt!','<p>Als je dit ziet is het je gelukt om FlexyAdmin te installeren en werkend te krijgen. <br />Je hebt nu een standaard-installatie van een zeer eenvoudige basis-site.</p>','',1,'','',''),
	(2,2,0,'een_pagina','Een pagina','','',1,'','',''),
	(3,3,2,'subpagina','Subpagina','<p>Een subpagina</p>','test_02.jpg',1,'','',''),
	(5,4,2,'nog_een_subpagina','Nog een subpagina','<p>Met plaatje:</p><p></p>','',1,'example','',''),
	(4,5,0,'contact','Contact','<p>Hier een voorbeeld van een eenvoudig contactformulier.</p>','',1,'forms.contact','',''),
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
