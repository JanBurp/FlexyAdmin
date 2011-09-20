<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Search settings
|--------------------------------------------------------------------------
|
| Set the database table and field names where the search must take place
|
*/

$config['search']['table']=get_menu_table();
$config['search']['title_field']='str_title';
$config['search']['text_field']='txt_text';
$config['search']['extra_fields']=array();


/*
|--------------------------------------------------------------------------
| Search result page
|--------------------------------------------------------------------------
|
*/

// Set a direct uri of a result page.
$config['search']['result_page_uri']='';

// Or set a test for a certain page which has...
// $config['search']['result_page_where']='';
$config['search']['result_page_where']=array('str_module','search');



/*
|--------------------------------------------------------------------------
| Search Term
|--------------------------------------------------------------------------
|
| Field in POST data that holds the search term
|
*/

$config['search']['search_term']='search';
$config['search']['empty_value']='zoeken';



/*
|--------------------------------------------------------------------------
| Result settings
|--------------------------------------------------------------------------
|
| result_max_type = CHARS | WORDS | LINES
|
*/

$config['search']['order_as_tree']=TRUE;				// only possible with menu tables.

$config['search']['result_max_type']='CHARS';
$config['search']['result_max_length']=0;
$config['search']['result_max_ellipses']='...';




/*
|--------------------------------------------------------------------------
| Pre Uri
|--------------------------------------------------------------------------
|
| Set a uri that will be added to the front of the results uri's
|
*/

$config['search']['pre_uri']='';



/* End of file config.php */
/* Location: ./system/application/config/config.php */
