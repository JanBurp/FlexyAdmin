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
	 * $site is an array containing all data that's given to the site's view.
	 * It contains standard data, but you can add own data.
	 */
	var $site;

	/**
	 * function Main(), Just leave it this way.
	 */
	function Main() {
		parent::FrontEndController();
	}




	
	/**
	 * function index()
	 *
	 * This is called everytime your site is loaded.
	 * Here you have to decide according to the given uri what is to be showed.
	 */

	function index() {
		
		/***********************************************
		 * Set Language for localisation
		 */
		$this->site['language']=$this->config->item('language');
		setlocale(LC_ALL, $this->site['language'].'_'.strtoupper($this->site['language']));
		
		
		/***********************************************
		 * Get current uri and give it to menu
		 */
		$this->site['uri']=$this->uri->get();
		$this->menu->set_current($this->site['uri']);
		

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
		 * Get current page item from menu (with current uri)
		 */
		$item=$this->menu->get_item();


		/***********************************************
		 * Redirect to a page down, if current page is empty
		 */
		// if (empty($item['txt_text'])) {
		// 	$this->db->select('uri');
		// 	$this->db->where('self_parent',$item['id']);
		// 	$subItem=$this->db->get_row('res_menu_result');
		// 	if ($subItem) {
		// 		$newUri=$this->site['uri'].'/'.$subItem['uri'];
		// 		redirect($newUri);
		// 	}
		// }


		/***********************************************
		 * If item exists call _page
		 */
		if ($item) $this->_page($item);


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
	 * Extra functions start here.
	 * Always start your function with '_' for safety
	 */





	/*
	 * function _page($item)
	 * 
	 * This functions is called when a page item exists
	 * It handles what to do with the item and put the content.
	 */

	function _page($item) {
		// Process the text fields (make safe email links, put classes in p/img/h tags)
		foreach($item as $f=>$v) {if (get_prefix($f)=="txt") $item[$f]=$this->content->render($v);}

		// Add extra title and keywords, replace description (if any)
		if (isset($item["str_title"])) $this->add_title($item["str_title"]);
		if (isset($item["str_keywords"])) $this->add_keywords($item["str_keywords"]);
		if (isset($item['stx_description']) and !empty($item['stx_description'])) $this->site['description']=$item['stx_description'];

		// Add this page to the content
		$content=$this->show('page',$item,true);
		$this->add_content($content);
		
		// Is there a module set? If so, call the module function
		if (isset($item["str_module"]) and !empty($item["str_module"]))	$this->_module($item);
	}


	/*
	 * function _module($item)
	 * 
	 * This functions is called if a module is set
	 * It checks if a module file method exists, if so calls it.
	 * If not, if a module file exist (site/modules/...) load and call it.
	 */

	function _module($item) {
		$modules=$item['str_module'];
		
		// Loop trough all possible modules
		$modules=explode('|',$modules);
		foreach ($modules as $module) {
			$moduleFunction='_module_'.$module;
			// does module function exists (here in controller.php)?
			if (method_exists($this,$moduleFunction)) {
				// Yes, call module
				$this->$moduleFunction($item);
			}
			else {
				// No: Try to load the module from site/modules/
				$moduleFile='site/modules/module_'.$module.'.php';
				if (file_exists($moduleFile)) {
					// Load file
					include_once('site/modules/module_'.$module.'.php');
					// if function exists, call it
					if (function_exists($moduleFunction)) {
						$moduleFunction($item);
					}
				}
			}
		}

	}

	
	
	/*******************
	* Module functions
	* always starts with _module_
	*/ 
	
	function _module_example($item) {
		$this->add_content('<h2>MODULE EXAMPLE</h2>');
	}


}

?>
