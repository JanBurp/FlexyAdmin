# CodeIgniter update to 3.1.2
ALTER TABLE `cfg_sessions` CHANGE `id` `id` varchar(128) NOT NULL;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '3845';
