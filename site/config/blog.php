<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Blog table settings
|--------------------------------------------------------------------------
|
| Set the database table and field names.
|
*/

$config['table']='tbl_blog';

$config['field_date']='dat_date';
$config['field_title']='str_title';
$config['field_text']='txt_text';



/*
|--------------------------------------------------------------------------
| Blog comments
|--------------------------------------------------------------------------
|
| Set comment settings
|
*/


$config['comments']=FALSE;

// Override comments settings
// $config['comments']= array(
// 	'table'		=> 'tbl_blog_comments',
// 	'key_id'	=> 'id_blog'
// 	);




/* End of file config.php */
/* Location: ./system/application/config/config.php */
