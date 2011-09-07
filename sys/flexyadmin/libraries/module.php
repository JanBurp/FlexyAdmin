<?

// This is the basic Module library, used for frontend modules

class Module {

	var $CI;
	var $config;

	function __construct() {
		$this->CI=&get_instance();
	}

	function load_config($name) {
		$this->CI->config->load($name);
		$this->config=$this->CI->config->item($name);
	}

	function set_config($config,$merge=TRUE) {
		if ($merge)
			$this->config=array_merge($this->config,$config);
		else
			$this->config=$config;
	}

	// Module is the standard method
	function module($item) {
		return '<h1>Module</h1>';
	}


}

?>