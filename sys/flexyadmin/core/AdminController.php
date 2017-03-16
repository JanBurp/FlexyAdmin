<?php 
require_once(APPPATH."core/BasicController.php");


/**
 * AdminController Class extends BasicController
 *
 * Adds view methods and loads/views automatic header, menu and message.
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 **/
 
class AdminController extends BasicController {

  protected $view_data            = array();
  private   $current_uri          = '';
  private   $keep_uris            = array(
    '_admin/show/form/tbl_site',
    '_admin/show/form/cfg_users'
  );
  private   $replace_uris         = array(
    '#_admin/plugin/stats/.*#'   => '_admin/plugin/stats/',
    '#_admin/show/form/(.*)/.*#' => '_admin/show/grid/$1',
  );
  
  /**
   * Alle language files die nodig zijn
   */
  private $lang_files   = array( 'vue' );


	public function __construct() {
		parent::__construct(true);
    $this->load->helper('markdown');
    $this->load->library('flexy_auth');
    $this->load->model('version');
    $this->load->model("admin_menu");

		if ( ! $this->flexy_auth->logged_in() ) {
      redirect($this->config->item('API_login'),'refresh');
		}
		if ( ! $this->flexy_auth->allowed_to_use_cms() ) {
			$this->flexy_auth->logout();
			redirect(site_url(),'refresh');
		}
    
    // Uri voor current menu item
    $this->current_uri = uri_string();
    $parts = $this->uri->segment_array();
    $parts = array_slice($parts,0,4);
    $parts = implode('/',$parts);
    if ( !in_array($parts,$this->keep_uris) ) {
      foreach ($this->replace_uris as $search => $replace) {
        $this->current_uri = preg_replace($search,$replace,$this->current_uri);
      }
    }

	}
  
  
  /**
   * Verzamel alle languge keys en geef deze terug
   *
   * @return array
   * @author Jan den Besten
   */
  private function _collect_lang() {
    foreach ($this->lang_files as $file) {
      $this->lang->load($file);
    }
    $lang_keys = $this->lang->language;
    $lang_keys = filter_by_key($lang_keys,'vue_','');
    return $lang_keys;
  }
  
  private function _collect_help() {
    // Lees alle help bestanden in en zet ze om naar HTML
		$lang = $this->flexy_auth->get_user(null,'str_language');
    $map=APPPATH.'views/help';
    $helpFiles=read_map($map);
    ksort($helpFiles);
    
    $help_items = array();
    foreach ($helpFiles as $file => $item) {
      $title = str_replace('_',' ',get_suffix(str_replace('.html','',$item['name']),'__'));
      if (!empty($title)) {
        $html = file_get_contents($item['path']);
        $matches=array();
        if (preg_match_all("/\[(.*)\]/uiUsm", $html,$matches)) {
          foreach ($matches[1] as $match) {
            $help=$this->lang->ui_help($match);
            if ($help) {
              $help=h( $this->lang->ui(remove_prefix($match,'.')),2 ).$help;
              $html=str_replace('['.$match.']',$help,$html);
            }
          }
        }
        $html = Markdown($html);
        $html = str_replace('sys/flexyadmin/assets/',$this->config->item('ADMINASSETS'),$html);
        
        $uri = remove_prefix(remove_suffix($file,'.'),'__');
        $help_items[$uri] = array(
          'uri'     => $uri,
          'title'   => $title,
          'content' => $html,
        );
      }
    }
		$help = $this->load->view( 'admin/vue/help', array('items'=>$help_items), true );
    return $help;
  }

  /**
   * Verzamel alle data die meegegeven moet worden aan de admin view
   *
   * @return void
   * @author Jan den Besten
   */
  private function _prepare_view_data() {
    // tbl_site
    $this->view_data = $this->data->table('tbl_site')->select('str_title,url_url')->cache()->get_row();
    $this->view_data['url_url'] = str_replace('http://','',$this->view_data['url_url']);
    
    // User data
    $this->view_data['user'] = array_keep_keys($this->flexy_auth->get_user(),array('username','email','str_filemanager_view','auth_token'));
    
    // Help
    $this->view_data['help'] = $this->_collect_help();
    
    // Basic content
    $this->view_data['content'] = '';

    // API urls
    $this->view_data['base_url'] = $this->config->item('API_home');
    
    // Body class
    $this->view_data['class'] = 'uri-'.trim(str_replace('/','__',str_replace($this->config->item('API_home'),'',$this->current_uri)),'_');

    // Language
    $this->view_data['language'] = $this->language;
    $this->view_data['lang_keys'] = $this->_collect_lang();

    // Version
    $this->view_data['version'] = $this->version->get_version();
    $this->view_data['build'] = $this->version->get_build();

    // Menus
    $menus = $this->admin_menu->get_menus( $this->view_data['base_url'], $this->current_uri );
    $this->view_data = array_merge($this->view_data,$menus);
    $this->view_data['uri'] = $this->current_uri;
    
    // Editor stuff
    $this->config->load('admin_ui',true);
    $this->view_data['tinymceOptions'] = $this->config->get_item(array('admin_ui','wysiwyg'));
    $this->view_data['tinymceOptions']['language'] = $this->flexy_auth->get_user()['str_language'];
  }
  
  
  /**
   * Laad de admin pagina zien
   *
   * @param string [$view] view for content 
   * @param array [$data] data for content 
   * @return this
   * @author Jan den Besten
   */
	public function view_admin( $view='', $data=array() ) {
    $this->_prepare_view_data();
    $this->view_data = array_merge($this->view_data,$data);
    if ( !empty($view)) $this->view_data['content'] = $this->load->view('admin/'.$view,$this->view_data,true);
    $this->load->view('admin/admin',$this->view_data);
    return $this;
	}
  
  public function view_404() {
    $this->load->view('admin/admin_404');
    return $this;
  }

}

?>