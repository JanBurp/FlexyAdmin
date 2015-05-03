<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Plugin methods:
| Set the names of the methods you use when FlexyAdmin want to call you
| If empty or commented, FlexyAdmin doesn't call them.
|--------------------------------------------------------------------------
|
*/

$config['admin_api_method'] = '_admin_api';
$config['logout_method'] = '_admin_logout';
$config['after_update_method'] = '_after_update';


/*
|--------------------------------------------------------------------------
| Plugin Update/Delete Triggers
| Here you need to set when the update and delete methods of you're plugin are called
|--------------------------------------------------------------------------
|
*/

$config['trigger'] = array(
	'tables' 					=> array('cfg_media_info')
);


/*
|--------------------------------------------------------------------------
| Add auto .htaccess or not
|--------------------------------------------------------------------------
|
*/

$config['create_htaccess'] = TRUE;


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
	SITEPATH.'assets/lists'				=> 'js',
	SITEPATH.'assets/css'					=> 'css|htc|php|eot|svg|ttf|woff|otf|less|map',
	SITEPATH.'assets/img'					=> 'jpg|jpeg|gif|png|tiff|cur|ico|swf|flv', 
	SITEPATH.'assets/js'					=> 'js|css|html|swf|jpg|jpeg|gif|png|tiff|cur|map',
  SITEPATH.'assets/fonts'       => 'eot|svg|ttf|woff'
);



?>