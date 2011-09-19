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
$config['search']['result_page_where']='';
// $config['search']['result_page_where']='str_module="search"';

// Or, if both are empty, the search result is just added to current page.


/*
|--------------------------------------------------------------------------
| Filter by Uri
|--------------------------------------------------------------------------
|
| If TRUE only results that have the same first uri part as the post page, and that uri is also used for the result page
|
*/

$config['search']['filter_by_uri']=TRUE;




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

$config['search']['result_max_type']='LINES';
$config['search']['result_max_length']=2;
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
