<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Blog database instellingen
|--------------------------------------------------------------------------
|
| Stel hier de naam van de tabel en de velden in
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
| Als TRUE dan heeft het blog ook comments, de module comments moet dan wel worden geladen
| Mocht de comments tabel aangepast zijn dan kun je daar hier ook instellen.
*/


$config['comments']=FALSE;

// Override comments settings
// $config['comments']= array(
// 	'table'		=> 'tbl_blog_comments',
// 	'key_id'	=> 'id_blog'
// 	);




/* End of file config.php */
/* Location: ./system/application/config/config.php */
