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


		/***********************************************
		 * Init Menu
		 */
		$this->menu->set_current($this->site['uri']);
		$this->menu->set_menu_from_table();


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


		/**
		 * Rendering Menu and show site view
		 */
		$this->site['menu']=$this->menu->render();


		/**********************************************
		 * No Content? Show error page.
		 */
		if ($this->no_content()) $this->add_content($this->view('error','',true));


		/**
		 * Show site view
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
	 * If it has a return value it will be added to $item['module_content'].
	 */
	private function _module($item) {
		// See what modules to load
		$modules=array();
		if (isset($item[$this->config->item('module_field')]) and !empty($item[$this->config->item('module_field')])) {
			if (get_prefix($this->config->item('module_field'))=='id') {
				// Modules from foreign table
				$foreign_key=$this->config->item('module_field');
				$foreign_field='str_'.get_suffix($this->config->item('module_field'));
				$foreign_table=foreign_table_from_key($foreign_key);
				$modules=$this->db->get_field_where($foreign_table,$foreign_field,'id',$item[$foreign_key]);
			}
			else {
				// Modules direct from field
				$modules=$item[$this->config->item('module_field')];
			}
			$modules=explode('|',$modules);
		}
		// Autoload modules
		$autoload=$this->config->item('autoload_modules');
		if ($autoload) $modules=array_merge($autoload,$modules);
		// Loop trough all possible modules, load them, call them, and process return value
		// trace_($modules);
		$item['module_content']='';
		foreach ($modules as $module) {
			// split module and method
			$library=remove_suffix($module,'.');
			$method=get_suffix($module,'.');
			if ($method==$library) $method='index';
			// Load and call the module and process the return value
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
		if (!empty($library)) {
			// trace_('Module: '.$library.'.'.$method);
			$library_name=str_replace(' ','_',$library);
			if (file_exists(SITEPATH.'libraries/'.$library_name.'.php')) {
				// trace_('Loading module: '.$library_name.'.'.$method);
				$this->load->library($library_name);
				$this->$library_name->set_name($library);
				return $this->$library_name->$method($args);
			}
			elseif ($this->config->item('fallback_module')) {
				$fallback=$this->config->item('fallback_module');
				$fallback_name=str_replace(' ','_',$fallback);
				if (file_exists(SITEPATH.'libraries/'.$fallback_name.'.php')) {
					// trace_('Loading Fallback Module: '.$fallback_name.'.'.$method);
					$this->load->library($fallback_name);
					$this->$fallback->set_name($library);
					return $this->$fallback_name->index($args);
				}
			}
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
		if ( ! $this->_is_possible_language($lang) and isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
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
			if (isset($item['b_visible'])) $this->db->where('b_visible','1');
			$subItem=$this->db->get_row(get_menu_table());
			if ($subItem) {
				$newUri=$this->site['uri'].'/'.$subItem['uri'];
				redirect($newUri);
			}
		}
	}


}

?>
