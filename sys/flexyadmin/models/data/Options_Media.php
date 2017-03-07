<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */


Class Options_Media extends CI_Model {

	public function __construct( $table='' ) {
		parent::__construct();
	}
  
  
  public function get_options( $info=array() ) {
    $options=array();
    $path = $info['path'];
    $options = $this->assets->get_files_as_options($path);
    return $options;
  }
  


}
