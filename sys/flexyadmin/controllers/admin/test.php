<?php require_once(APPPATH."core/AdminController.php");


class Test extends AdminController {
	
	function __construct() {
		parent::__construct();
	}

	function index() {
    $this->load->view('admin/angular/main_app',array(),false);
	}


}

?>
