<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2008, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
**/

// ------------------------------------------------------------------------

/**
 * FlexyAdmin V1	Configuration
 *
 * Here you can find FlexyAdmin configuration settings that probably won't need adjustments.
 * The settings here are needed for internal use only. Do not change them if you don't know what
 * you're doing.
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
**/

$config['PROFILER']               = FALSE;

$config['PHP_version']						= substr(phpversion(),0,1);
$config['LOCAL']									= IS_LOCALHOST;
$config['IS_AJAX']								= IS_AJAX;
$config['AJAX_MODULE']						= IS_AJAX;
$config['LANGUAGES']							= array('nl','en','de','es','fr');
$config['MENU_TABLES']						= array('res_menu_result','tbl_menu');

// Directories
$config['SITE']										= SITEPATH;
$config['ADMINASSETS']						= "sys/flexyadmin/assets/";
$config['ASSETS']									= $config['SITE'].'assets/';
$config['THUMBCACHE']							= $config['ASSETS']."_thumbcache/";
$config['STATS']									= $config['SITE'].'stats/';
$config['PLUGINS']								= $config['SITE'].'plugins';
$config['BULKUPLOAD']							= 'bulk_upload';

$config['THUMBSIZE']							= array(100,100);
$config['IGNORE_MIME']						= FALSE;



/*
| UI settings
*/

$config['FORM_NICE_DROPDOWNS']		= TRUE;
$config['MULTIPLE_UPLOAD']		    = TRUE;
$config['PAGINATION']             = FALSE;
$config['GRID_EDIT']              = TRUE;
$config['GRID_WHERE']             = FALSE;


/*
|--------------------------------------------------------------------------
| PLUGIN cfg
|--------------------------------------------------------------------------
|
*/

$config['PLUGIN_ORDER']							= array('first'=>array('uri','links','striptags'),'last' =>array('automenu'));
$config['PLUGIN_URI_REPLACE_CHAR']	= '_';
$config['URI_HASH']                 = ':';  // possible other value: ~
$config['PLUGIN_URI_ARGS_CHAR']     = $config['URI_HASH'];
$config['FORBIDDEN_URIS']           = array("site","sys","admin","rss","file",'offset');

  
/*
|--------------------------------------------------------------------------
| Custom form validation rules (in MY_Form_validation)
|--------------------------------------------------------------------------
|
*/

$config['CUSTOM_VALIDATION_RULES']            = 'valid_rgb|valid_google_analytics|valid_password|valid_regex|valid_model_method';


/*
|--------------------------------------------------------------------------
| Wich foreign keys will show a level deeper
|--------------------------------------------------------------------------
|
*/
$config['DEEP_FOREIGNS']  = array(
  'id_lang'       => array(
                          'table'     => 'cfg_lang',
                          'abstract'  => 'key'
                          )
);



/*
|--------------------------------------------------------------------------
| API calls
|--------------------------------------------------------------------------
|
| URI to FlexyAdmin controllers.
|
*/
$config['API_home']									= "/admin/";
$config['API_user']									= "/admin/show/user/";
$config['API_login']								= "/admin/login/";
$config['API_logout']								= "/admin/logout/";

$config['API_view_order']						= "/admin/show/order/";
$config['API_view_grid']						= "/admin/show/grid/";
$config['API_view_tree']						= "/admin/show/tree/";
$config['API_view_form']						= "/admin/show/form/";

$config['API_filemanager']					= "/admin/filemanager/";
$config['API_filemanager_set_view']	= "/admin/filemanager/setview";
$config['API_filemanager_view']			= "/admin/filemanager/show/";
$config['API_filemanager_edit']			= "/admin/filemanager/edit/";
$config['API_filemanager_delete']		= "/admin/filemanager/delete/";
$config['API_filemanager_confirm']	= "/admin/filemanager/confirm/";
$config['API_filemanager_upload']		= "/admin/filemanager/upload/";

$config['API_popup_img']						= "/admin/popup/img/";

$config['API_delete']								= "/admin/edit/delete/";
$config['API_confirm']							= "/admin/edit/confirm/";

$config['API_db']										= "/admin/db/";
$config['API_db_backup']						= "/admin/db/backup/";
$config['API_db_restore']						= "/admin/db/restore/";
$config['API_db_export']						= "/admin/db/export/";
$config['API_db_import']						= "/admin/db/import/";
$config['API_db_sql']								= "/admin/db/sql/";

$config['API_search']								= '/admin/search/';
$config['API_fill']									= '/admin/fill/';
$config['API_help']									= '/admin/help/';
$config['API_bulk_upload']					= '/admin/bulkupload/';

// $config['API_stats']								= '/admin/stats/show';
$config['API_info']									= '/admin/info/';

$config['AJAX']											= "/admin/ajax/";

$config['FILES_view_types']					= array("list","icons");


$config['FILES_thumb_path']					= "/thumb/";
$config['FILES_big_path']						= "/big/";







/*
|--------------------------------------------------------------------------
| Rights
|--------------------------------------------------------------------------
|
*/
define("RIGHTS_ALL",15);
define("RIGHTS_DELETE",8);
define("RIGHTS_ADD",4);
define("RIGHTS_EDIT",2);
define("RIGHTS_SHOW",1);
define("RIGHTS_NO",0);



/*
|--------------------------------------------------------------------------
| Config Tables and items
|--------------------------------------------------------------------------
|
| Names of configuration fields and tables in database.
|
*/

$config['MENU_excluded']							= array('cfg_sessions');

$config['CFG_table_prefix']						= "cfg";
$config['LOG_table_prefix']						= "log";
$config['RES_table_prefix']						= "res";
$config['TABLE_prefix']								= "tbl";
$config['REL_table_prefix']						= "rel";
$config['REL_table_split']						= "__";


$config['CFG_configurations']					= "configurations";
$config['CFG_users']									= "users";
$config['CFG_media_info']							= "media_info";
$config['CFG_img_info']								= "img_info";

$config['LOG_login']									= "login";
$config['LOG_stats']									= "stats";


$config['FILE_types_forbidden']				= array('php','php3','php4','phtml','pl','py','jsp','asp','shtml','sh','cgi','js','exe','dmg','app');
$config['FILE_types_img']							= array('jpg','jpeg','gif','png','tiff','cur');
$config['FILE_types_mp3']							= array('mp3','wav','wma','aiff','ogg');
$config['FILE_types_flash']						= array('swf','flv');
$config['FILE_types_movies'] 					= array('mov','mp4');
$config['FILE_types_pdf']							= array('pdf');
$config['FILE_types_docs']						= array('doc','docx','odt');
$config['FILE_types_xls']							= array('xls','xlsx','ods');

$config['CFG_table']									= "table_info";
$config['CFG_table_name']							= "table";
$config['CFG_field']									= "field_info";
$config['CFG_field_name']							= "field_field";


/*
|--------------------------------------------------------------------------
| CFG_table_info AND cfg_field_info for cfg_tables
|--------------------------------------------------------------------------
|
*/


$config["CFG_"]=array(
  
/**
 * UI
 */
  
	"cfg_ui" => array(
		"cfg_ui"=>array(
			"table"=>'cfg_ui',
			"txt_help_nl"=>'<p>Maak hier teksten en help voor de backend userinterface.</p>',
			"txt_help_en"=>'<p>Create texts and help for the backend userinterface.</p>'
			),
		"cfg_configurations"=>array(
			"table"=>'cfg_configurations',
			"txt_help_nl"=>'<p>Globale instellingen</p>',
			"txt_help_en"=>'<p>Common settings</p>'
			),
		"cfg_auto_menu"=>array(
			"table"=>'cfg_auto_menu',
			"txt_help_nl"=>'<p>Instellingen voor een samengesteld menu. Heeft res_menu_result nodig.</p>',
			"txt_help_en"=>'<p>Settings for creating an automatic menu. Needs a res_menu_result table.</p>'
			),
		"cfg_admin_menu"=>array(
			"table"=>'cfg_admin_menu',
			"txt_help_nl"=>'<p>Pas het admin menu hier aan.</p>',
			"txt_help_en"=>'<p>Change your admin menu here.</p>'
			),
		"cfg_media_info"=>array(
			"table"=>'cfg_media_info',
			"txt_help_nl"=>'<p>Instellingen voor bestandsmappen.</p>',
			"txt_help_en"=>'<p>Settings for Files.</p>'
			),
		"cfg_img_info"=>array(
			"table"=>'cfg_img_info',
			"txt_help_nl"=>'<p>Instellingen voor resizen van afbeeldingen na uploaden.</p>',
			"txt_help_en"=>'<p>Settings for image resizing after uploading.</p>'
			),
		"cfg_table_info"=>array(
			"table"=>'cfg_table_info',
			"txt_help_nl"=>'<p>Instellingen voor tabellen.</p>',
			"txt_help_en"=>'<p>Settings for tables.</p>'
			),
		"cfg_field_info"=>array(
			"table"=>'cfg_field_info',
			"txt_help_nl"=>'<p>Instellingen voor velden.</p>',
			"txt_help_en"=>'<p>Settings for fields.</p>'
			),
		"cfg_rss"=>array(
			"table"=>'cfg_rss',
			"txt_help_nl"=>'<p>Instellingen voor RSS feeds.</p>',
			"txt_help_en"=>'<p>Settings for RSS feeds.</p>',
			),

		"cfg_users"=>array(
			"table"=>'cfg_users',
			"txt_help_nl"=>'<p>Maak hier gebruikers aan.</p>',
			"txt_help_en"=>'<p>Create users here.</p>'
			),
		"cfg_users.id_user_group"=>array(
			"field_field"=>'cfg_users.id_user_group',
			"txt_help_nl"=>'<p>Kies hier de groep. Dit bepaald welke rechten een gebruiker heeft.</p>',
			"txt_help_en"=>'<p>Choose e group. This will determine the rights of a user.</p>'
			),

		"cfg_user_groups"=>array(
			"table"=>'cfg_user_groups',
			"txt_help_nl"=>'<p>Maak hier usergroups aan voor gebruik bij Users.</p>',
			"txt_help_en"=>'<p>Create groups of rights here. For use in Users.</p>'
			),

		"cfg_configurations.str_class"=>array(
			"field_field"=>'cfg_configurations.str_class',
			"str_title_nl"=>'Omvang editor',
			"str_title_en"=>'Editor size'
			),
		"cfg_configurations.table"=>array(
			"field_field"=>'cfg_configurations.table',
			"str_title_nl"=>'Links Tabel',
			"str_title_en"=>'Links Table'
			),
		"cfg_field_info.str_validation_parameters"=>array(
			"field_field"=>'cfg_field_info.str_validation_parameters',
			"str_title_nl"=>'Val. Parameters',
			"str_title_en"=>'Val. Parameters'
			)
	),

/**
 * TABLE INFO
 */
	
	"cfg_table_info" => array(
		"cfg_ui"=>array(
			"order"=>'100',
			"table"=>'cfg_ui',
			'str_fieldsets'=>'English',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'field_field,table,path',
			),
		"cfg_configurations"=>array(
			"order"=>'101',
			"table"=>'cfg_configurations',
			'str_fieldsets'=>'Editor',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			'int_max_rows'=>1
			),
		"cfg_auto_menu"=>array(
			"order"=>'102',
			"table"=>'cfg_auto_menu',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'str_description',
			"str_order_by"=>'',
			),
		"cfg_admin_menu"=>array(
			"order"=>'103',
			"table"=>'cfg_admin_menu',
			"b_grid_add_many"=>'0',
      'b_pagination' => '0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			),
		"cfg_media_info"=>array(
			"order"=>'104',
			"table"=>'cfg_media_info',
			'str_fieldsets'=>'More',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			),
		"cfg_img_info"=>array(
			"order"=>'106',
			"table"=>'cfg_img_info',
			'str_fieldsets'=>'Size 1,Size 2',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			),
		"cfg_lang"=>array(
			"order"=>'107',
			"table"=>'cfg_lang'
    ),
		"cfg_table_info"=>array(
			"order"=>'108',
			"table"=>'cfg_table_info',
			'str_fieldsets'=>'Dropdown,More',
			"b_grid_add_many"=>'0',
      'b_pagination' => '0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			),
		"cfg_field_info"=>array(
			"order"=>'109',
			"table"=>'cfg_field_info',
			'str_fieldsets'=>'Options,Validation',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'field_field',
			),
		"cfg_rss"=>array(
			"order"=>'110',
			"table"=>'cfg_rss',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'id',
			),
		"cfg_plugins"=>array(
			"order"=>'115',
			"table"=>'cfg_plugins',
			"b_single_row"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			),
		"cfg_users"=>array(
			"order"=>'120',
			"table"=>'cfg_users',
      'b_pagination'=>true,
			"b_grid_add_many"=>'1',
			"str_abstract_fields"=>'str_username',
			"str_order_by"=>'b_active,str_username',
			),
		"cfg_user_groups"=>array(
			"order"=>'121',
			"table"=>'cfg_groups',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'str_description',
			"str_order_by"=>'id',
			"b_add_empty_choice"=>'1',
			),
		"log_login"=>array(
			"order"=>'130',
			"table"=>'log_login',
			'b_pagination'=>true,
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			),
		"log_stats"=>array(
			"order"=>'131',
			"table"=>'log_stats',
			'b_pagination'=>true,
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			),
        
    "res_menu_result"=>array(
      'table'=>'res_menu_result',
      'b_pagination'=>0
    )
	),


/**
 * FIELD INFO
 */

	"cfg_field_info" => array(
    
		"cfg_ui.path"=>array(
			"b_editable_in_grid"=>1
			),
		"cfg_ui.table"=>array(
			"b_editable_in_grid"=>1
			),
		"cfg_ui.field_field"=>array(
			"b_editable_in_grid"=>1
			),
		"cfg_ui.str_title_nl"=>array(
			"b_editable_in_grid"=>1
			),
		"cfg_ui.str_title_en"=>array(
			"b_editable_in_grid"=>1,
			"str_fieldset"=>'English'
			),
		"cfg_ui.txt_help_en"=>array(
			"str_fieldset"=>'English'
			),


		"cfg_admin_menu.str_ui_name"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_admin_menu.path"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_admin_menu.table"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_admin_menu.str_table_where"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_admin_menu.str_type"=>array(
			"field"=>'cfg_admin_menu.str_type',
 			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'api|tools|table|all_tbl_tables|all_cfg_tables|all_res_tables|media|all_media|seperator',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),

		"cfg_auto_menu.str_type"=>array(
			"field"=>'cfg_auto_menu.str_type',
 			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'menu item|from menu table|from submenu table|from category table|from table group by category|split by language|by module',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),

		"cfg_configurations.b_use_editor"=>array(
			"str_fieldset"=>'Editor'
 			),
		"cfg_configurations.str_valid_html"=>array(
			"str_fieldset"=>'Editor'
 			),
		"cfg_configurations.table"=>array(
			"str_ui_name"=>'Links Table',
			"str_fieldset"=>'Editor',
 			),
		"cfg_configurations.b_add_internal_links"=>array(
			"str_fieldset"=>'Editor'
 			),
		"cfg_configurations.str_class"=>array(
			"str_ui_name"=>'Editor size',
			"str_fieldset"=>'Editor',
			"str_options"=>'normal|high|wide|big',
 			),
		"cfg_configurations.str_buttons1"=>array(
			"str_fieldset"=>'Editor'
 			),
		"cfg_configurations.str_buttons2"=>array(
			"str_fieldset"=>'Editor'
 			),
		"cfg_configurations.str_buttons3"=>array(
			"str_fieldset"=>'Editor'
 			),
		"cfg_configurations.int_preview_width"=>array(
			"str_fieldset"=>'Editor'
 			),
		"cfg_configurations.int_preview_height"=>array(
			"str_fieldset"=>'Editor'
 			),
		"cfg_configurations.str_formats"=>array(
			"str_fieldset"=>'Editor'
 			),
		"cfg_configurations.str_styles"=>array(
			"str_fieldset"=>'Editor'
 			),
		"cfg_configurations.str_revision"=>array(
      'b_show_in_form'=>'0',
      'b_show_in_grid'=>'0'
 			),

		"cfg_field_info.field_field"=>array(
			"b_editable_in_grid"=>1,
			"b_multi_options"=>'1',
			"str_validation_rules"=>'required',
 			),
		"cfg_field_info.str_show_in_form_where"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_field_info.str_fieldset"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_field_info.str_options"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Options'
 			),
		"cfg_field_info.b_multi_options"=>array(
			'str_fieldset'=>'Options'
 			),
		"cfg_field_info.b_ordered_options"=>array(
			'str_fieldset'=>'Options'
 			),
		"cfg_field_info.str_options_where"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Options'
 			),

		"cfg_field_info.str_validation_rules"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Validation',
			"str_options"=>'|required|matches|min_length[]|max_length[]|exact_length[]|greater_than[]|less_than[]|alpha|alpha_numeric|alpha_dash|numeric|integer|decimal|is_natural|is_natural_no_zero|valid_email|valid_emails|valid_ip|valid_base64|prep_url|'.$config['CUSTOM_VALIDATION_RULES'],
			"b_multi_options"=>'1',
 			),
		"cfg_field_info.str_validation_parameters"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Validation'
 			),

		"cfg_table_info.table"=>array(
			"b_editable_in_grid"=>1,
			"str_validation_rules"=>'required',
 			),
		"cfg_table_info.str_order_by"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_table_info.str_fieldsets"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_table_info.str_abstract_fields"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Dropdown'
 			),
		"cfg_table_info.str_options_where"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Dropdown'
 			),
		"cfg_table_info.b_add_empty_choice"=>array(
			'str_fieldset'=>'Dropdown'
 			),
		"cfg_table_info.str_form_many_type"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Dropdown',
			"str_options"=>'dropdown|ordered_list', //'|subfields',
 			),
		"cfg_table_info.str_form_many_order"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Dropdown',
			"str_options"=>'first|last',
 			),
		"cfg_table_info.int_max_rows"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'More'
 			),
		"cfg_table_info.b_grid_add_many"=>array(
			'str_fieldset'=>'More'
 			),
		"cfg_table_info.b_freeze_uris"=>array(
			'str_fieldset'=>'More'
 			),
      
    'cfg_lang.key'=>array(
			"str_validation_rules"=>'required|is_unique[cfg_lang.key.id]'
    ),
    'cfg_lang.lang_uk'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_en'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_nl'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_de'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_fr'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_it'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_es'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_fi'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_pl'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_dk'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_pt'=>array(
      'b_editable_in_grid' => true
    ),
    'cfg_lang.lang_no'=>array(
      'b_editable_in_grid' => true
    ),
      
    'res_media_files.b_exists' => array(
      'b_show_in_grid' => false,
      'b_show_in_form' => false
    ),
    'res_media_files.file' => array(
      'b_show_in_form' => false
    ),


		"cfg_media_info.str_order"=>array(
			"field"=>'cfg_media_info.str_order',
 			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'More',
			"str_options"=>'name|_name|rawdate|_rawdate|type|_type|size|_size|width|_width|height|_height',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_media_info.path"=>array(
			"field"=>'cfg_media_info.path',
 			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"b_editable_in_grid"=>1,
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>'required',
 			),
		"cfg_media_info.str_types"=>array(
			"field"=>'cfg_media_info.str_types',
 			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"b_editable_in_grid"=>1,
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>'required',
 			),
		"cfg_media_info.str_autofill"=>array(
			"field"=>'cfg_media_info.str_autofill',
 			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'More',
			"str_options"=>'|single upload|bulk upload|both',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_media_info.fields_media_fields"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_media_info.b_user_restricted"=>array(
			'str_fieldset'=>'More'
 			),
		"cfg_media_info.fields_autofill_fields"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'More'
 			),
		"cfg_media_info.int_last_uploads"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'More'
 			),
		"cfg_media_info.fields_check_if_used_in"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'More'
 			),
		"cfg_media_info.b_in_database"=>array(
			'str_fieldset'=>'More'
 			),
		"cfg_media_info.b_in_media_list"=>array(
			'str_fieldset'=>'More'
 			),
		"cfg_media_info.b_in_img_list"=>array(
			'str_fieldset'=>'More'
 			),
		"cfg_media_info.b_in_link_list"=>array(
			'str_fieldset'=>'More'
 			),

		"cfg_img_info.path"=>array(
			"field"=>'cfg_img_info.path',
			"b_editable_in_grid"=>1,
			"str_validation_rules"=>'required',
 			),
		"cfg_img_info.b_create_1"=>array(
			'str_fieldset'=>'Size 1'
 			),
		"cfg_img_info.int_min_width"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_img_info.int_min_height"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_img_info.int_img_width"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_img_info.int_img_height"=>array(
			"b_editable_in_grid"=>1,
    ),
		"cfg_img_info.int_width_1"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Size 1'
 			),
		"cfg_img_info.int_height_1"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Size 1'
 			),
		"cfg_img_info.str_prefix_1"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Size 1'
 			),
		"cfg_img_info.str_suffix_1"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Size 1'
 			),
		"cfg_img_info.b_create_2"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Size 2'
 			),
		"cfg_img_info.int_width_2"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Size 2'
 			),
		"cfg_img_info.int_height_2"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Size 2'
 			),
		"cfg_img_info.str_prefix_2"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Size 2'
 			),
		"cfg_img_info.str_suffix_2"=>array(
			"b_editable_in_grid"=>1,
			'str_fieldset'=>'Size 2'
 			),



		"cfg_user_groups.rights"=>array(
			"field"=>'cfg_groups.rights',
			"str_ui_name"=>'Rights for',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_users.id_user_group"=>array(
			"field"=>'cfg_users.id_user_group',
			"str_ui_name"=>'Group',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>'required'
 			),
		"cfg_users.str_username"=>array(
			"field"=>'cfg_users.str_username',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_validation_rules"=>'required|min_length[4]|alpha_dash|is_unique[cfg_users.str_username.id]'
 			),
		"cfg_users.gpw_password"=>array(
			"field"=>'cfg_users.gpw_password',
			"b_show_in_grid"=>'0',
			"b_show_in_form"=>'1',
			"str_validation_rules"=>'required|valid_password'
 			),
    "cfg_users.email_email"=>array(
      "field"=>'cfg_users.email_email',
      "str_validation_rules"=>'valid_email|is_unique[cfg_users.email_email.id]'
    ),
		"cfg_users.ip_address"=>array(
			"field"=>'cfg_users.ip_address',
			"str_ui_name"=>'IP Address',
			"b_show_in_grid"=>'0',
			"b_show_in_form"=>'0',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_users.str_salt"=>array(
			"field"=>'cfg_users.str_salt',
 			"b_show_in_grid"=>'0',
			"b_show_in_form"=>'0',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_users.str_activation_code"=>array(
			"field"=>'str_activation_code',
 			"b_show_in_grid"=>'0',
			"b_show_in_form"=>'0',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_users.str_forgotten_password_code"=>array(
			"field"=>'str_forgotten_password_code',
 			"b_show_in_grid"=>'0',
			"b_show_in_form"=>'0',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_users.str_remember_code"=>array(
			"field"=>'str_remember_code',
 			"b_show_in_grid"=>'0',
			"b_show_in_form"=>'0',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_users.created_on"=>array(
			"field"=>'created_on',
 			"b_show_in_grid"=>'0',
			"b_show_in_form"=>'0',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_users.last_login"=>array(
			"field"=>'last_login',
 			"b_show_in_grid"=>'0',
			"b_show_in_form"=>'0',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_users.str_filemanager_view"=>array(
			"field"=>'cfg_users.str_filemanager_view',
 			"b_show_in_grid"=>'0',
			"b_show_in_form"=>'0',
			"str_options"=>'icons|list|detailed',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			),
		"cfg_users.str_language"=>array(
			"field"=>'cfg_users.str_language',
 			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'nl|en',
			"b_multi_options"=>'0',
			"str_validation_rules"=>''
 			)
	)
);


/*
|--------------------------------------------------------------------------
| Other configurations
|--------------------------------------------------------------------------
|
*/

$config['UI_required_label_extra']		= "<span class=\"required\"> !</span>";

/*
|--------------------------------------------------------------------------
| Special fieldnames
|--------------------------------------------------------------------------
|
| Names of fields like primary and foreign keys, and other special fields
|
*/

define('PRIMARY_KEY','id');

$config['PRIMARY_key']							= PRIMARY_KEY;
$config['FOREIGN_key_format']				= "/^id_.*/";		// regexpression used by preg_match

$config['ORDER_field_name']					= "order";
$config['ORDER_decimals']						= 3;
$config['ORDER_default_fields']			= array( "order","dat DESC",'date DESC',"tme DESC","str","id");

$config['ABSTRACT_field_name']			= "abstract";
$config['ABSTRACT_field_pre_types']	= array("str","url","img","dat","tme","time","int");
$config['ABSTRACT_field_types']			= array("varchar","int","date",'datetime','time');
$config['ABSTRACT_field_max']				= 2;
$config['ABSTRACT_field_split']			= " | ";

$config['URI_field_pre_types']			= array("str","url","dat","date","datetime","tme",'time',"int","media");



/*
|--------------------------------------------------------------------------
| Field information
|--------------------------------------------------------------------------
|
| Settings for different kind of fields. How FlexyAdmin deals with them.
| Mostly about rendering and validation.
|		prefix	=> prefix of the field (copies name)
| 	grid		=> template for the grid view (table)
|		gridmax => max length of string shown in gridview
|		form		=> template for form item view
|		update	=> validations and preparing data actions when updating or inserting
|		delete	=> actions to perform when deleting this record
|
*/

$config['FIELDS_media_fields']		= array( "file", "img", "mp3", "mov" );
$config['FIELDS_date_fields']		  = array( "dat", "date", "tme", "datetime" );

$config['FIELDS_default'] = array(
																		"grid"				=> "%s",
																		"form"				=> "",
																		"validation"	=> "",
																	);

$config['FIELDS_special'] = array(

	"id"				=> array(
												"grid"				=> "function_primary_key",
												"form"				=> "function_primary_key",
												"validation"	=> "trim|integer|required",
											),
	"id_group"	=> array(
												"grid"				=> "function_foreign_key",
												"form"				=> "function_id_group",
												"validation"	=> "integer|required",
											),
	"user"			=> array(
												"grid"				=> "function_user",
												"form"				=> "function_user",
												"validation"	=> "trim|integer",
											),
	"uri"				=> array(
												"grid"				=> "/%s",
												"form"				=> "hidden",
												"validation"	=> "trim",
											),
	"api"				=> array(
												"grid"				=> "%s",
												"form"				=> "function_dropdown_api",
												"validation"	=> "trim",
											),
	"plugin"		=> array(
												"grid"				=> "%s",
												"form"				=> "function_dropdown_plugin",
												"validation"	=> "trim",
											),
  "actions"   => array(
                    		"grid"				=> "function_actions",
                    		"form"				=> "",
                    		"validation"	=> "",
                      ),
	"order"			=> array(
												"grid"				=> "function_order",
												"form"				=> "hidden",
												"validation"	=> "trim",
											),
	"abstract"	=> array(
												"grid"				=> "%s",
												"form"				=> "",
												"validation"	=> "",
											),
	"table"			=> array(
												"grid"				=> "%s",
												"form"				=> "function_dropdown_tables",
												"validation"	=> "trim",
											),
	"rights"			=> array(
												"grid"				=> "%s",
												"form"				=> "function_dropdown_rights",
												"validation"	=> "trim",
											),
	"path"			=> array(
												"grid"				=> "%s",
												"form"				=> "function_dropdown_path",
												"validation"	=> "trim",
											),
	"file"			=> array(
												"grid"				=> "%s",
												"form"				=> "function_dropdown_allfiles",
												"validation"	=> "trim",
											),
	"str_fieldset" => array(
											"grid"				=> "%s",
											"form"				=> "function_dropdown_fieldsets",
											"validation"	=> "trim"
											)
										
);


$config['FIELDS_mysql'] = array(

);


$config['FIELDS_prefix'] = array (
	"id"				=> array (
											"grid"				=> "function_foreign_key",
											"form"				=> "dropdown",
											"validation"	=> "trim|integer"
											),
	"self"			=> array(
												"grid"				=> "function_self",
												"form"				=> "function_self",
												"validation"	=> "trim|integer",
											),
	"rel"				=> array (
											"grid"				=> "function_join",
											"form"				=> "function_join",
											"validation"	=> "function_join"
											),
	"field"			=> array(
												"grid"				=> "%s",
												"form"				=> "function_dropdown_field",
												"validation"	=> "trim",
											),
	"fields"		=> array(
												"grid"				=> "%s",
												"form"				=> "function_dropdown_fields",
												"validation"	=> "trim",
											),											
	"media"			=> array (
											"grid"				=> "function_dropdown_media",
											"form"				=> "function_dropdown_media",
											"validation"	=> "trim"
											),
	"medias"		=> array (
											"grid"				=> "function_dropdown_medias",
											"form"				=> "function_dropdown_media",
											"validation"	=> "trim"
											),
	"list"			=> array (
											"grid"				=> "%s",
											"form"				=> "function_dropdown_list",
											"validation"	=> "trim"
											),
	"str"				=> array (
											"grid"				=> "%s",
											"form"				=> ""
											),
	"stx"				=> array (
											"grid"				=> "function_text",
											"form"				=> "textarea"
											),
	"md"				=> array (
											"grid"				=> "function_text",
											"form"				=> "textarea"
											),
	"txt"				=> array (
											"grid"				=> "function_text",
											"form"				=> "htmleditor",
											"validation"	=> ""
											),
	"pwd"				=> array (
											"grid"				=> "***",
											"form"				=> "password",
											"validation"	=> "trim"
										),
	"gpw"				=> array (
											"grid"				=> "***",
											"form"				=> "password",
											"validation"	=> "trim"
											),
	"url"				=> array (
											"grid"				=> "<a target=\"_blank\" href=\"%s\">%s</a>",
											"form"				=> "",
											"validation"	=> "trim|prep_url"
											),
	"email"			=> array (
											"grid"				=> "<a href=\"mailto:%s\">%s</a>",
											"form"				=> "",
											"validation"	=> "trim|valid_email"
											),
	"file"			=> array (
											"grid"				=> "%s",
											"form"				=> "upload",
											"validation"	=> "trim"
											),
	"mp3"				=> array (
											"grid"				=> "%s",
											"form"				=> "upload",
											"validation"	=> "trim"
											),
	"mov"				=> array (
											"grid"				=> "%s",
											"form"				=> "upload",
											"validation"	=> "trim"
											),
	"img"				=> array (
											"grid"				=> "<img src=\"#IMG_MAP#/%s\" alt=\"%s\" /><p class=\"img_text\">%s</p>",
											"form"				=> "upload",
											"validation"	=> "trim"
											),
	"dat"				=> array (
											"grid"				=> "%s",
											"form"				=> "date",
											"validation"	=> "function_today"
											),
	"date"			=> array (
											"grid"				=> "%s",
											"form"				=> "date",
											"validation"	=> "function_today"
											),

	"tme"				=> array (
											"grid"				=> "%s",
											"form"				=> "datetime",
											"validation"	=> ""
											),
	"datetime"	=> array (
											"grid"				=> "%s",
											"form"				=> "datetime",
											"validation"	=> ""
											),
	"dtm"				=> array (
											"grid"				=> "%s",
											"form"				=> "datetime",
											"validation"	=> ""
											),
	"time"			=> array (
											"grid"				=> "%s",
											"form"				=> "time",
											"validation"	=> ""
											),
	"int"				=> array (
											"grid"				=> "%s",
											"form"				=> "",
											"validation"	=> "trim|integer"
											),
	"dec"				=> array (
											"grid"				=> "%s",
											"form"				=> "",
											"validation"	=> "trim|numeric"
											),
	"ip"				=> array (
											"grid"				=> "%s",
											"form"				=> "",
											"validation"	=> "trim|valid_ip"
											),
	"rgb"				=> array (
											"grid"				=> "<div class=\"rgb\" style=\"background-color:%s;\" title=\"%s\"><span class=\"hide\">%s</span></div>",
											"form"				=> "",
											"validation"	=> "trim|valid_rgb"
											),
	"b"					=> array (
											"grid"				=> "function_boolean",
											"form"				=> "checkbox",
											"validation"	=> ""
										),
	"is"					=> array (
											"grid"				=> "function_boolean",
											"form"				=> "checkbox",
											"validation"	=> ""
										)
		);




if (file_exists(SITEPATH."config/flexyadmin_config.php")) require_once(SITEPATH."config/flexyadmin_config.php");


/* End of file flexyadmin_config.php */
/* Location: ./system/application/config/flexyadmin_config.php */
