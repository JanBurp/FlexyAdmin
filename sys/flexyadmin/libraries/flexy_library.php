<?

// This is the basic library, used for frontend Modules and backend Plugins

class Flexy_library {

	var $CI;
	var $config=array();
	var $name='';

	public function __construct($name='') {
		$this->CI=&get_instance();
		if (empty($name)) $name=strtolower(get_class($this));
		if (!in_array($name, array('flexy_library','module','plugin_'))) {
			$this->set_name($name);
			$this->load_config();
		}
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
	
	public function set_config($config=array(),$merge=TRUE) {
		if (!empty($config)) {
			if ($merge)
				$this->config=array_merge($this->config,$config);
			else
				$this->config=$config;
		}
		return $this->config;
	}
	
	public function config($item,$default=NULL) {
		return el($item,$this->config,$default);
	}

}

?>