# This adds a language table
# 
# Use this if you want to use a language table instead of normal language files
# If the language key is not found or is empty, the key will be searched in the language files
#
# Set $config['language_table']  = "cfg_lang"; in site/config/config.php

DROP TABLE IF EXISTS cfg_lang;

CREATE TABLE `cfg_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL DEFAULT '',
  `lang_nl` varchar(100) NOT NULL DEFAULT '',
  `lang_en` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

