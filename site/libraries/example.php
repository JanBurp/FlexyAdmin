<?

// This is an Example of a Module. A frontend library


class Example extends Module {


	// index is the standard method
	
	public function index($item) {
		$content='<h1>Example Module</h1>';
		return $content;
	}


	// You can call other methods from you're controller, in FlexyAdmin str_module would be 'example.other'

	public function other($item) {

		// Do something...
		$item['module_content']='<h1>Example Module.Other</h1>';
		
		// There are two ways to return something. Just a string wich will be added to the content after page, see index()
		// Or return $item with 'module_content' as an extra field (which will result in the same), 
		// or just change $item or even $this->CI->site.
		// Offcourse you can use views with $this->view();
		return $item;
	}

}

?>