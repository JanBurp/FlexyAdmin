<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Menu
 */

$config['side_menu']['tables'] = array(
  'tbl_menu'  => array( 'type'=>'table' ),
  'tbl_links' => array( 'type'=>'table' ),
  '-' => array( 'type'=>'seperator' ),
  'tables'    => array( 'type'=>'tables', 'pre'=>'tbl', 'icon'=>'' ),
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
  'plugins'                   => 'fullscreen,paste,textpattern,wordcount,table,flexy_image,imagetools,flexy_link,autolink,charmap,media,code,visualblocks,preview',
  'max-height'                 => 400,
  'autoresize_max_height'     => 500,
  'paste_as_text'             => true,
  'paste_word_valid_elements' => 'b,strong,i,em,a',
  'link_title'                => false,
  'image_dimensions'          => false,
  // 'menubar'                => "edit format table",
  'menubar'                   => false,
  'toolbar1'                  => 'cut copy paste | undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | flexy_link unlink | charmap flexy_image media | visualblocks preview fullscreen | code',
);



?>