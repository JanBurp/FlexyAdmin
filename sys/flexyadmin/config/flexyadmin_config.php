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

$config['ASSETS']									= "site/assets/";
$config['ADMINASSETS']						= "sys/flexyadmin/assets/";

/*
|--------------------------------------------------------------------------
| API calls
|--------------------------------------------------------------------------
|
| URI to FlexyAdmin controllers.
|
*/
$config['API_home']								= "/admin/";
$config['API_user']								= "/admin/show/user/";
$config['API_login']							= "/admin/user/login/";
$config['API_logout']							= "/admin/user/logout/";

$config['API_view_order']					= "/admin/show/order/";
$config['API_view_grid']					= "/admin/show/grid/";
$config['API_view_tree']					= "/admin/show/tree/";
$config['API_view_form']					= "/admin/show/form/";

$config['API_filemanager']					= "/admin/filemanager/";
$config['API_filemanager_set_view']	= "/admin/filemanager/setview";
$config['API_filemanager_view']			= "/admin/filemanager/show/";
$config['API_filemanager_delete']		= "/admin/filemanager/delete/";
$config['API_filemanager_confirm']	= "/admin/filemanager/confirm/";
$config['API_filemanager_upload']		= "/admin/filemanager/upload/";

$config['API_filemanager_view_types']	= array("list","icons");
$config['FILES_thumb_path']						= "/thumb/";
$config['FILES_big_path']							= "/big/";

$config['API_popup_img']					= "/admin/popup/img/";

//$config['API_order_up']						= "/admin/order/up";
//$config['API_order_down']					= "/admin/order/down";

$config['API_delete']							= "/admin/edit/delete/";
$config['API_confirm']						= "/admin/edit/confirm/";

$config['AJAX']										= "/admin/ajax/";

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


$config['CFG_table']									= "table_info";
$config['CFG_table_name']							= "table";
$config['CFG_table_ui_name']					= "str_ui_name";

$config['CFG_field']									= "field_info";
$config['CFG_field_name']							= "field";
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
			"b_show_grid_with_joins"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>''
			),
		"cfg_media_info"=>array(
			"order"=>'101',
			"table"=>'cfg_media_info',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_show_grid_with_joins"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>''
			),
		"cfg_img_info"=>array(
			"order"=>'102',
			"table"=>'cfg_img_info',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_show_grid_with_joins"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>''
			),
		"cfg_editor"=>array(
			"order"=>'103',
			"table"=>'cfg_editor',
			"b_single_row"=>'1',
			"str_ui_name"=>'',
			"b_show_grid_with_joins"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>''
			),
		"cfg_table_info"=>array(
			"order"=>'104',
			"table"=>'cfg_table_info',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_show_grid_with_joins"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'',
			"txt_help"=>''
			),
		"cfg_field_info"=>array(
			"order"=>'105',
			"table"=>'cfg_field_info',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_show_grid_with_joins"=>'0',
			"str_abstract_fields"=>'',
			"str_order_by"=>'field',
			"txt_help"=>''
			),
		"cfg_users"=>array(
			"order"=>'106',
			"table"=>'cfg_users',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_show_grid_with_joins"=>'0',
			"str_abstract_fields"=>'str_user_name',
			"str_order_by"=>'id',
			"txt_help"=>''
			),
		"cfg_rights"=>array(
			"order"=>'108',
			"table"=>'cfg_rights',
			"b_single_row"=>'0',
			"str_ui_name"=>'',
			"b_show_grid_with_joins"=>'0',
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
			"str_options"=>'en|nl',
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
	"uri"				=> array(
												"grid"				=> "/%s",
												"form"				=> "hidden",
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
	"field"			=> array(
												"grid"				=> "%s",
												"form"				=> "function_dropdown_field",
												"validation"	=> "trim",
											),
	"fields"		=> array(
												"grid"				=> "%s",
												"form"				=> "function_dropdown_fields",
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
	"media"			=> array (
											"grid"				=> "function_dropdown_media",
											"form"				=> "function_dropdown_media",
											"validation"	=> "trim"
											),
	"medias"		=> array (
											"grid"				=> "function_dropdown_medias",
											"form"				=> "function_dropdown_medias",
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
