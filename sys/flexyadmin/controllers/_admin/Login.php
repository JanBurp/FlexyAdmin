<?php

/** \ingroup controllers
 * This Controller logs user in or out
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Login extends MY_Controller {

  var $language = '';

	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->library('flexy_auth');
		// Get prefered language from users browser settings or settings
		if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
      $lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }
    if (empty($lang) or !in_array($lang,$this->config->item('ADMIN_LANGUAGES'))) {
		  $lang = array_shift($this->config->item('ADMIN_LANGUAGES'));
    }
    $this->language = $lang;
    $this->lang->load('login',$lang);
	}

	public function index() {
    $site = array(
      'lang'    => $this->language,
  		'title'   => '- '.$this->data->table('tbl_site')->get_field('str_title'),
  		'message' => $this->session->userdata('message'),
    );
		$this->session->unset_userdata('message');
		$this->load->view('admin/login',$site);
	}

	public function check() {
		$username=$this->input->post("user",TRUE);
		$password=$this->input->post("password",FALSE);
    
		if ($this->flexy_auth->login( $username, $password, FALSE )) {
      $message = $this->flexy_auth->messages();
			$this->session->set_flashdata('message', $message );
			$user = $this->flexy_auth->get_user();
			// set user preferences in session
      $this->session->set_userdata("language",el('str_language', $user, 'nl') );
      $this->session->set_userdata("fileview",el('str_filemanager_view', $user, 'icons') );
			// login log
			$this->load->helper('date');
		}
		else { 
			// Unsuccesfull
      $message = $this->flexy_auth->errors();
			$this->session->set_userdata('message', $message);
		}
		redirect( $this->config->item('API_home'),'refresh' );
	}
  
  
	public function forgot() {
		$email = $this->input->post("email",FALSE);
    $message = lang('login_error');
		if ($email) {
      $this->flexy_auth->set_forgotten_password_uri( $this->config->item('API_login').'forgot_complete');
      if ($this->flexy_auth->forgotten_password( $email )) {
        $message = lang('login_forgot_mail_send');
      }
		}
		$this->session->set_userdata('message', $message);
		redirect( $this->config->item('API_home'),'refresh' );
	}
  
  
	public function forgot_complete() {
		$code = $this->input->get("code",FALSE);
    $message = lang('login_error');
		if ($code) {
      if ($this->flexy_auth->forgotten_password_complete($code)) {
        $message = lang('login_forgot_password_send');
      }
		}
		$this->session->set_userdata('message', $message);
		redirect( $this->config->item('API_home'),'refresh' );
	}
  
  


}

?>
