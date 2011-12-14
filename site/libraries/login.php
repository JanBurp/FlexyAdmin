<?php


/**
 * Login
 *
 * !! When registration and password resetting needs to be active: make sure that $config['query_urls']=TRUE; in site/config/config.php
 *
 * @package default
 * @author Jan den Besten
 */




class Login extends Module {
	
	var $errors = '';
	var $form;
	
	public function __construct() {
		parent::__construct();
		$this->CI->load->library('user');
		$this->CI->load->language('login');
		if ($this->config('auto_uris')) $this->_find_uris();
		
		// redirect ion_auth to other email_templates
		$this->CI->config->set_item('email_templates','login/'.$this->CI->site['language'].'/','ion_auth');
	}
	
	
	public function index($page) {
		// If logged in, set class
		if ($this->CI->user->logged_in()) $this->CI->add_class($this->config('class'));
		// If Login is called also with other methods, don't go on with this call
		$modules=$this->CI->site['modules'];
		if (in_array('login.login',$modules) or in_array('login.logout',$modules) or in_array('login.register',$modules) or in_array('login.forgot_password',$modules) or in_array('login.reset_password',$modules)) {
			return $page;
		}
		return $this->login($page,false);
	}
	
	public function login($page, $show_if_allready=true) {
		$title='';
		$content='';
		
		if (!$this->CI->user->logged_in()) {
			$this->_loadform('login');
			$title=lang('login_submit');

			if ($this->form->validation()) {
				$data = $this->form->get_data();
				$u = $data['str_login_username'];
				$p = $data['gpw_login_password'];
				if (!empty($u) && !empty($p)) {
					$result	= $this->CI->user->login($u, $p, $data['b_login_remember']);
					if (!$result) {
						$this->form->reset();
						$this->errors='<p class="error">'.lang('login_error').'</p>';
					}
				}
			}
			else {
				// Show validation errors if any
				$validationErrors=validation_errors('<p class="error">', '</p>');
				if (!empty($validationErrors)) $this->errors=$validationErrors;
			}
		}
		
		if (!$this->CI->user->logged_in()) {
			// Show login form and nothing else
			$this->break_content();
			$view=array('title'=>$title,'errors'=>$this->errors,'form'=> $this->form->render());
			if ($this->config('forgotten_password_uri')) {
				$view['forgotten_password']=lang('forgot_password');
				$view['forgotten_password_uri']=$this->config('forgotten_password_uri');
			}
			if ($this->config('register_uri')) {
				$view['register']=lang('register');
				$view['register_uri']=$this->config('register_uri');
			}
			$content=$this->CI->show('login/login',$view,true);
			
		}
		else {
			// make sure that POSTdata is empty, so other modules with a form start fresh
			if (!empty($_POST)) {
				unset($_POST['str_login_username']);
				unset($_POST['gpw_login_password']);
				if (isset($_POST['submit']) and $_POST['submit']==lang('login_submit')) unset($_POST['submit']);
			}
			// Show message if a dedicated login page
			if ($show_if_allready) {
				$content=langp('login_already',$this->CI->user->user_name);
			}
		}
		
		return $content;
	}
	
	
	public function logout($page) {
		$this->CI->user->logout();
		return lang('logout_done');
	}
	
	
	
	public function forgot_password($page) {
		$content='';
		$code=$this->CI->input->get('code');
		
		if ($code) {
			// reset password
			$reset = $this->CI->user->forgotten_password_complete($code,lang('reset_password_mail_subject'));
			if ($reset) {
				$content=langp('reset_password_succes',$this->config('login_uri'));
			}
			else {
				$content=lang('reset_password_error');
			}
		}
		else {
			// show form to reset password
			$this->_loadform('forgot_password');
			if ($this->form->validation()) {
				$data	= $this->form->get_data();
				// Complete
				if ($this->CI->user->forgotten_password($data['email'],$this->config('forgotten_password_uri'),lang('forgot_password_mail_subject')) ) {
					$content = langp('forgot_password_completed',$this->config('login_uri'));
				}
				// Error finding/sending mail
				else {
					$content = '<span class="error">'.$this->CI->user->errors()."</span>";
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
				$content .= lang('forgot_password_intro');
				$content .= $this->form->render();
			}
		}
		return $content;
	}


	public function register($page) {
		$errors		= '';
		$content	= '';
		
		$id=$this->CI->input->get('id');
		$activation=$this->CI->input->get('activation');
		if ($id and $activation) {
			if ($this->CI->user->activate($id,$activation)) {
				$content=langp('register_succes',$this->config('login_uri'));
			}
			else {
				$content=langp('register_fail');
			}
		}
		else {
			// Allready logged in
			if ($this->CI->user->logged_in()) {
				$content=langp('login_already',$this->CI->user->user_name);
			}
			else {
				// Register form
				$this->_loadform('register');
				if ($this->form->validation()) {
					$data=$this->form->get_data();

					// a. password mismatch
					if ($data['gpw_login_password']!=$data['gpw_login_password2']) {
						$errors="<p class='error'>".lang('password_mismatch')."</p>";
					}
					// b. email exists
					elseif ($this->CI->user->email_check($data['email_login_email'])) {
						$errors="<p class='error'>".lang('email_used')."</p>";
					}
					// c. username exists
					elseif ($this->CI->user->username_check($data['str_login_username'])) {
						$errors="<p class='error'>".lang('username_used')."</p>";
					}
					else {
						// d. Register
						$this->CI->user->register($data['str_login_username'], $data['gpw_login_password'],	$data['email_login_email'],array('id_user_group'=>$this->config('group_id')),false, lang('register_mail_subject'));
						$errors = $this->CI->user->errors();
						if ($errors!='') {
							$content="<div class='error'>".$errors."</div>";
						} else {
							$content=lang('register_completed');
						}
					}
				}
				if (empty($content)) {
					$validationErrors=validation_errors('<p class="error">', '</p>');
					if (!empty($validationErrors)) $errors.=$validationErrors;
					$content=$this->CI->view('login/register',array('errors'=>$errors,'form'=>$this->form->render()),true);
				}
			}
		}

		return $content;
	}


	
	private function _loadform($type) {
		if (!$this->form) {
			$this->CI->load->library('form');
			switch($type) {
				case 'login': $data			= array("str_login_username"			=> array("label"=>lang('username'),"validation"=>"required"),
																				"gpw_login_password"			=> array('type'=>'password', "label"=>lang('password'),"validation"	=>  "required"),
																				"b_login_remember"				=> array('type'=>'checkbox', "label"=>lang('remember')));
											// $caption	= lang('login_caption');
											$buttons	= array('submit'=>array("submit"=>"login_submit","value"=>lang('login_submit')));
											break;
											
				case 'forgot_password':
											$data			= array("email"	=>array("label"=>"Email","validation"	=>  "required|valid_email"));
											// $caption	= lang('forgot_password_caption');
											$buttons	= array('submit'=>array("submit"=>"submit","value"=>lang('forgot_password_submit')));
											break;
											
				case 'register':
											$data			=array(	"str_login_username"=>array("label"=>lang('username'), "validation"	=>  "required"),
																				"gpw_login_password"=>array("type"=>'password', "label"=>lang('password'), "validation"	=>  "required"),
																				"gpw_login_password2"=>array("type"=>'password', "label"=>lang('password2'), "validation"	=>  "required"),
																				"email_login_email"	=>array("label"=>lang('email'),"validation"	=>  "required|valid_email"));
											// $caption	= lang('register_caption');
											$buttons	= array('submit'=>array("submit"=>"submit","value"=>lang('register_submit')));
											break;

				default:			die('form type not found: '.$type);
											break;
			}
			$this->form=new form();
			$this->form->set_data($data,'login');
			$this->form->set_buttons($buttons);
		}
	}
	
	private function _find_uris() {
		$this->config('login_uri')=$this->CI->find_module_uri('login.login');
		$this->config('register_uri')=$this->CI->find_module_uri('login.register');
		$this->config('forgotten_password_uri')=$this->CI->find_module_uri('login.forgot_password');
	}
	

	

}

?>