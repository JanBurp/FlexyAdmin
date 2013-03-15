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
    * De module method kan twee soorten variabele teruggeven:
    * 
    * 1. Een string variabele met daarin HTML gegenereerd door de module (bijvoorbeeld met een eigen view). Dit wordt automatisch aan de pagina toegevoegd. (`$page['module_content']`).
    * 2. De complete array `$page` die later wordt gebruikt in de view `page.php`. Je kunt hiermee dus je pagina op allerlei manieren aanpassen.
    * 
    * In sommige gevallen wil je met je module geen aanpassingen doen aan de huidige pagina maar bijvoorbeeld een extra stuk content toevoegen in een kolom op de site.
    * Via een instelling kun je dan zorgen dat de output van de module aan `$site` wordt gegeven.
    * 
    * De instelling ziet er zo uit:
    * 
    *     $config['__return']='';
    * 
    * Dit zijn de mogelijk waarden:
    * 
    * - '' of 'page - dit is de standaard manier: de output wordt aan de pagina gegeven zoals hierboven beschreven.
    * - 'site' - geeft de returnwaarde aan `$this->site[module_naam.method]` (of als method index is: `$this->site[module_naam]`)
    * - Een combinatie is ook mogelijk, gescheiden door een pipe: 'page|site'
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
		$content='<h1>Example Module.Other</h1>';
		return $content;
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