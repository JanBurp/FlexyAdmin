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
 * This routing makes sure all uri's go to the frontend site, except soms sysyem uri's
 */
$route['(?!__api|admin|rss|file|_cronjob|_unittest)(.*)'] = "";

/**
 * CI unit test
 */
$route['_unittest'] = "ciunit_controller/index";
$route['_unittest/(.*)'] = "ciunit_controller/index/$1";


/**
 * Api routing (controllers/api/class/args)
 */
$route['__api/([^/]*)/(.*)'] = '__api/$1/$2';


/**
 * This route makes a nice download path
 */
$route['file/(.*)/(.*)'] = "file/download/this/$1/$2";

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