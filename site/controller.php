<?
/**
 * FlexyAdmin
 * A Flexible Database based CMS
 *
 * @package FlexyAdmin
 * @author Jan den Besten
 * @copyright Copyright (c) 2009-2012, Jan den Besten
 * @link http://www.flexyadmin.com
 */

// ------------------------------------------------------------------------

/**
 * main Frontend Controller
 * This Controller handles the url and loads views of the site accordingly
 *
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
    if (IS_AJAX) {
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

		// Process the text fields (make safe email links, put classes in p/img/h tags)
		foreach($page as $f=>$v) {if (get_prefix($f)=='txt') $page[$f]=$this->content->render($v);}

		// Add extra title and keywords, replace description (if any)
		if (isset($page['str_title'])) $this->add_title($page['str_title']);
		if (isset($page['str_keywords'])) $this->add_keywords($page['str_keywords']);
		if (isset($page['stx_description']) and !empty($page['stx_description'])) $this->site['description']=$page['stx_description'];

		// Add page content (if no break)
    $page['show_page']=!$this->site['break'];
    
    $this->add_content( $this->view($this->config->item('page_view'),$page,true) );
		return $page;
	}


	/*
	 * function _module($page)
	 * 
	 * This functions collects all the modules which need to be loaded and called. Then calls them.
	 * If modules have a return value it will be added to $page['module_content'].
	 */
	protected function _module($page) {
		// See what modules to load
		$modules=array();
		// First autoload modules
		$autoload=$this->config->item('autoload_modules');
		if ($autoload) $modules=array_merge($autoload,$modules);
		// Autoload modules if
		$autoload_if=$this->config->item('autoload_modules_if');
		if ($autoload_if) {
			foreach ($autoload_if as $module_if => $where) {
				$load_if=FALSE;
				foreach ($where as $field => $value) {
					if (is_array($value)) {
						foreach ($value as $val) {
							$load_if = $load_if || $page[$field]==$val;
						}
					}
					else {
						$load_if = $load_if || $page[$field]==$value;
					}
				}
				if ($load_if) $modules[]=$module_if;
			}
		}
		// User set modules
		if (isset($page[$this->config->item('module_field')]) and !empty($page[$this->config->item('module_field')])) {
			$user_modules=$this->find_modules_in_item($page);
			$user_modules=explode('|',$user_modules);
			$modules=array_merge($modules,$user_modules);
		}
		// Keep it so modules can check what other modules are called
    $this->site['modules']=array_fill_keys($modules,'');
		// Loop trough all possible modules, load them, call them, and process return value
		$page['module_content']='';
		foreach ($modules as $module) {
			// split module and method
			$library=remove_suffix($module,'.');
			$method=get_suffix($module,'.');
			if ($module==$library) $method='index';
			// Load and call the module
			$return=$this->_call_library($library,$method,$page);
      // Process the return value according to module settings
			if ($return) {
        $to='';
        if ($method=='index') $to=$this->$library->config('__return','');
        if (empty($to)) $to=$this->$library->config('__return'.'.'.$method,'page');
        $to=explode('|',$to);
        // put result in site
        if (in_array('site',$to)) {
          $this->site['modules'][$module]=$return;
        }
        // put result in page (default)
        if (in_array('page',$to)) {
  				if (is_array($return))
  					$page=$return;
  				else
  					$page['module_content'].=$return;
        }
      }
			$this->add_class('module_'.str_replace('.','_',$module));
      // stop loading more modules if break is set
			if ($this->site['break']) break;
		}
		return $page;
	}


	/*
	 * function _call_library()
	 * 
	 * This functions loads the given library and calls it.
	 * Used for loading and calling modules
	 */
	public function _call_library($library,$method='index',$args=NULL) {
		if (is_array($library)) {
			$args=array_slice($library,2);
			$method=el(2,$library,'index');
			$library=el(1,$library,'app');
			// prevent loading hidden modules
			if (substr($library,0,1)=='_') return FALSE;
		}
		if (!empty($library)) {
			$library_name=str_replace(' ','_',$library);
			if (file_exists(SITEPATH.'libraries/'.$library_name.'.php')) {
				$this->load->library($library_name);
        $this->$library_name->set_name($library);
				return $this->$library_name->$method($args);
			}
			elseif ($this->config->item('fallback_module')) {
				$fallback=$this->config->item('fallback_module');
				$fallback_name=str_replace(' ','_',$fallback);
				if (file_exists(SITEPATH.'libraries/'.$fallback_name.'.php')) {
					$this->load->library($fallback_name);
          $this->$fallback_name->set_name($library);
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
				redirect($newUri);
			}
		}
	}


}

?>
