<?php require_once(APPPATH."core/AdminController.php");

/**
 * Geeft lijs van beschikbare plugins en roept ze aan.
 *
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */
class Plugin_controller extends AdminController {

	public function __construct() {
		parent::__construct();
		$this->load->model('plugin_handler');
	}

  /**
   * Shows all plugins
   *
   * @return void
   * @author Jan den Besten
   */
	public function index() {
    if ( $this->flexy_auth->is_super_admin() ) {
      $this->_add_content( h('Plugins') );

      $this->load->library('menu');
      $this->load->library('documentation');
    
      $plugin_menu = new Menu();
      $plugins = $this->plugin_handler->get_plugins();
      foreach ($plugins as $name => $plugin) {
        if ($name!='plugin_template' and isset($plugin['config']['admin_api_method'])) {
          $help = $this->documentation->get('sys/flexyadmin/libraries/plugins/'.ucfirst($name).'.php');
          $plugin_menu->add( array( 'uri'=>$this->uri->uri_string().'/'.str_replace('plugin_','',$name), 'name' => '<b>'.ucfirst($name).'</b> - '.$help['short'] ) );
        }
      }
      $this->_add_content( $plugin_menu->render() );
    }
    $this->_show_all();
	}
  

  /**
   * Calls the plugin, this is rerouted from admin/plugin/###
   *
   * @return void
   * @author Jan den Besten
   */
	public function call() {
    $this->load->model('queu');
		
		$args=func_get_args();
		$ajax=false;
		$show_type='';

		if (!empty($args)) {
			$ajax=($args[0]=='ajax');
			if ($ajax) {
				array_shift($args);
				// next arg is plugin name
				$plugin='plugin_'.$args[0];
				array_shift($args);
				$this->plugin_handler->call_plugin_ajax_api($plugin,$args);
			}
			else {
				// first arg is plugin name
				$plugin='plugin_'.$args[0];
				array_shift($args);
				$this->_add_content( $this->plugin_handler->call_plugin_admin_api($plugin,$args) );
				$show_type=$this->plugin_handler->get_plugin_showtype($plugin);
			}
		}
		// output
		$this->_show_type($show_type);
		if (!empty($show_type)) $this->use_editor();
		if (!$ajax) $this->_show_all();
	}

}

?>
