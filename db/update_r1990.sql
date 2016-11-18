# Update standard fields in cfg_ui for Help pages

UPDATE `cfg_ui` SET `txt_help_nl` = "<p>Vul hier de titel -de naam- in van je site.</p><p>Dit wordt de aanklikbare kop bij zoekresultaten in Google. Ook komt de titel boven alle webpagina's van je site te staan.</p>" WHERE `field_field` = 'tbl_site.str_title';
UPDATE `cfg_ui` SET `txt_help_nl` = '<p>Vul hier zoektermen in, gescheiden door komma\'s.</p><p>Zoektermen worden door zoekmachines gebruikt om je site beter vindbaar te maken. Lees <a href="admin/help/tips_voor_een_goede_site" target="_self">hier meer over SEO</a>.<br /><br />Afhankelijk van de opzet van je site is het mogelijk om per pagina extra zoektermen toe te voegen.</p>' WHERE `field_field` = 'tbl_site.stx_keywords';

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '1990';