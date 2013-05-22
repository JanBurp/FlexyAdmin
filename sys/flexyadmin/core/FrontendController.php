<?

/**
 * Dit is de basis voor de controller aan de frontend (site/controller.php)
 *
 * @package default
 * @author Jan den Besten
 */
class FrontEndController extends MY_Controller {

  /**
   * Array waar alle onderdelen van de site terechtkomen (zie [Frontend Controller](Frontend-controller))
   * 
   * Standaard worden de volgende onderdelen klaargezet:
   * 
   *     $this->site              = array(
   *      ['title']                => 'Titel',                          // Titel van de site (= tbl_site.str_title)
   *      ['author']               => 'Jan den Besten',                 // Auteur van de site (= tbl_site.str_author)
   *      ['url']                  => 'http://www.flexyadmin.com',      // Url van de site (= tbl_site.url_url)
   *      ['email']                => 'info@flexyadmin.com',            // Email van de site (= tbl_site.email_email) (Deze wordt standaard gebruikt bij de modules contact_form en comments)
   *      ['description']          => '',                               // Description (= tbl_site.stx_description)
   *      ['keywords']             => '',                               // Keywords (= tbl_site.stx_keywords)
   *      ['str_google_analytics'] => '',                               // Wordt gebruikt voor Google Analytics. (= tbl_site.str_google_analytics)
   *      ['assets']               => 'site/assets',                    // verwijzing naar de assets map
   *      ['admin_assets']         => 'sys/flexyadmin/assets',          // verwijzing naar de flexyadmin assets map
   *      ['languages']            => array('nl'),                      // array met mogelijke talen. Zoals ingesteld in _site/config/config.php_
   *      ['uri']                  => '',                               // Uri van huidige pagina
   *      ['menu']                 => '',                               // Bij aanvang leeg, wordt gevuld met het menu (HTML)
   *      ['content']              => '',                               // Bij aanvang leeg. Wordt gevuld met alle HTML content
   *      ['class']                => '',                               // Bij aanvang leeg. Wordt gebruikt om een class mee te geven aan de body tag
   *      ['break']                => FALSE                             // Als TRUE dan heeft een module aangegeven dat er verder geen content en modules mogen worden geladen en getoond
   *     )
   *
   * @var array
   */
	public $site;


  /**
    * @ignore
    */
	public function __construct() {
		// Init controller, and load all libraries
		parent::__construct();

    // Set $_GET if asked for
    // See http://www.askaboutphp.com/tutorials/58/codeigniter-mixing-segment-based-url-with-querystrings.html
    // For this to work, config.php: $config['uri_protocol']  = "PATH_INFO";
		if ($this->config->item('query_urls'))	parse_str($_SERVER['QUERY_STRING'],$_GET);

    
    if (IS_AJAX) {
  		// Load standard Ajax Module Class
  		$this->load->library('ajax_module');
    }
    else {
  		// Load standard Module Class & Formaction model
  		$this->load->library('module');
      $this->load->model('formaction');
      // Load frontend helpers, libraries and so on
  		$this->load->library('user_agent');
  		$this->load->helper('date');
  		$this->load->helper("html_helper");
  		$this->load->helper("language");
  		$this->load->library("menu");
  		$this->load->library("content");
      $this->content->initialize($this->config->item('parse_content'));
  		$this->load->library('form_validation');
    }
		// Init global site data
		$this->_init_globals();
	}
	
	/**
	 * For compatibility with older sites
	 *
	 * @return void
	 * @author Jan den Besten
	 * @depricated
   * @ignore
	 */
	public function FrontEndController() {
		$this->__construct();
	}
	

	/**
	 * Stop alle globale variabelen in $site
	 *
	 * @return void
	 * @author Jan den Besten
	 * @internal
	 * @ignore
	 */
  private function _init_globals() {
		$this->site=array();

		/**
		 * Set global site info from tbl_site (if it doesn't exist, put some standard info)
		 */
		if ($this->db->table_exists("tbl_site")) {
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


    if (!IS_AJAX) {

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
   				if ($this->db->field_exists('self_parent',$menuTable)) $this->db->order_as_tree();
   				if ($this->db->field_exists('uri',$menuTable)) {
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
	}


  /**
   * Voeg keywords toe
   *
   * @param string $words 
   * @return void
   * @author Jan den Besten
   */
	public function add_keywords($words) {
		$this->site["keywords"]=add_string($this->site["keywords"],$words,",");
	}
	
  /**
   * Voeg achter de titel nog een extra tekst toe (gescheiden door -)
   *
   * @param string $title 
   * @return void
   * @author Jan den Besten
   */
	public function add_title($title) {
		$this->site["title"].=" - ".$title;
	}

  /**
   * Zet huidige uri in $site['uri']
   *
   * @param string $uri 
   * @return void
   * @author Jan den Besten
   */
	public function set_uri($uri) {
		$this->site["uri"]=$uri;
	}
  
  /**
   * Pakt huidige uri uit $site['uri']
   *
   * @param string $max[0] Aantal parts 
   * @return void
   * @author Jan den Besten
   */
	public function get_uri($max=0) {
		if ($max==0) return $this->site["uri"];
		$u=explode("/",$this->site["uri"]);
		$u=array_slice($u,0,$max);
		return implode("/",$u);
	}

  /**
   * Voegt meer content toe (aan $site['content'])
   *
   * @param string $c Extra content
   * @return void
   * @author Jan den Besten
   */
	public function add_content($c) {
		if (!isset($this->site["content"]))
			$this->site["content"]=$c;
		else
			$this->site["content"].=$c;
	}
	
  /**
   * Test of er wel content is
   *
   * @return bool FALSE als $site['content'] leeg is
   * @author Jan den Besten
   */
	public function has_content() {
		return (isset($this->site["content"]) and !empty($this->site["content"]));
	}
	
  /**
   * Zelfde als has_content() maar dan omgekeerd
   *
   * @return bool TRUE als er geen content is
   * @author Jan den Besten
   */
	public function no_content() {
		return !$this->has_content();
	}
	
  
  /**
   * Laad een error 404 pagina zien, met alle modules geladen
   *
   * @return void
   * @author Jan den Besten
   */
  public function show_404() {
    $this->site['title'].=' - Error 404';
    $page=array();
    $page['str_title']='';
    $page['txt_text']=$this->view('error','',true);
      
  	// Load and call modules
    $page=$this->_module($page);
		// Add page content (if no break)
    $page['show_page']=!$this->site['break'];
    $this->add_content( $this->view($this->config->item('page_view'),$page,true) );
	}
  
  /**
   * Voegt een class toe aan de body tag ($site['class'])
   *
   * @param string $class
   * @author Jan den Besten
   */
	public function add_class($class) {
		$this->site['class']=add_string($this->site['class'],$class,' ');
	}
	
  /**
   * Voegt een variabel aan $site toe
   *
   * @param string $key 
   * @param string $value 
   * @return void
   * @author Jan den Besten
   */
	public function add($key,$value) {
		$this->site[$key]=$value;
	}

  /**
   * Pakt bepaalde waarde van $site
   *
   * @param string $key 
   * @return mixed
   * @author Jan den Besten
   */
	public function get($key) {
		return el($key,$this->site);
	}

  /**
   * Laad een view, praktisch hetzelfde als standaard CodeIgniter $this->load->view()
   *
   * @param string $view[''] Als leeg dan wordt de main view geladen die ingesteld is in de config
   * @param string $data[''] Als leeg dan wordt $site meegegeven
   * @param string $return[FALSE]
   * @return string
   * @author Jan den Besten
   */
	public function view($view='',$data='',$return=FALSE) {
		if (empty($view)) {
			$view=$this->config->item('main_view');
			if ( ! $view) $view='home'; // for backwards compatibility
		}
		if (empty($data)) $data=$this->site;
		return $this->load->view($view,$data,$return);
	}
  
  /**
   * Zelfde als view()
   *
   * @param string $view 
   * @param string $data 
   * @param string $return 
   * @return void
   * @author Jan den Besten
   */
	public function show($view='',$data='',$return=FALSE) {
		return $this->view($view,$data,$return);
	}
	
	
  /**
   * Geeft de modules die bij huidige pagina horen
   *
   * @param string $page
   * @return mixed string als één module, array als meerdere modules
   * @author Jan den Besten
   */
	public function find_modules_in_item($page) {
		if (get_prefix($this->config->item('module_field'))=='id') {
			// Modules from foreign table
			$foreign_key=$this->config->item('module_field');
			$foreign_field='str_'.get_suffix($this->config->item('module_field'));
			$foreign_table=foreign_table_from_key($foreign_key);
			$modules=$this->db->get_field_where($foreign_table,$foreign_field,'id',$page[$foreign_key]);
		}
		else {
			// Modules direct from field
			$modules=$page[$this->config->item('module_field')];
		}
		return $modules;
	}
	
	
  /**
   * @author Jan den Besten
   * @depricated
   * @ignore
   */
	public function getFormByModule($module) {
		$this->load->module('getform');
		return $this->getform->by_module($module);
	}
  /**
   * @author Jan den Besten
   * @depricated
   * @ignore
   */
	public function getFormByTitle($title) {
		$this->load->module('getform');
		return $this->getform->by_title($module);
	}
  /**
   * @author Jan den Besten
   * @depricated
   * @ignore
   */
	public function getFormById($id) {
		$this->load->module('getform');
		return $this->getform->by_id($module);
	}
	
}

?>