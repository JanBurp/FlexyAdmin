<?

/**
 * Geeft plugin pagina, voor backend van FlexyAdmin
 * 
 * @author Jan den Besten
 */

class get_plugin extends Api_Model {
  
  var $needs = array(
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
    if ( !$this->has_args() ) return $this->_result_wrong_args();
    
    $args=$this->args;
    $plugin='plugin_'.$args['plugin'];
    unset($args['plugin']);
    $title=$plugin;
    $html = $this->plugin_handler->call_plugin_admin_api($plugin,$args);
    $html = str_replace(array("\r","\n","\t","'"),array('',"'"),$html);
    
    // RESULT
    $this->result['data']=array(
      'plugin' =>$plugin,
      'title' => $title,
      'html' => $html
    );
    return $this->_result_ok();
  }

}


?>
