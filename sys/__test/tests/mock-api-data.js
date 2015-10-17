/**
 * FlexyAdmin (c) Jan den Besten
 * www.flexyadmin.com
 * 
 * 
 * Just a basic service for mock data used for mocking the api service
 * 
 * 
 * @author: Jan den Besten
 * @copyright: Jan den Besten
 * @license: n/a
 * 
 * $Author$
 * $Date$
 * $Revision$
 */

flexyAdmin.factory( 'flexyApiMock', ['flexySettingsService',function(flexySettingsService) {
  'use strict';
  
  var api = flexySettingsService.item('api_base_url');
  
  // created with /plugin/export/full/json -> see data/full_dump.json
  var database = {
    'cfg_admin_menu':[{"id":"1","order":"0","str_ui_name":"Home","b_visible":"1","id_user_group":"3","str_type":"api","api":"API_home","path":"","table":"","str_table_where":""},{"id":"2","order":"1","str_ui_name":"Logout","b_visible":"1","id_user_group":"3","str_type":"api","api":"API_logout","path":"","table":"","str_table_where":""},{"id":"3","order":"3","str_ui_name":"Help","b_visible":"1","id_user_group":"3","str_type":"api","api":"API_help","path":"","table":"","str_table_where":""},{"id":"8","order":"4","str_ui_name":"","b_visible":"1","id_user_group":"3","str_type":"seperator","api":"","path":"","table":"","str_table_where":""},{"id":"4","order":"5","str_ui_name":"# all normal tables (if user has rights)","b_visible":"1","id_user_group":"3","str_type":"all_tbl_tables","api":"","path":"","table":"","str_table_where":""},{"id":"5","order":"6","str_ui_name":"# all media (if user has rights)","b_visible":"1","id_user_group":"3","str_type":"all_media","api":"","path":"","table":"","str_table_where":""},{"id":"9","order":"7","str_ui_name":"","b_visible":"1","id_user_group":"3","str_type":"seperator","api":"","path":"","table":"","str_table_where":""},{"id":"11","order":"8","str_ui_name":"_stats_menu","b_visible":"1","id_user_group":"3","str_type":"api","api":"API_plugin_stats","path":"","table":"","str_table_where":""},{"id":"12","order":"9","str_ui_name":"","b_visible":"1","id_user_group":"3","str_type":"seperator","api":"","path":"","table":"","str_table_where":""},{"id":"6","order":"10","str_ui_name":"# all tools (if user has rights)","b_visible":"1","id_user_group":"3","str_type":"tools","api":"","path":"","table":"","str_table_where":""},{"id":"10","order":"11","str_ui_name":"","b_visible":"1","id_user_group":"3","str_type":"seperator","api":"","path":"","table":"","str_table_where":""},{"id":"16","order":"12","str_ui_name":"# all result tables (if there are any)","b_visible":"1","id_user_group":"1","str_type":"all_res_tables","api":"","path":"","table":"","str_table_where":""},{"id":"17","order":"13","str_ui_name":"","b_visible":"1","id_user_group":"1","str_type":"seperator","api":"","path":"","table":"","str_table_where":""},{"id":"7","order":"14","str_ui_name":"# all config tables (if user has rights)","b_visible":"1","id_user_group":"1","str_type":"all_cfg_tables","api":"","path":"","table":"","str_table_where":""}],
    'cfg_configurations':[{"id":"1","int_pagination":"20","b_use_editor":"1","str_class":"high","str_valid_html":"","table":"tbl_links","b_add_internal_links":"1","str_buttons1":"cut,copy,pastetext,pasteword,selectall,undo,bold,italic,bullist,formatselect,removeformat,link,unlink,image,embed","str_buttons2":"","str_buttons3":"","int_preview_width":"450","int_preview_height":"500","str_formats":"h2,h3","str_styles":"","txt_help":"","str_revision":"2890"}],
    'cfg_email':[{"id":"3","key":"login_accepted","str_subject_nl":"Account voor {site_title} geaccepteerd","txt_email_nl":"<h1>Account aanvraag voor {identity} is geaccepteerd.</h1>\n<p>U kunt nu inloggen.</p>","str_subject_en":"Account for {site_title} accepted","txt_email_en":"<h1>Account registration for {identity} is accepted.</h1>\n<p>You can login now.</p>"},{"id":"4","key":"login_activate","str_subject_nl":"Activeer account voor {site_title}","txt_email_nl":"<h1>Activeer de aanmelding voor {identity}</h1>\n<p>Klik op <a href=\"{site_url}/{activate_uri}?id={user_id}&amp;activation={activation}\">deze link</a> om je account te activeren.</p>","str_subject_en":"Activate your account for {site_title}","txt_email_en":"<h1>Activate account for {identity}</h1>\n<p>Please click <a href=\"{site_url}/{activate_uri}?id={user_id}&amp;activation={activation}\">this link</a> to activate your account.</p>"},{"id":"2","key":"login_admin_new_register","str_subject_nl":"Nieuw account aangevraagd voor {site_title}","txt_email_nl":"<h1>Een nieuw account is aangevraag door {identity} </h1>\n<p>Log in om de aanvraag te beoordelen.</p>\n","str_subject_en":"New account asked for {site_title}","txt_email_en":"<h1>A new account is being asked for by {identity} </h1>\n<p>Log in to deny or accept the registration.</p>"},{"id":"5","key":"login_deny","str_subject_nl":"Account aanvraag voor {site_title} afgewezen","txt_email_nl":"<h1>Afgewezen account voor {identity}</h1>\n<p>Uw aanvraag voor een account is afgewezen.</p>","str_subject_en":"Account for {site_title} denied","txt_email_en":"<h1>Denied account for {identity}</h1>\n<p>Your account is denied.</p>"},{"id":"6","key":"login_forgot_password","str_subject_nl":"Nieuw wachtwoord voor {site_title}","txt_email_nl":"<h1>Nieuw wachtwoord aanvragen voor {identity}</h1>\n<p>Klik hier om <a href=\"{site_url}{forgotten_password_uri}?code={forgotten_password_code}\">wachtwoord te resetten</a>.</p>","str_subject_en":"New password for {site_title}","txt_email_en":"<h1>New password request for {identity}</h1>\n<p>Click on <a href=\"{site_url}{forgotten_password_uri}?code={forgotten_password_code}\">to restet your password</a>.</p>"},{"id":"8","key":"login_new_account","str_subject_nl":"Welkom en inloggegevens voor {site_title}","txt_email_nl":"<h1>Welkom bij {site_title}</h1>\n<p>Hieronder staan je inloggegevens.</p>\n<p>Gebruiker: {identity}<br /> Wachtwoord:{password}</p>","str_subject_en":"New login for {site_title}","txt_email_en":"<h1>Welcome at {site_title}</h1>\n<p>Login with these settings:</p>\n<p>Username : {identity}<br />Password : {password}</p>"},{"id":"7","key":"login_new_password","str_subject_nl":"Nieuwe inloggegevens voor {site_title}","txt_email_nl":"<h1>Je nieuwe inlogggevens voor {site_title}:</h1>\n<p>Gebruiker: {identity}<br /> Wachtwoord:{password}</p>","str_subject_en":"New login for {site_title}","txt_email_en":"<h3>You got an account.</h3>\n<p>Login with these settings:</p>\n<p>Username : {identity}<br />Password : {password}</p>"},{"id":"1","key":"test","str_subject_nl":"Een test email van {site_title}","txt_email_nl":"<p>Dit is een testmail, verzonden van {site_title} op {site_url}</p>\n<p>&nbsp;</p>\n<p>&nbsp;</p>\n<p>Naam {name}</p>\n<p>&nbsp;</p>\n<p>Bestaat niet {bestaat_niet}</p>\n<p>&nbsp;</p>","str_subject_en":"test","txt_email_en":"<p>TEST</p>"}],
    'cfg_field_info':[{"id":"12","field_field":"tbl_links.url_url","b_show_in_grid":"1","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"prep_url_mail","str_validation_parameters":""},{"id":"13","field_field":"tbl_links.url_url","b_show_in_grid":"1","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"prep_url_mail","str_validation_parameters":""},{"id":"3","field_field":"tbl_menu.str_keywords","b_show_in_grid":"0","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"Extra","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"0","str_validation_parameters":""},{"id":"11","field_field":"tbl_menu.str_module","b_show_in_grid":"1","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"Extra","b_editable_in_grid":"0","str_options":"|forms.contact|example","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"0","str_validation_parameters":""},{"id":"2","field_field":"tbl_menu.stx_description","b_show_in_grid":"0","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"Extra","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"0","str_validation_parameters":""},{"id":"7","field_field":"tbl_site.email_email","b_show_in_grid":"1","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"","str_validation_parameters":""},{"id":"5","field_field":"tbl_site.str_author","b_show_in_grid":"1","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"","str_validation_parameters":""},{"id":"10","field_field":"tbl_site.str_google_analytics","b_show_in_grid":"1","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"valid_google_analytics","str_validation_parameters":""},{"id":"4","field_field":"tbl_site.str_title","b_show_in_grid":"1","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"","str_validation_parameters":""},{"id":"8","field_field":"tbl_site.stx_description","b_show_in_grid":"1","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"","str_validation_parameters":""},{"id":"9","field_field":"tbl_site.stx_keywords","b_show_in_grid":"1","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"","str_validation_parameters":""},{"id":"6","field_field":"tbl_site.url_url","b_show_in_grid":"1","b_show_in_form":"1","str_show_in_form_where":" ","str_fieldset":"","b_editable_in_grid":"0","str_options":"","b_multi_options":"0","b_ordered_options":"0","str_options_where":"","str_validation_rules":"prep_url_mail","str_validation_parameters":""}],
    'cfg_img_info':[{"id":"1","path":"pictures","int_min_width":"0","int_min_height":"0","b_resize_img":"1","int_img_width":"300","int_img_height":"2000","b_create_1":"1","int_width_1":"100","int_height_1":"1000","str_prefix_1":"_thumb_","str_suffix_1":"","b_create_2":"0","int_width_2":"0","int_height_2":"0","str_prefix_2":"","str_suffix_2":""}],
    'cfg_media_info':[{"id":"1","order":"1","path":"pictures","b_visible":"1","str_types":"jpg,jpeg,gif,png","b_encrypt_name":"0","fields_media_fields":"0","b_pagination":"1","b_add_empty_choice":"1","b_dragndrop":"1","str_order":"name","int_last_uploads":"5","fields_check_if_used_in":"0","str_autofill":"","fields_autofill_fields":"0","b_in_database":"1","b_in_media_list":"0","b_in_img_list":"1","b_in_link_list":"0","b_user_restricted":"0","b_serve_restricted":"0"},{"id":"2","order":"2","path":"downloads","b_visible":"1","str_types":"pdf,doc,docx,xls,xlsx,png,jpg","b_encrypt_name":"0","fields_media_fields":"0","b_pagination":"1","b_add_empty_choice":"0","b_dragndrop":"0","str_order":"name","int_last_uploads":"5","fields_check_if_used_in":"","str_autofill":"","fields_autofill_fields":"0","b_in_database":"1","b_in_media_list":"0","b_in_img_list":"0","b_in_link_list":"1","b_user_restricted":"0","b_serve_restricted":"0"}],
    'cfg_table_info':[{"id":"1","order":"0","table":"tbl_site","b_visible":"1","str_order_by":"","b_pagination":"0","b_jump_to_today":"0","str_fieldsets":"","str_abstract_fields":"","str_options_where":"","b_add_empty_choice":"1","str_form_many_type":"dropdown","str_form_many_order":"last","int_max_rows":"1","b_grid_add_many":"0","b_form_add_many":"1","b_freeze_uris":"0"},{"id":"3","order":"2","table":"tbl_menu","b_visible":"1","str_order_by":"","b_pagination":"0","b_jump_to_today":"0","str_fieldsets":"Extra","str_abstract_fields":"","str_options_where":"","b_add_empty_choice":"0","str_form_many_type":"dropdown","str_form_many_order":"last","int_max_rows":"0","b_grid_add_many":"0","b_form_add_many":"1","b_freeze_uris":"0"},{"id":"2","order":"3","table":"tbl_links","b_visible":"1","str_order_by":"","b_pagination":"0","b_jump_to_today":"0","str_fieldsets":"","str_abstract_fields":"str_title","str_options_where":"","b_add_empty_choice":"1","str_form_many_type":"dropdown","str_form_many_order":"last","int_max_rows":"0","b_grid_add_many":"0","b_form_add_many":"1","b_freeze_uris":"0"}],
    'cfg_ui':[{"id":"5","path":"downloads","table":"","field_field":"","str_title_nl":"Downloads","str_title_en":"","txt_help_nl":"<p>Voeg hier bestanden toe die je in je tekst als download-link wilt gebruiken.</p>","txt_help_en":""},{"id":"4","path":"pictures","table":"","field_field":"","str_title_nl":"Foto's","str_title_en":"","txt_help_nl":"<p>Upload of verwijder hier de foto's van je site.</p>","txt_help_en":""},{"id":"2","path":"","table":"tbl_links","field_field":"","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Een tabel met links die je in alle teksten van de site kunt gebruiken.</p>","txt_help_en":""},{"id":"3","path":"","table":"tbl_menu","field_field":"","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Het menu van de site, met de onderliggende pagina's en teksten.</p>","txt_help_en":""},{"id":"1","path":"","table":"tbl_site","field_field":"","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Algemene informatie van de site en informatie voor zoekmachines.</p>","txt_help_en":""},{"id":"17","path":"","table":"","field_field":"tbl_menu.self_parent","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Wil je dat de nieuwe pagina onder een al bestaande pagina uit het hoofdmenu komt te staan? Geef dan hier aan onder welke pagina. Als je niets kiest dan komt de pagina in het hoofdmenu.</p>","txt_help_en":""},{"id":"21","path":"","table":"","field_field":"tbl_menu.str_keywords","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Vul hier zoektermen in voor deze pagina.</p><p>Ze worden toegevoegd aan de zoektermen die je bij <strong><a href=\"admin/help/site\">Site</a></strong> hebt ingevoerd.</p>","txt_help_en":""},{"id":"19","path":"","table":"","field_field":"tbl_menu.str_module","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Kies hier eventueel een module.</p><p>Modules voegen extra inhoud toe aan je pagina: een contactformulier, een overzicht van alle links, een agenda of een speciaal voor jouw site geschreven module.</p>","txt_help_en":""},{"id":"16","path":"","table":"","field_field":"tbl_menu.str_title","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Vul de titel van de pagina in.</p>","txt_help_en":""},{"id":"20","path":"","table":"","field_field":"tbl_menu.stx_description","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Vul hier een korte omschrijving van deze pagina in.</p><p>Die wordt gebruikt door zoekmachines als Google. Als je niets invult, wordt de algemene omschrijving gebruikt die je bij <strong><a href=\"admin/help/site\">Site</a></strong> hebt ingevuld.</p>","txt_help_en":""},{"id":"18","path":"","table":"","field_field":"tbl_menu.txt_text","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Vul hier de <a href=\"admin/help/tekst_aanpassen\">tekst</a> van je pagina in.</p><p>Eventueel kun je hier ook <a href=\"admin/help/fotos\">foto's</a> of <a href=\"admin/help/youtube_googlemaps_etc\">YouTube</a> filmpjes tussen de tekst plaatsen.</p>","txt_help_en":""},{"id":"11","path":"","table":"","field_field":"tbl_site.email_email","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Vul hier je e-mailadres in.</p><p>Heb je formulieren op je site staan? Als bezoekers ze invullen en opzenden, ontvang je ze via dit e-mailadres.</p>","txt_help_en":""},{"id":"9","path":"","table":"","field_field":"tbl_site.str_author","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Vul hier je naam in.</p><p>De naam van de auteur is onzichtbaar voor bezoekers van de site, maar vindbaar voor zoekmachines, zodat bezoekers ook via jouw naam op je site terechtkomen.</p>","txt_help_en":""},{"id":"14","path":"","table":"","field_field":"tbl_site.str_google_analytics","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>FlexyAdmin biedt statistieken over de bezoekers van je site. Als je uitgebreider statistieken wilt, kun je bijvoorbeeld <a href=\"http://www.google.com/intl/nl/analytics/\" target=\"_blank\">Google Analytics</a> gebruiken. Voer hier de code daarvan in.</p>","txt_help_en":""},{"id":"8","path":"","table":"","field_field":"tbl_site.str_title","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Vul hier de titel in van je site.</p><p>De titel is zichtbaar in de kop van de <a href=\"admin/help/faq\" target=\"_self\">browser</a> en in de zoekresultaten van Google.</p>","txt_help_en":""},{"id":"12","path":"","table":"","field_field":"tbl_site.stx_description","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Vul hier een korte algemene omschrijving van je site in.</p><p>Die is onzichtbaar op de site, maar wordt gebruikt door zoekmachines.</p><p>Afhankelijk van de opzet van de site kun je voor elke pagina een eigen omschrijving maken. Die vervangt voor die pagina deze algemene omschrijving.</p>","txt_help_en":""},{"id":"13","path":"","table":"","field_field":"tbl_site.stx_keywords","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Vul hier zoektermen in gescheiden door komma's.</p><p>Zoektermen worden door zoekmachines gebruikt om je site beter vindbaar te maken. Lees <a href=\"admin/help/tips_voor_een_goede_site\" target=\"_self\">hier meer over SEO</a>.<br /><br />Afhankelijk van de opzet van je site is het mogelijk om per pagina extra zoektermen toe te voegen.</p>","txt_help_en":""},{"id":"10","path":"","table":"","field_field":"tbl_site.url_url","str_title_nl":"","str_title_en":"","txt_help_nl":"<p>Vul hier het webadres van je site in, bijvoorbeeld: \"www.voorbeeldsite.nl\"</p>","txt_help_en":""}],
    'cfg_user_groups':[{"id":"1","str_name":"super_admin","str_description":"Super Administrator","rights":"*","b_all_users":"1","b_backup":"1","b_tools":"1","b_delete":"1","b_add":"1","b_edit":"1","b_show":"1"},{"id":"2","str_name":"admin","str_description":"Administrator","rights":"tbl_*|media_*|cfg_users","b_all_users":"0","b_backup":"1","b_tools":"1","b_delete":"1","b_add":"1","b_edit":"1","b_show":"1"},{"id":"3","str_name":"user","str_description":"User","rights":"tbl_*|media_*","b_all_users":"0","b_backup":"0","b_tools":"0","b_delete":"1","b_add":"1","b_edit":"1","b_show":"1"},{"id":"4","str_name":"visitor","str_description":"Visitor","rights":"tbl_*|media_*","b_all_users":"0","b_backup":"0","b_tools":"0","b_delete":"0","b_add":"0","b_edit":"0","b_show":"0"}],
    'cfg_users':[{"id":"1","str_username":"admin","id_user_group":"1","gpw_password":"454dda399f9922f1c4c900e5322f136cf7ef9142","email_email":"info@flexyadmin.com","ip_address":"","str_salt":"","str_activation_code":"","str_forgotten_password_code":"","str_remember_code":"","created_on":"0","last_login":"1417038989","b_active":"1","str_language":"nl","str_filemanager_view":"list"},{"id":"2","str_username":"user","id_user_group":"3","gpw_password":"6a1fd98dfda4d527adeeef1e334456ee1729eaa0","email_email":"jan@burp.nl","ip_address":"","str_salt":"","str_activation_code":"","str_forgotten_password_code":"0","str_remember_code":"","created_on":"0","last_login":"1417039057","b_active":"1","str_language":"nl","str_filemanager_view":"list"}],
    'log_login':[{"id":"153","id_user":"1","tme_login_time":"2015-03-22 09:16:31","str_changed_tables":"cfg_email","ip_login_ip":"::1"}],
    'log_stats':[],
    'res_media_files':[{"id":"2","b_exists":"1","file":"test_03.jpg","path":"pictures","str_type":"jpg","str_title":"test_03","dat_date":"2014-09-16","int_size":"114","int_img_width":"960","int_img_height":"720"}],
    'tbl_links':[{"id":"7","str_title":"Email FlexyAdmin","url_url":"mailto:info@flexyadmin.com"},{"id":"2","str_title":"FlexyAdmin","url_url":"http://www.flexyadmin.com"},{"id":"1","str_title":"Jan den Besten - webontwerp en geluidsontwerp","url_url":"http://www.jandenbesten.net"}],
    'tbl_menu':[
      {"id":"1","order":"0","self_parent":"0","uri":"gelukt","str_title":"Gelukt!","txt_text":"Als je dit ziet is het je gelukt om FlexyAdmin te installeren en werkend te krijgen.","str_module":"","stx_description":"","str_keywords":""},
      {"id":"2","order":"1","self_parent":"0","uri":"een_pagina","str_title":"Een pagina","txt_text":"Lorem ipsum dolor sit amet","str_module":"","stx_description":"","str_keywords":""},
      {"id":"3","order":"2","self_parent":"2","uri":"subpagina","str_title":"Subpagina","txt_text":"Een subpagina","str_module":"","stx_description":"","str_keywords":""},
      {"id":"9","order":"3","self_parent":"3","uri":"sub_sub","str_title":"Sub Sub","txt_text":"","str_module":"","stx_description":"","str_keywords":""},
      {"id":"5","order":"4","self_parent":"2","uri":"nog_een_subpagina","str_title":"Nog een subpagina","txt_text":"","str_module":"example","stx_description":"","str_keywords":""},
      {"id":"8","order":"5","self_parent":"0","uri":"onderaan","str_title":"Onderaan","txt_text":"","str_module":"","stx_description":"","str_keywords":""},
      {"id":"4","order":"6","self_parent":"0","uri":"contact","str_title":"Contact","txt_text":"Hier een voorbeeld van een eenvoudig contactformulier.","str_module":"forms.contact","stx_description":"","str_keywords":""}
    ],
    'tbl_site':[{"id":"1","str_title":"FlexyAdmin Demo","str_author":"Jan den Besten","url_url":"http://www.flexyadmin.com/","email_email":"info@flexyadmin.com","stx_description":"","stx_keywords":"","str_google_analytics":""}],
  };
  
  var get_admin_nav = {
    "header":[{"name":"Help","uri":"help/index","type":"info"},{"name":"admin","uri":"form/cfg_users/current","type":"form","args":{"table":"cfg_users","id":"1"}},{"name":"Loguit","uri":"logout","type":"logout"}],
    "sidebar":[{"name":"Menu","uri":"grid/tbl_menu","type":"table","args":{"table":"tbl_menu"},"help":"Het menu van de site, met de onderliggende pagina's en teksten."},{"name":"Links","uri":"grid/tbl_links","type":"table","args":{"table":"tbl_links"},"help":"Een tabel met links die je in alle teksten van de site kunt gebruiken."},{"name":"Foto's","uri":"media/pictures","type":"media","args":{"path":"pictures"},"help":"Upload of verwijder hier de foto's van je site."},{"name":"Downloads","uri":"media/downloads","type":"media","args":{"path":"downloads"},"help":"Voeg hier bestanden toe die je in je tekst als download-link wilt gebruiken."},{"type":"seperator"},{"type":"seperator"},{"name":"Exporteer Database","uri":"tools/db_export","type":"tools","args":{"api":"/admin/db/export/"}},{"name":"Importeer Database","uri":"tools/db_import","type":"tools","args":{"api":"/admin/db/import/"}},{"name":"Zoeken/Vervangen","uri":"tools/search","type":"tools","args":{"api":"/admin/search/"}},{"name":"Automatisch vullen","uri":"tools/fill","type":"tools","args":{"api":"/admin/fill/"}},{"type":"seperator"},{"name":"Media Files","uri":"grid/res_media_files","type":"result","args":{"table":"res_media_files"},"help":""},{"type":"seperator"},{"name":"Ui","uri":"grid/cfg_ui","type":"config","args":{"table":"cfg_ui"},"help":"Maak hier teksten en help voor de backend userinterface."},{"name":"Configurations","uri":"grid/cfg_configurations","type":"config","args":{"table":"cfg_configurations"},"help":"Globale instellingen"},{"name":"Admin Menu","uri":"grid/cfg_admin_menu","type":"config","args":{"table":"cfg_admin_menu"},"help":"Pas het admin menu hier aan."},{"name":"Media Info","uri":"grid/cfg_media_info","type":"config","args":{"table":"cfg_media_info"},"help":"Instellingen voor bestandsmappen."},{"name":"Img Info","uri":"grid/cfg_img_info","type":"config","args":{"table":"cfg_img_info"},"help":"Instellingen voor resizen van afbeeldingen na uploaden."},{"name":"Email","uri":"grid/cfg_email","type":"config","args":{"table":"cfg_email"},"help":""},{"name":"Table Info","uri":"grid/cfg_table_info","type":"config","args":{"table":"cfg_table_info"},"help":"Instellingen voor tabellen."},{"name":"Field Info","uri":"grid/cfg_field_info","type":"config","args":{"table":"cfg_field_info"},"help":"Instellingen voor velden."},{"name":"Users","uri":"grid/cfg_users","type":"config","args":{"table":"cfg_users"},"help":"Maak hier gebruikers aan."},{"name":"User Groups","uri":"grid/cfg_user_groups","type":"config","args":{"table":"cfg_user_groups"},"help":"Maak hier usergroups aan voor gebruik bij Users."},{"name":"Login","uri":"grid/log_login","type":"log","args":{"table":"log_login"},"help":""},{"name":"Stats","uri":"grid/log_stats","type":"log","args":{"table":"log_stats"},"help":""}],
    "footer":[{"name":"Instellingen","uri":"form/tbl_site/first","type":"form","args":{"table":"tbl_site"}},{"name":"Statistieken","uri":"plugin/stats","type":"plugin","args":{"plugin":"stats"}}],
  };
  
  
  var flexyApiMock = {};
  
  /**
   * Return full database
   */
  flexyApiMock.database = function() {
    return database;
  };
  

  /**
   * Return all the tablenames
   */
  flexyApiMock.tables = function() {
    var tables=[];
    angular.forEach(database, function(value, key) {
      tables.push(key);
    });
    return tables;
  };
  

  /**
   * Return data of a table
   */
  flexyApiMock.table = function(table) {
    return database[table];
  };


  /**
   * Create a API_GET_table URL
   */
  flexyApiMock.api_get_table_url =  function(args) {
    return flexyApiMock.api_url('table', args);
  };


  /**
   * Create API
   */
  flexyApiMock.api_url = function(type,args) {
    var serializedData=jdb.serializeJSON(args);
    if (serializedData!=='') serializedData = '?'+serializedData;
    serializedData=serializedData.replace('&&','&');
    return encodeURI(api + type + serializedData );
  }


  /**
   * Create a API URL with random args
   */
  flexyApiMock.api_random_args =  function() {
    var args  = {};
    var count = jdb.randomInt(1,4);
    for (var i = 0; i < count; i++) {
      args[i+jdb.randomString()] = jdb.randomString();
    }
    return args;
  };
  
  
  /**
   * Create a API_GET_data RESPONSE
   */
  flexyApiMock.api_get_data_response =  function(args, api) {
    return flexyApiMock.api_response(args,database[args.table], api);
  };
  
  
  /**
   * Create response for get_admin_nav
   */
  flexyApiMock.api_get_admin_nav_response = function() {
    return flexyApiMock.api_response({},get_admin_nav);
  }
  
  
  /**
   * Create a success RESPONSE
   */
  flexyApiMock.api_response =  function(args,data, api) {
    var response = {
      'success' : true,
      'data'    : data,
      'args'    : args
    };
    // Add info
    switch (api) {
      case 'table':
        response['info'] = {
          'rows'        : data.length,
          'total_rows'  : data.length,
          'table_rows'  : data.length
        };
        break;
    }
    // add mock settings
    if (angular.isDefined(args.settings)) {
      response['settings']={'mock':'data'};
      if (args.table=='tbl_menu') response['settings'].table_info={'tree':true};
    }
    return response;
  };


  /**
   * Create a API error_wrong_arguments RESPONSE
   */
  flexyApiMock.api_error_wrong_args =  function(args) {
    return {
      'success' : false,
      'error'   :'WRONG ARGUMENTS',
      'args' : args,
    }
  };

  
  return flexyApiMock;
  
}]);
