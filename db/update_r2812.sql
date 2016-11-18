# Voeg b_form_add_many toe aan cfg_table_info
ALTER TABLE `cfg_table_info` ADD `b_form_add_many` TINYINT(1)  NOT NULL  DEFAULT '1'  AFTER `b_grid_add_many`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2812';




