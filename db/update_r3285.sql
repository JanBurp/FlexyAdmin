# Verwijder `b_in_database` => alle bestanden komen standaard in de database
ALTER TABLE `cfg_media_info` DROP `b_in_database`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '3285';




