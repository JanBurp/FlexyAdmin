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
$config['after_update_method'] = '_after_update';
$config['after_delete_method'] = '_after_delete';


/*
|--------------------------------------------------------------------------
| Plugin Update/Delete Triggers
| Here you need to set when the update and delete methods of you're plugin are called
|--------------------------------------------------------------------------
|
*/

$config['trigger'] = array(
	'field_types'			=> array('uri','url'),
	'fields'					=> array('uri','self_parent')
);


?>
