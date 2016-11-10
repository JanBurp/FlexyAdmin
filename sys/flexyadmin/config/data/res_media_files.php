<?php defined('BASEPATH') OR exit('No direct script access allowed');


// DIT MOET NOG PER PAD WORDEN INGESTELD
$config['number_of_recent_uploads'] = 10;


/* --- Settings for data model 'res_media_files' --- zie voor uitleg config/data/data_model.php */

$config['table'] = 'res_media_files';
$config['fields'] = array('id','b_exists','file','path','str_type','str_title','dat_date','int_size','int_img_width','int_img_height');

$config['field_info'] = array(
		'id'             => array('validation' => array('trim','integer','required') ), 
		'b_exists'       => array('validation' => array('') ), 
		'file'           => array('validation' => array('trim','max_length[255]') ), 
		'path'           => array('validation' => array('trim','max_length[255]') ), 
		'str_type'       => array('validation' => array('max_length[10]') ), 
		'str_title'      => array('validation' => array('max_length[255]') ), 
		'dat_date'       => array('validation' => array('') ), 
		'int_size'       => array('validation' => array('trim','integer') ), 
		'int_img_width'  => array('validation' => array('trim','integer') ), 
		'int_img_height' => array('validation' => array('trim','integer') ), 
	);


// $config['files_fields'] = array('id','name','type','alt','rawdate','date','size','width','height');
// $config['files_field_info'] = array(
//   'id'      => array('validation' => array('trim','integer','required') ),
//   'name'    => array('validation' => array('trim','max_length[255]') ),
//   'type'    => array('validation' => array('max_length[10]') ),
//   'alt'     => array('validation' => array('max_length[255]') ),
//   'rawdate' => array('validation' => array('') ),
//   'date'    => array('validation' => array('') ),
//   'size'    => array('validation' => array('trim','integer') ),
//   'width'   => array('validation' => array('trim','integer') ),
//   'height'  => array('validation' => array('trim','integer') ),
// );



$config['order_by'] = 'dat_date DESC';
$config['abstract_fields'] = array('file','dat_date');

$config['relations'] = array();

$config['grid_set'] = array(
		'fields'        => array('id','b_exists','file','path','str_type','str_title','dat_date','int_size','int_img_width','int_img_height'), 
		'order_by'      => 'dat_date DESC', 
		'jump_to_today' => 'dat_date', 
		'pagination'    => true, 
		'with'          => array('many_to_one' => array() ), 
	);

$config['form_set'] = array(
		'fields'    => array('id','b_exists','file','path','str_type','str_title','dat_date','int_size','int_img_width','int_img_height'), 
    // 'fieldsets' => array('res_media_files' => array('id','b_exists','file','path','str_type','str_title','dat_date','int_size','int_img_width','int_img_height') ),
		'with'      => array(), 
	);