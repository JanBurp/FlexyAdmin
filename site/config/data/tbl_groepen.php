<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['table']           = 'tbl_groepen';

$config['relations'] = array(
	'one_to_many' => array(
		'tbl_kinderen'  => array(
		 'other_table' => 'tbl_kinderen',
		 'foreign_key' => 'id_groepen',
		 'result_name' => 'tbl_kinderen',
		),
	),
	'many_to_many' => array(
		'rel_groepen__adressen' => array(
			'this_table'	=> 'tbl_groepen',
			'other_table' => 'tbl_adressen',
			'rel_table'		=> 'rel_groepen__adressen',
			'this_key'		=> 'id_groepen',
			'other_key'		=> 'id_adressen',
			'result_name' => 'tbl_adressen',
		),
	),
);

$config['grid_set'] = array( 
	'fields' => array('id', 'uri', 'order', 'str_title', 'str_soort', 'media_tekening', 'rgb_kleur','tbl_kinderen'),
	'pagination' => true, 
	'with' => array('one_to_many'),
);

$config['form_set'] = array( 
	'fieldsets' => array(
		'tbl_groepen' => array('id', 'uri', 'order', 'str_title', 'str_soort', 'media_tekening', 'rgb_kleur'),
		'Kinderen' 		=> array('tbl_kinderen'),
	), 
	'with' => array('one_to_many'),
);