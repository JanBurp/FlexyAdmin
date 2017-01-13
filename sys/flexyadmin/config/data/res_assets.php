<?php defined('BASEPATH') OR exit('No direct script access allowed');


// DIT MOET NOG PER PAD WORDEN INGESTELD
$config['number_of_recent_uploads'] = 10;


/* --- Settings for data model 'res_assets' --- zie voor uitleg config/data/data_model.php */

$config['table'] = 'res_assets';
$config['fields'] = array('id','b_exists','file','path','type','alt','date','size','width','height');

$config['field_info'] = array(
		'id'       => array('validation' => array('trim','integer','required') ), 
		'b_exists' => array('validation' => array('') ), 
		'file'     => array('validation' => array('trim','max_length[255]') ), 
		'path'     => array('validation' => array('trim','max_length[255]') ), 
		'type'     => array('validation' => array('max_length[10]') ), 
		'alt'      => array('validation' => array('max_length[255]') ), 
		'date'     => array('validation' => array('') ), 
		'size'     => array('validation' => array('trim','integer') ), 
		'width'    => array('validation' => array('trim','integer') ), 
		'height'   => array('validation' => array('trim','integer') ), 
	);


$config['order_by'] = 'date DESC';
$config['abstract_fields'] = array('file','date');

$config['relations'] = array();


/* Settings voor ->get_files() (en varianten) */
$config['files'] = array(
  'select'        => array('alt','file','path','type','date','size','width','height'),
  'thumb_select'  => array('id','media_thumb','alt','type','date','size','width','height'),
);


$config['grid_set'] = array(
		'fields'        => array('id','b_exists','file','path','type','alt','date','size','width','height'), 
		'order_by'      => 'date DESC', 
		'jump_to_today' => 'date', 
		'pagination'    => true, 
		'with'          => array('many_to_one' => array() ), 
	);

$config['form_set'] = array(
		'fields'    => array('id','b_exists','file','path','type','alt','date','size','width','height'), 
    'fieldsets' => array('res_assets' => array('id','b_exists','file','path','type','alt','date','size','width','height') ),
		'with'      => array(), 
	);