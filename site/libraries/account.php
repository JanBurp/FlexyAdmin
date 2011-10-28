<?php
class Account extends Module {
	var $errors = '';
	var $submitted;
	var $form;
	
	public function __construct() {
		parent::__construct();
		$this->CI->load->model('form');
		$this->CI->load->language('account');
		
		$this->submitted = $this->CI->input->post('submit');
		
		// Login submitted.        We must check this here, before page gets rendered, so we can output headers (cookie) first.
		if ('login' == $this->CI->uri->get_last() && $this->submitted) {
			$this->_login_submit();
		}
		// Logout submitted.
		else if('logout' == $this->CI->uri->get_last()) {
			$this->_logout_submit();
		}
	}
	/**
	 * NOTE: if type is different than before, this DOESN'T override the form.
	 **/
	public function _loadform($type) {
		if (!$this->form) {
			switch($type) {
				case 'login': $data			= array("str_username"			=> array("label"=>lang('username'),"validation"=>"required"),
																				"gpw_password"			=> array('type'=>'password', "label"=>lang('password'),"validation"	=>  "required"),
																				"remember"					=> array('type'=>'checkbox', "label"=>lang('remember')));
											$caption	= lang('login_caption');
											$buttons	= array('submit'=>array("submit"=>"submit","value"=>lang('login_submit')));
											break;
											
				case 'forgot_password':
											$data			= array("email"	=>array("label"=>"Email","validation"	=>  "required|valid_email"));
											$caption	= lang('forgot_password_caption');
											$buttons	= array('submit'=>array("submit"=>"submit","value"=>lang('forgot_password_submit')));
											break;
				case 'register':
											$data			=array(	"str_username"=>array("label"=>lang('username'), "validation"	=>  "required"),
																				"gpw_password"=>array("type"=>'password', "label"=>lang('password'), "validation"	=>  "required"),
																				"gpw_password2"=>array("type"=>'password', "label"=>lang('password2'), "validation"	=>  "required"),
																				"email_email"	=>array("label"=>lang('email'),"validation"	=>  "required|valid_email"),
																				'str_picture'	=>array("type"=>"file","label"=>"Picture"));
											$caption	= lang('register_caption');
											$buttons	= array('submit'=>array("submit"=>"submit","value"=>lang('register_submit')));
											break;

				default:			die('form type not found: '.$type);
											break;
			}
			$this->form=new form();
			$this->form->set_data($data,$caption);
			$this->form->set_buttons($buttons);
		}
	}
	
	public function register($item) {
		$this->_loadform('register');
		$content	= '';
		$title		= $item['str_title'];
		
		// Start
		if (!$this->submitted) {
			// a. Show form
			if (!$this->CI->user->logged_in()) {
				$content	= $this->form->render();
			}
			// b. Already logged in
			else {
				$title		= lang('register_already_title');
				$content	= sprintf(lang('register_already'), $this->CI->session->userdata['str_username']);
			}
		}
		// Submitted
		else if ($this->form->validation()) {
			$data=$this->form->get_data();
			
			// a. password mismatch
			if ($data['gpw_password']!=$data['gpw_password2']) {
				$content="<p class='error'>".lang('password_mismatch')."</p>";
				$content.=$this->form->render();
			}
			// b. email exists
			else if ($this->CI->user->email_check($data['email_email'])) {
				$content="<p class='error'>".lang('email_used')."</p>";
				$content.=$this->form->render();
			}
			// c. username exists
			else if ($this->CI->user->username_check($data['str_username'])) {
				$content="<p class='error'>".lang('username_used')."</p>";
				$content.=$this->form->render();
			}
			else {
				// d. Register
				$additional_data = array('str_picture'=>$data['str_picture']);
				$this->CI->user->register($data['str_username'],
																	$data['gpw_password'],
																	$data['email_email'],
																	$additional_data);
				$errors = $this->CI->user->errors();
				if ($errors!='') {
					$content="<div class='error'>".$errors."</div>";
				} else {
					$content=lang('register_completed');
				}
			}
		}
		// error with form
		else {
			$validationErrors=validation_errors('<p class="error">', '</p>');
			if (!empty($validationErrors)) {
				$content.=($validationErrors);
				$content.=$this->form->render();
			}
		
		}		
		$item['str_title']			= $title;
		$item['module_content']	= $content;
		return $item;
	}

	public function login($item) {
		$this->_loadform('login');
		$content	= '';
		$title		= $item['str_title'];
		
		// Login start
		if (!$this->submitted) {
		
			// a. Show form
			if (!$this->CI->user->logged_in()) {
				$content	= $this->form->render();
			}
			// b. Already logged in
			else {
				$title		= lang('login_already_title');
				$content	= sprintf(lang('login_already'), $this->CI->session->userdata['str_username']);
			}
		}
		// Login done	
		else if ($this->CI->user->logged_in()) {
			$title		= lang('login_completed_title');
			$content	= sprintf(lang('login_completed'), $this->CI->session->userdata['str_username']);
		}
		// Login error
		else if ($this->errors) {
			$title		= lang('login_error_title');
			$content	= $this->errors . $this->form->render();
		}
		// wtf?
		else {
			$content = 'unknown login error';
		}
				
		$item['str_title']			= $title;
		$item['module_content']	= $content;
		return $item;
	}

	/**
	 * from __construct()
	 **/
	private function _login_submit() {
		$this->_loadform('login');
		if ($this->form->validation()) {
			
			$data		= $this->form->get_data();
			$u			= $data['str_username'];
			$p			= $data['gpw_password'];
			if (!empty($u) && !empty($p)) {
				$result	= $this->CI->user->login($u, $p, $data['remember']);
				
				// Login ok.
				if ($result) {
					// ... See login()
				}
				// Login error
				else {
					unset($this->form->data['gpw_password']['repopulate']); // clear password field
					$this->errors='<p class="error">'.lang('login_error').'</p>';
				}
			}
			
		} else {
			// Show validation errors if any
			$validationErrors=validation_errors('<p class="error">', '</p>');
			if (!empty($validationErrors)) $this->errors=$validationErrors;
		}
	}
	/**
	 * from __construct()
	 **/
	private function _logout_submit() {
		$this->CI->user->logout();
	}
	public function logout($item) {
		return lang('logout_done');
	}
	
	public function forgot_password($item) {
		$this->_loadform('forgot_password');
		$content			= '';
		
		// Start
		if (!$this->submitted) {
			$content .= lang('forgot_password_intro');
			$content .= $this->form->render();
		}
		// Submitted
		if ($this->form->validation()) {
			$data		= $this->form->get_data();
			
			// Complete
			if ($this->CI->user->forgotten_password($data['email'])) {
				$content	= lang('forgot_password_completed');
			}
			// Error finding/sending mail
			else {
				$content	= '<span class="error">'.$this->CI->user->errors()."</span>";
				$content.= $this->form->render();
			}
		}
		// Error with form
		else {
			$validationErrors=validation_errors('<p class="error">', '</p>');
			if (!empty($validationErrors)) {
				$content.=($validationErrors);
				$content.= $this->form->render();
			}
		}
	
		return $content;
	}

	public function reset_password($item) {
		$content	= '';
		$code			= $this->CI->input->get('code');
		$reset		= $this->CI->user->forgotten_password_complete($code);
		if ($reset) {  //if the reset worked then send them to the login page
			$this->CI->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("nl/login", 'refresh');
		}
		else { //if the reset didnt work then send them back to the forgot password page
			$this->CI->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("nl/forgot_password", 'refresh');
		}
	}
}

?>