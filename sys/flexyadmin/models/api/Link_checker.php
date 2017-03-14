<?php

/** \ingroup models
 * API user
 * 
 *    
 * @author Jan den Besten
 */

class Link_checker extends Api_Model {
  
	public function __construct() {
		parent::__construct();
  }
  
  /**
   * Check links in tbl_links
   *
   * @return mixed
   * @author Jan den Besten
   */
  public function index() {
    // Check rechten
    if (!$this->has_args()) return $this->_result_wrong_args(); 
    if (!$this->_has_rights('tbl_links')) {
      return $this->_result_status401();
    }
    
    
    $this->data->table('tbl_links');
    if (isset($this->args['where'])) {
      $this->data->where( $this->args['where'] );
    }
    $this->result['data'] = $this->data->check_links();
    
    $message = $this->data->get_message();
    $this->_set_message( $message );
    $this->_set_message_type( 'popup' );
    
    return $this->_result_ok();
  }
  
}


?>
