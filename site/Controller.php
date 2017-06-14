<?php
/** \ingroup controllers
 * FlexyAdmin frontend controller
 * 
 * Verzorgt de afhandeling van alle frontend uri's:
 * - Checkt of het een AJAX request is, zo ja laad dan de AJAX module
 * - Stelt de taal in
 * - Maakt het menu
 * - Laad modules
 * - Laad een pagina met eventueel gekoppelde modules
 * - Verzorgt de output
 * 
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Main extends FrontEndController {

	public function __construct() {
		parent::__construct();
	}

	
	/**
	 * Elke uri wordt hier afgehandeld
	 */
	public function index() {
    
    if (defined('PHPUNIT_TEST')) return;
		
    /********************************************
     * Als een AJAX request binnenkomt, stuur deze door naar de desbetreffende ajax module en roep de desbetreffende method aan.
     * De naam van de AJAX module komt overeen met 'ajax_' + het eerste deel van de uri. Het tweede deel bepaald eventueel de aan te roepen method.
     */
    if ($this->is_ajax_module) {
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
		if ($this->config->item('redirect') or el('b_redirect',$page,false)) $this->_redirect($page);


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
		 * Chache directory: SITEPATH.cache must be writable.
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
		// If not: Get prefered language from ?lang=xx
		if ( ! $this->_is_possible_language($lang) ) $lang=$this->input->get('lang');
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
	 * Redirect to a (set) page (and anchor), or down in the menu tree if current page is empty
	 */
	private function _redirect($page) {
		if ( el('b_redirect',$page,false) or (el('txt_text',$page,'')=='' and el('str_module',$page,'')=='') ) {
      if (el('list_redirect',$page,'')) {
        $newUri=$page['list_redirect'];
      }
      else {
        $this->data->table('tbl_menu');
  			$this->data->select('uri');
  			$this->data->where('self_parent',$page['id']);
  			if (isset($page['b_visible'])) $this->data->where('b_visible','1');
  			$subItem = $this->data->get_row();
  			if ($subItem) $newUri=$this->site['uri'].'/'.$subItem['uri'];
      }
      if (isset($newUri)) {
        if (el('str_anchor',$page,'')) {
          $newUri.='#'.$page['str_anchor'];
        }
				redirect($newUri,'refresh');
			}
		}
	}


}

?>
