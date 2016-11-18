<?php require_once(APPPATH."core/AdminController.php");

/**
 * Geeft lijs van beschikbare plugins en roept ze aan.
 *
 * @author: Jan den Besten
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
      $this->load->library('documentation');
      $plugins = $this->plugin_handler->get_plugins();
      foreach ($plugins as $name => $plugin) {
        $help='';
        if ($name!=='plugin_template' and $name!=='plugin' and isset($plugin['config']['admin_api_method'])) {
          $help = $this->documentation->get('sys/flexyadmin/libraries/plugins/'.ucfirst($name).'.php');
          $plugins[$name] = array(
            'name'   => str_replace('plugin_','',$name),
            'uri'    => 'admin/plugin/'.$name,
            'doc'    => $help,
          );
        }
        else {
          unset($plugins[$name]);
        }
      }
      $view_data = array(
        'title'   => 'Plugins',
        'plugins' => $plugins,
      );
      return $this->view_admin('plugins/plugins',$view_data);
    }
    $this->view_404();
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
				$plugin=array_shift($args);
        $content =  $this->plugin_handler->call_plugin_admin_api($plugin,$args);
			}
		}
		// output
		if (!$ajax) $this->view_admin('',array('title'=>$plugin,'content'=>$content));
	}

}

?>
