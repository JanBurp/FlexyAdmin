<?php

class Main extends MY_Controller {
	
	public function __construct()	{
		parent::__construct();
    $this->load->library('flexy_auth');
	}

  /**
   * Main view
   *
   * @return void
   * @author Jan den Besten
   */
	public function index() {
    $site = $this->data->table('tbl_site')->select('str_title,url_url')->get_row();
    $user = $this->flexy_auth->get_user();
    $view_data = array(
      'title'     => $site['str_title'],
      'language'  => $user['str_language']
    );
    $this->load->view('admin/__test/main_app', $view_data, false);
	}
  

}




?>