<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Map instellingen
|--------------------------------------------------------------------------
|
*/
$config['type']='map';
$config['width']=350;
$config['height']=350;
$config['zoomlevel']=8;


/*
|--------------------------------------------------------------------------
| Eén of meer adressen
|--------------------------------------------------------------------------
|
| Bij één adres moet het adres staan in tbl_site.str_adress
| Bij meerdere in een andere tabel, zie hieronder
*/
$config['multiple'] = FALSE;

/*
|--------------------------------------------------------------------------
| Adres tabel
|--------------------------------------------------------------------------
|
*/
$config['table'] = 'tbl_gm_addresses';




/* End of file config.php */
/* Location: ./system/application/config/config.php */
