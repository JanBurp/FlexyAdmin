<?

// This is the basic Plugin_ class used for backend plugins

class Plugin_ extends Flexy_library {
	
	var $content;
	var $oldData;
	var $newData;
	var $table;
	var $id;
	var $trigger=array();

	public function __construct($name='') {
		parent::__construct($name);
	}
	
	public function set_config($config=array()) {
    if (!isset($config['config'])) $config=array('config'=>$config);
		if (isset($config['config'])) parent::set_config($config['config']);
		if (isset($config['trigger'])) $this->trigger=$config['trigger'];
	}

	public function set_data($data) {
		if (isset($data['old'])) 		$this->oldData=$data['old'];
		if (isset($data['new'])) 		$this->newData=$data['new'];
		if (isset($data['table'])) 	$this->table=$data['table'];
		if (isset($data['id'])) 		$this->id=$data['id'];
	}

	// This returns the output, called by the Plugin Handler
	public function get_content() {
		return $this->content;
	}

	public function add_content($content) {
		$this->content.=$content;
		return $this->content;
	}
	
	// Hopefully not needed anymore in future... Return showtype (form,grid,actiongrid ...)
	function get_show_type() {
		return '';
	}


}

?>