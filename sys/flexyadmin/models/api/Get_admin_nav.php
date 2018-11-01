<?php

/**
 * API: Geeft het admin menu terug voor in het backend deel van FlexyAdmin.
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class get_admin_nav extends Api_Model {
  
  /**
   */
	public function __construct($name='') {
		parent::__construct();
    return $this;
	}
  
  public function index() {
    if (!$this->logged_in()) return $this->_result_status401();
    $this->load->model('admin_menu');
    $menu = $this->admin_menu->get_menu();
    $this->result['data']=$menu;
    return $this->_result_ok();
  }

}


?>
