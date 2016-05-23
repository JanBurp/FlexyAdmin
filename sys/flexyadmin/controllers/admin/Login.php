<?php

/** \ingroup controllers
 * This Controller logs user in or out
 *
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Login extends MY_Controller {

	var $homePage;

	function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->library('flexy_auth');
		$this->homePage=$this->config->item('API_home');
	}

	function index() {
		$site['title']=$this->_get_site_title();
		$site['message']=$this->session->userdata('message');
		$this->session->unset_userdata('message');
		$this->load->view('admin/login',$site);
	}

	private function _get_site_title() {
		$title='';
		if ($this->db->table_exists('tbl_site')) {
			if ($this->db->field_exists('str_title','tbl_site'))
				$title.='- '.$this->db->get_field('tbl_site','str_title');
		}
		return $title;
	}

	public function check() {
		$username=$this->input->post("user",TRUE);
		$password=$this->input->post("password",FALSE);
    
		if ($this->flexy_auth->login( $username, $password, FALSE )) {
			
			$message = $this->flexy_auth->messages();
			$this->session->set_flashdata('message', $message);

			// Succesfull login
			$this->session->set_flashdata('message', $this->flexy_auth->messages());
			$userData = object2array( $this->flexy_auth->user() );

			// set user preferences in session data
			if (isset($userData['str_language'])) $this->session->set_userdata("language",$userData['str_language']);	else $this->session->set_userdata("language",'nl');
			if (isset($userData['str_filemanager_view'])) $this->session->set_userdata("fileview",$userData['str_filemanager_view']);	else $this->session->set_userdata("fileview",'icons');
			
			// login log
			$this->load->helper('date');
		}
		else { 
			// Unsuccesfull
      if (empty($message)) $message = $this->flexy_auth->errors();
			$this->session->set_userdata('message', $message);
		}
		redirect($this->homePage);
	}


}

?>
