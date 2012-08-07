<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Database instellingen voor zoeken
|--------------------------------------------------------------------------
|
| Stel hier de tabel en de velden van die tabel in waarin wordt gezocht
|
*/
$config['table']=get_menu_table();
$config['title_field']='str_title'; // Titelveld: wordt in gezocht en gebruikt als titel voor het resultaat
$config['text_field']='txt_text';   // Tekstveld: wordt in gezocht
$config['extra_fields']=array();    // Geef hier extra velden waarin gezocht moet worden


/*
|--------------------------------------------------------------------------
| De pagina waar het zoekresultaat terechtkomt
|--------------------------------------------------------------------------
|
*/
// Hier kun je een uri instellen
$config['result_page_uri']='';
// Of laat de module een pagina zoeken (veld, waarde)
$config['result_page_where']=array('str_module','search');


/*
|--------------------------------------------------------------------------
| Resultaat
|--------------------------------------------------------------------------
|
| Hier bepaal je hoe het zoekresultaat eruit komt te zien
*/
$config['order_as_tree']=TRUE;				    // Gesorteerd als een menu, kan alleen als gezocht wordt in een menu tabel
$config['show_full_title']=FALSE;			    // Laat titel helemaal zien

$config['group_result_by_uris']=array();  // Je kunt het resultaat groeperen in groepen met dezelfde uri-parts

$config['result_max_length']=0;           // Lengte van tekst onder de titel van de zoekresultaten
$config['result_max_type']='CHARS';       // Lengte wordt geteld als: CHARS | WORDS | LINES
$config['result_max_ellipses']='...';     // Als de getoonde tekst langer is: stel hier het eind van de tekst in.


/*
|--------------------------------------------------------------------------
| Pre Uri
|--------------------------------------------------------------------------
|
| Je kunt hier een uri toevoegen aan alle zoekresultaten (als bijvoorbeeld in tbl_blogs gezocht wordt dan kun je hier instellen wat de pagina is waar de blog module staat)
|
*/
$config['pre_uri']='';



/* End of file config.php */
/* Location: ./system/application/config/config.php */
