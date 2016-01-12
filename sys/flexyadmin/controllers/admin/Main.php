<?php require_once(APPPATH."core/AdminController.php");

/**
 * main Controller Class
 *
 * This Controller shows the startscreen
 * 
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Main extends AdminController {

	public function __construct() {
    parent::__construct();
	}

	public function index() {
		$this->load->model("grid");
		$this->lang->load("home");
    $this->_show_type("stats");

    // homepage plugin (stats)
    $data['homeplugins']=$this->plugin_handler->call_plugins_homepage();
    
    $this->_set_content($this->load->view("admin/home",$data,true));
		$this->_show_all();
	}



}

?>
