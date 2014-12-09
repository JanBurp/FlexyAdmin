<?php require_once(APPPATH."core/AjaxController.php");

/**
 * Laad Main Angular
 *
 * @package default
 * @author Jan den Besten
 */

class __test extends AjaxController {
	
	public function __construct() {
		parent::__construct();
	}
  
  /**
   * Main view
   *
   * @return void
   * @author Jan den Besten
   */
	public function index() {
    $this->load->view('admin/__test/main_app',array('language'=>$this->user->language),false);
	}

}

?>
