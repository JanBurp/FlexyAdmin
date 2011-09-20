<?

// This is the basic Module library, used for frontend modules

class Module {

	var $CI;
	var $config;
	var $name='';

	function __construct() {
		$this->CI=&get_instance();
	}

	// if method of module can't be found, print a simple warning
	public function __call($function, $args) {
		echo '<div class="warning">Method: `'.ucfirst($function)."` doesn't exists.<div>";
	}

	// Module is the standard method
	function index($item) {
		return '<h1>'.__CLASS__.'</h1>';
	}

	// Methods for loading and setting config
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

	function set_name($name) {
		$this->name=$name;
	}


}

?>