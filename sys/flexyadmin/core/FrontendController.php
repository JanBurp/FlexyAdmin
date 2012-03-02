<?


/**
 * FrontEndController Class extends MY_Controller
 *
 * Same as MY_Controller
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class FrontEndController extends MY_Controller {

	var $site;

	function __construct() {
		/**
		 * Init controller, and load all libraries
		 */
		parent::__construct();
		$this->load->library('user_agent');
		$this->load->helper('date');
		$this->load->helper("html_helper");
		$this->load->helper("language");
		$this->load->library("menu");
		$this->load->library("content",$this->config->item('parse_content'));
		$this->load->library('form_validation');
		/**
		 * Load standard Module Class
		 */
		$this->load->library('module');
				

		/**
			*	Set $_GET if asked for
			* See http://www.askaboutphp.com/tutorials/58/codeigniter-mixing-segment-based-url-with-querystrings.html
			* For this to work, config.php: $config['uri_protocol']	= "PATH_INFO";
			*/
		if ($this->config->item('query_urls'))	parse_str($_SERVER['QUERY_STRING'],$_GET);

		/**
		 * Init global site data
		 */
		$this->_init_globals();
	}
	
	// For compatibility with older sites
	function FrontEndController() {
		$this->__construct();
	}
	

	/**
	 * _init_globals()
	 *
	 * Here are all global site parameters set.
	 * - asset folder
	 * - all fields from tbl_site (the not standard with prefix)
	 */
	function _init_globals() {
		$this->site=array();

		/**
		 * Set global site info from tbl_site (if it doesn't exist, put some standard info)
		 */
		if ($this->db->table_exists("tbl_site")) {
			// $fields = $this->db->list_fields("tbl_site");
			$stdFields=array("str_title","str_author","url_url","email_email","stx_description","stx_keywords");
			$query=$this->db->get("tbl_site");
			$this->site=$query->row_array();
			$query->free_result();
			// remove the unneeded
			unset($this->site['id']);
			// rename standard fields
			foreach ($stdFields as $f) {
				if (isset($this->site[$f])) {
					$this->site[remove_prefix($f)]=$this->site[$f];
				}
			}
		}
		else {
			$this->site["title"]="title";
			$this->site["author"]="author of site";
			$this->site["url"]="http://www.flexyadmin.com/";
			$this->site["email"]="email of site administrator";
			$this->site["description"]="Put some site description here,";
			$this->site["keywords"]="site, keywords";
		}

		/**
		 * Set Asset folders
		 */
		$this->site["assets"]=assets();
		$this->site["rel_assets"]=$this->config->item("ASSETS");
		$this->site["admin_assets"]=admin_assets();

		/**
		 * Set Some Config
		 */
		$this->site['languages']=$this->config->item('languages');

		/**
		 * Declare and init some variables
		 */
		$declare=array('menu','content','break','class');
		if ($this->config->item('site_variables')) $declare=array_merge($declare,$this->config->item('site_variables'));	
		foreach ($declare as $variable) {
			$this->site[$variable]='';
		}


		/**
		 * Make sure that uri's after ':' are removed when trying to load a page. And so modules van use all uri-parts after ':'
		 */
     $this->uri->set_remove( $this->config->item('PLUGIN_URI_ARGS_CHAR') );
  
		
		/**
		 * Set home uri (top from tbl_menu) if content comes from database
		 */
     if ( $this->config->item('menu_autoset_home')) {
			$menuTable=get_menu_table();
			if ( ! empty($menuTable)) {
				if ($this->db->has_field($menuTable,'self_parent')) $this->db->order_as_tree();
				if ($this->db->has_field($menuTable,'uri')) {
					$this->db->select('uri');
					$top=$this->db->get_row($menuTable);
					$this->uri->set_home($top['uri']);
				}
				else {
					$this->uri->set_home('');
				}
			}
		}
		elseif ($this->config->item('menu_homepage_uri')) {
			$this->uri->set_home($this->config->item('menu_homepage_uri'));
		}
		
	}

	function add_keywords($words) {
		$this->site["keywords"]=add_string($this->site["keywords"],$words,",");
	}
	
	function add_title($title) {
		$this->site["title"].=" - ".$title;
	}

	function set_uri($uri) {
		$this->site["uri"]=$uri;
	}
	function get_uri($max=0) {
		if ($max==0) return $this->site["uri"];
		$u=explode("/",$this->site["uri"]);
		$u=array_slice($u,0,$max);
		return implode("/",$u);
	}

	function add_content($c) {
		if (!isset($this->site["content"]))
			$this->site["content"]=$c;
		else
			$this->site["content"].=$c;
	}
	
	function has_content() {
		return (isset($this->site["content"]) and !empty($this->site["content"]));
	}
	
	function no_content() {
		return !$this->has_content();
	}
	
	function add_class($class) {
		$this->site['class']=add_string($this->site['class'],$class,' ');
	}
	
	function add($key,$value) {
		$this->site[$key]=$value;
	}

	function get($key) {
		return el($key,$this->site);
	}

	function view($view='',$data='',$return=FALSE) {
		if (empty($view)) {
			$view=$this->config->item('main_view');
			if ( ! $view) $view='home'; // for backwards compatibility
		}
		if (empty($data)) $data=$this->site;
		return $this->load->view($view,$data,$return);
	}
	function show($view='home',$data='',$return=FALSE) {
		return $this->view($view,$data,$return);
	}
	
	
	function find_module_uri($module,$full_uri=true) {
		$this->db->select('id,uri');
		if ($full_uri) {
			$this->db->select('order,self_parent');
			$this->db->uri_as_full_uri();
		}
		if (get_prefix($this->config->item('module_field'))=='id') {
			// Modules from foreign table
			$foreign_key=$this->config->item('module_field');
			$foreign_field='str_'.get_suffix($this->config->item('module_field'));
			$foreign_table=foreign_table_from_key($foreign_key);
			$this->db->add_foreigns();
			$like_field=$foreign_table.'__'.$foreign_field;
		}
		else {
			// Modules direct from field
			$like_field=$this->config->item('module_field');
		}
		$this->db->like($this->config->item('module_field'),$module);
		$item=$this->db->get_row(get_menu_table());
		return $item['uri'];
	}

	function find_modules_in_item($item) {
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
		return $modules;
	}
	
	
	// These are just here for backward compability... ##DEPRICATED
	function getFormByModule($module) {
		$this->load->module('getform');
		return $this->getform->by_module($module);
	}
	function getFormByTitle($title) {
		$this->load->module('getform');
		return $this->getform->by_title($module);
	}
	function getFormById($id) {
		$this->load->module('getform');
		return $this->getform->by_id($module);
	}
	
}

?>