<?php require_once(APPPATH."core/ApiController.php");

/**
 * API/plugin
 * Roept een plugin aan en geeft output van plugin terug
 *
 * @package default
 * @author Jan den Besten
 */


class Plugin extends ApiController {
  
  // var $args = 'cfg_admin_menu';
  
  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();
    return $this;
	}
  
  public function index() {
    $data = '<p>TEST</p>';
    return $this->_result(array('data'=>$data));
  }

}


?>
