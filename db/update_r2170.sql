# Maak admin menu items zichtbaar voor minimale user_groups (default is met minimaal de rechten van User (3))
ALTER TABLE `cfg_admin_menu` ADD `id_user_group` INT(11)  NOT NULL  DEFAULT '3'  AFTER `b_visible`;

# Aantal items alleen voor super_admin (result & config tables & seperators)
UPDATE `cfg_admin_menu` SET `id_user_group` = '1' WHERE `id` = '7';
UPDATE `cfg_admin_menu` SET `id_user_group` = '1' WHERE `id` = '13';
UPDATE `cfg_admin_menu` SET `id_user_group` = '1' WHERE `id` = '16';
UPDATE `cfg_admin_menu` SET `id_user_group` = '1' WHERE `id` = '17';