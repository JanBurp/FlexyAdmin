<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Plugin methods:
| Set the names of the methods you use when FlexyAdmin want to call you
| If empty or commented, FlexyAdmin doesn't call them.
|--------------------------------------------------------------------------
|
*/

$config['admin_api_method'] = '_admin_api';


// paths of Old & New site (auto)
$config['old'] = str_replace(array('sys/flexyadmin/config/plugins','/www.'),array('','/_www.'),__DIR__);
$config['new'] = str_replace('sys/flexyadmin/config/plugins','',__DIR__);

// Paths set by hand
$config['old'] = '';
$config['new'] = '';

// Old database
$config['db'] = 'import'; // set this group in config/database_local.php

$config['truncate_demo_tables'] = array(
  'log_login_attempts',
  'log_activity',
  'log_stats',
  'res_assets',
  'tbl_links',
  'tbl_menu',
);

$config['merge_tables'] = array(
  // 'cfg_user_groups',
  // 'cfg_users',
);

$config['merge_and_complete_tables'] = array(
  'tbl_menu',
  'tbl_links',
  'tbl_site',
);


// Paths to clean before move new content in
$config['empty'] = array(
  '_tmp',
  '_thumbcache',
  'pictures',
  'downloads',
  'img',
);

// Paths & files  to move
$config['move'] = array(
  'public' => array(
    'js',
    'img',
    'css',
    'less-bootstrap',
    'less-default',
  ),
  'private' => array(
    'pictures',
    'downloads',
  ),
);


// Paths & files to merge (keep newest)
$config['merge'] = array(
  'views/',
  'libraries/',
  'models/',
  'helpers/',
  'config/',
  'stats/'
);


?>