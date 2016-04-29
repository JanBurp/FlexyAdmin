<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for data model 'cfg_admin_menu' --- Created @ Fri 29 April 2016, 09:41 */

$config['table']     = 'cfg_admin_menu';

$config['order_by']  = 'order';

$config['relations'] = array( 
		'many_to_one' => array( 
				'id_user_group'  => array( 
						'other_table'  => 'cfg_user_groups', 
						'foreign_key'  => 'id_user_group', 
						'result_name'  => 'cfg_user_groups', 
					), 
			), 
	);
