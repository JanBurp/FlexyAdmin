<?php require_once(APPPATH."core/AdminController.php");

/**
 * LESS compiler and minimizer for frontend styles, use: admin/build and set config in config/build.php
 *
 * @package FlexyAdmin
 * @author Jan den Besten
 */

class Build extends AdminController {

	public function __construct() {
		parent::__construct();
    $this->load->model('builder','build');
	}
  
  public function index() {
    $this->build->go();
    $this->_set_content($this->build->report());
		$this->_show_all();
  }
  
}

?>
