# Zichtbaarheid van Media
ALTER TABLE `cfg_media_info` ADD `b_visible` TINYINT(1)  NOT NULL  DEFAULT '1'  AFTER `path`;