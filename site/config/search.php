<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Search settings
|--------------------------------------------------------------------------
|
| Set the database table and field names where the search must take place
|
*/

$config['table']=get_menu_table();
$config['title_field']='str_title';
$config['text_field']='txt_text';
$config['extra_fields']=array();


/*
|--------------------------------------------------------------------------
| Search result page
|--------------------------------------------------------------------------
|
*/

// Set a direct uri of a result page.
$config['result_page_uri']='';

// Or set a test for a certain page which has...
// $config['result_page_where']='';
$config['result_page_where']=array('str_module','search');



/*
|--------------------------------------------------------------------------
| Result settings
|--------------------------------------------------------------------------
|
| result_max_type = CHARS | WORDS | LINES
|
*/

$config['order_as_tree']=TRUE;				// only possible with menu tables.

$config['result_max_type']='CHARS';
$config['result_max_length']=0;
$config['result_max_ellipses']='...';




/*
|--------------------------------------------------------------------------
| Pre Uri
|--------------------------------------------------------------------------
|
| Set a uri that will be added to the front of the results uri's
|
*/

$config['pre_uri']='';



/* End of file config.php */
/* Location: ./system/application/config/config.php */
