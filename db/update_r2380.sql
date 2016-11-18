# Update cfg_field_info: edit in grid
ALTER TABLE `cfg_field_info` ADD `b_editable_in_grid` TINYINT(1)  NOT NULL  DEFAULT '0'  AFTER `str_fieldset`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2380';

