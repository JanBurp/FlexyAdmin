<?php require_once(APPPATH."core/AdminController.php");

/**
 * 
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Editor extends AdminController {

	public function __construct() {
    parent::__construct();
	}

	public function index() {
    $this->_prepare_view_data();
    $this->view_data = array_merge($this->view_data);
    $this->load->view('admin/editor',$this->view_data);
    return $this;
	}



}

?>
