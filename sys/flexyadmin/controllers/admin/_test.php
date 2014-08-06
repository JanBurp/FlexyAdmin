<?php require_once(APPPATH."core/AdminController.php");


class _test extends AdminController {
	
	function __construct() {
		parent::__construct();
	}

	function index() {
    $this->load->view('admin/_test/main_app',array(),false);
	}


}

?>
