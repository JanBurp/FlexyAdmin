# Maak tbl_links.url_url ook geschikt om emails in te bewaren
INSERT INTO `cfg_field_info` (`field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_fieldset`, `b_editable_in_grid`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`)
VALUES ('tbl_links.url_url', 1, 1, ' ', '', 0, '', 0, 0, NULL, 'prep_url_mail', '');

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '2753';




