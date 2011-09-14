<?

/**
 * FlexyAdmin 2009
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin 2009
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2009-2011, Jan den Besten
 * @link			http://flexyadmin.com
 */

// ------------------------------------------------------------------------

/**
 * main Frontend Controller
 * This Controller handles the url and loads views of the site accordingly *
 */

class Main extends FrontEndController {

	public function __construct() {
		parent::__construct();
	}

	
	/**
	 * function index()
	 *
	 * This is called everytime a page of you're site is loaded.
	 * Here you have to decide according to the given uri what is to be showed and what models/views are loaded.
	 */
	public function index() {
		
		/***********************************************
		 * Set Language for localisation (set possible languages at the start of the controller, near line 30)
		 * See site/config/config.php for language settings
		 */
		$this->_set_language();


		/***********************************************
		 * If you need pagination for something, uncomment these lines and just set $config['auto']=TRUE in the pagination config.
		 * You don't need to set $config['base_url'] and $config['uri_segment'], these are set automatic. uripart 'offset' is used standard.
		 */
		// $this->load->library('pagination');
		// $this->uri->remove_pagination();


		/***********************************************
		 * Get current uri and add it to class
		 */
		$this->site['uri']=$this->uri->get();
		$this->add_class(str_replace('/','__',$this->site['uri']));


		if ($this->config->item('uri_as_modules')) {
			
			/***********************************************
			 * Load and call module (library) according to uri: file/method/args
			 */
			$uri=$this->uri->segment_array();
			$this->_call_library($uri);
			
		}
		else {

			/***********************************************
			 * Create menu from menu table
			 */
			$this->menu->set_current($this->site['uri']);
			$this->menu->set_menu_from_table();
			$this->site['menu']=$this->menu->render();

			// Example of a simple submenu, show $submenu somewhere in views/site.php
			//
			// $sub_uri$this->uri->get(1);
			// if ($sub_uri) {
			// 	$this->site['submenu']=$this->menu->render_branch($sub_uri);
			// }


			/***********************************************
			 * Get current page item from menu
			 */
			$item=$this->menu->get_item();


			/***********************************************
			 * Redirect to a page down in the menu tree, if current page is empty.
			 * Comment this if not neeeded
			 */
			// $this->_redirect($item);

			/***********************************************
			 * If item exists call _page (which calls modules and loads views if set)
			 */
			if ($item) $item=$this->_page($item);
		}


		/**********************************************
		 * No Content? Show error page.
		 */
		if ($this->no_content()) $this->add_content($this->view('error','',true));
		
		/**
		 * Show home view
		 */
		$this->view();

		
		/***********************************************
		 * Caching
		 * See: http://codeigniter.com/user_guide/general/caching.html
		 * and: http://stevenbenner.com/2010/12/caching-with-codeigniter-zen-headaches-and-performance
		 * Chache directory: site/cache must be writable.
		 * After each change in admin the whole cache is flushed. So don't worry about that.
		 * You have to flush the page yourself if the page is (partly) dynamic with the cache_helper function: delete_cache( $this->uri->uri_string() );
		 * Or decide if the page needs to be cached or not.
		 */

		// $this->output->cache(1440); // cache for 24 hours (1440 minutes)
	}





	/*
	 * function _page($item)
	 * 
	 * This functions is called when a page item exists
	 * It handles what to do with the item and shows the content.
	 */
	
	private function _page($item) {
		// Process the text fields (make safe email links, put classes in p/img/h tags)
		foreach($item as $f=>$v) {if (get_prefix($f)=='txt') $item[$f]=$this->content->render($v);}

		// Add extra title and keywords, replace description (if any)
		if (isset($item['str_title'])) $this->add_title($item['str_title']);
		if (isset($item['str_keywords'])) $this->add_keywords($item['str_keywords']);
		if (isset($item['stx_description']) and !empty($item['stx_description'])) $this->site['description']=$item['stx_description'];

		// Load and call modules
		$item=$this->_module($item);

		// Add content
		$this->add_content( $this->view('page',$item,true) );
		if (isset($item['module_content'])) $this->add_content($item['module_content']);

		return $item;
	}




	/*
	 * function _module($item)
	 * 
	 * This functions is called if a module is set
	 * It loads the module (a special CI library) and calls it.
	 * If it has a return value, check if it is $item of just a string.
	 */
	private function _module($item) {
		$modules=array();
		if (isset($item[$this->config->item('module_field')])) {
			$modules=$item[$this->config->item('module_field')];
			$modules=explode('|',$modules);
		}
		// Autoload modules
		$autoload=$this->config->item('autoload_modules');
		if ($autoload) $modules=array_merge($autoload,$modules);
		// Loop trough all possible modules, load them, call them, and process return value
		$item['module_content']='';
		foreach ($modules as $module) {
			// If module exists (a library): load it, call the given or standard method, and add modelname to class
			$library=remove_suffix($module,'.');
			$method=get_suffix($library,'.');
			if ($method==$library) $method='index';
			$return=$this->_call_library($library,$method,$item);
			if ($return) {
				if (is_array($return))
					$item=$return;
				else
					$item['module_content'].=$return;
				$this->add_class('module_'.$module);
			}
		}
		return $item;
	}

	/*
	 * function _call_library()
	 * 
	 * This functions loads the given library and calls it.
	 * Used for loading and calling modules
	 */
	private function _call_library($library,$method='index',$args=NULL) {
		if (is_array($library)) {
			$args=array_slice($library,2);
			$method=el(2,$library,'index');
			$library=el(1,$library,'app');
			// prevent loading hidden modules
			if (substr($library,0,1)=='_') return FALSE;
		}
		if (file_exists('site/libraries/'.$library.'.php')) {
			$this->load->library($library);
			return $this->$library->$method($args);
		}
		return FALSE;
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
		if ( ! $this->_is_possible_language($lang) ) $lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		// If not: Get prefered language from config
		if ( ! $this->_is_possible_language($lang) ) $lang=$this->config->item('language');
		// Sets some stuff
		setlocale(LC_ALL, $lang.'_'.strtoupper($lang));
		$this->site['language']=$lang;
		$this->add_class('language_'.$lang);
		return $lang;
	}
	
	// Test if language is set to a possible language (and not empty)
	private function _is_possible_language($lang) {
		return (in_array($lang,$this->site['languages']));
	}

	
	
	/*
	 * function _redirect($item)
	 * 
	 * Redirect to a page down in the menu tree, if current page is empty
	 */
	private function _redirect($item) {
		// Use you're own test if you need to.
		if (empty($item['txt_text'])) {
			$this->db->select('uri');
			$this->db->where('self_parent',$item['id']);
			$subItem=$this->db->get_row(get_menu_table());
			if ($subItem) {
				$newUri=$this->site['uri'].'/'.$subItem['uri'];
				redirect($newUri);
			}
		}
	}


}

?>
