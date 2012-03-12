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
$config['before_update_method'] = '_before_update';

/*
|--------------------------------------------------------------------------
| Plugin Update/Delete Triggers
| Here you need to set when the update and delete methods of you're plugin are called
|--------------------------------------------------------------------------
|
*/

$config['trigger'] = array(
  'tables'           => array('tbl_newsletters')
);



/*
|--------------------------------------------------------------------------
| Plugins specific config settings
|--------------------------------------------------------------------------
|
*/

$config['intro_length']  = 300;
$config['allowed_tags']  = '<br/><strong><italic><em><b><a><p><h1><h2><h3><h4>';

$config['send_to_address_table'] = 'tbl_newsletter_addresses';
$config['send_to_address_field'] = 'email_email';
$config['send_to_name_field']    = 'str_name';


// Wizards
$config['wizard_create'] = array( 'include_pages'  => array('label'=>lang('include_pages'),'method'=>'_create_include_pages'),
                                  'edit_text'      => array('label'=>lang('edit_text'),'method'=>'_create_edit_text'),
                                  'send_test'      => array('label'=>lang('send_test'),'method'=>'_create_send_test') );
$config['wizard_send']  = array(  'send_select'    => array('label'=>lang('send_select'),'method'=>'_send_select'),
                                  'send_it'        => array('label'=>lang('send_it'),'method'=>'_send_it') );



?>