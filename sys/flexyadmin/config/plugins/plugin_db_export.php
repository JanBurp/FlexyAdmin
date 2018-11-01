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


/*
|--------------------------------------------------------------------------
| tables
| Set the tables which are possible choices.
| If empty the possible tables will be the tables the user has rights for
|--------------------------------------------------------------------------
|
*/

$config['tables']=array();


/*
|--------------------------------------------------------------------------
| Add foreign/many data
|--------------------------------------------------------------------------
|
*/

$config['add_foreigns']=FALSE;
$config['add_foreigns_as_abstracts']=FALSE;
$config['add_many']=FALSE;



/*
|--------------------------------------------------------------------------
| Use UI names instead of database names
|--------------------------------------------------------------------------
|
*/

$config['use_ui_names']=FALSE;


?>