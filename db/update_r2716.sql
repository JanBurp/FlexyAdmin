# Add b_jump_to_today to cfg_table_info
ALTER TABLE `cfg_table_info` ADD `b_jump_to_today` TINYINT(1)  NOT NULL  DEFAULT '0'  AFTER `b_pagination`;

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2716';




