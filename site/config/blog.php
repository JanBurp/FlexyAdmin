<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Blog table settings
|--------------------------------------------------------------------------
|
| Set the database table and field names.
|
*/

$config['blog']['table']='tbl_blog';

$config['blog']['field_date']='dat_date';
$config['blog']['field_title']='str_title';
$config['blog']['field_text']='txt_text';



/*
|--------------------------------------------------------------------------
| Blog comments
|--------------------------------------------------------------------------
|
| Set comment settings
|
*/


$config['blog']['comments']=FALSE;

// Override comments settings
// $config['blog']['comments']= array(
// 	'table'		=> 'tbl_blog_comments',
// 	'key_id'	=> 'id_blog'
// 	);




/* End of file config.php */
/* Location: ./system/application/config/config.php */
