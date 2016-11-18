# Remove int_version, not needed anymore
ALTER TABLE `tbl_site` DROP `int_version`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2745';




