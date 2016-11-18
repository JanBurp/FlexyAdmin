# Update standard field info, str_module
# UPDATE_IF:Replace form modules with new module Forms (ie contact_form)?

UPDATE `cfg_field_info` SET `str_options` = '|forms.contact|links|example' WHERE `field_field` = 'tbl_menu.str_module';
UPDATE `tbl_menu` SET `str_module` = 'forms.contact' WHERE `str_module` = 'contact_form';

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2015';