# Maak veld aan in res_media_files: een flag of het bestand idd bestaat
ALTER TABLE `res_media_files` ADD `b_exists` TINYINT(1)  NOT NULL  DEFAULT '1'  AFTER `id`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2277';