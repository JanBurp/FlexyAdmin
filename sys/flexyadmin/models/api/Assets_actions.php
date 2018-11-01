<?php

/** \ingroup models
 * API: assets
 * 
 * @author Jan den Besten
 */

class Assets_actions extends Api_Model {
  
  var $needs        = array( 'action'  => false );

	public function __construct() {
		parent::__construct();
	}
  

  public function index() {
    if (!$this->flexy_auth->is_super_admin()) return $this->_result_status401();
    if (!$this->has_args()) return $this->_result_wrong_args();

    $this->load->model('assets');

    switch ($this->args['action']) {
      case 'refresh':
        $this->refresh();
        break;

      case 'resize':
        $this->resize();
        break;
    }

    return $this->_result_ok();
  }


  private function refresh() {
    $paths = $this->assets->refresh();
    if (is_array($paths)) $paths = implode(', ',$paths);
    $this->_set_message( 'Assets: `'.ucwords($paths).'` are refreshed. Please reload page to see result.' );
    $this->_set_message_type( 'danger' );
  }
  
  private function resize() {
    $paths = $this->assets->resize_all();
    if (is_array($paths)) $paths = implode(', ',$paths);
    $this->_set_message( 'Images in: `'.ucwords($paths).'` are resized.' );
    $this->_set_message_type( 'danger' );
  }

  
  
  



}


?>
