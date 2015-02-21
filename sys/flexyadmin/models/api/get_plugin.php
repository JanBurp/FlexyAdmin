<?

/**
 * Geeft plugin pagina
 *
 * @package default
 * @author Jan den Besten
 */

class get_plugin extends ApiModel {
  
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
    return $this;
	}
  
  public function index() {
    if (!$this->logged_in()) return $this->_result_status401();
    
    $args=array();
    $plugin='plugin_'.$this->args['plugin'];
    $title=$plugin;
    $html = $this->plugin_handler->call_plugin_admin_api($plugin,$args);
    $html = str_replace(array("\r","\n","\t","'"),array('',"'"),$html);
    
    // RESULT
    $data=array(
      'plugin' =>$plugin,
      'title' => $title,
      'html' => $html
    );
    $this->result['data']=$data;
    return $this->_result_ok();
  }

}


?>
