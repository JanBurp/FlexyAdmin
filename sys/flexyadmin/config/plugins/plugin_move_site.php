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


// paths of Old & New site
$config['old'] = '';//str_replace(array('sys/flexyadmin/config','/www.'),array('','/_www.'),__DIR__);
$config['new'] = '';//str_replace('sys/flexyadmin/config','',__DIR__);

// $config['old'] = str_replace(array('sys/flexyadmin/config','/www.'),array('','/_www.'),__DIR__);
// $config['new'] = str_replace('sys/flexyadmin/config','',__DIR__);

// Paths to clean before move new content in
$config['empty'] = array(
  'site/assets/_thumbcache',
  'site/assets/pictures',
  'site/assets/downloads',
  'site/assets/img',
);

// Paths & files  to move
$config['move'] = array(
  'site/assets/',
  'ontwerp/',
);

// Paths & files to merge (keep newest)
$config['merge'] = array(
  'site/views/',
  'site/libraries/',
  'site/models/',
  'site/helpers/',
  'site/config/',
);


?>