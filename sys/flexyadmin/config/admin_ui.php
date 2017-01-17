<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Menu
 */
$config['header_menu'] = array(
  'statistics' => array( 'name'=>'statistics', 'uri'=>'plugin/stats',                  'icon'=>'bar-chart'),
  'settings'   => array( 'name'=>'settings',   'uri'=>'show/form/tbl_site/1',          'icon'=>'cog'),
  'user'       => array( 'name'=>'{username}', 'uri'=>'show/form/cfg_users/{user_id}', 'icon'=>'user') ,
  'logout'     => array( 'name'=>'logout',     'uri'=>'logout',                        'icon'=>'power-off' ),
  'help'       => array( 'name'=>'help',       'uri'=>'help/index',                    'icon'=>'question-circle' ),
);

$config['side_menu'] = array(
  
  'tables'=> array(
    'tbl_menu'  => array( 'type'=>'table', 'name'=>'MENU TEST' ),
    'tbl_links' => array( 'type'=>'table' ),
    '-' => array( 'type'=>'seperator' ),
    'tables'    => array( 'type'=>'tables', 'pre'=>'tbl', 'user_group'=>'super_admin', 'icon'=>'' ),
  ),
    
  // 'media'=> array(
  //   'pictures' => array( 'type'=>'media' ),
  //   'downloads'=> array( 'type'=>'media' ),
  // ),
  //
  // 'tools'=> array(
  //   'export'   => array( 'user_group'=>'super_admin', 'name'=>'db_export',          'uri'=>'db/export',   'icon'=>'cog' ),
  //   'import'   => array( 'user_group'=>'super_admin', 'name'=>'db_import',          'uri'=>'db/import',   'icon'=>'cog' ),
  //   'backup'   => array( 'user_group'=>'[b_backup]',  'name'=>'db_backup',          'uri'=>'db/backup',   'icon'=>'cog' ),
  //   'restore'  => array( 'user_group'=>'[b_backup]',  'name'=>'db_restore',         'uri'=>'db/restore',  'icon'=>'cog' ),
  //   'search'   => array( 'user_group'=>'[b_tools]',   'name'=>'sr_search_replace',  'uri'=>'search',      'icon'=>'cog' ),
  //   'restore'  => array( 'user_group'=>'[b_tools]',   'name'=>'fill_fill',          'uri'=>'fill',        'icon'=>'cog' ),
  // ),
  //
  'res_tables'=> array(
    'res_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'res', 'icon'=>'cloud' ),
  ),

  'cfg_tables'=> array(
    'cfg_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'cfg', 'icon'=>'cog', 'class'=>'text-muted' ),
    'log_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'log', 'icon'=>'bar-chart', 'class'=>'text-muted' ),
  ),

  'rel_tables'=> array(
    'rel_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'rel', 'icon'=>'link' ),
  ),
  
);



/**
 * TinyMCE settings
 */
$config['wysiwyg'] = array(
  
);



?>