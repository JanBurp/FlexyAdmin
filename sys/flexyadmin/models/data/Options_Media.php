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
    return array();
  }
  


}
