<?

// This is an Example of a Module. A frontend library


class Example extends Module {


	public function __construct() {
		parent::__construct();
		// $this->CI->menu->register_change_module($this); // Call this if you use the change_menu_item() method.
	}

	// index is the standard method
	
	public function index($page) {
		$content='<h1>Example Module</h1>';
		return $content;
	}


	// You can call other methods from you're controller, in FlexyAdmin str_module would be 'example.other'

	public function other($page) {

		// Do something...
		$page['module_content']='<h1>Example Module.Other</h1>';
		
		// There are two ways to return something. Just a string wich will be added to the content after page, see index()
		// Or return $page with 'module_content' as an extra field (which will result in the same), 
		// or just change $page or even $this->CI->site.
		// Offcourse you can use views with $this->view();
		return $page;
	}



	// This is a method that will be called by Menu. Use it to change menu items
	// public function change_menu_item($menu_item) {
	// 	if ($menu_item['full_uri']==$this->CI->uri->get()) {
	// 		$menu_item['name']='EXAMPLE';
	// 	}
	// 	return $menu_item;
	// }


}

?>