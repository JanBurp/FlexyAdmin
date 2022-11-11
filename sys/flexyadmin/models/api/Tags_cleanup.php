<?php

/** \ingroup models
 * API: assets
 * 
 * @author Jan den Besten
 */

class Tags_cleanup extends Api_Model {
  
	public function __construct() {
		parent::__construct();
	}
  

  public function index() {
    if (!$this->flexy_auth->has_rights('tbl_tags')) return $this->_result_status401();
    $this->cleanup();
    return $this->_result_ok();
  }


  private function cleanup() {
    $tags = $this->data->table('tbl_tags')->cleanup_tags();
    $this->_set_message( count($tags) .' tags are used, other tags are removed. Please reload page to see result.' );
    $this->_set_message_type( 'danger' );
  }

}


?>
