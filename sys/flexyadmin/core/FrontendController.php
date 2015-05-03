<?php 

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
   *      ['use_minimized']        => [true|false]                      // geeft aan of er de geminificeerde js,css bestanden moeten worden gebruikt
   *      ['framework']            => ['default'|'bootstrap']           // welk frontend framework moet worden geladen
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
  var $ajax_module = false;

  /**
    * @ignore
    */
	public function __construct() {
		// Init controller, and load all libraries
		parent::__construct();
    
    // In testmode a temp message will be shown (if not logged in as administrator) and sitemap.xml is deleted
    if ($this->config->item('testmode')) {
      $this->load->library('user');
      if (!$this->user->is_super_admin()) {
        $temp=file_get_contents('index_temp.html');
        echo $temp;
        unlink('sitemap.xml');
        die();
      }
    }
        
    // Set $_GET if asked for
    // See http://www.askaboutphp.com/tutorials/58/codeigniter-mixing-segment-based-url-with-querystrings.html
    // For this to work, config.php: $config['uri_protocol']  = "PATH_INFO";
		if ($this->config->item('query_urls'))	parse_str($_SERVER['QUERY_STRING'],$_GET);

    $this->ajax_module = $this->config->item('AJAX_MODULE');
    
    if ($this->ajax_module) {
      // Load standard Ajax Module Class
      $this->load->library('ajax_module');
  		$this->load->helper("language"); // nodig voor lang() in config files etc.
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
      if ($this->config->item('use_old_menu')) {
        $this->load->library("old_menu",'menu');
        $this->menu = $this->old_menu;
        unset($this->old_menu);
      }
      else
        $this->load->library("menu");
      
      $framework=$this->config->item('framework');
      $this->menu->set('framework',$framework);
      
  		$this->load->library("content");
      $this->content->initialize($this->config->item('parse_content'));
  		$this->load->library('form_validation');
    }
		// Init global site data
		$this->_init_globals();
    
    // Simulate cronjobs?
    if ($this->config->item('simulate_cronjobs') and $this->uri->segment(1)!='_cronjob') {
      $this->load->model('cronjob');
      $this->cronjob->go();
    }
    
    // Version timestamp
    $files=array('site/assets/css/styles.min.css','site/assets/js/scripts.min.js');
    $version=0;
    foreach ($files as $file) {
      $time=0;
      if (file_exists($file)) $time=filemtime($file);
      if ($time>$version) $version=$time;
    }
    $this->site['int_version']=$version;
    
    // Is there a library that needs to be run first??
    if (file_exists(SITEPATH.'libraries/before_controller.php')) {
      $this->load->library('module');
      $this->load->library('before_controller');
    }
    
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
    $this->site['framework']=$this->config->item('framework');
		$this->site['languages']=$this->config->item('languages');
    $this->site['use_minimized']=$this->config->item('use_minimized');

    if (!$this->ajax_module) {
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
          if (!is_array($value)) $value=array($value);
					foreach ($value as $val) {
            if (isset($page[$field])) $load_if = $load_if || $page[$field]==$val;
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
      $library=get_suffix(str_replace(' ','_',$library),'/');
      // Process the return value according to module settings
			if ($return) {
        $to='';
        if ($method=='index') $to=$this->$library->config('__return','');
        if (empty($to)) $to=$this->$library->config('__return'.'.'.$method, '');
        if (empty($to)) $to='page';
        $to=explode('|',$to);
        // put result in site
        if (in_array('site',$to)) {
          $this->site['modules'][$module]=$return;
        }
        // put result in page (default)
        if (in_array('page',$to)) {
  				if (is_array($return))
  					$page=array_merge($page,$return);
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
			$library_file=str_replace(' ','_',$library);
      $library_name=get_suffix($library_file,'/');
			if (file_exists(SITEPATH.'libraries/'.$library_file.'.php') or file_exists(APPPATH.'libraries/'.$library_file.'.php')) {
				$this->load->library($library_file,array('name'=>$library_name,'file'=>$library_file));
        // $this->$library_name->set_name($library);
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
   * Voeg keywords toe
   *
   * @param string $words 
   * @return void
   * @author Jan den Besten
   */
	public function add_keywords($words) {
		$this->site["keywords"]=add_string(el('keywords',$this->site,''),$words,",");
	}
	
  /**
   * Voeg achter de titel nog een extra tekst toe
   *
   * @param string $page_title
   * @param string $format["%s - %p"] Hiermee bepaal je hoe de titel wordt toegevoegd: s=site titel, p=page titel
   * @return void
   * @author Jan den Besten
   */
	public function add_title($page_title,$format="%s - %p") {
		$this->site["title"]=str_replace(array('%s','%p'),array($this->site["title"],$page_title),$format);
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
    $page['str_title']=' ';
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
		$this->site['class']=add_string(el('class',$this->site,''),$class,' ');
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
   * Stelt de standaard view in van een pagina
   * Staat standaard ingesteld in 'site/config/config.php' bij `$config['page_view']='page';`, maar hiermee kun je dat in een module aanpassen
   *
   * @param string $page_view['page]
   * @return object $this
   * @author Jan den Besten
   */
  public function set_page_view($page_view='page') {
    $this->config->set_item('page_view',$page_view);
    return $this;
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
	public function view($view='',$data=array(),$return=FALSE) {
    $default=array('assets'=>assets(),'language'=>el('language',$this->site,$this->config->item('language')));
    $main_view=(empty($view));
		if (empty($data)) {
      $data=$this->site;
      if ($main_view and isset($data['content'])) {
        $data['content']=$this->content->render($data['content']);
      }
    }
    $data=array_merge($default,$data);

		if ($main_view) {
			$view=$this->config->item('main_view');
		}
    $html=$this->load->view($view,$data,TRUE);
    if ($main_view) $html=$this->content->render($html,true);
    if (!$return) echo $html;
		return $html;
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