<?

// This is the basic Module library, used for frontend modules

class Module {

	var $CI;
	var $config=array();
	var $name='';

	function __construct($name='') {
		$this->CI=&get_instance();
		if (empty($name)) $name=strtolower(get_class($this));
		if ($name!='module') {
			$this->set_name($name);
			$this->load_config();
		}
	}

	// if method of module can't be found, print a simple warning
	public function __call($function, $args) {
		$args=implode(',',$args);
		echo '<div class="warning">Method: `'.ucfirst($function)."(".$args.")` doesn't exists.<div>";
	}

	public function set_name($name) {
		$this->name=$name;
	}


	public function load_config($name='') {
		if (empty($name)) $name=$this->name;
		if ( $this->CI->config->load($name,true) ) {
			$this->set_config( $this->CI->config->item($name) );
		}
		return $this->config;
	}

	
	public function set_config($config,$merge=TRUE) {
		if ($merge)
			$this->config=array_merge($this->config,$config);
		else
			$this->config=$config;
		return $this->config;
	}


	// index is the standard method
	public function index($item) {
		return '<h1>'.__CLASS__.'</h1>';
	}


}

?>