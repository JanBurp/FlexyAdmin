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
    $this->load->library('documentation');
	}

  /**
   * Shows all plugins
   *
   * @return void
   * @author Jan den Besten
   */
	public function index() {
    if ( $this->flexy_auth->is_super_admin() ) {
      $plugins = $this->plugin_handler->get_plugins();
      ksort($plugins);
      foreach ($plugins as $name => $plugin) {
        $help='';
        if ($name!=='plugin_template' and $name!=='plugin' and isset($plugin['config']['admin_api_method'])) {
          $help = $this->documentation->get( $this->config->item('SYS').'flexyadmin/libraries/plugins/'.ucfirst($name).'.php', '<br>' );
          if (empty($help)) $help = $this->documentation->get( $this->config->item('SITE').'libraries/plugins/'.ucfirst($name).'.php', '<br>' );
          $plugins[$name] = array(
            'name'   => str_replace('plugin_','',$name),
            'uri'    => $this->config->item('API_plugin').str_replace('plugin_','',$name),
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
   * Calls the plugin, this is rerouted from _admin/plugin/###
   *
   * @return void
   * @author Jan den Besten
   */
	public function call() {
    if ( !$this->flexy_auth->allowed_to_use_cms() ) return false;

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
				$plugin  = 'plugin_'.array_shift($args);
        $help    = $this->documentation->get( $this->config->item('SYS').'flexyadmin/libraries/plugins/'.ucfirst($plugin).'.php', '<br>' );
        if (empty($help)) $help = $this->documentation->get( $this->config->item('SITE').'libraries/plugins/'.ucfirst($plugin).'.php', '<br>' );
        $content = $this->plugin_handler->call_plugin_admin_api($plugin,$args,$help);
        if (empty($content)) {
          $content = '<h2>'.$help['short'].'</h2>'.trim(trim($help['long']),'<br>');
        }
        // title
        $ui_name = el(array($plugin,'config','title'),$this->plugin_handler->plugins);
        if (empty($ui_name)) $ui_name = lang($plugin);
        if (substr($ui_name,0,1)==='[') $ui_name = ucfirst(str_replace(array('Plugin_','_'),array('',' '),$help['name']));
        if (empty($ui_name)) $ui_name = ucfirst(str_replace(array('plugin_','_'),array('',' '),$plugin));
			}
		}
		// output
		if (!$ajax) $this->view_admin('plugins/plugin',array( 'title'=>$ui_name,'content'=>$content));
	}

}

?>
