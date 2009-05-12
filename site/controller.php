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
		$this->load->helper('text');
	
		/***********************************************
		 * Sets current URI
		 */
		$this->set_uri($this->uri->get());
		
		/***********************************************
		 * Set menu
		 * - loads menu
		 * - render
		 */
		$this->menu->set_current($this->get_uri());
		$this->menu->set_menu_from_table();
		$this->site["menu"]=$this->menu->render();

		/**********************************************
		 * Get content and show the page
		 */
		$this->db->where_uri($this->get_uri());
		$item=$this->db->get_row("tbl_menu");
		if (!empty($item)) $this->_page($item);

		/**********************************************
		 * No Content? Show error page.
		 */
		if ($this->no_content())
			$this->add_content($this->show("error","",true));
		
		/**
		 * View the page
		 */
		$this->show();
	}

	/**
	 * extra functions:
	 * - start with _
	 * - call them from index as $this->_name()
	 */

	function _page($item) {
		$this->content->add_popups();
		// Make safe content from all txt_ fields
		foreach($item as $f=>$v) {
			if (get_prefix($f)=="txt") $item[$f]=$this->content->render($v);
		}
		// Add title to title of site
		if (isset($item["str_title"])) $this->add_title($item["str_title"]);
		// Add keywords if any
		if (isset($item["str_keywords"])) $this->add_keywords($item["str_keywords"]);
		// Add page to content of the site
		$this->add_content($this->show('page',$item,true));
		// Is there a module set? If so, add module content.
		if (isset($item["tbl_module__str_module"]) and !empty($item["tbl_module__str_module"]))
			$this->_module($item);
	}

	function _module($item) {
		$type=$item["tbl_module__str_type"];
		switch ($type) {
				
			case 'sitemap':
				$table=$item["tbl_module__table"];
				$sitemap=new menu();
				$sitemap->set_current($this->get_uri());
				$sitemap->set_menu_from_table($table);
				$this->add_content($sitemap->render());
				break;
				
			case 'contact_form'	:
				$this->_contact_form($item);
				break;
			case 'contact_form_send':
				break;
				
			case 'table' :
			default:
				$table=$item["tbl_module__table"];
				$fields=str_replace("|",",",$item["tbl_module__fields"]);
				$view=$item["tbl_module__str_view"];
				$this->db->select($fields);
				$data=$this->db->get_result($table);
				$this->add_content($this->show($view,array("table"=>$table,"fields"=>$fields,"view"=>$view,"links"=>$data),true));
				break;
				
		}
	}

// 
// 	function _contact_form($item) {
// 		$this->load->library('form_validation');
// 		$this->load->helper('html');
// 		$this->load->helper('language');
// 		$this->load->model("form");
// 		$this->lang->load("update_delete");
// 		$formData=array(
// 								"str_name"		=>array(	"type"				=>	"input",
// 																				"label"				=>	"Naam",
// 																				"name"				=>	"str_name",
// 																				"value"				=>	"",
// 																				"validation"	=>  "required"),
// 								"email_email"	=>array(	"type"				=>	"input",
// 																				"label"				=>	"Email",
// 																				"name"				=>	"email_email",
// 																				"value"				=>	"",
// 																				"validation"	=>  "required|valid_email"),
// 								"str_subject"	=>array(	"type"				=>	"input",
// 																				"label"				=>	"Onderwerp",
// 																				"name"				=>	"str_subject",
// 																				"value"				=>	"",
// 																				"validation"	=>  "required"),																				
// 								"txt_text"		=>array(	"type"				=>	"textarea",
// 																				"label"				=>	"Vraag",
// 																				"name"				=>	"txt_text",
// 																				"value"				=>	"",
// 																				"validation"	=>  "required"));
// 		$form=new form($this->get_uri());
// 		$form->set_data($formData,"Contact");
// 		/**
// 		 * Validate form, if succes, make form do an update
// 		 */
// 		if ($form->validation()) {
// 			// Send form
// 			$this->db->select("email_email,str_author");
// 			$to=$this->db->get_result("tbl_site",1);
// 			$to=current($to);
// 			$data=$form->get_data();
// 			$this->load->library('email');
// 			$this->email->to($to["email_email"],$to["str_author"]);
// 			$this->email->from($data["email_email"]["value"]);
// 			$this->email->subject("Email van site: '".$data["str_subject"]["value"]."'");
// 			$this->email->message($data["txt_text"]["value"]);
// 			$this->email->send();
// 			// redirect to send page
// 			$this->db->add_foreigns();
// 			$this->db->select("uri");
// 			$this->db->where("id","44");
// 			$res=$this->db->get_result("tbl_menu");
// 			$res=current($res);
// 			redirect($res["uri"]);
// 		}
// 		else {
// 			// Show form
// 			$validationErrors=validation_errors('<div class="error">', '</div>');
// 			if (!empty($validationErrors)) $this->add_content($validationErrors);
// 			$this->add_content($form->render());
// 		}
// 	}


}

?>
