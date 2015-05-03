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
    $site = $this->crud->get_row(array(
      'table'   => 'tbl_site',
      'select'  => 'str_title,url_url'
    ));
    $this->load->view('admin/__test/main_app',array('title'=>$site['str_title'],'language'=>$this->user->language),false);
	}

}

?>
