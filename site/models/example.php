<?

// This is an Example of a Model module, more CI like than earlier modules


class Example extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	// Main is the method that is called standard.
	
	function main($item) {
		$content='<h2>EXAMPLE MODEL</h2>';
		// easiest way to return some content is by returning a simple string (with html)
		return $content;
	}


	// You can call other methods from you're controller, in FlexyAdmin str_module would be 'example.other'
	function other($item) {
		// You can return an array with the name for the view. The whole array is given to the view's data
		return array('view'=>'links','links'=>array());
	}

}

?>