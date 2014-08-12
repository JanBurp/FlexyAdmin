<?php require_once(APPPATH."core/AjaxController.php");

/**
 * API of admin with Angular
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
    $this->load->view('admin/__test/main_app',array(),false);
	}

  /**
   * Test an AJAX call
   *
   * @return void
   * @author Jan den Besten
   */
  public function ajax_test() {
    $this->_test();
    $args=func_get_args();
    $call=array_shift($args);
    $output=$this->$call($args);
    $output['__test']=true;
    $output['__testcall']=$call;
    trace_($this->_result($output));
  }
  
  /**
   * Will return the admin_menu
   *
   * @return void
   * @author Jan den Besten
   */
  public function menu() {
    $this->load->model('cfg_admin_menu');
    $menu=$this->cfg_admin_menu->get();
    return $this->_result($menu);
  }
  
  
  
  
  
  
  
  
  
  


}

?>
