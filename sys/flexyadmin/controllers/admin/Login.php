<?php

/** \ingroup controllers
 * This Controller logs user in or out
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Login extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->library('flexy_auth');
	}

	public function index() {
		$site['title'] = $this->_get_site_title();
		$site['message'] = $this->session->userdata('message');
		$this->session->unset_userdata('message');
		$this->load->view('admin/login',$site);
	}

	private function _get_site_title() {
    return '- '.$this->data->table('tbl_site')->get_field('str_title');
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
		redirect( $this->config->item('API_home') );
	}


}

?>
