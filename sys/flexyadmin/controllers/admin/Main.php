<?php require_once(APPPATH."core/AdminController.php");

/**
 * main Controller Class
 *
 * This Controller shows the startscreen
 * 
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Main extends AdminController {

	public function __construct() {
    parent::__construct();
	}

	public function index() {
    $data = array(
      'plugins' => $this->plugin_handler->call_plugins_homepage(),
    );
		$this->view_admin( 'content_home', $data );
	}



}

?>
