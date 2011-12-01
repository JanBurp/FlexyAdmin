<?

// This is the basic Module library, used for frontend modules

class Module extends Flexy_library {

	function __construct($name='') {
		parent::__construct($name);
	}

	// if method of module can't be found, print a simple warning
	public function __call($function, $args) {
		$args=implode(',',$args);
		echo '<div class="warning">Method: `'.ucfirst($function)."(".$args.")` doesn't exists.<div>";
	}


	// index is the standard method
	public function index($item) {
		return '<h1>'.__CLASS__.'</h1>';
	}


	public function break_content() {
		$this->CI->site['content']='';
		$this->CI->site['break']=true;
	}

}

?>