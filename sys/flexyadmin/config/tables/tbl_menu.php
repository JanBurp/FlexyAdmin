<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* --- Settings for table model `tbl_menu` - for help on settings, see `table_model.php` --- */

$config['table'] = 'tbl_menu';
$config['fields'] = array('id','order','self_parent','uri','str_title','txt_text','str_module','stx_description','str_keywords');
$config['field_info'] = array(
		'id'              => array('validation' => 'trim|integer|required' ), 
		'order'           => array('validation' => 'trim' ), 
		'self_parent'     => array('validation' => 'trim|integer' ), 
		'uri'             => array('validation' => 'trim|max_length[100]' ), 
		'str_title'       => array('validation' => 'required|max_length[255]' ), 
		'txt_text'        => array('validation' => '' ), 
		'str_module'      => array('validation' => 'max_length[30]|valid_option[,forms.contact,example]', 'options' => '|forms.contact|example', 'multiple_options' => false ), 
		'stx_description' => array('validation' => '' ), 
		'str_keywords'    => array('validation' => 'max_length[255]' ), 
	);
$config['order_by'] = 'order';
$config['max_rows'] = 0;
$config['update_uris'] = true;
$config['abstract_fields'] = array('str_title','str_module');
$config['abstract_filter'] = '';
$config['admin_grid'] = array(
		'fields'        => array('id','order','self_parent','uri','str_title','txt_text','str_module'), 
		'order_by'      => 'order', 
		'with'          => array(''), 
		'jump_to_today' => true, 
	);
$config['admin_form'] = array(
		'fields'    => array('id','order','self_parent','uri','str_title','txt_text','str_module','stx_description','str_keywords'), 
		'with'      => array(''), 
		'fieldsets' => array('Extra' => array('str_module','stx_description','str_keywords') ), 
	);
