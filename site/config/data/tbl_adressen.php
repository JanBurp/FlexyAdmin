<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Only for test AES testing with flexyadmin_unittest_...sql database

$config['table'] = 'tbl_adressen';

$config['field_info']['str_address']['encrypted'] = TRUE;
$config['field_info']['str_zipcode']['encrypted'] = TRUE;
$config['field_info']['str_city']['encrypted'] = TRUE;
