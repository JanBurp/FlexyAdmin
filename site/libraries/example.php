<?

// This is an Example of a Model module, more CI like than earlier modules


class Example {


	// Module is the standard method
	
	function module($item) {
		$content='<h1>Example Module</h1>';
		return $content;
	}

	// You can call other methods from you're controller, in FlexyAdmin str_module would be 'example.other'
	function other($item) {
		// Do something...
		$item['module_content']='<h1>Example Module.Other</h1>';
		
		// There are two ways to return something. Just a string wich will be added to the content after page
		// Or return $item with 'module_content' as an extra field (which will be added to the content after page), 
		// of just change $item.
		// Offcourse you can use views with $this->show();
		return $item;
	}

}

?>