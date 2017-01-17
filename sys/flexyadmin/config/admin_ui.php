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
    'tbl_menu'  => array( 'type'=>'table' ),
    'tbl_links' => array( 'type'=>'table' ),
    '-' => array( 'type'=>'seperator' ),
    'tables'    => array( 'type'=>'tables', 'pre'=>'tbl', 'user_group'=>'super_admin', 'icon'=>'' ),
  ),
    
  'media'=> array(
    // 'medias'   => array( 'type'=>'medias', 'icon'=>'folder-open' ),
    'pictures' => array( 'type'=>'media', 'icon'=>'folder-open' ),
    'downloads'=> array( 'type'=>'media', 'icon'=>'folder-open' ),
  ),

  'tools'=> array(
    'export'   => array( 'user_group'=>'super_admin', 'name'=>'db_export',          'uri'=>'db/export',   'icon'=>'cog', 'class'=>'text-muted' ),
    'import'   => array( 'user_group'=>'super_admin', 'name'=>'db_import',          'uri'=>'db/import',   'icon'=>'cog', 'class'=>'text-muted' ),
    'backup'   => array( 'user_group'=>'[b_backup]',  'name'=>'db_backup',          'uri'=>'db/backup',   'icon'=>'cog', 'class'=>'text-muted' ),
    'restore'  => array( 'user_group'=>'[b_backup]',  'name'=>'db_restore',         'uri'=>'db/restore',  'icon'=>'cog', 'class'=>'text-muted' ),
    '-'        => array('type'=>'seperator'),
    'search'   => array( 'user_group'=>'[b_tools]',   'name'=>'sr_search_replace',  'uri'=>'search',      'icon'=>'cog', 'class'=>'text-muted' ),
    'fill'     => array( 'user_group'=>'[b_tools]',   'name'=>'fill_fill',          'uri'=>'fill',        'icon'=>'cog', 'class'=>'text-muted' ),
  ),

  'rel_tables'=> array(
    'rel_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'rel', 'icon'=>'link' ),
  ),

  'res_tables'=> array(
    'res_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'res', 'icon'=>'cloud' ),
  ),
  
  'cfg_tables'=> array(
    'cfg_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'cfg', 'icon'=>'cog',       'class'=>'text-muted' ),
    'log_tables' => array( 'user_group'=>'super_admin', 'type'=>'tables', 'pre'=>'log', 'icon'=>'bar-chart', 'class'=>'text-muted' ),
  ),
  
);



/**
 * TinyMCE settings
 */
$config['wysiwyg'] = array(
  'plugins'    => 'fullscreen,table,image,link,code',
  'height'     => 400,
  // 'menubar'    => "edit format table",
  'menubar'    => false,
  // 'toolbar'    => false,
  'toolbar1'   => 'cut copy paste | undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link unlink | charmap image | code fullscreen',
  // 'toolbar2'   => $this->cfg->get('CFG_configurations',"str_buttons2"),
  // 'toolbar3'   => $this->cfg->get('CFG_configurations',"str_buttons3"),
);



?>