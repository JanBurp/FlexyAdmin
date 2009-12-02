<?
require_once(APPPATH."controllers/admin/MY_Controller.php");


class Plugin extends AdminController {

	function Plugin() {
		parent::AdminController();
	}

	function index() {
		$this->_show_all();
	}
	
	function call() {
		$args=func_get_args();
		$ajax=false;
		if (!empty($args)) {
			$ajax=$args[0]=='ajax';
			if ($ajax) {
				array_shift($args);
				// next arg is plugin name
				$plugin='plugin_'.$args[0];
				array_shift($args);
				// call plugin if exists
				if (isset($this->$plugin) and method_exists($this->$plugin,'_ajax_api')) $this->$plugin->_ajax_api($args);
			}
			else {
				// first arg is plugin name
				$plugin='plugin_'.$args[0];
				array_shift($args);
				// call plugin if exists
				if (isset($this->$plugin) and method_exists($this->$plugin,'_admin_api')) $this->$plugin->_admin_api($args);
			}
		}
		// output
		if (!$ajax) $this->_show_all();
	}

}

?>
