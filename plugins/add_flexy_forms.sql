# This adds Flexy Form tables to add the funcionality that users can change their forms

DROP TABLE IF EXISTS `tbl_forms`;

CREATE TABLE `tbl_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `str_name` varchar(50) NOT NULL,
  `str_title_nl` varchar(100) NOT NULL DEFAULT '',
  `str_title_en` varchar(100) NOT NULL,
  `txt_text_nl` tinytext NOT NULL,
  `txt_text_en` tinytext NOT NULL,
  `txt_error_nl` tinytext NOT NULL,
  `txt_error_en` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `tbl_forms` (`str_name`,`str_title_nl`, `str_title_en`, `txt_text_nl`, `txt_text_en`, `txt_error_nl`, `txt_error_en`)
VALUES
	('contact','Contact','Contact','<p>Bedankt voor uw interesse.</p>','<p>Thanks for you\'re question.</p>','<p>Helaas, om een onduidelijke oorzaak is het niet gelukt uw verzoek te verzenden.<br />Probeer het nogmaals of neem direct per email contact met ons op.</p>','<p>Sorry, an error has occured while sending you\'re mail. Try again or contact us in another way.</p>');


DROP TABLE IF EXISTS `tbl_formfields`;

CREATE TABLE `tbl_formfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(6) NOT NULL,
  `id_form` int(11) NOT NULL,
  `str_type` varchar(20) NOT NULL,
  `str_name` VARCHAR(50)  NOT NULL,
  `str_label_nl` varchar(255) NOT NULL DEFAULT '',
  `str_label_en` varchar(255) NOT NULL,
  `str_options` VARCHAR(255)  NOT NULL DEFAULT '',
  `str_default` VARCHAR(255)  NULL DEFAULT '',
  `str_validation` varchar(100) NOT NULL,
  `str_validation_parameters` varchar(50) NOT NULL,
  `txt_html` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `tbl_formfields` (`order`, `id_form`, `str_type`, `str_name`, `str_label_nl`, `str_label_en`, `str_validation`, `str_validation_parameters`, `txt_html`)
VALUES
	(1,1,'input','str_name', 'Naam','Name','required','',''),
	(2,1,'input','email_email','Email','Email','required|valid_email','',''),
	(3,1,'input','str_telephone', 'Telefoon','Phonenumber','min_length[]','10',''),
	(4,1,'input','str_address', 'Adres','Address','0','',''),
	(5,1,'textarea','txt_question', 'Stel uw vraag','Question','0','',''),
	(6,1,'button.submit','','Verstuur','Send','0','','');


# Add info for the tables
INSERT INTO cfg_table_info (`order`, `table`, `int_max_rows`, `b_grid_add_many`, `str_form_many_type`, `str_form_many_order`, `str_abstract_fields`, `str_options_where`, `str_order_by`, `b_add_empty_choice`, `b_freeze_uris`) VALUES (100, 'tbl_forms', 1, 0, 'dropdown', 'last', 'str_title_nl', '', '', 0, 0);
INSERT INTO cfg_table_info (`order`, `table`, `int_max_rows`, `b_grid_add_many`, `str_form_many_type`, `str_form_many_order`, `str_abstract_fields`, `str_options_where`, `str_order_by`, `b_add_empty_choice`, `b_freeze_uris`) VALUES (101, 'tbl_formfields', 0, 0, 'dropdown', 'last', '', '', '', 0, 0);


# Add info for the fields
INSERT INTO cfg_field_info (`field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('tbl_formfields.id_form', 1, 1, ' ', '', 0, 0, '', '', '');
INSERT INTO cfg_field_info (`field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('tbl_formfields.str_type', 1, 1, ' ', 'input|textarea|hidden|password|checkbox|radio|select|option|html|file|fieldset|button.submit|button.reset|button.cancel', 0, 0, '', 'required', '');
INSERT INTO cfg_field_info (`field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('tbl_formfields.str_validation', 1, 1, ' ', 'required|numeric|valid_email|prep_url|min_length[]|max_length[]', 1, 0, '', '', '');
INSERT INTO cfg_field_info (`field_field`, `b_show_in_grid`, `b_show_in_form`, `str_show_in_form_where`, `str_options`, `b_multi_options`, `b_ordered_options`, `str_options_where`, `str_validation_rules`, `str_validation_parameters`) VALUES ('tbl_forms.str_name', 1, 1, ' ', 'contact', 0, 0, '', '', '');

# Add UI info for tbl_forms and tbl_formfields
INSERT INTO `cfg_ui` (`path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('', 'tbl_forms', '', 'Formulieren', '', '', '');
INSERT INTO `cfg_ui` (`path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('', 'tbl_formfields', '', 'Formuliervelden', '', '', '');
INSERT INTO `cfg_ui` (`path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('', '', 'tbl_formfields.id_form', 'Formulier', 'Form', '', '');
INSERT INTO `cfg_ui` (`path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES ('', '', 'tbl_formfields.str_validation_parameters', 'Val. Parameters', 'Val. Parameters', '', '');

