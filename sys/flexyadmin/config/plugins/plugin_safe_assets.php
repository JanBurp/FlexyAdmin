<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Plugin methods:
| Set the names of the methods you use when FlexyAdmin want to call you
| If empty or commented, FlexyAdmin doesn't call them.
|--------------------------------------------------------------------------
|
*/

$config['admin_api_method']    = '_admin_api';
$config['home_method']         = '_admin_homepage';
$config['logout_method']       = '_admin_logout';

/*
|--------------------------------------------------------------------------
| Add auto .htaccess or not
|--------------------------------------------------------------------------
|
*/

$config['create_htaccess'] = !SAFE_INSTALL;


/*
|--------------------------------------------------------------------------
| Possible filetypes per directory
|--------------------------------------------------------------------------
|
*/

$config['file_types'] = array(
	SITEPATH.'stats'				      => 'xml',
	SITEPATH.'assets'							=> 'css|img|js',
	SITEPATH.'assets/_thumbcache'	=> 'jpg|jpeg|gif|png|tiff|cur',
	SITEPATH.'assets/css'					=> 'css|htc|php|eot|svg|ttf|woff|otf|less|map',
	SITEPATH.'assets/img'					=> 'jpg|jpeg|gif|png|tiff|cur|ico|swf|flv', 
	SITEPATH.'assets/js'					=> 'js|css|html|swf|jpg|jpeg|gif|png|tiff|cur|map',
  SITEPATH.'assets/fonts'       => 'eot|svg|ttf|woff'
);


/**
 * Actions at logout
 */
$config['logout_actions'] = array(
  'cleanup_captha' => true,
  'clean_all'      => !SAFE_INSTALL,
);



?>
