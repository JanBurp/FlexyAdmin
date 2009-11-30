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
		if (!empty($args)) {
			// first arg is plugin name
			$plugin='plugin_'.$args[0];
			unset($args[0]);
			// call plugin if exists
			if (isset($this->$plugin) and method_exists($this->$plugin,'_admin_api')) $this->$plugin->_admin_api($args);
		}
		// output
		$this->_show_all();
	}

}

?>
