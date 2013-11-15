<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Files belonging to this module/plugin
| (views,config and language files with same name not needed)
|--------------------------------------------------------------------------
*/

$config['_files']=array(
  'db/add_google_map.sql',
  'site/views/google_map_popup.php'
);





/*
|--------------------------------------------------------------------------
| Map instellingen
|--------------------------------------------------------------------------
| Je kunt meerdere omvangen weergeven, standaard bestaan er twee
*/

$config['normal']['type']='map';
$config['normal']['width']=575;
$config['normal']['height']=500;
$config['normal']['zoomlevel']=8;

$config['small']['type']='map';
$config['small']['width']=200;
$config['small']['height']=200;
$config['small']['zoomlevel']=6;



/*
|--------------------------------------------------------------------------
| Eén of meer adressen
|--------------------------------------------------------------------------
|
| Bij één adres moet het adres staan in tbl_site.str_adress
| Bij meerdere in een andere tabel, zie hieronder
*/
$config['multiple'] = TRUE;

/*
|--------------------------------------------------------------------------
| Adres tabel
|--------------------------------------------------------------------------
|
*/
$config['table'] = 'tbl_gm_addresses';




/* End of file config.php */
/* Location: ./system/application/config/config.php */
