<?
require_once(APPPATH."controllers/admin/MY_Controller.php");


class Test extends AdminController {

	function Test() {
		parent::AdminController();
	}

	function index() {
		$this->load->model('html_model','Model');
		$this->Model->set_title('Ja!');
		
		$this->_add_content($this->Model->view());
		$this->_show_all();
	}

}

?>
