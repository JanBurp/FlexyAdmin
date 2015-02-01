<?php require_once(APPPATH."core/ApiController.php");

/**
 * Geeft plugin pagina
 *
 * @package default
 * @author Jan den Besten
 */


class get_plugin extends ApiController {
  
  var $args = array(
    'plugin' => '',
  );
  
  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();
    $this->load->model('plugin_handler');
    $this->load->model('queu');
    $this->plugin_handler->init_plugins();
	}
  
  public function index() {
    $args=array();
    $plugin='plugin_'.$this->args['plugin'];
    $title=$plugin;
    $html = $this->plugin_handler->call_plugin_admin_api($plugin,$args);
    $html = str_replace(array("\r","\n","\t","'"),array('',"'"),$html);
    return $this->_result(array('plugin'=>$plugin,'title'=>$title,'html'=>$html));
  }

}


?>
