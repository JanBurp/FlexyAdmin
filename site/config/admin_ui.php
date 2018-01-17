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
  'plugins'                   => 'fullscreen,paste,textpattern,wordcount,lists,table,flexy_image,imagetools,flexy_link,autolink,charmap,media,code',//,visualblocks,preview',
  // 'content_css'               => 'assets/css/admin.css',
  
  'max-height'                => 300,
  'autoresize_max_height'     => 500,
  
  'paste_as_text'             => true,
  'paste_word_valid_elements' => 'b,strong,i,em,a',

  'document_base_url'         => site_url(),
  'relative_urls'             => true,
  'link_title'                => false,
  'link_context_toolbar'      => true,

  'image_dimensions'          => false,
  'imagetools_toolbar'        => 'rotateleft rotateright | flipv fliph | editimage ', //' imageoptions',
  
  'style_formats'             => array(
    array('title' => 'Paragraaf',   'block'=>'p'),
    array('title' => 'Tussenkop 1', 'block'=>'h2'),
    array('title' => 'Tussenkop 2', 'block'=>'h3'),
    // array('title' => 'Afbeelding Links',  'selector'=>'img', 'classes'=>'pull-left' ),
    // array('title' => 'Afbeelding Rechts', 'selector'=>'img', 'classes'=>'pull-right' ),
  ),

  // 'menubar'                   => "edit format table tools",
  'menubar'                   => false,
  'toolbar1'                  => 'cut copy paste | undo redo | bold italic styleselect removeformat | bullist numlist | flexy_link unlink | charmap flexy_image media | fullscreen | code',
  // 'toolbar1'                  => 'cut copy paste | undo redo | bold italic styleselect | bullist numlist | alignleft aligncenter alignright | flexy_link unlink | charmap flexy_image media | fullscreen | code',
);



?>