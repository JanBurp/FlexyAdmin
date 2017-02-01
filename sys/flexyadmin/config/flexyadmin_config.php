<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin	Configuration
 *
 * Here you can find FlexyAdmin configuration settings that probably won't need adjustments.
 * The settings here are needed for internal use only. Do not change them if you don't know what
 * you're doing.
 *
 * @author			Jan den Besten
**/

$config['USE_OLD_DB']             = defined('PHPUNIT_TEST')?TRUE:FALSE;


$config['PROFILER']               = FALSE;

$config['PHP_version']						= substr(phpversion(),0,1);
$config['LOCAL']									= IS_LOCALHOST;
$config['IS_AJAX']								= IS_AJAX;
$config['AJAX_MODULE']						= IS_AJAX;
$config['IS_ADMIN']               = FALSE;
if (isset($_SERVER['PATH_INFO']) and strpos($_SERVER['PATH_INFO'],'admin')!==false) $config['IS_ADMIN'] = TRUE;
$config['LANGUAGES']							= array('nl','en','de','es','fr');
$config['MENU_TABLES']						= array('res_menu_result','tbl_menu');

// Directories
$config['SITE']										= SITEPATH;
$config['SYS']										= 'sys/';
$config['ADMINASSETS']						= $config['SYS'].'flexyadmin/assets/';
$config['PUBLICASSETS']						= $config['SITE'].'assets/';
$config['ASSETS']									= $config['PUBLICASSETS'];
$config['ASSETSFOLDER']						= $config['ASSETS'];
$config['THUMBCACHE']							= $config['ASSETS']."_thumbcache/";
$config['STATS']									= $config['SITE'].'stats/';
$config['PLUGINS']								= $config['SITE'].'plugins';
if (SAFE_INSTALL) {
  $config['SYS']				 = '../sys/';
  $config['ADMINASSETS'] = "_admin_assets/";
  $config['PUBLICASSETS']= 'assets/';
  $config['ASSETS']      = '_media/';
  $config['ASSETSFOLDER']= SITEPATH.'assets/';
  $config['THUMBCACHE']  = $config['ASSETSFOLDER']."_thumbcache/";
  $config['STATS']       = $config['SITE'].'stats/';
  $config['PLUGINS']     = $config['SITE'].'plugins';
}

$config['THUMBSIZE']							= array(200,200);
$config['IGNORE_MIME']						= FALSE;



/*
| UI settings
*/

$config['FORM_NICE_DROPDOWNS']		= TRUE;
$config['MULTIPLE_UPLOAD']		    = TRUE;
$config['PAGINATION']             = TRUE;
$config['GRID_EDIT']              = TRUE;
$config['GRID_WHERE']             = FALSE;
$config['ADMIN_LANGUAGES']        = array('nl','en');



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
$config['FORBIDDEN_URIS']           = array("site","sys","admin","rss","file",'offset','_cronjob','_unittest');

  
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

// $config['API_stats']								= '/admin/stats/show';
$config['API_info']									= '/admin/info/';

$config['AJAX']											= "/admin/ajax/";

$config['FILES_view_types']					= array("list","thumbs");


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
| UNIX timestamps
|--------------------------------------------------------------------------
|
*/
define('TIME_SECOND', 1);
define('TIME_MINUTE', TIME_SECOND * 60);
define('TIME_HOUR',   TIME_MINUTE * 60);
define('TIME_DAY',    TIME_HOUR * 24);
define('TIME_WEEK',   TIME_DAY * 7);
define('TIME_4WEEKS', TIME_WEEK * 4);
define('TIME_MONTH',  TIME_DAY * 30);
define('TIME_YEAR',   TIME_DAY * 365);



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

$config['LOG_activity']							  = "activity";
$config['LOG_stats']									= "stats";

$config['FILE_types_forbidden']				= array('php','php3','php4','phtml','pl','py','jsp','asp','shtml','sh','cgi','js','exe','dmg','app');
$config['FILE_types_img']							= array('jpg','jpeg','gif','png','tiff','cur','tif','tiff');
$config['FILE_types_mp3']							= array('mp3','wav','wma','aiff','ogg');
$config['FILE_types_sound']					  = $config['FILE_types_mp3'];
$config['FILE_types_flash']						= array('swf','flv');
$config['FILE_types_movies'] 					= array('mov','mp4','wmv');
$config['FILE_types_pdf']							= array('pdf');
$config['FILE_types_docs']						= array('doc','docx','odt');
$config['FILE_types_xls']							= array('xls','xlsx','ods');

$config['CFG_table']									= "table_info";
$config['CFG_table_name']							= "table";
$config['CFG_field']									= "field_info";
$config['CFG_field_name']							= "field_field";


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
$config['PASSWORD_field_types']     = array('gpw','pwd');
$config['ALWAYS_SHOW_FIELDS']       = array(PRIMARY_KEY,'uri','order','self_parent');

$config['ORDER_field_name']					= "order";
$config['ORDER_decimals']						= 3;
$config['ORDER_default_fields']			= array( "order","dat DESC",'date DESC',"tme DESC","str","id");

$config['NON_EDITABLE_FIELDS']      = array(PRIMARY_KEY,'uri','order','self_parent');
$config['INCOMPLETE_DATA_TYPES']    = array('txt','stx','md');

$config['ABSTRACT_field_name']			= "abstract";
$config['ABSTRACT_field_pre_types']	= array("str","url","img","dat","tme","time","int");
$config['ABSTRACT_field_types']			= array("varchar","int","date",'datetime','time');
$config['ABSTRACT_field_max']				= 2;
$config['ABSTRACT_field_split']			= " | ";

$config['URI_field_pre_types']			= array("str","url","dat","date","datetime","tme",'time',"int","media");
$config['DATE_fields_pre']			    = array("dat","date","tme","datetime",);



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
$config['FIELDS_bool_fields']		  = array( "b", "is", "has" );
$config['FIELDS_number_fields']		= array( 'id', 'int', 'dec','order','self' );


require_once(APPPATH."config/schemaform.php");


if (file_exists(SITEPATH."config/flexyadmin_config.php")) require_once(SITEPATH."config/flexyadmin_config.php");


/* End of file flexyadmin_config.php */
/* Location: ./system/application/config/flexyadmin_config.php */
