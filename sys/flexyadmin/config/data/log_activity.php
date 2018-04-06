<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* --- Settings for data model 'log_activity' --- Created @ Tue 17 May 2016, 17:10 */


$config['table']       = 'log_activity';
$config['order_by']    = 'tme_timestamp DESC';

$config['relations'] = array( 
		'many_to_one' => array( 
				'id_user' => array( 
						'other_table' => 'cfg_users', 
						'foreign_key' => 'id_user', 
						'result_name' => 'cfg_user', 
					), 
			), 
	);
