# Update cfg_ui voor Helpteksten
UPDATE `cfg_ui` SET `txt_help_nl` = '<p>Upload of verwijder hier de foto\'s van je site.</p>' WHERE `id` = '4';
UPDATE `cfg_ui` SET `txt_help_nl` = '<p>Voeg hier bestanden toe die je in je tekst als download-link kunt toevoegen.</p>' WHERE `id` = '5';
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES (NULL, '', '','tbl_menu.str_title','','','<p>Titel van de pagina</p>','');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES (NULL, '', '','tbl_menu.self_parent','','','<p>Hiermee kun je de pagina eventueel in een submenu zetten.</p>','');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES (NULL, '', '','tbl_menu.txt_text','','','<p>Tekst van de pagina. Eventueel ook foto\'s, YouTube filmpjes etc.</p>','');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES (NULL, '', '','tbl_menu.str_module','','','<p>Kies hier eventueel een module.</p>\n<p>Modules voegen extra inhoud toe aan je pagina. Bijvoorbeeld een contact formulier, of een overzicht van alle links of een speciaal voor jouw site geschreven module.</p>','');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES (NULL, '', '','tbl_menu.stx_description','','','<p>Je kunt een afwijkende omschrijving van je pagina maken voor zoekmachines als Google. Als je hier niets invult wordt de standaard omschrijving gebruikt die je bij <strong>Site</strong> kunt invoeren.</p>','');
INSERT INTO `cfg_ui` (`id`, `path`, `table`, `field_field`, `str_title_nl`, `str_title_en`, `txt_help_nl`, `txt_help_en`) VALUES (NULL, '', '','tbl_menu.str_keywords','','','<p>Hier kun je extra keywords toevoegen voor zoekmachines. Ze worden toegevoegd bij de standaard keywords die je bij <strong>Site</strong> hebt ingevoerd.</p>','');

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '1794';

