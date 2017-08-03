<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Home Wizards
 */
$config['home_menu'] = array(
  'statistics' => array( 'name'=>'watch_statistics', 'uri'=>'plugin/stats',             'icon'=>'bar-chart',         "class"=>"primary" ),
  'user'       => array( 'name'=>'edit_user',        'uri'=>'edit/cfg_users/{user_id}', 'icon'=>'user',              "class"=>"primary" ),
);


/**
 * Menu
 */
$config['header_menu'] = array(
  'statistics' => array( 'name'=>'statistics', 'uri'=>'plugin/stats',              'icon'=>'bar-chart'),
  'settings'   => array( 'name'=>'settings',   'uri'=>'edit/tbl_site/1',           'icon'=>'cog'),
  'user'       => array( 'name'=>'{username}', 'uri'=>'edit/cfg_users/{user_id}',  'icon'=>'user') ,
  'logout'     => array( 'name'=>'logout',     'uri'=>'logout',                    'icon'=>'power-off' ),
  'help'       => array( 'name'=>'help',       'uri'=>'help',                      'icon'=>'question-circle' ), // Hier komt extra code bij
);


$config['side_menu']           = array();
$config['side_menu']['tables'] = array();
$config['side_menu']['media']  = array();

$config['side_menu']['tools'] = array(
  'export'   => array( 'user_group'=>'super_admin', 'name'=>'db_export',          'uri'=>'tools/db_export',   'icon'=>'database', 'class'=>'text-muted' ),
  'import'   => array( 'user_group'=>'super_admin', 'name'=>'db_import',          'uri'=>'tools/db_import',   'icon'=>'database', 'class'=>'text-muted' ),
  'backup'   => array( 'user_group'=>'[b_backup]',  'name'=>'db_backup',          'uri'=>'tools/db_backup',   'icon'=>'download', 'class'=>'text-muted' ),
  'restore'  => array( 'user_group'=>'[b_backup]',  'name'=>'db_restore',         'uri'=>'tools/db_restore',  'icon'=>'upload', 'class'=>'text-muted' ),
  '-'        => array( 'type'=>'seperator' ),
  'search'   => array( 'user_group'=>'super_admin',   'name'=>'sr_search_replace',  'uri'=>'tools/search',      'icon'=>'search', 'class'=>'text-muted' ),
  'fill'     => array( 'user_group'=>'super_admin',   'name'=>'fill_fill',          'uri'=>'tools/fill',        'icon'=>'arrow-circle-o-down', 'class'=>'text-muted' ),
  '--'       => array( 'type'=>'seperator' ),
  'plugins'  => array( 'user_group'=>'super_admin',  'name'=>'plugins',           'uri'=>'plugin',      'icon'=>'cog',    'class'=>'text-muted' ),
);
$config['side_menu']['rel_tables'] = array(
  'rel_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'rel', 'icon'=>'link' ),
);
$config['side_menu']['res_tables'] = array(
  'res_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'res', 'icon'=>'cloud' ),
);
$config['side_menu']['cfg_tables'] = array(
  'cfg_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'cfg', 'icon'=>'cog',       'class'=>'text-muted' ),
  'log_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'log', 'icon'=>'bar-chart', 'class'=>'text-muted' ),
);



/**
 * TinyMCE settings
 */
$config['wysiwyg'] = array(
  'selector'      => 'textarea.wysiwyg',
  'plugins'       => 'lists,fullscreen,table,image,link,code',
  'height'        => 300,
  'block_formats' => 'Paragraph=p;Header 1=h1;Header 2=h2;Header 3=h3',
  'content_style' => 'h1,h2 {margin:0px} p {margin-top:0px}',
  // 'menubar'    => "edit format table",
  'menubar'       => false,
  // 'toolbar'    => false,
  'toolbar1'      => 'cut copy paste | undo redo | bold italic | bullist numlist |alignleft aligncenter alignright | link unlink | charmap image | code fullscreen',
);



?>