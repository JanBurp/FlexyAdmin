<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */


Class Options_Core extends CI_Model {

	public function __construct( $table='' ) {
		parent::__construct();
	}
  
  public function get_options( $info=array() ) {
    $options=array();
    return $options;
  }
  


}
