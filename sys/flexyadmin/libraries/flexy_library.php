<?

// This is the basic library, used for frontend Modules and backend Plugins

class Flexy_library {

	var $CI;
	var $config=array();
	var $name='';
	var $shortname='';

	public function __construct($name='') {
		$this->CI=&get_instance();
		if (empty($name)) $name=strtolower(get_class($this));
		if (!in_array($name, array('flexy_library','module','plugin_'))) {
			$this->set_name($name);
      if (file_exists(SITEPATH.'language/'.$this->CI->config->item('language').'/'.$name.'_lang.php')) {
        $this->CI->lang->load($name);
        if (substr($name,0,6)=='plugin') {
          $this->CI->config->unload($name);
        }
      }
      $this->load_config();
		}
	}

	public function set_name($name) {
		$this->name=$name;
		$this->shortname=str_replace('plugin_','',$name);
	}

	public function load_config($name='') {
		if (empty($name)) $name=$this->name;
    if (!is_array($name)) { // Hack, want hoe komt het dat $name soms de $config is???
  		$this->CI->config->load($name,true,false);
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