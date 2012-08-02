<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 *
 * @author Jan den Besten
 * @package FlexyAdmin_comments
 **/

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

$config['key_id']='id_menu';
// $config['key_id']='id_blog';


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


/* End of file config.php */
/* Location: ./system/application/config/config.php */
