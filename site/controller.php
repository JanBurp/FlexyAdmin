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
	 *
	 * Standard $site contains:
	 * 	$site["assets"]						Assets folder
	 * 	$site["admin_assets"]			FlexyAdmin Assets (folder for including jQuery etc.)
	 * 	$site["title"]						All data set in tbl_site, at least:
	 * 	$site["author"]
	 *  $site["url"]
	 * 	$site["email"]
	 *  $site["description"]
	 * 	$site["keywords"]
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
	 * Here you have to decide according to the given url what is to be showed.
	 */

	function index() {
		
		/***********************************************
		 * Default information & localisation
		 */
		$this->site['language']=$this->config->item('language');
		setlocale(LC_ALL, $this->site['language'].'_'.strtoupper($this->site['language']));
		$this->site['uri']=$this->uri->get();

		/***********************************************
		 * Set current uri to menu & gets menu data from standard menu table & creates menu
		 */
		$this->menu->set_current($this->site['uri']);
		$this->menu->set_menu_from_table();
		$this->site["menu"]=$this->menu->render();

		/**********************************************
		 * Get content according to current uri and show the page
		 */
		$this->db->where_uri($this->site['uri']);
		$item=$this->db->get_row('tbl_menu');
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




	/**
	 * extra functions:
	 * - start names with '_'
	 */


	function _page($item) {
		// Get content through the content class (replaces mailto into spamsafe javascript, creates extra classes for p and img tags, additional creates popups of images with a longdesc parameter)
		// $this->content->add_popups();
		foreach($item as $f=>$v) {if (get_prefix($f)=="txt") $item[$f]=$this->content->render($v);}

		// Add extra title and keywords to the sites meta tags
		if (isset($item["str_title"])) $this->add_title($item["str_title"]);
		if (isset($item["str_keywords"])) $this->add_keywords($item["str_keywords"]);
		// Replace description if any
		if (isset($item['stx_description'])) $this->site['description']=$item['stx_description'];

		// Add this page to the sites content
		$content=$this->show('page',$item,true);
		$this->add_content($content);
		
		// Is there a module set? If so, add module content.
		if (isset($item["str_module"]) and !empty($item["str_module"]))	$this->_module($item);
	}






	function _module($item) {
		$type=$item["str_module"];
		switch ($type) {
				
			case 'contact_form'	:
				$this->_contact_form($item);
				break;
			case 'contact_form_send':
				break;

		}
	}


	function _contact_form($item) {
		$this->load->library('form_validation');
		$this->load->helper('html');
		$this->load->helper('language');
		$this->load->model("form");
		$this->lang->load("update_delete");
		$this->lang->load("form");
		$formData=array(
								"str_name"		=>array(	"type"				=>	"input",
																				"label"				=>	"Naam",
																				"name"				=>	"str_name",
																				"value"				=>	"",
																				"validation"	=>  "required"),
								"email_email"	=>array(	"type"				=>	"input",
																				"label"				=>	"Email",
																				"name"				=>	"email_email",
																				"value"				=>	"",
																				"validation"	=>  "required|valid_email"),
								"str_subject"	=>array(	"type"				=>	"input",
																				"label"				=>	"Onderwerp",
																				"name"				=>	"str_subject",
																				"value"				=>	"",
																				"validation"	=>  "required"),																				
								"txt_text"		=>array(	"type"				=>	"textarea",
																				"label"				=>	"Vraag",
																				"name"				=>	"txt_text",
																				"value"				=>	"",
																				"validation"	=>  "required"));
		$form=new form($this->get_uri());
		$form->set_data($formData,"Contact");
		/**
		 * Validate form, if succes, make form do an update
		 */
		if ($form->validation()) {
			// Send form
			$this->db->select("email_email,str_author");
			$to=$this->db->get_result("tbl_site",1);
			$to=current($to);
			$data=$form->get_data();
			$this->load->library('email');
			$this->email->to($to["email_email"],$to["str_author"]);
			$this->email->from($data["email_email"]["value"]);
			$this->email->subject("Email van site: '".$data["str_subject"]["value"]."'");
			$this->email->message($data["txt_text"]["value"]);
			$this->email->send();
			// redirect to send page
			$this->db->add_foreigns();
			$this->db->select("uri");
			$this->db->where("id","44");
			$res=$this->db->get_result("tbl_menu");
			$res=current($res);
			redirect($res["uri"]);
		}
		else {
			// Show form
			$validationErrors=validation_errors('<div class="error">', '</div>');
			if (!empty($validationErrors)) $this->add_content($validationErrors);
			$this->add_content($form->render());
		}
	}


}

?>
