<?

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2009, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
 */

// ------------------------------------------------------------------------

/**
 * main Frontend Controller
 *
 * This Controller handles the url and loads views of the site accordingly
 *
 */

class Main extends FrontEndController {

	/**
	 * $site is an array containing all data that's given to the site's view.
	 * It contains standard data, but you can add own data.
	 *
	 * Standard $site contains:
	 * 	$site["assets"]						Assets folder (set in flexyadmin_config)
	 * 	$site["title"]						Set in tbl_site:
	 * 	$site["author"]
	 *  $site["url"]
	 * 	$site["email"]
	 *  $site["description"]
	 * 	$site["keywords"]
	 */
	var $site;

	/**
	 * function Main()
	 *
	 * Just leave it this way.
	 */
	function Main() {
		parent::FrontEndController();
	}

	/**
	 * function index()
	 *
	 * This is called everytime your site is loaded.
	 */
	function index() {
		$this->load->helper('text');
		
		$this->site['uri']=$this->uri->get();
		$this->site['uri_array']=$this->uri->segment_array();
		
		/**
		 * View the page home.php
		 */
		$this->show();
	}

}

?>
