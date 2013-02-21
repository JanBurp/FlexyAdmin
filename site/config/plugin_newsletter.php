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

$config['intro_length']          = 300;
$config['allowed_tags']          = '<br/><strong><italic><em><b><a><p><h1><h2><h3><h4>';

$config['send_to_address_table'] = 'tbl_newsletter_addresses';
$config['send_to_address_field'] = 'email_email';
$config['send_to_name_field']    = 'str_name';

$config['content_table']         = 'tbl_teksten'; // If empty then the standard menu table will be choosen

$config['send_one_by_one']       = TRUE; // SET this to TRUE if Your provider can't send bulk mail with bcc

?>