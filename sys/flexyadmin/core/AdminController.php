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

  private $view_data            = array();
  private $current_uri          = '';
  private $keep_uris            = array(
    'admin/show/form/tbl_site',
    'admin/show/form/cfg_users'
  );
  private $replace_uris         = array(
    '#admin/plugin/stats/.*#'   => 'admin/plugin/stats/',
    '#admin/show/form/(.*)/.*#' => 'admin/show/grid/$1',
  );
  
  /**
   * Alle language files die nodig zijn
   */
  private $lang_files   = array( 'vue' );


	public function __construct() {
		parent::__construct(true);
    $this->load->library('flexy_auth');
    $this->load->model('version');
    $this->load->model("admin_menu");
    // $this->load->library("menu");

		if ( ! $this->flexy_auth->logged_in() ) {
			redirect($this->config->item('API_login'));
		}
		if ( ! $this->flexy_auth->allowed_to_use_cms() ) {
			$this->flexy_auth->logout();
			redirect(site_url());
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
  
  // public function _show_message() {
  //     if (!IS_AJAX) {
  //       $this->message->show();
  //       $this->message->reset();
  //       $this->message->reset_errors();
  //     }
  // }

  // public function _show_trace() {
  //   $trace=$this->session->userdata('trace');
  //   if (!empty($trace)) {
  //     $this->load->view('admin/trace',array('trace'=>$trace));
  //   }
  //   $this->session->unset_userdata('trace');
  // }

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

    // Basic content
    $this->view_data['content'] = '';

    // API urls
    $this->view_data['base_url'] = 'admin/';

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
    if ( !empty($view)) $this->view_data['content'] = $this->load->view('admin/'.$view,$data,true);
    $this->load->view('admin/admin',$this->view_data);
    return $this;
	}
  
  public function view_404() {
    $this->load->view('admin/admin_404');
    return $this;
  }

}

?>