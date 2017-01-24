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
  'log_activity',
  'log_stats',
  'res_assets',
  'res_menu_result',
  'tbl_links',
  'tbl_menu',
);

$config['merge_tables'] = array(
  // 'cfg_auto_menu',
  // 'cfg_field_info',
  'cfg_img_info',
  // 'cfg_media_info',
  // 'cfg_table_info',
  // 'cfg_ui',
  // 'cfg_user_groups',
  // 'cfg_users',
);

$config['merge_and_complete_tables'] = array(
  'tbl_menu',
  'tbl_links',
  'tbl_site',
  // 'res_menu_result',
);



// Paths to clean before move new content in
$config['empty'] = array(
  SITEPATH.'assets/_thumbcache',
  SITEPATH.'assets/pictures',
  SITEPATH.'assets/downloads',
  SITEPATH.'assets/img',
);

// Paths & files  to move
$config['move'] = array(
  SITEPATH.'assets/js',
  SITEPATH.'assets/img',
  SITEPATH.'assets/css',
  SITEPATH.'assets/less-bootstrap',
  SITEPATH.'assets/less-default',
  SITEPATH.'assets/pictures',
  SITEPATH.'assets/downloads',
);

// Paths & files to merge (keep newest)
$config['merge'] = array(
  SITEPATH.'views/',
  SITEPATH.'libraries/',
  SITEPATH.'models/',
  SITEPATH.'helpers/',
  SITEPATH.'config/',
);


?>