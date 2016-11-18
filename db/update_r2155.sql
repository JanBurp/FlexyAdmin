# Encrypten van bestanden bij uploaden
ALTER TABLE `cfg_media_info` ADD `b_encrypt_name` TINYINT(1)  NOT NULL  DEFAULT '0'  AFTER `str_types`;
