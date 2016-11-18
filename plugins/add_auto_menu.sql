# Voeg cfg_auto_menu & res_menu_result toe voor samengestelde menus
# Zie: userguide/FlexyAdmin/index.html#section_samengesteld_menu

DROP TABLE IF EXISTS cfg_auto_menu;

CREATE TABLE `cfg_auto_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(50) NOT NULL DEFAULT '',
  `order` tinyint(6) NOT NULL DEFAULT '0',
  `self_parent` int(11) NOT NULL DEFAULT '0',
  `str_parent_uri` varchar(100) NOT NULL,
  `b_active` TINYINT(1)  NOT NULL DEFAULT '1',
  `str_description` varchar(50) NOT NULL DEFAULT '',
  `str_type` varchar(50) NOT NULL DEFAULT '',
  `b_keep_parent_modules` tinyint(1) NOT NULL DEFAULT '0',
  `table` varchar(50) NOT NULL DEFAULT '',
  `field_group_by` varchar(50) NOT NULL DEFAULT '',
  `str_parent_where` varchar(100) NOT NULL,
  `str_where` varchar(100) NOT NULL DEFAULT '',
  `int_limit` mediumint(3) NOT NULL DEFAULT '0',
  `str_parameters` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

INSERT INTO cfg_auto_menu (`id`, `uri`, `order`, `self_parent`, `str_parent_uri`, `str_description`, `str_type`,`b_keep_parent_modules`, `table`, `field_group_by`, `str_parent_where`, `str_where`, `int_limit`, `str_parameters`) VALUES (1, '', 0, 0, '', 'Standard Menu', 'from menu table', 0, 'tbl_menu', '', '', '', 0, '');


DROP TABLE IF EXISTS `res_menu_result`;
CREATE TABLE `res_menu_result` (
  `id` int(11) NOT NULL,
  `uri` varchar(50) NOT NULL default '',
  `order` smallint(6) NOT NULL default '0',
  `self_parent` int(11) NOT NULL default '0',
  `str_title` varchar(100) NOT NULL default '',
  `txt_text` text NOT NULL default '',
	`str_module` varchar(30) NOT NULL default '',
	`str_table` varchar(50) NOT NULL default '',
	`str_uri` varchar(50) NOT NULL default '',
	`int_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
