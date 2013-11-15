<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Files belonging to this module/plugin
| (views,config and language files with same name not needed)
|--------------------------------------------------------------------------
*/

$config['_files']=array(
  'db/add_simple_blog.sql'
);


/*
|--------------------------------------------------------------------------
| Output routing of module
|--------------------------------------------------------------------------
|
| Stel hier in wat er met de return waarden van de module (methods) moet gebeuren:
|
| - Als er niets staat wordt het aan de pagina teruggegeven (zelfde als 'page')
| - 'page' - geeft de returnwaarde terug aan de pagina ($page)
| - 'site' - geeft de returnwaarde aan $this->site[module_naam.method]
| - Een combinatie is ook mogelijk, gescheiden door een pipe: 'page|site'
*/

$config['__return']='';
$config['__return.latest']='site';


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
| Date format
|--------------------------------------------------------------------------
|
| Uses php strftime()
*/

$config['date_format'] = '%A %e %b %Y';

/*
|--------------------------------------------------------------------------
| Read more...
|--------------------------------------------------------------------------
|
*/

$config['intro_length'] = 200;
$config['read_more'] = lang('read_more');


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
|
| Set $config['auto_pagination']	= TRUE; in site/config/config.php
|
| 0 - no pagination, else it is the items per page
*/

$config['pagination'] = 10;
$config['latest_items'] = 5;

/*
|--------------------------------------------------------------------------
| Blog comments
|--------------------------------------------------------------------------
|
| Als TRUE dan heeft het blog ook comments, de module comments moet dan wel worden geladen
| Mocht de comments tabel aangepast zijn dan kun je daar hier ook instellen.
*/


$config['comments']=TRUE;

// Override comments settings
// $config['comments']= array(
// 	'table'		=> 'tbl_blog_comments',
// 	'key_id'	=> 'id_blog'
// 	);




/* End of file config.php */
/* Location: ./system/application/config/config.php */
