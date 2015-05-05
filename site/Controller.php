<?php
/**
 * FlexyAdmin
 * 
 * A flexible userfriendly CMS build on CodeIgniter
 * 
 * Copyright (c) 2009-2015, Jan den Besten, www.flexyadmin.com
 * All rights reserved.
 * 
 * Disclaimer
 * 
 * De voorwaarden van deze disclaimer zijn van toepassing op het Content Management Systeem ‘FlexyAdmin’ (hierna te noemen ‘CMS’) ontwikkeld door Jan den Besten.
 * Door het CMS te gebruiken stemt u (hierna te noemen ‘gebruiker’) in met deze disclaimer.
 * 
 * De rechten op de inhoud van het CMS waaronder de rechten van intellectuele eigendom berusten bij Jan den Besten.
 * Onder de inhoud van dit CMS wordt onder meer verstaan: functionaliteit, ontwerpstructuur, database-structuur, teksten, lay-out, afbeeldingen, logo's, (beeld)merken, geluids- en/of videofragmenten, foto's, hulpdocumenten en andere artikelen exclusief alle inhoud die de gebruiker toevoegd.
 * Het maken van kopieën, aanpassingen, bewerkingen, wijzigingen van het geheel of van een gedeelte van het CMS in welke vorm of op welke manier dan ook zonder voorafgaande schriftelijke toestemming van Jan den Besten, is niet toegestaan.
 * 
 * Jan den Besten besteedt de uiterste zorg aan het zo actueel, toegankelijk, correct en compleet mogelijk maken en houden van de inhoud en de werking van het CMS.
 * De inhoud van het CMS houdt geen aanbieding in en er kunnen geen rechten aan worden ontleend.
 * 
 * Aanvullend biedt het CMS de mogelijkheid om te werken met profielen en persoonsinformatie.
 * Jan den Besten besteedt de uiterste zorg aan het zo veilig mogelijk maken en houden van deze informatie.
 * Door het CMS te gebruiken stemt u ermee in dat Jan den Besten op geen enkele wijze verantwoordelijk kan worden gehouden voor eventuele misstanden betreffende deze gegevens en/of voor eventuele gevolgschade.
 * Indien er zich een probleem voordoet, dient de gebruiker het probleem eerst en tijdig aan Jan den Besten aan te bieden, zodat naar een passende oplossing kan worden gezocht.
 * 
 * Dit CMS kan links bevatten naar websites of naar webpagina’s van derden.
 * Jan den Besten heeft geen zeggenschap over de inhoud of over andere kenmerken van deze websites en -pagina’s van derden en is in geen geval aansprakelijk of verantwoordelijk voor de inhoud ervan.
 * 
 * Alle rechten worden voorbehouden.
 * Op deze disclaimer is het Nederlands recht van toepassing.
 * 
 * Laatst bijgewerkt: mei 2015
 * 
 * $Author$
 * $Date$
 * $Revision$
 * 
 * @package	FlexyAdmin
 * @author	Jan den Besten
 * @copyright	(c) Jan den Besten
 * @link	http://flexyadmin.com
 */

// ------------------------------------------------------------------------

/**
 * Frontend Controller
 * This Controller handles the url and loads views of the site accordingly
 */

class Main extends FrontEndController {

	public function __construct() {
		parent::__construct();
	}

	
	/**
	 * function index()
	 *
	 * This is called everytime a page of you're site is loaded.
	 */
	public function index() {
		
    /********************************************
     * Als een AJAX request binnenkomt, stuur deze door naar de desbetreffende ajax module en roep de desbetreffende method aan.
     * De naam van de AJAX module komt overeen met 'ajax_' + het eerste deel van de uri. Het tweede deel bepaald eventueel de aan te roepen method.
     */
    if ($this->ajax_module) {
      $uri=$this->uri->segment_array();
      $ajax_module='ajax_'.array_shift($uri);
      $ajax_method=array_shift($uri);
      if (empty($ajax_method)) $ajax_method='index';
      $ajax_args=$uri;
      die($this->_call_library($ajax_module,$ajax_method,$ajax_args));
    }
    
		/***********************************************
		 * Set Language for localisation (set possible languages at the start of the controller, near line 30)
		 * See config.php for language settings
		 */
		$this->_set_language();


		/***********************************************
		 * If you need pagination for something, set the 'auto_pagination' config in config.php to TRUE.
		 * Set also $config['auto']=TRUE in the pagination config.
		 * Now you don't need to set $config['base_url'] and $config['uri_segment'], these are set automatic and uses uripart 'offset'.
		 */
		if ($this->config->item('auto_pagination')) {
			$this->load->library('pagination');
			$this->uri->remove_pagination();
		}


		/***********************************************
		 * Get current uri and add it to class
		 */
		$this->site['uri']=$this->uri->get();
		$this->add_class(str_replace('/','__',$this->site['uri']));


		/***********************************************
		 * Init Menu
		 */
    if ($this->config->item('menu')) $this->menu->initialize($this->config->item('menu'));
		$this->menu->set_current($this->site['uri']);
		$this->menu->set_menu_from_table();


		/***********************************************
		 * Get current page item from menu
		 */
		$page=$this->menu->get_item();


		/***********************************************
		 * Redirect to a page down in the menu tree, if current page is empty.
		 * If needed, set the redirect config to TRUE in config.php
		 */
		if ($this->config->item('redirect')) $this->_redirect($page);


		/***********************************************
		 * If item exists call _page
		 */
		if ($page) $page=$this->_page($page);


		/**
		 * Rendering Menu and show site view
		 */
		$this->site['menu']=$this->menu->render();


		/**********************************************
		 * No Content? Show error page.
		 */
    if ($this->no_content()) $this->show_404();

		/**
		 * Send the site view to the browser
		 */
		$this->view();
		
		/***********************************************
		 * Caching
		 * See: http://codeigniter.com/user_guide/general/caching.html
		 * and: http://stevenbenner.com/2010/12/caching-with-codeigniter-zen-headaches-and-performance
		 * Chache directory: site/cache must be writable.
		 *
		 * After each change in FlexyAdmin the whole cache is flushed. So don't worry about that.
		 * You have to flush the page yourself (or set an smaller time) if the page is (partly) dynamic with the cache_helper function: delete_cache( $this->uri->uri_string() );
		 * If $_POST or $_GET data are set (not empty) the page is not loaded from cache. So don't worry about forms etc.
		 */
		if ($this->config->item('caching')) $this->output->cache( $this->config->item('caching_time') );
	}




	/*
	 * function _page($page)
	 * 
	 * This functions is called when a page item exists
	 * It handles what to do with the item and shows the content.
	 */
	
	private function _page($page) {
		// Load and call modules
    $page=$this->_module($page);

		// Add extra title and keywords, replace description (if any)
		if (isset($page['str_title'])) $this->add_title($page['str_title']);
		if (isset($page['str_keywords'])) $this->add_keywords($page['str_keywords']);
		if (isset($page['stx_description']) and !empty($page['stx_description'])) $this->site['description']=$page['stx_description'];

		// Add page content (if no break)
    $page['show_page']=!$this->site['break'];
    
    $this->add_content( $this->view($this->config->item('page_view'),$page,true) );
		return $page;
	}


	/**
	 * function _set_language()
	 * 
	 * Sets the current prefered language of the visitor. If you use other methods (ie: query string or sessions), change it accordingly.
	 */
	private function _set_language() {
		$lang='';
		// Is language set by the first part of the URI?
		if ( ! $this->_is_possible_language($lang) ) $lang=$this->uri->get(1);
		// If not: get prefered language from users browser settings
		if ( ! $this->_is_possible_language($lang) and isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		// If not: Get prefered language from config
		if ( ! $this->_is_possible_language($lang) ) $lang=$this->config->item('language');
		// Sets some stuff
		setlocale(LC_ALL, $lang.'_'.strtoupper($lang));
		$this->site['language']=$lang;
		$this->add_class('language_'.$lang);
    $this->config->set_item('language',$lang);
		return $lang;
	}
	
	// Test if language is set to a possible language (and not empty)
	private function _is_possible_language($lang) {
		return (in_array($lang,$this->site['languages']));
	}

	
	
	/*
	 * function _redirect($page)
	 * 
	 * Redirect to a page down in the menu tree, if current page is empty
	 */
	private function _redirect($page) {
		if (empty($page['txt_text'])) {
			$this->db->select('uri');
			$this->db->where('self_parent',$page['id']);
			if (isset($page['b_visible'])) $this->db->where('b_visible','1');
			$subItem=$this->db->get_row(get_menu_table());
			if ($subItem) {
				$newUri=$this->site['uri'].'/'.$subItem['uri'];
				redirect($newUri, 'refresh');
			}
		}
	}


}

?>
