<?

// This is the basic Module library, used for frontend modules

class Module {

	var $CI;

	function __construct() {
		$this->CI=&get_instance();
	}

	// Module is the standard method
	function module($item) {
		return '<h1>Module</h1>';
	}


}

?>