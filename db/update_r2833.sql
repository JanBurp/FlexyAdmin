# Voeg str_name toe aan flexyfields (tbl_formfields)
# UPDATE_IF:Do you use FlexyForms? (add str_name to tbl_formfields)
ALTER TABLE `tbl_formfields` ADD `str_name` VARCHAR(50)  NOT NULL  AFTER `str_type`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2833';




