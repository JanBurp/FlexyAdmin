<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for data model 'cfg_users' --- Created @ Thu 28 April 2016, 17:02 */


$config['table'] = 'cfg_users';

$config['fields'] = array( 'id', 'str_username', 'id_user_group', 'gpw_password', 'email_email', 'ip_address', 'str_salt', 'str_activation_code', 'str_forgotten_password_code', 'str_remember_code', 'created_on', 'last_login', 'b_active', 'str_language', 'str_filemanager_view' );

$config['order_by'] = 'id_user_group,str_username';

$config['relations'] = array( 
		'many_to_one' => array( 
				'id_user_group' => array( 
						'other_table' => 'cfg_user_groups', 
						'foreign_key' => 'id_user_group', 
						'result_name' => 'cfg_user_groups', 
					), 
			), 
	);


$config['grid_set'] = array(
  'fields'    => array( 'id','str_username', 'id_user_group', 'email_email', 'str_language'),
);


$config['form_set'] = array(
  'fields'    => array( 'id','str_username', 'id_user_group', 'email_email', 'str_language'),
  'with'      => array( 'many_to_one'=>array('id_user_group') ),
);
