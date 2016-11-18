# Update cfg_field_info: validation for google analytics
UPDATE `cfg_field_info` SET `str_validation_rules` = 'valid_google_analytics' WHERE `field_field` = 'tbl_site.str_google_analytics';

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2354';

