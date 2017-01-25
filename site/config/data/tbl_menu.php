<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['table'] = 'tbl_menu';

$config['options'] = array( 
	'self_parent'  => array( 'special' => 'self_parent' ), 
	'medias_fotos' => array(
    'model'    => 'media',
    'path'     => 'pictures',
    'multiple' => true
  ), 
  'str_module'  => array(
    'data' => array(''=>'','forms.contact'=>'forms.contact','example'=>'example'),
  ),
);
