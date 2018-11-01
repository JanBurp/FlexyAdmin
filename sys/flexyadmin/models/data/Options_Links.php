<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */


Class Options_Links extends CI_Model {

	public function __construct( $table='' ) {
		parent::__construct();
	}
  
  
  public function get_options( $info=array() ) {
    $options = $this->data->table('tbl_links')->get_options_link_list();
    return $options;
  }
  


}
