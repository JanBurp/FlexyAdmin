<?

/**
 * FlexyAdmin 2009
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin 2009
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2009-2010, Jan den Besten
 * @link			http://flexyadmin.com
 */

// ------------------------------------------------------------------------

/**
 * main Frontend Controller
 * This Controller handles the url and loads views of the site accordingly *
 */

class Main extends FrontEndController {

	/**
	 * $site is an array containing all data from tbl_site, and is the array which is given to the home view.
	 */
	var $site;
	
	/**
	 * $languages is an array containing all the possible language prefixes useds by the site.
	 */
	var $languages = array('nl');
	// var $languages = array('nl','en');


	function __construct() {
		parent::__construct();
	}


	
	/**
	 * function index()
	 *
	 * This is called everytime a page of you're site is loaded.
	 * Here you have to decide according to the given uri what is to be showed and what models/views are loaded.
	 */
	function index() {
		
		/***********************************************
		 * Set Language for localisation (set possible languages at the start of the controller, near line 30)
		 */
		$this->_set_language();


		/***********************************************
		 * Get current uri and give it to menu and add it to class
		 */
		$this->site['uri']=$this->uri->get();
		$this->menu->set_current($this->site['uri']);
		$this->add_class(str_replace('/','__',$this->site['uri']));


		/***********************************************
		 * Create menu from standard menu table (tbl_menu or res_menu_result if exists)
		 */
		$this->menu->set_menu_from_table();
		$this->site['menu']=$this->menu->render();

		// Example of a simple submenu, show $submenu somewhere in views/home.php
		//
		// $sub_uri$this->uri->get(1);
		// if ($sub_uri) {
		// 	$this->site["submenu"]=$this->menu->render_branch($sub_uri);
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
		 * If item exists call _page (which calls modules and loads views)
		 */
		if ($item) $item=$this->_page($item);


		/**********************************************
		 * No Content? Show error page.
		 */
		if ($this->no_content()) $this->add_content($this->show("error","",true));
		
		/**
		 * Show site
		 */
		$this->show();
	}





	/*************************************************
	 * Always start functions with '_' for safety
	 */


	/**
	 * function _set_language()
	 * 
	 * Sets the current prefered language of the visitor. If you use other methods (ie: query string or sessions), change it accordingly.
	 */
	function _set_language() {
		$lang='';

		// Is language set by the first part of the URI?
		if ( ! $this->_is_possible_language($lang) ) $lang=$this->uri->get(1);
		
		// If not: get prefered language from users browser settings
		if ( ! $this->_is_possible_language($lang) ) $lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		
		// If not: Get prefered language from config
		if ( ! $this->_is_possible_language($lang) ) $lang=$this->config->item('language');

		// Sets some stuff
		setlocale(LC_ALL, $lang.'_'.$lang);
		$this->site['language']=$lang;
		$this->add_class('language_'.$lang);
		return $lang;
	}
	
	// Test if language is set to a possible language (and not empty)
	function _is_possible_language($lang) {
		return (in_array($lang,$this->languages));
	}





	/*
	 * function _page($item)
	 * 
	 * This functions is called when a page item exists
	 * It handles what to do with the item and shows the content.
	 */
	
	function _page($item) {
		// Process the text fields (make safe email links, put classes in p/img/h tags)
		foreach($item as $f=>$v) {if (get_prefix($f)=="txt") $item[$f]=$this->content->render($v);}

		// Add extra title and keywords, replace description (if any)
		if (isset($item["str_title"])) $this->add_title($item["str_title"]);
		if (isset($item["str_keywords"])) $this->add_keywords($item["str_keywords"]);
		if (isset($item['stx_description']) and !empty($item['stx_description'])) $this->site['description']=$item['stx_description'];

		// Is there a module set? If so, call the module
		if (isset($item["str_module"]) and !empty($item["str_module"]))	$item=$this->_module($item);

		// Add content
		$this->add_content( $this->show('page',$item,true) );
		if (isset($item['module_content'])) $this->add_content($item['module_content']);

		return $item;
	}




	/*
	 * function _module($item)
	 * 
	 * This functions is called if a module is set
	 * It loads the module model and calls it, if needed it calls also the corresponding view
	 */
	function _module($item) {
		$modules=$item['str_module'];
		// Loop trough all possible modules
		$modules=explode('|',$modules);
		$item['module_content']='';
		foreach ($modules as $module) {
			// If model exists: load it, call the given or standard method, and add modelname to class
			$model=remove_postfix($module,'.');
			$method=get_postfix($model,'.');
			if ($method==$model) $method='main';
			if (file_exists('site/models/'.$model.'.php')) {
				$this->load->model($model);
				$modeldata=$this->$model->$method($item);
				if (is_string($modeldata))
					$item['module_content']=$modeldata;
				else {
					if (isset($modeldata['item'])) $item = $modeldata['item'];
					if (isset($modeldata['view']) and !empty($modeldata['view'])) $item['module_content'] = $this->show( $modeldata['view'], $modeldata, true );
				}
				$this->add_class('module_'.$model);
			}
		}
		return $item;
	}
	
	
	/*
	 * function _redirect($item)
	 * 
	 * Redirect to a page down in the menu tree, if current page is empty
	 */
	function _redirect($item) {
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
