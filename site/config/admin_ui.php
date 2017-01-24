<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Menu
 */

$config['side_menu']['tables'] = array(
  'tbl_menu'  => array( 'type'=>'table' ),
  'tbl_links' => array( 'type'=>'table' ),
  '-' => array( 'type'=>'seperator' ),
  'tables'    => array( 'type'=>'tables', 'pre'=>'tbl', 'user_group'=>'super_admin', 'icon'=>'' ),
);

$config['side_menu']['media'] = array(
  'pictures' => array( 'type'=>'media', 'icon'=>'folder-open' ),
  'downloads'=> array( 'type'=>'media', 'icon'=>'folder-open' ),
  '-' => array( 'type'=>'seperator' ),
  'medias'   => array( 'type'=>'medias', 'icon'=>'folder-open' ),
);


/**
 * TinyMCE settings
 */
$config['wysiwyg'] = array(
  'plugins'               => 'fullscreen,table,image,imagetools,link,autolink,charmap,code',
  'autoresize_max_height' => 500,
  // 'height'                => 400,
  // 'menubar'            => "edit format table",
  'menubar'               => false,
  'toolbar1'              => 'cut copy paste | undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link unlink | charmap image | code fullscreen',
  
  'link_title'            => false,
  'image_dimensions'      => false,
);



?>