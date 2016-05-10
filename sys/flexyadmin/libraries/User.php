<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."libraries/Ion_auth.php");


/** \ingroup libraries
 * Class voor het inloggen, aanmaken etc van gebruikers
 * 
 * Is een uitbreiding op Ion Auth dus kijk ook zeker daar voor te gebruiken methods!
 *
 * Bepaalde methods geven rechten terug, deze zijn samengesteld uit de volgende constanten:
 *  
 * - RIGHTS_ALL    = 15 (RIGHTS_DELETE + RIGHTS_ADD + RIGHTS_EDIT + RIGHTS_SHOW)
 * - RIGHTS_DELETE = 8
 * - RIGHTS_ADD    = 4
 * - RIGHTS_EDIT   = 2
 * - RIGHTS_SHOW   = 1
 * - RIGHTS_NO     = 0
 * - Allerlei combinaties van bovenstaande zijn mogelijk (net zoals RIGHTS_ALL een combinatie is)
 *
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */
 
class User extends Ion_auth {
	

  /**
   * Rechten van huidige gebruiker
   */
	public $rights;

  /**
   * id van huidige gebruiker
   */
	public $user_id;
  public $group_id;
  public $user_name;
  public $language;
  
  
  /**
   * Array van tbl_site
   */
	private $siteInfo;
  
  /**
   * Is er een tabel met emails?
   */
  private $mail_table=false;
  
	
  /**
     */
  public function __construct($tables='') {
		parent::__construct($tables);
		// set standard configurations
		$this->CI->db->select('url_url,str_title,email_email');
		$this->siteInfo = $this->CI->db->get_row('tbl_site');
		$this->CI->config->set_item('site_title', $this->siteInfo['str_title'],'ion_auth');
		$this->CI->config->set_item('admin_email', $this->siteInfo['email_email'],'ion_auth');
    if ($this->CI->db->table_exists('cfg_email')) $this->mail_table='cfg_email';
	}
  
  
  /**
   * Login
   *
   * @param string $identity
   * @param string $password 
   * @param string $remember 
   * @return bool TRUE als login is gelukt
   * @author Jan den Besten
   */
	public function login($identity, $password, $remember=false) {
    if ( ! $this->_check_if_userdate_ok()) {
      $this->set_message('update_needed');
    }
    else {
			if ($this->CI->ion_auth_model->login($identity, $password, $remember)) {
        $this->load_user_cfg();
        $this->CI->load->model('log_activity');
        $this->CI->log_activity->auth();
				return TRUE;
			}
    }
		$this->set_error('login_unsuccessful');
		return FALSE;
	}
  
  /**
   * Logout
   *
   * @return void
   * @author Jan den Besten
   */
  public function logout() {
    $logout = parent::logout();
    if ($logout) {
      $this->CI->load->model('log_activity');
      $this->CI->log_activity->auth('logout',$this->user_id);
    }
    return $logout;
  }
	
  /**
   * _check_if_userdate_ok()
   *
   * @return bool
   * @author Jan den Besten
   * @internal
   */
	private function _check_if_userdate_ok() {
		return ($this->CI->db->field_exists('str_username',$this->tables['users']) and $this->CI->db->field_exists('gpw_password',$this->tables['users']) );
	}
	
	
	/**
	 * Check of er al ingelogd is
	 *
	 * @return bool TRUE als er al is ingelogd
	 * @author Jan den Besten
	 */
	public function logged_in() {
		$logged_in = parent::logged_in();
		if ($logged_in) {
			$this->user_id = $this->CI->session->userdata("user_id");
			$this->user_name = $this->CI->session->userdata("str_username");
			$this->language= $this->CI->session->userdata("language");
			$this->rights = $this->create_rights( $this->user_id );
      $this->group_id = $this->CI->session->userdata("id_user_group");
      $this->load_user_cfg();
		}
		return (bool) $logged_in;
	}
	
  
  /**
   * Add user cfg from user groups to config
   *
   * @return void
   * @author Jan den Besten
   */
  private function load_user_cfg() {
    $rights=$this->get_rights();
    if (isset($rights['stx_extra_config']) and !empty($rights['stx_extra_config'])) {
      $extra_cfg=$rights['stx_extra_config'];
      $extra_cfg=str_replace(array(' ','"',"'"),'',$extra_cfg);
      $extra_cfg=explode(';',$extra_cfg);
      foreach ($extra_cfg as $cfg) {
        $cfg=trim($cfg);
        if (!empty($cfg)) {
          $key=get_prefix($cfg,'=');
          $key=trim(trim($key,'['),']');
          $keys=explode('][',$key);
          $value=trim(get_suffix($cfg,'='));
          array_set_multi_key($this->CI->config->config,$keys,$value);
        }
      }
    }
  }
  
  
	/**
	 * Verstuurt een mail naar gebruiker met een nieuw wachtwoord
	 *
	 * @param string $email Emailadres van gebruiker
	 * @param string $uri 
	 * @param string $subject default='Forgotten Password Verification' Onderwerp van de te sturen email 
	 * @return bool TRUE als proces is gelukt, FALS als gebruiker niet bekent is
	 * @author Jan den Besten
	 */
	public function forgotten_password_send($email,$uri,$subject='Forgotten Password Verification') {
		$user = $this->get_user_by_email($email);
		// User not found?
		if (empty($user)) {
			$this->set_error('forgot_password_email_not_found');
			return FALSE;
		}
		else if ( $this->CI->ion_auth_model->forgotten_password($email) ) {
			$data = array(
				'user'										=> $user->str_username,
				'forgotten_password_uri'	=> $uri,
				'forgotten_password_code' => $this->CI->ion_auth_model->forgotten_password_code,
			);
			$message = $this->CI->load->view($this->CI->config->item('email_templates', 'ion_auth').$this->CI->config->item('email_forgot_password', 'ion_auth'), $data, true);

			$this->CI->email->clear();
			$config['mailtype'] = $this->CI->config->item('email_type', 'ion_auth');
			$this->CI->email->initialize($config);
			$this->CI->email->from($this->CI->config->item('admin_email', 'ion_auth'), $this->CI->config->item('site_title', 'ion_auth'));
			$this->CI->email->to($email);
			$this->CI->email->subject($this->CI->config->item('site_title', 'ion_auth').' - '.$subject);
			$this->CI->email->message($message);
			
			if ( $this->CI->email->send() ) {
				$this->set_message('forgot_password_successful');
				return TRUE;
			}
			else {
				$this->set_error('forgot_password_unsuccessful');
				return FALSE;
			}
		}
		else {
			$this->set_error('forgot_password_unsuccessful');
			return FALSE;
		}
	}
  
  
	
	/**
	 * Verzorgt het proces als een gebruiker het paswoord is vergeten
	 * 
	 * Verstuurt een mail naar gebruiker met link voor nieuw wachtwoord
	 *
	 * @param string $email Emailadres van gebruiker
	 * @param string $uri URI van pagina waar gebruiker naartoe wordt geleid door de email
	 * @param string $subject default='Forgotten Password Verification' Onderwerp van de te sturen email 
	 * @return bool TRUE als proces is gelukt, FALS als gebruiker niet bekent is
	 * @author Jan den Besten
	 */
  public function forgotten_password($email,$uri,$subject='Forgotten Password Verification') {
    
		$user = $this->get_user_by_email($email);
		// User not found?
		if (empty($user)) {
			$this->set_error('forgot_password_email_not_found');
			return FALSE;
		}
		else if ( $this->CI->ion_auth_model->forgotten_password($email) ) {
			$data = array(
				'user'										=> $user->str_username,
				'forgotten_password_uri'	=> $uri,
				'forgotten_password_code' => $this->CI->ion_auth_model->forgotten_password_code,
			);
      
      if ($this->send_mail($user->id,'forgot_password',$subject,$data)) {
				$this->set_message('forgot_password_successful');
				return TRUE;
      }
			else {
				$this->set_error('forgot_password_unsuccessful');
				return FALSE;
			}
		}
		else {
			$this->set_error('forgot_password_unsuccessful');
			return FALSE;
		}
	}
	
	/**
	 * Rond het proces van vergeten wachtwoord af
	 *
	 * @param string $code Code die gebruiker heeft gekregen
	 * @param string $subject  default='New Password'
	 * @param $extra_email=''
	 * @return bool TRUE als geslaagd
	 * @author Jan den Besten
	 */
	public function forgotten_password_complete($code,$subject='New Password',$extra_email='') {
		$identity = $this->CI->config->item('identity', 'ion_auth');
		$profile  = $this->CI->ion_auth_model->profile($code, true);
		if (!is_object($profile))	{
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}
		$new_password = $this->CI->ion_auth_model->forgotten_password_complete($code, $profile->str_salt);
		if ($new_password) {
			$data = array(
				'identity' => $profile->{$identity},
				'password' => $new_password
			);
      return $this->send_mail($profile->id,'email_new_password',$this->CI->config->item('site_title', 'ion_auth') . ' - '.$subject,$data);
		}

		$this->set_error('password_change_unsuccessful');
		return FALSE;
	}


	
	/**
	 * Registreer een nieuwe gebruiker
	 *
	 * @param string $username 
	 * @param string $password 
	 * @param string $email 
	 * @param string $additional_data 
	 * @param string $group_name 
	 * @param string $subject 
	 * @param string $uri 
	 * @return mixed $id als geslaagd, FALSE als niet geslaagd
	 * @author Jan den Besten
	 */
	public function register($username, $password, $email, $additional_data=array(), $group_name = false, $subject='Account Activation', $uri='') {
		if (empty($uri)) $uri=$this->CI->uri->get();
		$email_activation = $this->CI->config->item('email_activation', 'ion_auth');
    $admin_activation = $this->CI->config->item('admin_activation', 'ion_auth');
		if ($admin_activation) $email_activation=true;

		if (!$email_activation)	{
			$id = $this->CI->ion_auth_model->register($username, $password, $email, $additional_data, $group_name);
			if ($id !== FALSE) {
				$this->set_message('account_creation_successful');
				return $id;
			}
			else {
				$this->set_error('account_creation_unsuccessful');
				return FALSE;
			}
		}
		else {
			$id = $this->CI->ion_auth_model->register($username, $password, $email, $additional_data, $group_name);
			if (!$id)	{
				$this->set_error('account_creation_unsuccessful');
				return FALSE;
			}

			$deactivate = $this->CI->ion_auth_model->deactivate($id);

			if (!$deactivate)	{
				$this->set_error('deactivate_unsuccessful');
				return FALSE;
			}

			if (!$admin_activation) {
				return $this->send_activation_mail($id,$subject,$uri);
			}
			else {
				return $this->send_admin_new_register_mail($id);
			}
		}
	}
	
  /**
   * Stuur een mail waarmee nieuw geregistreerde gebruiker zichzelf kan activeren
   *
   * @param string $id 
   * @param string $subject
   * @param string $uri 
   * @return void
   * @author Jan den Besten
   */
	public function send_activation_mail($id,$subject='Account Activation',$uri) {
		$user       = $this->CI->ion_auth_model->get_user($id)->row();
		$data = array(
      'user_id'     => $user->id,
			'activate_uri'=> $uri,
			'activation' 	=> $user->str_activation_code,
		);
		return $this->send_mail($id,'email_activate',$subject,$data);
	}

  /**
   * Stuur administrater een mail dat een nieuwe gebruiker zich heeft geregistreerd
   *
   * @param string $id 
   * @return void
   * @author Jan den Besten
   */
	public function send_admin_new_register_mail($id) {
		return $this->send_mail($id,'email_admin_new_register','',array(),true);
	}

  /**
   * Stuur gebruiker mail dat registratie is geaccepteerd
   *
   * @param string $id 
   * @param string $subject  default='Account accepted and activated'
   * @param $extra_email default=''
   * @return void
   * @author Jan den Besten
   */
	public function send_accepted_mail($id,$subject='Account accepted and activated',$extra_email='') {
		return $this->send_mail($id,'email_accepted',$subject,array('extra_email'=>$extra_email));
	}

  /**
   * Stuur gebruiker mail dat account is aangemaakt, met (nieuwe) inloggegevens
   *
   * @param string $id 
   * @param string $subject  default='New account'
   * @param string $extra_email  default=''
   * @return void
   * @author Jan den Besten
   */
	public function send_new_account_mail($id,$subject='New account',$extra_email='') {
		$user  = $this->CI->ion_auth_model->get_user($id)->row();
		$email = $user->email_email;
    $code=$this->CI->ion_auth_model->forgotten_password($email);
    $password=$this->CI->ion_auth_model->forgotten_password_complete($code);
		return $this->send_mail($id,'email_new_account',$subject,array('password'=>$password,'extra_email'=>$extra_email));
	}
  
  
  /**
   * Stuur gebruiker mail met nieuwe inloggegevens (met nieuw wachtwoord)
   *
   * @param string $id 
   * @param string $subject  default='New account'
   * @param string $password  default=''
   * @param string $extra_email  default=''
   * @return bool
   * @author Jan den Besten
   */
	public function send_new_password_mail($id,$subject='New account',$password='',$extra_email='') {
		$user  = $this->CI->ion_auth_model->get_user($id)->row();
		$email = $user->email_email;
    if (empty($password)) {
      // Create random password and save it to user
      $password=$this->CI->ion_auth_model->create_password();
      $this->CI->ion_auth_model->save_password($id,$password);
    }
		return $this->send_mail($id,'email_new_password',$subject,array('password'=>$password,'extra_email'=>$extra_email));
	}


  /**
   * Stuur gebruiker mail dat registratie niet is toegestaan
   *
   * @param string $id 
   * @param string $subject default='Account denied'
   * @param string $extra_email  default=''
   * @return bool
   * @author Jan den Besten
   */
	public function send_deny_mail($id,$subject='Account denied',$extra_email='') {
		return $this->send_mail($id,'email_deny',$subject,array('extra_email'=>$extra_email));
	}

  /**
   * Stuur mail
   *
   * @param string $id 
   * @param string $template 
   * @param string $subject 
   * @param string $additional_data 
   * @param string $to_admin 
   * @return void
   * @author Jan den Besten
   */
	private function send_mail($id,$template,$subject,$additional_data=array(),$to_admin=false) {
		$identity   = $this->CI->config->item('identity', 'ion_auth');
		$user       = $this->CI->ion_auth_model->get_user($id)->row();
		if ($to_admin)
			$email = $this->CI->config->item('admin_email','ion_auth');
		else
			$email = $user->email_email;

		$data = array(
			'identity'   	=> $user->{$identity},
			'id'         	=> $user->id,
			'email'      	=> $email,
		);
		$data=array_merge($data,$additional_data);
    if (isset($additional_data['extra_email'])) $extra_email=$additional_data['extra_email'];
    
    // meta data from meta tables?
    $tables=$this->CI->config->item('tables', 'ion_auth');
    if (isset($tables['meta']) and !empty($tables['meta'])) {
      $this->CI->db->where($this->CI->config->item('join', 'ion_auth'),$id);
      $meta_data=$this->CI->db->get_row($tables['meta']);
      if (!empty($meta_data)) $data=array_merge($data,$meta_data);
    }
    
    // Send mail
		$this->CI->email->clear();
		$config['mailtype'] = $this->CI->config->item('email_type', 'ion_auth');
		$this->CI->email->initialize($config);
		$this->CI->email->from($this->CI->config->item('admin_email', 'ion_auth'), $this->CI->config->item('site_title', 'ion_auth'));
		$this->CI->email->to($email);
    if (isset($extra_email) and $extra_email) $this->CI->email->cc($extra_email);

    if ($this->mail_table) {
      $key='login_'.str_replace('email_','',$template);
      $send=$this->CI->email->send_lang($key,$data);
    }
    else {
  		$message = $this->CI->load->view($this->CI->config->item('email_templates', 'ion_auth').$this->CI->config->item($template, 'ion_auth'), $data, true);
  		$this->CI->email->subject($this->CI->config->item('site_title', 'ion_auth') . ' - '.$subject);
  		$this->CI->email->message($message);
      $send=$this->CI->email->send();
    }
    
		if ($send !== TRUE)	{
  		$this->set_error('activation_email_unsuccessful');
      return $send;
		}
		return $id;
	}


	/**
	 * Maak gebruiker (nieuw geregistreerd) actief
	 *
	 * @param string $user_id 
	 * @return void
	 * @author Jan den Besten
	 */
	public function activate_user($user_id) {
		$data=array('str_activation_code'=>'','b_active'=>true);
		$this->update_user($user_id,$data);
	}

  /**
   * Test of huidige gebruiker super admin rechten heeft
   *
   * @return bool TRUE als dat zo is
   * @author Jan den Besten
   */
	public function is_super_admin() {
		return ($this->rights["rights"]=="*");
	}
	
  /**
   * Test of huidige gebruiker genoeg rechten heeft om nieuwe gebruikers te mogen activeren
   *
   * @return bool TRUE als dat zo is
   * @author Jan den Besten
   */
	public function can_activate_users() {
		return $this->has_rights($this->tables['users']);
	}
	
  /**
   * Test of huidige gebruiker genoeg rechten heeft om backup van database te maken
   *
   * @return bool TRUE als dat zo is
   * @author Jan den Besten
   */
	public function can_backup() {
		if ($this->rights['b_backup']) return TRUE;
		return FALSE;
	}

  /**
   * Test of huidige gebruiker genoeg rechten heeft om admin tools te mogen gebruiken (search/replace, Automatisch vullen)
   *
   * @return bool TRUE als dat zo is
   * @author Jan den Besten
   */
	public function can_use_tools() {
		if ($this->rights['b_tools']) return TRUE;
		return FALSE;
	}

  /**
   * Checkt welke rechten de gebruiker heeft en maakt daar mooie variabel van
   *
   * @param int $userId 
   * @return array
   * @author Jan den Besten
   * @internal
   */
	private function create_rights($userId) {
		$this->CI->db->select('id,id_user_group');
		$this->CI->db->where($this->tables['users'].'.id',$userId);
		$this->CI->db->add_foreigns();
		$user=$this->CI->db->get_row($this->tables['users']);
		$rights=array();
		if ($user) {
			foreach ($user as $key => $value) {
				if (!in_array($key,array('id','id_group'))) {
					$rights[str_replace('cfg_user_groups__','',$key)]=$value;
				}
			}
		}
		return $rights;
	}


  /**
   * Deze functie wordt alleen gebruikt door has_rights()
   *
   * @param string $&found 
   * @param string $rights 
   * @return void
   * @author Jan den Besten
   * @internal
   */
	private function _change_rights(&$found,$rights) {
		foreach ($found as $key => $value) {
			if ($rights[$key]) $found[$key]=TRUE;
		}
	}
  
  /**
   * Test of gebruiker rechten heeft voor bepaald item (en row)
   * 
   * Mogelijke uitkomsten:
   * 
   * - RIGHTS_ALL    = 15 (RIGHTS_DELETE + RIGHTS_ADD + RIGHTS_EDIT + RIGHTS_SHOW)
   * - RIGHTS_DELETE = 8
   * - RIGHTS_ADD    = 4
   * - RIGHTS_EDIT   = 2
   * - RIGHTS_SHOW   = 1
   * - RIGHTS_NO     = 0 
   * - Of een combinatie van bovenstaande (een optelling)
   *
   * @param string $item tabel of media map waar de rechten voor getest worden
   * @param string $id default=0 Alleen nodig als rows/bestanden aan gebruikers gekoppeld zijn, hiermee kan dat getest worden
   * @param string $whatRight default=0 Eventueel checken op welke rechten getest wordt
   * @return int Rechten zie boven
   * @author Jan den Besten
   */
	public function has_rights($item,$id="",$whatRight=0) {
    if (  $item===$this->tables['users'] and !empty($id) and ($id!==-1) ) {
      $user_info = $this->get_user();
      // Eigen info kunnen aanpassen
      if ($id===$user_info->id) return 2;
      // No rights to edit/delete a user with more rights
      $deleted_group = $this->CI->db->get_field_where('cfg_users','id_user_group','id',$id);
      if ( $user_info->id_user_group > $deleted_group ) return false;
    }
		
		$found=array('b_delete'=>FALSE,'b_add'=>FALSE,'b_edit'=>FALSE,'b_show'=>FALSE);
		$pre=get_prefix($item);
		$preAll=$pre."_*";

		$foundRights=RIGHTS_NO;
		
		// $condition=($item=='media_knipsels');
		// trace_if($condition,$item);
		// trace_if($condition,$this->rights);
		// trace_if($condition,array('item'=>$item,'pre'=>$pre,'preAll'=>$preAll));

		$rights=$this->rights;
    
    if (isset($rights['stx_specific_rights']) and preg_match("/".$item."=\[(.*)\]/uiUsm", $rights['stx_specific_rights'],$match)) {
      $keys=explode(',',$match[1]);
      foreach ($keys as $key) {
        $name=get_prefix($key,'=');
        $value=get_suffix($key,'=');
        switch ($name) {
          case 'delete':  if ($value) $foundRights+=RIGHTS_DELETE;break;
          case 'add':     if ($value) $foundRights+=RIGHTS_ADD;break;
          case 'edit':    if ($value) $foundRights+=RIGHTS_EDIT;break;
          case 'show':    if ($value) $foundRights+=RIGHTS_SHOW;break;
        }
      }
    }
    else {
  		if ($rights and ($rights['rights']=="*" or (strpos($rights['rights'],$preAll)!==FALSE) or (strpos($rights['rights'],$item)!==FALSE)) ) {
  			$this->_change_rights($found,$rights);
  		}
  		// trace_if($condition,$found);
  		if (!empty($found['b_delete'])	and $found['b_delete'])	$foundRights+=RIGHTS_DELETE;
  		if (!empty($found['b_add']) 		and $found['b_add'])		$foundRights+=RIGHTS_ADD;
  		if (!empty($found['b_edit'])		and $found['b_edit'])		$foundRights+=RIGHTS_EDIT;
  		if (!empty($found['b_show'])		and $found['b_show'])		$foundRights+=RIGHTS_SHOW;
    }
    
    // trace_($foundRights);
    // trace_($whatRight);

		if ($whatRight==0)
			return $foundRights;
		else
			return ($foundRights>=$whatRight);
	}
	
  /**
   * Test of de rows van tabel/media_map gekoppeld zijn aan gebruikers
   *
   * @param string $table
   * @return bool TRUE als dat zo is
   * @author Jan den Besten
   */
	public function restricted_id($table) {
		$restricted=TRUE;
		$pre=get_prefix($table);
		$preAll=$pre."_*";
		$rights=$this->rights;
    
		if ($rights['rights']=="") $restricted=FALSE;
		if ($rights['rights']=="*" or (strpos($rights['rights'],$preAll)!==FALSE) or (strpos($rights['rights'],$table)!==FALSE) ) $restricted=$restricted and TRUE;
		if ($restricted) {
			return $this->user_id;
		}
		else
			return FALSE;
	}


  /**
   * Geeft array terug van alle tabellen waar gebruiker rechten voor heeft
   *
   * @param string $atLeast default=RIGHTS_ALL Minimal rechten die een tabel moet hebben om in her resultaat te komen 
   * @return array Tabellen waar de gebruiker de gevraagde rechten voor heeft.
   * @author Jan den Besten
   */
	public function get_table_rights($atLeast=RIGHTS_ALL) {
		$tables=$this->CI->db->list_tables();
		$tableRights=array();
		foreach ($tables as $key => $table) {
			$pre=get_prefix($table);
			if ($pre==$this->CI->config->item('REL_table_prefix')) {
				$rTable=table_from_rel_table($table);
				$rights=$this->has_rights($rTable);
			}
			else {
				$rights=$this->has_rights($table);
			}
			if ($rights>=$atLeast) $tableRights[]=$table;
		}
		return $tableRights;
	}
	
  /**
   * Geeft rechten van huidige gebruiker
   *
   * @return array
   * @author Jan den Besten
   */
	public function get_rights() {
		return $this->rights;
	}

	/**
	 * Geeft alle inactieve gebruikers die langer dan bepaalde tijd geleden geregistreerd zijn
	 *
	 * @param string $group_name default=FALSE
	 * @param int $time default=1209600 tijd geleden in sec (dag=86400, week=604800, 2 weken=1209600, 4 weken=2419200)
	 * @return object met alle inactieve gebruikers
	 * @author Jan den Besten
	 **/
	public function get_inactive_old_users($group_name = false, $time=1209600) {
		return $this->CI->ion_auth_model->get_inactive_old_users($group_name,$time)->result();
	}



}
