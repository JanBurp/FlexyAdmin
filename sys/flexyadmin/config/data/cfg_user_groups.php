<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for data model 'cfg_user_groups' --- Created @ Tue 17 May 2016, 17:25 */

$config['table']           = 'cfg_user_groups';
$config['fields']          = array('id','name','description','rights','b_all_users','b_backup','b_tools','b_delete','b_add','b_edit','b_show');
$config['order_by']        = 'cfg_user_groups.id';
$config['abstract_fields'] = array('description');

$config['grid_set'] = array( 
		'fields'        => array('id','name','description','rights','b_all_users','b_backup','b_tools','b_delete','b_add','b_edit','b_show'), 
		'order_by'      => 'id', 
		'jump_to_today' => false, 
		'pagination'    => true, 
		'with'          => array( 
				'many_to_one' => array(), 
			), 
	);

$config['form_set'] = array( 
		'fieldsets' => array( 
				'cfg_user_groups' => array('id','name','description','rights','b_all_users'), 
        'Tools' => array('b_backup','b_tools','b_delete','b_add','b_edit','b_show'), 
			), 
		'with'      => array(), 
	);
  
