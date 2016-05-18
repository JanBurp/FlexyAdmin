<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for data model 'cfg_user_groups' --- Created @ Tue 17 May 2016, 17:25 */

$config['table']           = 'cfg_user_groups';
$config['primary_key']     = 'id';
$config['result_key']      = 'id';
$config['fields']          = array('id','str_name','str_description','rights','b_all_users','b_backup','b_tools','b_delete','b_add','b_edit','b_show');
$config['order_by']        = 'id';
$config['abstract_fields'] = array('str_description');
$config['relations']       = array();

$config['grid_set'] = array( 
		'fields'        => array('id','str_name','str_description','rights','b_all_users','b_backup','b_tools','b_delete','b_add','b_edit','b_show'), 
		'order_by'      => 'id', 
		'jump_to_today' => false, 
		'pagination'    => true, 
		'relations'     => array( 
				'many_to_one' => array(), 
			), 
	);

$config['form_set'] = array( 
		'fields'    => array('id','str_name','str_description','rights','b_all_users','b_backup','b_tools','b_delete','b_add','b_edit','b_show'), 
		'fieldsets' => array( 
				'cfg_user_groups' => array('id','str_name','str_description','rights','b_all_users','b_backup','b_tools','b_delete','b_add','b_edit','b_show'), 
			), 
		'with'      => array(), 
	);
  
