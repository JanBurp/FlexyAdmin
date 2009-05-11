<?
require_once(APPPATH."controllers/admin/MY_Controller.php");

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2008, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * main Controller Class
 *
 * This Controller shows the startscreen
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Info extends AdminController {

	function Info() {
		parent::AdminController();
	}

	function index() {
		// last login info
		$data["username"]=$this->session->userdata("user");
		$data["language"]=$this->session->userdata('language');
		$data["revision"]=$this->get_revision();
		$this->_set_content($this->load->view("admin/info_".$data["language"],$data,true));
		$this->_show_type("info");
		$this->_show_all();
	}

	function code() {
		$this->load->library('form_validation');
		$this->load->helper('html');
		$this->load->helper('language');
		$this->load->model("form");
		$this->lang->load("update_delete");
		$formData=array( "str_line" => array(	"validation"	=>  "required"),
										 "str_code"	=> array(	"validation"	=>  "required", "value"=>"2009"));
		$form=new form('admin/info/code');
		$form->set_data($formData,"Code");
		if ($form->validation()) {
			$data=$form->get_data();
			$c=$this->_encode($data['str_line']['value']);
			$this->_set_content("'$c'");
		}
		else {
			$this->_set_content($form->render());
		}
	$this->_show_type("code");
	$this->_show_all();	}
}

?>
