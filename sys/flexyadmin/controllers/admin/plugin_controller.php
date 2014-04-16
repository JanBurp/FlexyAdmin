<?php require_once(APPPATH."core/AdminController.php");

/**
 * This Controller loads a plugin and calls the method
 */


class Plugin_controller extends AdminController {

	function __construct() {
		parent::__construct();
		$this->load->model('plugin_handler');
	}

	function index() {
		$this->_show_all();
	}

  /**
   * Calls the plugin, this is rerouted from admin/plugin/###
   *
   * @return void
   * @author Jan den Besten
   */
	function call() {
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
