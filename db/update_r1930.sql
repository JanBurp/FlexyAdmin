# Added option in cfg_table_info to hide a table in the menu

ALTER TABLE `cfg_table_info` ADD `b_visible` TINYINT(1)  NOT NULL  DEFAULT '1'  AFTER `table`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '1930';