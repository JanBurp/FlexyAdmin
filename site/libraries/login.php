<?php


/**
	* Met deze module kun je bezoekers laten inloggen
	*
	* De login module kan gebruikt worden om bezoekers te laten inloggen op de site.
	* Dit kan voor de hele site, maar ook per pagina.
	*
	* Bestanden
	* ----------------
	*
	* - site/config/login.php - Hier kun je een een aantal dingen instellen
	* - site/libraries/plugin_login_activate.php - Plugin die het aactiveren van nieuwe gebruikers in het admin deel verzorgd
	* - site/views/login/* - Enkele views en per taal een aantal email templates
	* - site/language/##/login_lang.php - Taalbestanden
	* 
	* Voorwaarden
	* ----------------
	*
	* - Als inloggen voor de hele site nodig is: laadt de module dan automatisch in.
	* - Als inloggen alleen op enkele pagina's nodig is: laadt de module dan alleen in op die pagina's
	* - Je moet in het menu in ieder geval ergens een pagina hebben die de module login.logout aanroept.
	* - Als gebruikers zichzelf moeten kunnen registreren dan moet ergens in het menu ook een pagina bestaan die login.register aanroept.
	* - Als gebruikers zichzelf moeten kunnen registreren of hun paswoord moeten kunnen resetten,
  * zet dan de volgende instelling in site/config/config.php: `$config['query_urls']=TRUE;`
	*
	* @author Jan den Besten
	* @package FlexyAdmin_login
	**/

class Login extends Module {
	
	private $errors = '';
	private $form;
	
  /**
   * @ignore
   */
   public function __construct() {
		parent::__construct();
		$this->CI->load->library('user',$this->config['tables']);
    $this->CI->config->set_item('dont_cache_this_page',TRUE);
		if ($this->config('auto_uris')) $this->_find_uris();
		// redirect ion_auth to other email_templates
    if (isset($this->CI->site)) $this->CI->config->set_item('email_templates','login/'.$this->CI->site['language'].'/','ion_auth');
		// if admin activation tell user/ion_auth
		$this->CI->config->set_item('admin_activation',$this->config('admin_activation'),'ion_auth');
    // Tell ion_auth if needs to check for double email
    $this->CI->config->set_item('check_double_email',$this->config('check_double_email'),'ion_auth');
	}
	
  /**
  	* Hier wordt de module standaard aangeroepen: als nog niet is ingelogd dan wordt login.login aangeroepen
  	*
  	* @param string $page
  	* @return string 
  	* @author Jan den Besten
  	* @ignore
  	*/
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
	
  
  /**
  	* Als nog niet is ingelogd dan wordt gevraagd om dat te doen
  	*
  	* @param string $page 
  	* @param string $show_if_allready[true]
  	* @return string
  	* @author Jan den Besten
  	*/
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
		
		return $this->_output($page,$content);
	}
	
	/**
		* Uitloggen van huidige gebruiker
		*
		* @param string $page 
		* @return string
		* @author Jan den Besten
		*/
	public function logout($page) {
		$this->CI->user->logout();
		return $this->_output($page,lang('logout_done'));
	}
	
	
  /**
  	* Reset wachtwoord (gebruiker krijgt een mail)
  	*
  	* @param string $page 
  	* @return string
  	* @author Jan den Besten
  	*
  	* LET OP: deze method werkt alleen met de volgende instelling in site/config/config.php:
  	* <code> $config['query_urls']&nbsp;=&nbsp;TRUE;</code>
  	*/
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
		
		return $this->_output($page,$content);
	}

  /**
  	* Registreer nieuwe gebruiker (gebruiker krijgt een mail)
  	*
  	* @param string $page 
  	* @return string
  	* @author Jan den Besten
  	*
  	* LET OP: deze method werkt alleen met de volgende instelling in site/config/config.php:
  	* <code> $config['query_urls']&nbsp;=&nbsp;TRUE;</code>
  	*/
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
							if ($this->config('admin_activation'))
								$content=lang('register_wait');
							else
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

		return $this->_output($page,$content);
	}


	/**
		* Zet een formulier klaar
		*
		* @param string $type 
		* @return void
		* @author Jan den Besten
  	* @ignore
		*/
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
	
  /**
  	* @param string $page 
  	* @param string $content
  	* @return void
  	* @author Jan den Besten
  	* @ignore
  	*/
	private function _output($page,$content) {
		$content='<div id="login">'.$content.'</div>';
		return $this->CI->view('login/main', array('content'=>$content),true);
	}
	
  /**
  	* @return void
  	* @author Jan den Besten
  	* @ignore
  	*/
   private function _find_uris() {
		$this->config['login_uri']=$this->CI->find_module_uri('login.login');
		$this->config['register_uri']=$this->CI->find_module_uri('login.register');
		$this->config['forgotten_password_uri']=$this->CI->find_module_uri('login.forgot_password');
	}
	

	

}

?>