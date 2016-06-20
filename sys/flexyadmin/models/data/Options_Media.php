<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */


Class Options_Media extends CI_Model {

	public function __construct( $table='' ) {
		parent::__construct();
	}
  
  
  public function get_options( $info=array() ) {
    $options=array();
    $path = $info['path'];
    $media = new Data();
    $media->table( 'res_media_files' );
    $options = $media->get_files_as_options($path);
    return $options;
  }
  


}
