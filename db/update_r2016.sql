# Update ook deze als tbl_forms bestaat
# UPDATE_IF:Do you use FlexyForms and wan't to use the module Forms?
UPDATE `tbl_forms` SET `str_module` = 'contact' WHERE `str_module` = 'flexy_form';
ALTER TABLE `tbl_forms` MODIFY COLUMN `str_module` VARCHAR(50) CHARACTER SET latin1 NOT NULL AFTER `id`;
ALTER TABLE `tbl_forms` CHANGE `str_module` `str_name` VARCHAR(50)  CHARACTER SET latin1  NOT NULL  DEFAULT '';
