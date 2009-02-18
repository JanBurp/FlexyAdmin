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
	 * Here you have to decide according to the given url what is to be showed.
	 */
	function index() {
		/***********************************************
		 * Set home uri: which uri will be the homepage?
		 * (standard is 'home')
		 */
		$this->uri->set_home("blog");

		/***********************************************
		 * Set menu
		 * - loads menu
		 * - sets the current acive item from current uri
		 * - render
		 */
		$uri=$this->uri->get(1);
		$this->menu->set_current($uri);
		$this->menu->set_menu_from_table();
		$this->site["menu"]=$this->menu->render();


		/**********************************************
		 * Get content
		 */

		/**
		 * First the random text
		 */
		$this->site["random"]=$this->_get_random_text();
		/**
		 * Now the content (normal or from blog/portfolio)
		 */
		$query=$this->db->where("uri",$uri);
		$query=$this->db->get("tbl_menu");
		$item=$query->row_array();
		$item["str_title"]=ucfirst($item["str_title"]);
		$this->site["content"]=$this->_item($item);
		/**
		 * Module content
		 */
		$this->site["module"]=$item["str_module"];
		if ($this->site["module"]!="normaal") {
			$this->site["content"]="";
			$table="tbl_".$this->site["module"];
			// get items from table
			$items=$this->fd->get_results($table);
			foreach($items as $item) {
				$this->site["content"].=$this->_item($item);
				if (!empty($item["str_keywords"])) $this->site["keywords"].=",".$item["str_keywords"];
			}
		}

		/**
		 * View the page
		 */
		$this->load->site_view('home',$this->site);
	}

	/**
	 * extra functions:
	 * - start with _
	 * - call them from index as $this->_name()
	 */

	function _item($item) {
		$item["map"]=$this->site["assets"]."/pictures/";
		$item["txt_tekst"]=$this->content->render($item["txt_tekst"]);
		return $this->load->site_view('item',$item,true);
	}

	function _get_random_text() {
		$this->db->order_by("RAND()");
		$query=$this->db->get("tbl_random_teksten",1);
		$item=$query->row_array();
		return $item["txt_tekst"];//$this->content->render($item["txt_tekst"]);
	}

}

?>
