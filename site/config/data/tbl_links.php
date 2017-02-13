<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['table']           = 'tbl_links';
$config['order_by']        = 'str_title';
$config['abstract_fields'] = array('str_title','url_url');

$config['field_info']['str_title']['validation'] = array('required');
$config['field_info']['url_url']['validation']   = array('required');

