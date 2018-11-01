<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Alleen gebruikt voor UNIT TESTS

$config['table']           = 'tbl_kinderen';
if (defined('PHPUNIT_TEST')) {
	$config['order_by']        = 'str_first_name';
}

