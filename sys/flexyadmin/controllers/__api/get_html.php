<?php require_once(APPPATH."core/ApiController.php");

/**
 * API/plugin
 * Roept een plugin aan en geeft output van plugin terug
 *
 * @package default
 * @author Jan den Besten
 */


class get_html extends ApiController {
  
  var $args = array(
    'name' => '',
    'uri'  => ''
  );
  
  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();
    // $this->load->model('plugin_handler');
    //     $this->load->model('queu');
    $this->load->model('ui');
    return $this;
	}
  
  public function index() {
    $title=$this->ui->get($this->args['name']);
    $html = '<p>TEST</p>';
    
		$plugin='plugin_'.$this->args['name'];
    array_shift($this->args);
    $html = $this->plugin_handler->call_plugin_admin_api($plugin,$this->args);
    
    return $this->_result(array('title'=>$title,'html'=>$html));
  }

}


?>
