<?

// This is an Example of a Model module, more CI like than earlier modules


class Example_file extends Model {

	function Example_file() {
		parent::Model();
	}

	// Main is the method that normaly would be called.
	function main($item) {
		$this->site['content'].='<h2>EXAMPLE MODEL</h2>';
		// Return the item if no view is needed (you can change $item if you need to)
		return $item; 
	}
	
	// You can call other methods, in FlexyAdmin str_module would be 'example_file.other'
	function other($item) {
		// if you need to call a view, return an array with 'view'=>'yourview' and the data you need to give the view
		return array('view'=>'links','links'=>array());
	}

}

?>