<?php require_once(APPPATH."core/AjaxController.php");

/**
 * Laad Main Angular
 *
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
    $site = $this->data->table('tbl_site')
                              ->select('str_title,url_url')
                              ->get_row();
    $this->load->view('admin/__test/main_app',array('title'=>$site['str_title'],'language'=>$this->user->language),false);
	}

}

?>
