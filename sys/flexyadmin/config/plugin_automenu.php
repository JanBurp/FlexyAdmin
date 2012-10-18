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
$config['before_grid_method'] = '_before_grid';
$config['after_update_method'] = '_after_update';
$config['after_delete_method'] = '_after_delete';



/*
|--------------------------------------------------------------------------
| Dynamic trigger method
| If the above trigger settings needs to be dynamically set, set a method here
| The plugin will allways be loaded!!
|--------------------------------------------------------------------------
|
*/

$config['trigger_method'] = '_trigger';



/*
|--------------------------------------------------------------------------
| Plugin automenu config
|--------------------------------------------------------------------------
|
*/
$config['module_field']='str_module';
$config['update_fields']=array('id','order','self_parent','uri',$config['module_field']);


?>