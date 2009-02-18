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
$config['LOCAL']									= (strpos("localhost",$_SERVER['HTTP_HOST'])!==FALSE);

$config['ASSETS']									= "site/assets/";

/*
|--------------------------------------------------------------------------
| API calls
|--------------------------------------------------------------------------
|
| URI to FlexyAdmin controllers.
|
*/
$config['API_home']								= "/admin/";
$config['API_login']							= "/admin/user/login/";
$config['API_logout']							= "/admin/user/logout/";

$config['API_view_order']					= "/admin/show/order/";
$config['API_view_grid']					= "/admin/show/grid/";
$config['API_view_form']					= "/admin/show/form/";

//$config['API_view_tree']					= "/admin/view/tree/";

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
| Config Tables and items
|--------------------------------------------------------------------------
|
| Names of configuration fields and tables in database.
|
*/

$config['MENU_excluded']							= array('cfg_sessions');

$config['CFG_table_prefix']						= "cfg";
$config['TABLE_prefix']								= "tbl";
$config['REL_table_prefix']						= "rel";
$config['REL_table_split']						= "__";



$config['CFG_configurations']					= "configurations";
$config['CFG_users']									= "users";
$config['CFG_login_log']							= "login_log";
$config['CFG_editor']									= "editor";
$config['CFG_media_info']							= "media_info";
$config['CFG_img_info']								= "img_info";

$config['FILE_types_img']							= array('jpg','jpeg','gif','png');
$config['FILE_types_mp3']							= array('mp3','wav','wma');
$config['FILE_types_flash']						= array('swf','flv');
$config['FILE_types_movies'] 					= array('mov');


$config['CFG_table']									= "table_info";
$config['CFG_table_name']							= "table";
$config['CFG_table_ui_name']					= "str_ui_name";

$config['CFG_field']									= "field_info";
$config['CFG_field_name']							= "field";
$config['CFG_field_ui_name']					= "str_ui_name";


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
												"form"				=> "hidden",
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
	"str"				=> array (
											"grid"				=> "%s",
											"form"				=> ""
											),
	"stx"				=> array (
											"grid"				=> "%s",
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
