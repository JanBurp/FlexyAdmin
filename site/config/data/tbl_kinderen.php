<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Only for test AES testing with flexyadmin_unittest_...sql database

$config['table']           = 'tbl_kinderen';

$config['order_by']				 = 'str_first_name, str_last_name';
$config['abstract_fields'] = array('str_first_name','str_middle_name','str_last_name');