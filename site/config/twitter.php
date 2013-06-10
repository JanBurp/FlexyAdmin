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

/* End of file config.php */
/* Location: ./system/application/config/config.php */
