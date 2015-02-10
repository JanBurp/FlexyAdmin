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
$route['(?!__api|admin|rss|file|_cronjob|_unittest|_ajax|_api)(.*)'] = "";

/**
 * CI unit test
 */
$route['_unittest'] = "ciunit_controller/index";
$route['_unittest/(.*)'] = "ciunit_controller/index/$1";

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
 * This route makes a nice download path and a serve (restricted) path
 */
$route['file/serve/(.*)/(.*)']    = "file/file/serve/$1/$2";
$route['file/download/(.*)/(.*)'] = "file/file/download/$1/$2";
$route['file/(.*)/(.*)']          = "file/file/download/$1/$2";

/**
 * This routing reroutes plugin calls
 */
$route['admin/plugin/(.+)'] = "admin/plugin_controller/call/$1";

/**
 * This routing reroutes help subpages
 */
$route['admin/help/(.+)'] = "admin/help/index/$1";


// Reserved routes

$route['default_controller'] = "Main";


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */