<?
require_once(APPPATH."core/AdminController.php");


class Test extends AdminController {
	
	function __construct() {
		parent::__construct();
	}

	function index() {
		$this->load->library('tag');
		$tag=new tag();
		$tag->add_class('test');
		$tag->add_attributes('id','test_id');
		$tag->add_html('test');

		$this->_add_content( $tag->view() );
		$this->_show_all();
	}


}

?>
