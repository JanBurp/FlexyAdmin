<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* --- Settings for Assets --- Created @ Thu 22 December 2016, 01:00 */

/* EXAMPLE:

$config['assets'] = array( 

	'pictures'  => array(
    'types'            => 'jpg,jpeg,gif,png',
    'encrypt_name'     => false,
    'media_fields'     => 'tbl_groepen.media_tekening|tbl_menu.medias_fotos',
    'autofill'         => '',
    'autofill_fields'  => false,
    'in_link_list'     => false,
    'user_restricted'  => false,
    'serve_restricted' => false,
    
    'min_width'        => false,
    'min_height'       => false,
    'resize_img'       => true,
    'img_width'        => 300,
    'img_height'       => 1000,
    'create_1'         => true,
    'width_1'          => 100,
    'height_1'         => 1000,
    'prefix_1'         => '_thumb_',
    'suffix_1'         => '',
    'create_2'         => false,
    'width_2'          => false,
    'height_2'         => false,
    'prefix_2'         => '',
    'suffix_2'         => ''
  ), 

	'downloads' => array(
    'types'            => 'pdf,doc,docx,xls,xlsx,png,jpg',
    'encrypt_name'     => false,
    'media_fields'     => false,
    'autofill'         => '',
    'autofill_fields'  => false,
    'in_link_list'     => true,
    'user_restricted'  => false,
    'serve_restricted' => false
  ), 
);

*/

$config['assets'] = array( 
		'downloads' => array( 'types' => 'pdf,doc,docx,xls,xlsx,png,jpg', 'encrypt_name' => 0, 'media_fields' => 0, 'autofill' => '', 'autofill_fields' => 0, 'in_link_list' => 1, 'user_restricted' => 0, 'serve_restricted' => 0 ), 
		'pictures'  => array( 'types' => 'jpg,jpeg,gif,png', 'encrypt_name' => 0, 'media_fields' => 'tbl_groepen.media_tekening|tbl_menu.medias_fotos', 'autofill' => '', 'autofill_fields' => 0, 'in_link_list' => 0, 'user_restricted' => 0, 'serve_restricted' => 0, 'min_width' => 0, 'min_height' => 0, 'resize_img' => 1, 'img_width' => 300, 'img_height' => 1000, 'create_1' => 1, 'width_1' => 100, 'height_1' => 1000, 'prefix_1' => '_thumb_', 'suffix_1' => '', 'create_2' => 0, 'width_2' => 0, 'height_2' => 0, 'prefix_2' => '', 'suffix_2' => '' ), 
	);

?>