<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['admin_api_method']        = '_admin_api';
$config['logout_method']        = '_admin_logout';
$config['ajax_api_method']      = '_ajax_api';
$config['after_update_method']  = '_after_update';
$config['after_delete_method']  = '_after_delete';


/*
|--------------------------------------------------------------------------
| Plugin Update/Delete Triggers
| Here you need to set when the update and delete methods of you're plugin are called
|--------------------------------------------------------------------------
|
*/

$config['trigger'] = array(
  // 'existing_tables' => array('res_tags'),
  'tables'           => array('tbl_items','tbl_categorie'),
  // 'field_types'      => array('uri','txt'),
  // 'fields'          => array('uri','str_tags','txt_text')
);



$config['limit'] = 1;

// pre uri's
$config['tbl_categorie']='categorie-';


?>
