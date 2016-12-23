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

  private $view_data    = array();
  private $current_uri  = '';
  /**
   * Alle language files die nodig zijn
   */
  private $lang_files   = array( 'vue' );


	public function __construct() {
		parent::__construct(true);
    $this->load->library('flexy_auth');
    $this->load->model('version');
		$this->load->model("ui");
		$this->load->library("menu");

		if ( ! $this->flexy_auth->logged_in() ) {
			redirect($this->config->item('API_login'));
		}
		if ( ! $this->flexy_auth->allowed_to_use_cms() ) {
			$this->flexy_auth->logout();
			redirect(site_url());
		}
    
    // Uri voor current menu item
    $this->current_uri = $this->uri->segment_array();
    $this->current_uri = array_slice($this->current_uri,0,4);
    $this->current_uri = implode('/',$this->current_uri);
    $this->current_uri = str_replace('/show/form/','/show/grid/',$this->current_uri);
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

    // Menus
    $menus = $this->data->table('cfg_admin_menu')->get_menus( $this->view_data['base_url'], $this->current_uri );
    $this->view_data = array_merge($this->view_data,$menus);
    $this->view_data['uri'] = $this->current_uri;
    
    // Editor stuff
    $this->view_data['tinymceOptions'] = array(
      'language'   => $this->flexy_auth->get_user()['str_language'],

      'menubar'    => false,
      'toolbar1'   => $this->cfg->get('CFG_configurations',"str_buttons1"),
      'toolbar2'   => $this->cfg->get('CFG_configurations',"str_buttons2"),
      'toolbar3'   => $this->cfg->get('CFG_configurations',"str_buttons3"),

      // 'statusbar'  => false,
      'plugins'    => "fullscreen",
      // toolbar: "fullscreen"

      'height' => 300,
    );
    if ($this->flexy_auth->is_super_admin()) {
      if (strpos($this->view_data['tinymceOptions']['toolbar1'],'code')===FALSE) $this->view_data['tinymceOptions']['toolbar1'].=',|,code';
    }
    // $formats=$this->cfg->get('CFG_configurations',"str_formats");
    // $styles=$this->cfg->get('CFG_configurations',"str_styles");

    // $this->_show_message();
    // $this->_show_content();
    // $this->_show_trace();
    // $this->_show_footer();
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