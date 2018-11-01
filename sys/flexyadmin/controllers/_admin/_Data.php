<?php require_once(APPPATH."core/AdminController.php");

/**
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Data extends AdminController {

	public function __construct() {
    parent::__construct();
	}


	public function index() {
		$this->view_admin( 'content_home' );
	}


  public function grid() {
    $this->view_admin( 'content_home' );
  }



}

?>
