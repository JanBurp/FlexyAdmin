<?php require_once(APPPATH."core/AdminController.php");


class __test extends AdminController {
	
	function __construct() {
		parent::__construct();
	}

	function index() {
    $this->load->view('admin/__test/main_app',array(),false);
	}


}

?>
