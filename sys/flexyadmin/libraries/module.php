<?

// This is the basic Module library, used for frontend modules

class Module extends Flexy_library {

	function __construct($name='') {
		parent::__construct($name);
	}

	// if method of module can't be found, print a simple warning
	public function __call($function, $args) {
		// if (is_array($args)) $args=implode(',',$args);
		echo '<div class="warning">Method: `'.ucfirst($function)."()` doesn't exists.<div>";
	}


	// index is the standard method
	public function index($page) {
		return '<h1>'.__CLASS__.'</h1>';
	}


  // If this is called, no more modules and content are shown and loaded by the controller
	public function break_content() {
		$this->CI->site['content']='';
		$this->CI->site['break']=true;
	}
  
  
  // Methods for using uri parts as arguments for the module
  public function set_module_uri() {
    if (!isset($this->config['module_uri'])) $this->config['module_uri']=$this->CI->find_module_uri($this->name).'/'.$this->CI->config->item('PLUGIN_URI_ARGS_CHAR');
    return $this->config['module_uri'];
  }
  public function get_uri_args() {
    if (!isset($this->config['uri_args'])) $this->config['uri_args']=$this->CI->uri->get_from_part($this->CI->config->item('PLUGIN_URI_ARGS_CHAR'));
    return $this->config['uri_args'];
  }
  

}

?>