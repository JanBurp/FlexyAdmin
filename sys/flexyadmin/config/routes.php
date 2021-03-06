<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
*/


/**
 * This routing makes sure all uri's go to the frontend site, except soms system uri's
 */
$route['(?!__api|_rss|_media|_admin|_admin_assets|_cronjob|_unittest|_ajax|_api|_cli)(.*)'] = "";

/**
 * RSS feed(s)
 */
$route['_rss'] = 'rss/index';
$route['_rss/(.*)'] = 'rss/index/$1';


/**
 * Frontend Ajax testing
 */
$route['_ajax'] = "ajax/index";
$route['_ajax/(.*)'] = "ajax/index/$1";


/**
 * Api routing (controllers/api/class/args)
 */
$route['_api'] = 'api/index';
$route['_api/(.*)'] = 'api/index/$1';


/**
 * CLI routing
 */
$route['_cli'] = 'cli/index';
$route['_cli/(.*)'] = 'cli/index/$1';


/**
 * This route makes a nice download path and media path (which may serve files if they are restricted, but user has rights)
 */
$route['_media/download/(.*)/(.*)'] = "file/file/download/$1/$2";
$route['_media/serve/(.*)/(.*)']    = "file/file/serve/$1/$2";
$route['_media/thumb/(.*)/(.*)']    = "file/file/thumb/$1/$2";
$route['_media/(.*)/(.*)']          = "file/file/serve/$1/$2";
$route['_admin_assets/(.*)']   			= "file/file/admin_assets/$1";
$route['_admin_assets/(.*)/(.*)']   = "file/file/admin_assets/$1/$2";

/**
 * Editor Popup
 */
$route['_admin/load/editor'] 				= "_admin/editor";
$route['_admin/load/editor/image'] 	= "_admin/editor/image";


/**
 * This routing reroutes plugin calls
 */
$route['_admin/load/plugin']      = "_admin/plugin_controller";
$route['_admin/load/plugin/(.+)'] = "_admin/plugin_controller/call/$1";

/**
 * Update actions
 */
$route['_admin/update/(.+)'] = "_admin/update/index/$1";

/**
 * Other Admin Routes
 */
$route['_admin/login'] 				= "_admin/login";
$route['_admin/login/(.*)'] 	= "_admin/login/$1";

$route['_admin'] 								= "_admin";
$route['_admin/(.*)'] 					= "_admin";
$route['_admin/(.*)/(.*)'] 			= "_admin";
// $route['_admin/(.*)/(.*)/(*.)'] = "_admin";


// Reserved routes
$route['default_controller'] = "Main";


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */