<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for table model 'res_media_files' --- zie voor uitleg config/tables/table_model.php */

$config['table'] = 'res_media_files';

$config['primary_key'] = 'id';

$config['result_key'] = 'id';

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

$config['order_by'] = 'dat_date DESC';

$config['max_rows'] = 0;

$config['update_uris'] = true;

$config['abstract_fields'] = array('str_type','str_title');

$config['abstract_filter'] = '';

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
		'fieldsets' => array('res_media_files' => array('id','b_exists','file','path','str_type','str_title','dat_date','int_size','int_img_width','int_img_height') ), 
		'with'      => array(), 
	);