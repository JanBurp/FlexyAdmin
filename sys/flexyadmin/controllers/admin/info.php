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

}

?>
