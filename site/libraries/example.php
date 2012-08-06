<?

/**
 * Voorbeeld module, gebruik dit als basis voor je eigen modules
 *
 * @author Jan den Besten
 */
 
 class Example extends Module {


	public function __construct() {
		parent::__construct();
		// $this->CI->menu->register_change_module($this); // Call this if you use the change_menu_item() method.
	}

  /**
   * Hier komt je eigen code
   *
   * @param string $page 
   * @return void
   * @author Jan den Besten
   */
	public function index($page) {
		$content='<h1>Example Module</h1>';
		return $content;
	}


  /**
   * Eventueel andere methods kunnen ook 'example.other'
   *
   * @author Jan den Besten
   */
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