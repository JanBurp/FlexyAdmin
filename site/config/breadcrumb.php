<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

$config['__return']='site';


/**
 * Voeg home element toe, deze wordt automatisch vanuit het menu opgehaald
 */
$config['include_home'] = TRUE;


/**
 * Veld in de menu tabel waar de titel uit gehaald moet worden
 */
$config['title_field']  = 'str_title';



/* End of file breadcrumb.php */
/* Location: ./system/application/config/breadcrumb.php */