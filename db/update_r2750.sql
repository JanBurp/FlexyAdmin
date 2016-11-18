# Voeg b_serve_restricted toe aan cfg_media_info (en maakt van b_user_restricted een kleiner boolean veld)
ALTER TABLE `cfg_media_info` ADD `b_serve_restricted` TINYINT(1)  NOT NULL  DEFAULT '0'  AFTER `b_user_restricted`;
ALTER TABLE `cfg_media_info` CHANGE `b_user_restricted` `b_user_restricted` TINYINT(1)  NOT NULL  DEFAULT '0';

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2750';




