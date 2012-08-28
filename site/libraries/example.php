<?

/**
  * Example
  *  
	* Voorbeeld module, gebruik dit als basis voor je eigen modules. Lees ook [Modules maken]({Modules-maken}).
	*
	* @author Jan den Besten
	*/
 
 class Example extends Module {

  /**
    * Initialiseer module
    *
    * @author Jan den Besten
    */
  public function __construct() {
    parent::__construct();
    // $this->CI->menu->register_change_module($this); // Als je change_menu_item() wilt gebruiken moet je dat hiermee aankondigen
  }

  /**
  	* Standaard wordt index() aangeroepen
  	* 
  	* Je kunt op 3 manieren met je module iets aan de inhoud van de site veranderen:
  	* 
  	* - return een string met de extra content die je wilt toevoegen aan de huidige pagina
  	* - return $page en pas dingen aan in $page
  	* - return niets en doe heel wat anders, bijvoorbeeld $this->CI->site aanpassen
  	*
  	* @param string $page 
  	* @return mixed
  	* @author Jan den Besten
  	*/
	public function index($page) {
		$content='<h1>Example Module</h1>';
		return $content;
	}


  /**
  	* Eventueel andere methods kunnen ook worden aangeroepen 'example.other'
  	*
  	* @param string $page 
  	* @return mixed
  	* @author Jan den Besten
  	*/
	public function other($page) {
		$page['module_content']='<h1>Example Module.Other</h1>';
		return $page;
	}



  // /**
   // * Deze method wordt aangeroepen door Menu zodat je eventueel een menu-item kunt aanpassen
   // *
   // * @param string $menu_item 
   // * @return void
   // * @author Jan den Besten
   // */
	// public function change_menu_item($menu_item) {
	// 	if ($menu_item['full_uri']==$this->CI->uri->get()) {
	// 		$menu_item['name']='EXAMPLE';
	// 	}
	// 	return $menu_item;
	// }


}

?>