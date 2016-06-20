<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['table']      = 'cfg_admin_menu';
$config['fields']     = array('id','order','str_ui_name','b_visible','id_user_group','str_type','api','path','table','str_table_where');
$config['field_info'] = array( 
		'id'                => array( 'default' => -1, 'validation' => array('trim','integer','required'), ), 
		'order'             => array( 'default' => 0, 'validation'=> array('trim'), ), 
		'str_ui_name'       => array( 'default' => NULL, 'validation'=> array('max_length[50]'), ), 
		'b_visible'         => array( 'default' => 1, 'validation'=> array(''), ), 
		'id_user_group'     => array( 'default' => 3, 'validation'=> array('trim','integer'), ), 
		'str_type'          => array( 'default' => NULL,	'validation'=> array('max_length[20]','valid_option[api,tools,table,all_tbl_tables,all_cfg_tables,all_res_tables,media,all_media,seperator]'),
                                  'options' => array(
                                  	'data'=>array(
                                  		 array( 'value'=>'api', 'name'=>'api'),
                                  		 array( 'value'=>'tools', 'name'=>'tools'),
                                  		 array( 'value'=>'table', 'name'=>'table'),
                                  		 array( 'value'=>'all_tbl_tables', 'name'=>'all_tbl_tables'),
                                  		 array( 'value'=>'all_cfg_tables', 'name'=>'all_cfg_tables'),
                                  		 array( 'value'=>'all_res_tables', 'name'=>'all_res_tables'),
                                  		 array( 'value'=>'media', 'name'=>'media'),
                                  		 array( 'value'=>'all_media', 'name'=>'all_media'),
                                  		 array( 'value'=>'seperator', 'name'=>'seperator'	)
                                  		),
                                  	)
                            ), 
		'api'               => array( 'default' => NULL, 'validation'=> array('trim','max_length[50]'), ), 
		'path'              => array( 'default' => NULL, 'validation'=> array('trim','max_length[50]'), ), 
		'table'             => array( 'default' => NULL, 'validation'=> array('trim','max_length[25]'), ), 
		'str_table_where'   => array( 'default' => NULL, 'validation'=> array('max_length[50]'), ), 
	);
$config['order_by']        = 'order';
$config['abstract_fields'] = array('str_ui_name','str_type');
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
		'fields'        => array('id','order','str_ui_name','b_visible','id_user_group','str_type','api','path','table','str_table_where'), 
		'order_by'      => 'order', 
		'jump_to_today' => false, 
		'pagination'    => false, 
		'with'          => array( 
				'many_to_one' => array( 
						'id_user_group' => array( 'other_table' => 'cfg_user_groups', 'foreign_key' => 'id_user_group', 'result_name' => 'cfg_user_groups', 'fields' => 'abstract', 'flat' => true ), 
					), 
			), 
	);
$config['form_set']  = array( 
		'fields'           => array('id','order','str_ui_name','b_visible','id_user_group','str_type','api','path','table','str_table_where'), 
		'fieldsets'        => array( 
				'cfg_admin_menu' => array('id','order','str_ui_name','b_visible','id_user_group','str_type','api','path','table','str_table_where'), 
			), 
		'with'      => array(), 
	);
