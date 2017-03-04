<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['table']      = 'tbl_menu';
$config['result_key'] = 'full_uri';

$config['field_info']['str_title']['validation'] = 'required';

$config['options'] = array( 
	'self_parent'  => array( 'special' => 'self_parent' ), 
	'medias_fotos' => array(
    'model'    => 'media',
    'path'     => 'pictures',
    'multiple' => true
  ), 
  'str_module'  => array(
    'data' => array(
      ''              =>'',
      'forms.contact' =>'forms.contact',
      'example'       =>'example'
    ),
  ),
);


$config['grid_set'] = array(
  'fields' => array('id','order','self_parent','uri','str_title','txt_text','medias_fotos','b_visible','str_module'),
);

$config['form_set'] = array(
  'fieldsets' => array(
    'tbl_menu' => array('id','order','self_parent','uri','str_title','txt_text','medias_fotos','b_visible'),
    'Extra'    => array('str_module','stx_description','str_keywords'),
  ),
);


/**
 * Stel hier in of er een samengesteld menu moet komen, er zijn verschillende opties:
 * - Extra items aan het menu toevoegen
 * - Extra items vanuit een andere tabel toevoegen
 * - Splitsen op taal
 */
// $config['merged_menu'] = array(
//
//   'items' => array()
//
//
//   ),
//
//
// );
