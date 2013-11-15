<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Files belonging to this module/plugin
| (views,config and language files with same name not needed)
|--------------------------------------------------------------------------
*/

$config['_files']=array(
  'db/add_comments.sql',
  'site/models/formaction_comments.php'
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

$config['__return']='page|site';


/*
|--------------------------------------------------------------------------
| Waar zijn de comments aan gekoppeld? (foreign key)
|--------------------------------------------------------------------------
|
| Dit veld verwijst naar de pagina waar een comment bij hoort.
| Standaard verwijst het naar 'tbl_menu'.
| Door dit aan te passen kun je comments koppelen aan een andere tabel (bijvoorbeeld alleen bij een blog).
| LET OP: Als je dit veld aanpast, dan moet je het veld in de database ook aanpassen!!
|    
| NB er wordt vanzelf rekening gehouden met samengesteld menu (res_menu_result).
*/

$config['key_id']='id_blog';


/*
|--------------------------------------------------------------------------
| Formulier config (voor module forms)
|--------------------------------------------------------------------------
*/

$config['form']= array(
  'model'             => 'formaction_comments.get_fields',
  'table'             => 'tbl_comments',
  'title'             => lang('comments_title'),
  'buttons'           => array( 'submit'=>array('submit'=>'submit', 'value'=>lang('comments_submit')) ),
  'validation_place'  => 'field',
  'check_for_spam'    => true,
  'formaction'        => array('formaction_comments'),
  '__return'          => ''
);



/*
|--------------------------------------------------------------------------
| Stuur mail als een comment is geplaatst
|--------------------------------------------------------------------------
*/

$config['mail_owner']=TRUE;           // Stuur mail naar site owner (mail adres in tbl_site)
$config['mail_others']=FALSE;         // Stuur mail naar alle mensen die comments hebben geplaatst op huidige pagina, zo blijven ze op de hoogte.


/*
|--------------------------------------------------------------------------
| Database instellingen
|--------------------------------------------------------------------------
|
| Mocht je wijzigingen aanbrengen in de comments tabel, zorg dan dat je hier de juiste koppelingen maakt.
| Voor gevorderden!
*/

$config['table']='tbl_comments';

$config['field_date']='tme_date';
$config['field_name']='str_name';
$config['field_email']='email_email';
$config['field_title']='str_title';
$config['field_text']='txt_text';

$config['field_spamscore']='int_spamscore';


/*
|--------------------------------------------------------------------------
| Date format
|--------------------------------------------------------------------------
|
| Uses php strftime()
*/

$config['date_format'] = '%A %e %b %Y %R';


/* End of file config.php */
/* Location: ./system/application/config/config.php */
