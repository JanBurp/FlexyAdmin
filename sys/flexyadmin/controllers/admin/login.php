<?php /**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @copyright Copyright (c) 2008, Jan den Besten
 * @link http://flexyadmin.com
 * @version V1 0.1
 * @filesource  */

// ------------------------------------------------------------------------

/**
 * user Controller Class
 *
 * This Controller logs user in or out
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @version V1 0.1
 *
 */

class Login extends CI_Controller {

	var $homePage;

	function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->library('user');
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
		$password=$this->input->post("password",TRUE);
		if ($this->user->login( $username, $password, FALSE )) {
			
			$message = $this->user->messages();
			$this->session->set_flashdata('message', $message);

			// Succesfull login
			$this->session->set_flashdata('message', $this->user->messages());
			$userData = object2array( $this->user->get_user() );

			// set user preferences in session data
			if (isset($userData['str_language'])) $this->session->set_userdata("language",$userData['str_language']);	else $this->session->set_userdata("language",'nl');
			if (isset($userData['str_filemanager_view'])) $this->session->set_userdata("fileview",$userData['str_filemanager_view']);	else $this->session->set_userdata("fileview",'icons');
			
			// login log
			$this->load->helper('date');
			$this->db->set('id_user',$userData['id']);
			$this->db->set('tme_login_time',standard_date('DATE_W3C',time()));
			$this->db->set('ip_login_ip',$this->session->userdata('ip_address'));
			$this->db->insert($this->config->item('LOG_table_prefix')."_".$this->config->item('LOG_login'));

		}
		else { 
			// Unsuccesfull
			$message = $this->user->messages();
			if (empty($message)) $message = $this->user->errors();
			$this->session->set_userdata('message', $message);
		}
		redirect($this->homePage);
	}


  //forgot password
	// public function forgot_password() {
	// 	$email=$this->input->post('email',TRUE);
	// 	$site['title']=$this->_get_site_title();
	// 	$site['message']=$this->session->userdata('message');
	// 	$this->session->unset_userdata('message');
	// 
	// 	$user=FALSE;
	// 	if ($email) {
	// 		$user=$this->user->get_user_by_email($email);
	// 	}
	// 	
	// 	if ( ! $user) {
	// 		$this->load->view('admin/login_forgot', $site);
	// 	}
	// 	else {
	// 		if ( $this->user->forgotten_password($email) ) {
	// 			//if there were no errors
	// 			$this->session->set_flashdata('message', $this->user->messages());
	// 			redirect("admin/login");
	// 		}
	// 		else {
	// 			// else try again
	// 			$this->session->set_flashdata('message', $this->user->errors());
	// 			redirect("admin/login/forgot_password");
	// 		}
	// 	}
	// }

	// public function reset_password($code) {
	// 	$reset = $this->ion_auth->forgotten_password_complete($code);
	// 	if ($reset) {  //if the reset worked then send them to the login page
	// 		$this->session->set_flashdata('message', $this->ion_auth->messages());
	// 		redirect("auth/login", 'refresh');
	// 	}
	// 	else { //if the reset didnt work then send them back to the forgot password page
	// 		$this->session->set_flashdata('message', $this->ion_auth->errors());
	// 		redirect("auth/forgot_password", 'refresh');
	// 	}
	// }




}

?>
