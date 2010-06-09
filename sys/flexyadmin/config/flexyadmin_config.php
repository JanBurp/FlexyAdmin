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
 * @filesource
 */

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
 */

$config['PHP_version']						= substr(phpversion(),0,1);
$config['LOCAL']									= IS_LOCALHOST;

// Directories
$config['SITE']										= 'site';
$config['ADMINASSETS']						= "sys/flexyadmin/assets/";
$config['ASSETS']									= $config['SITE'].'/assets/';
$config['THUMBCACHE']							= $config['ASSETS']."_thumbcache/";
$config['STATS']									= $config['SITE'].'/stats/';
$config['PLUGINS']								= $config['SITE'].'/plugins';
$config['BULKUPLOAD']							= 'bulk_upload';

$config['THUMBSIZE']							= array(100,100);

$config['PLUGIN_ORDER']						= array('first'=>array('uri','links','striptags'),
																					'last' =>array('automenu'));


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
$config['API_login']								= "/admin/user/login/";
$config['API_logout']								= "/admin/logout/";

$config['API_view_order']						= "/admin/show/order/";
$config['API_view_grid']						= "/admin/show/grid/";
$config['API_view_tree']						= "/admin/show/tree/";
$config['API_view_form']						= "/admin/show/form/";

$config['API_filemanager']					= "/admin/filemanager/";
$config['API_filemanager_set_view']	= "/admin/filemanager/setview";
$config['API_filemanager_view']			= "/admin/filemanager/show/";
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
$config['CFG_editor']									= "editor";
$config['CFG_media_info']							= "media_info";
$config['CFG_img_info']								= "img_info";

$config['LOG_login']									= "login";
$config['LOG_stats']									= "stats";


$config['FILE_types_img']							= array('jpg','jpeg','gif','png');
$config['FILE_types_mp3']							= array('mp3','wav','wma');
$config['FILE_types_flash']						= array('swf','flv');
$config['FILE_types_movies'] 					= array('mov');
$config['FILE_types_pdf']							= array('pdf');
$config['FILE_types_docs']						= array('doc','docx','odt');
$config['FILE_types_xls']							= array('xls','xlsx','ods');

$config['CFG_table']									= "table_info";
$config['CFG_table_name']							= "table";
$config['CFG_table_ui_name']					= "str_ui_name";

$config['CFG_field']									= "field_info";
$config['CFG_field_name']							= "field_field";
$config['CFG_field_ui_name']					= "str_ui_name";


/*
|--------------------------------------------------------------------------
| CFG_table_info AND cfg_field_info for cfg_tables
|--------------------------------------------------------------------------
|
*/


$config["CFG_"]=array(
	"cfg_table_info" => array(
		"cfg_configurations"=>array(
			"order"=>'100',
			"table"=>'cfg_configurations',
			"b_single_row"=>'1',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>'<p>Common settings</p>'
			),
		"cfg_auto_menu"=>array(
			"order"=>'101',
			"table"=>'cfg_auto_menu',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>'<p>Settings for creating an automatic menu. Needs a res_table table.</p>'
			),
		"cfg_admin_menu"=>array(
			"order"=>'102',
			"table"=>'cfg_admin_menu',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>'<p>Change your admin menu here.</p>'
			),
		"cfg_media_info"=>array(
			"order"=>'103',
			"table"=>'cfg_media_info',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>'<p>Settings for Files.</p>'
			),
		"cfg_media_files"=>array(
			"order"=>'104',
			"table"=>'cfg_media_files',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>''
			),
		"cfg_img_info"=>array(
			"order"=>'105',
			"table"=>'cfg_img_info',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>'<p>Settings for image resizing after uploading.</p>'
			),
		"cfg_table_info"=>array(
			"order"=>'107',
			"table"=>'cfg_table_info',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>''
			),
		"cfg_field_info"=>array(
			"order"=>'108',
			"table"=>'cfg_field_info',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'field_field',
			"txt_help"=>''
			),
		"cfg_editor"=>array(
			"order"=>'109',
			"table"=>'cfg_editor',
			"b_single_row"=>'1',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>''
			),
		"cfg_rss"=>array(
			"order"=>'110',
			"table"=>'cfg_rss',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'id',
			"txt_help"=>''
			),
		"cfg_plugins"=>array(
			"order"=>'115',
			"table"=>'cfg_plugins',
			"b_single_row"=>'',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>''
			),
		"cfg_users"=>array(
			"order"=>'120',
			"table"=>'cfg_users',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'1',
			"str_abstract_fields"=>'str_user_name',
			"str_order_by"=>'id',
			"txt_help"=>''
			),
		"cfg_rights"=>array(
			"order"=>'121',
			"table"=>'cfg_rights',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_grid_add_many"=>'0',
			"str_abstract_fields"=>'str_name',
			"str_order_by"=>'id',
			"txt_help"=>'<p>Create groups of rights here. For use in Users.</p>'
			)
		
	),
	"cfg_field_info" => array(
		"cfg_configurations.key"=>array(
			"field"=>'cfg_configurations.key',
			"str_ui_name"=>'License Key',
			"b_show_in_grid"=>'0',
			"b_show_in_form"=>'1',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>'<p>Enter your license for FlexyAdmin here.</p>'
			),
		"cfg_admin_menu.str_type"=>array(
			"field"=>'cfg_admin_menu.str_type',
			"str_ui_name"=>'',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'api|tools|table|all_tbl_tables|all_cfg_tables|all_res_tables|media|all_media|seperator',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_auto_menu.str_type"=>array(
			"field"=>'cfg_auto_menu.str_type',
			"str_ui_name"=>'',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'from menu table|from category table|from table group by category',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_editor.str_class"=>array(
			"field"=>'cfg_editor.class',
			"str_ui_name"=>'Editor size',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'normal|wide|big',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
			"cfg_editor.table"=>array(
			"field"=>'cfg_editor.table',
			"str_ui_name"=>'Links Table',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_field_info.str_validation_rules"=>array(
			"field"=>'cfg_field_info.str_validation_rules',
			"str_ui_name"=>'',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'|required|min_length[]|max_length[]|exact_length[]|alpha|alpha_numeric|alpha_dash|numeric|integer|is_natural|is_natural_no_zero|valid_email|valid_emails|callback_valid_ip|callback_valid_rgb|prep_url',
			"b_multi_options"=>'1',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_field_info.str_validation_parameters"=>array(
			"field"=>'cfg_field_info.str_validation_parameters',
			"str_ui_name"=>'Val. Parameters',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'',
			"b_multi_options"=>'1',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_table_info.str_form_many_type"=>array(
			"field"=>'cfg_table_info.str_form_many_type',
			"str_ui_name"=>'',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'dropdown|ordered_list|subfields',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_media_info.str_order"=>array(
			"field"=>'cfg_media_info.str_order',
			"str_ui_name"=>'',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'name|_name|rawdate|_rawdate|type|_type|size|_size|width|_width|height|_height',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_media_info.str_type"=>array(
			"field"=>'cfg_media_info.str_type',
			"str_ui_name"=>'',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'image|flash|doc|pdf|other|all',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_media_info.str_autofill"=>array(
			"field"=>'cfg_media_info.str_autofill',
			"str_ui_name"=>'',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'|single upload|bulk upload|both',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_img_info.fields"=>array(
			"field"=>'cfg_img_info.fields',
			"str_ui_name"=>'Auto fill fields',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_rights.rights"=>array(
			"field"=>'cfg_rights.rights',
			"str_ui_name"=>'Rights for',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_users.str_filemanager_view"=>array(
			"field"=>'cfg_users.str_filemanager_view',
			"str_ui_name"=>'',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'icons|list|detailed',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
			),
		"cfg_users.str_language"=>array(
			"field"=>'cfg_users.str_language',
			"str_ui_name"=>'',
			"b_show_in_grid"=>'1',
			"b_show_in_form"=>'1',
			"str_options"=>'nl|en',
			"b_multi_options"=>'0',
			"str_overrule_prefix"=>'',
			"str_validation_rules"=>'',
			"txt_help"=>''
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

$config['PRIMARY_key']							= "id";
$config['FOREIGN_key_format']				= "/^id_.*/";		// regexpression used by preg_match

$config['ORDER_field_name']					= "order";
$config['ORDER_decimals']						= 3;
$config['ORDER_default_fields']			= array( "order","dat DESC","tme DESC","str","id");

$config['ABSTRACT_field_name']			= "abstract";
$config['ABSTRACT_field_pre_types']	= array("str","url","img","dat","tme","int");
$config['ABSTRACT_field_types']			= array("varchar","int","date");
$config['ABSTRACT_field_max']				= 2;
$config['ABSTRACT_field_split']			= " | ";

$config['URI_field_pre_types']			= array("str","url","img","dat","tme","int");



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
												"validation"	=> "trim|integer|required",
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
	"str"				=> array (
											"grid"				=> "%s",
											"form"				=> ""
											),
	"stx"				=> array (
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
											"validation"	=> "trim|required"
										),
	"gpw"				=> array (
											"grid"				=> "***",
											"form"				=> "password",
											"validation"	=> "trim|required"
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
	"tme"				=> array (
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
											"validation"	=> "trim|callback_valid_ip"
											),
	"rgb"				=> array (
											"grid"				=> "<div class=\"rgb\" style=\"background-color:%s;\" title=\"%s\"><span class=\"hide\">%s</span></div>",
											"form"				=> "",
											"validation"	=> "trim|callback_valid_rgb"
											),
	"b"					=> array (
											"grid"				=> "function_boolean",
											"form"				=> "checkbox",
											"validation"	=> ""
										)
		);


/* End of file flexyadmin_config.php */
/* Location: ./system/application/config/flexyadmin_config.php */
