<?php

/**
 * API: Geeft het admin menu terug voor in het backend deel van FlexyAdmin.
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

class get_admin_nav extends Api_Model {
  
  var $table = 'cfg_admin_menu';
  
  /**
   */
	public function __construct($name='') {
		parent::__construct();
    $this->load->model($this->table);
    return $this;
	}
  
  public function index() {
    if (!$this->logged_in()) return $this->_result_status401();

    $table=$this->table;
    $menu =$this->$table->get();
    $this->result['data']=$menu;
    return $this->_result_ok();
  }

}


?>
