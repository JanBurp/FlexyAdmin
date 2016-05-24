<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."libraries/Ion_auth.php");


/** \ingroup libraries
 * Class voor het inloggen, aanmaken etc van gebruikers
 * 
 * NB Is een uitbreiding op IonAuth. Gebruik IonAuth niet direct, maar altijd via Flexy_auth, dus ->flexy_auth->...
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
 
class Flexy_auth extends Ion_auth {
  
  private $_rights_settings = array(
    8 => 'DELETE',
    4 => 'ADD',
    2 => 'EDIT',
    1 => 'SHOW',
  );

  /**
   * Rechten van huidige gebruiker
   */
	public $rights;

  /**
   * gegevens van huidige gebruiker
   */
	public $user_id = FALSE;
  public $group_id;
  public $username;
  public $language;
  
  
  /**
   * Array van tbl_site
   */
	private $siteInfo;
  
  /**
   * Is er een tabel met emails?
   */
  private $mail_table=false;
  
  public function __construct() {
		parent::__construct();
    // Stel site afhankelijke instelling in
		$this->db->select('`str_title` AS `site_title`, `email_email` AS `admin_email`');
    $site_config=$this->db->get('tbl_site')->row_array();
    $this->set_config( $site_config );
    $this->tables = $this->config->item( 'tables', 'ion_auth');
	}
  
  /**
   * Overrule standaard instellingen.
   *
   * @param array $config 
   * @return $this
   * @author Jan den Besten
   */
  public function set_config( $config ) {
    foreach ($config as $key => $value) {
      $this->config->set_item( $key, $value, 'ion_auth' );
    }
    return $this;
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
		if (parent::login($identity, $password, $remember)) {
      // $this->load_user_cfg();
      $this->load->model('log_activity');
      $this->log_activity->auth();
			return TRUE;
		}
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
      $this->load->model('log_activity');
      $this->log_activity->auth('logout',$this->user_id);
    }
    return $logout;
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
      $user=$this->get_user();
			$this->user_id     = $user['user_id'];
      $this->username    = $user['username'];
      $this->language    = $user['str_language'];
			$this->rights      = $this->create_rights( $this->user_id );
      $this->group_id    = $user['group_id'];
      // $this->load_user_cfg();
		}
    return (bool) $logged_in;
	}
  
  
  /**
   * Creert een mooie user array, de standaard (zinvolle) velden zoals ze in de cfg_users tabel voorkomen inclusief groep:
   *
   * 'user_id'
   * 'username'
   * 'email'
   * 'b_active'
   * 'group_id'
   * 'group_name'
   * 'group_description'
   *
   * @param array $user 
   * @return array $user
   * @author Jan den Besten
   */
  private function _create_nice_user($user) {
    // Alleen zinvolle velden
    $user = array_keep_keys( $user, array( 'id','str_username', 'email_email', 'str_language','b_active') );
    // Voeg groepen toe
    $groups = $this->get_users_groups($user['id'])->row();
    $groups = object2array($groups);
    $user['group_id'] = el('id',$groups,0);
    $user['group_name'] = el('name',$groups,'');
    $user['group_description'] = el('description',$groups,'');
    // Hernoem/Alias
    $user['user_id'] = $user['id'];
    $user['username'] = $user['str_username'];
    $user['email'] = $user['email_email'];
    // Extra emailadressen
    $user['extra_email'] = array();
    $user['extra_email_string'] = '';
    return $user;
  }
  
  
  /**
   * Geeft (zinvolle) user info terug als array met iig de volgende keys
   * 
   * 'user_id'
   * 'username'
   * 'email_email'
   * 'b_active'
   * 'group_id'
   * 'group_name'
   * 'group_description'
   *
   * @param int $user_id [NULL]
   * @return array
   * @author Jan den Besten
   */
  public function get_user( $user_id=NULL ) {
    $user = $this->user( $user_id )->row_array();
    if ($user) {
      $user = $this->_create_nice_user($user);
      return $user;
    }
    return FALSE;
  }

  /**
   * Geeft user gekoppeld aan email
   *
   * @param string $email 
   * @return array
   * @author Jan den Besten
   */
  public function get_user_by_email($email) {
    $query = $this->db->where('email_email',$email)->get('cfg_users');
    if ($query) {
      $user = $query->row_array();
      if ($user) {
        $user = $this->_create_nice_user($user);
        return $user;
      }
    }
    return FALSE;
  }
  
  
  /**
   * Geeft (zinvolle) users (zie get_user)
   *
   * @param mixed $groups[NULL]
   * @return array
   * @author Jan den Besten
   */
  public function get_users( $groups = NULL ) {
    $users = $this->users($groups)->result_array();
    foreach ($users as $key => $user) {
      $users[$key] = $this->_create_nice_user($user);
    }
    return $users;
  }
  
  
  
  /**
   * Pas gebruikers(data) aan.
   *
   * @param int $user_id 
   * @param array $data 
   * @return bool (true als gelukt)
   * @author Jan den Besten
   */
  public function update($id,$data) {
    // Extra velden toevoegen
    if ( $this->db->field_exists('user_changed','cfg_users') and $this->user_id) $data['user_changed'] = $this->user_id;
    // Update
    $success = parent::update($id,$data);
    // Log als gelukt
    if ($success) $this->log_activity->database( $this->db->last_query(), 'cfg_users', $id );
    return $success;
  }
  
  /**
   * Alias voor ->update()
   *
   * @param int $id 
   * @param array $data 
   * @return bool
   * @author Jan den Besten
   */
  public function update_user($id,$data) {
    return $this->update($id,$data);
  }
  
	
  
  // /**
  //  * Add user cfg from user groups to config
  //  *
  //  * @return void
  //  * @author Jan den Besten
  //  */
  // private function load_user_cfg() {
  //   $rights=$this->get_rights();
  //   // if (isset($rights['stx_extra_config']) and !empty($rights['stx_extra_config'])) {
  //   //   $extra_cfg=$rights['stx_extra_config'];
  //   //   $extra_cfg=str_replace(array(' ','"',"'"),'',$extra_cfg);
  //   //   $extra_cfg=explode(';',$extra_cfg);
  //   //   foreach ($extra_cfg as $cfg) {
  //   //     $cfg=trim($cfg);
  //   //     if (!empty($cfg)) {
  //   //       $key=get_prefix($cfg,'=');
  //   //       $key=trim(trim($key,'['),']');
  //   //       $keys=explode('][',$key);
  //   //       $value=trim(get_suffix($cfg,'='));
  //   //       array_set_multi_key($this->config->config,$keys,$value);
  //   //     }
  //   //   }
  //   // }
  // }
  
  
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
		else if ( $this->ion_auth_model->forgotten_password($email) ) {
			$data = array(
				'user'										=> $user->str_username,
				'forgotten_password_uri'	=> $uri,
				'forgotten_password_code' => $this->ion_auth_model->forgotten_password_code,
			);
			$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_forgot_password', 'ion_auth'), $data, true);

			$this->email->clear();
			$config['mailtype'] = $this->config->item('email_type', 'ion_auth');
			$this->email->initialize($config);
			$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
			$this->email->to($email);
			$this->email->subject($this->config->item('site_title', 'ion_auth').' - '.$subject);
			$this->email->message($message);
			
			if ( $this->email->send() ) {
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
	 * Verstuurt een mail naar gebruiker met link voor nieuw wachtwoord (TODO de link!!)
	 *
	 * @param string $email Emailadres van gebruiker
	 * @return bool TRUE als proces is gelukt
	 * @author Jan den Besten
	 */
  public function forgotten_password( $email ) {
    $user = $this->get_user_by_email( $email );
    if ( !$user ) FALSE;
    $mail_info = parent::forgotten_password( $user['username'] );
    return $this->_mail( 'login_forgot_password', $user, $mail_info );
  }
  
  /**
   * Afronden van vergeten wachtwoord actie
   *
   * @param string $code 
   * @return void
   * @author Jan den Besten
   */
  public function forgotten_password_complete($code) {
    $mail_info = parent::forgotten_password_complete($code);
    if (!$mail_info) return FALSE;
    $mail_info['password'] = $mail_info['new_password'];
    $send = $this->_mail( 'login_new_password', $mail_info );
    if ($send!==true) $this->set_error('password_change_unsuccessful');
    return $send;
  }
  
  /**
   * Maakt nieuw random wachtwoord voor gebruiker
   *
   * @param int $user_id 
   * @return string
   * @author Jan den Besten
   */
  private function _create_new_password( $user_id ) {
    $salt = $this->db->get_field_where('cfg_users','salt','id',$user_id);
    $password = $this->salt();
    $hashed_password = $this->hash_password( $password, $salt);
    return array(
      'password' => $password,
      'hash_password' => $hashed_password,
    );
  }
	
  
  /**
   * Stuur gebruiker mail met nieuw wachtwoord
   *
   * @param mixed $user user array of id
   * @param array $data[null] 
   * @param string $template ['login_new_password']
   * @return bool
   * @author Jan den Besten
   */
  public function send_new_password( $user, $data=array(), $template = 'login_new_password' ) {
    if (!is_array($user)) $user = $this->get_user($user);
    // Create random password and save it to user
    $password_info = $this->_create_new_password($user['user_id']);
		$set = array(
		   'gpw_password'  => $password_info['hash_password'],
		   'remember_code' => NULL,
		);
    $successfully_changed_password_in_db = $this->db->update('cfg_users', $set, array('id' => $user['user_id'] ));
		if ($successfully_changed_password_in_db)
		{
			$this->trigger_events(array('post_change_password', 'post_change_password_successful'));
			$this->set_message('password_change_successful');
      $data = array_merge($data,$set);
      $data['identity'] = $user['username'];
      $data['password'] = $password_info['password'];
      return $this->_mail($template, $user, $data);
		}
		else
		{
			$this->trigger_events(array('post_change_password', 'post_change_password_unsuccessful'));
			$this->set_error('password_change_unsuccessful');
      return FALSE;
		}
  }

  /**
   * Stuur gebruiker mail dat account is aangemaakt, met (nieuwe) inloggegevens
   *
   * @param mixed $user user array of id
   * @param array $data[null] 
   * @return bool
   * @author Jan den Besten
   */
  public function send_new_account( $user, $data=array() ) {
    return $this->send_new_password($user,$data,'login_new_account');
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
  // public function register($username, $password, $email, $additional_data=array(), $group_name = false, $subject='Account Activation', $uri='') {
  //   if (empty($uri)) $uri=$this->uri->get();
  //   $email_activation = $this->config->item('email_activation', 'ion_auth');
  //     $admin_activation = $this->config->item('admin_activation', 'ion_auth');
  //   if ($admin_activation) $email_activation=true;
  //
  //   if (!$email_activation)  {
  //     $id = $this->ion_auth_model->register($username, $password, $email, $additional_data, $group_name);
  //     if ($id !== FALSE) {
  //       $this->set_message('account_creation_successful');
  //       return $id;
  //     }
  //     else {
  //       $this->set_error('account_creation_unsuccessful');
  //       return FALSE;
  //     }
  //   }
  //   else {
  //     $id = $this->ion_auth_model->register($username, $password, $email, $additional_data, $group_name);
  //     if (!$id)  {
  //       $this->set_error('account_creation_unsuccessful');
  //       return FALSE;
  //     }
  //
  //     $deactivate = $this->ion_auth_model->deactivate($id);
  //
  //     if (!$deactivate)  {
  //       $this->set_error('deactivate_unsuccessful');
  //       return FALSE;
  //     }
  //
  //     if (!$admin_activation) {
  //       return $this->send_activation_mail($id,$subject,$uri);
  //     }
  //     else {
  //       return $this->send_admin_new_register_mail($id);
  //     }
  //   }
  // }
	
  /**
   * Stuur een mail waarmee nieuw geregistreerde gebruiker zichzelf kan activeren
   *
   * @param string $id 
   * @param string $subject
   * @param string $uri 
   * @return void
   * @author Jan den Besten
   */
  // public function send_activation_mail($id,$subject='Account Activation',$uri) {
  //   $user       = $this->ion_auth_model->user($id)->row();
  //   $data = array(
  //       'user_id'     => $user->id,
  //     'activate_uri'=> $uri,
  //     'activation'   => $user->str_activation_code,
  //   );
  //   return $this->send_mail($id,'email_activate',$subject,$data);
  // }

  /**
   * Stuur administrater een mail dat een nieuwe gebruiker zich heeft geregistreerd
   *
   * @param string $id 
   * @return void
   * @author Jan den Besten
   */
  // public function send_admin_new_register_mail($id) {
  //   return $this->send_mail($id,'email_admin_new_register','',array(),true);
  // }

  /**
   * Stuur gebruiker mail dat registratie is geaccepteerd
   *
   * @param string $id 
   * @param string $subject  default='Account accepted and activated'
   * @param $extra_email default=''
   * @return void
   * @author Jan den Besten
   */
  public function user_accepted_mail( $user_id ) {
    return $this->_mail( 'login_accepted', $user_id );
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
  public function user_denied_mail( $user_id ) {
    return $this->_mail( 'login_deny',$user_id );
  }

  /**
   * Stuur email, TODO: als meerdere emailadressen gekoppeld zijn, stuur deze dan ook door
   *
   * @param string $template 
   * @param mixed $user (array of id)
   * @param array $data [array()] Eventueel extra gegegevens
   * @param bool $send_to_admin [FALSE] Als true dan wordt de mail gestuurd naar de administrator
   * @return bool
   * @author Jan den Besten
   */
  private function _mail( $template, $user, $data=array(), $send_to_admin = FALSE ) {
    // Collect data die in de mail komt
    if (!is_array($user)) $user = $this->get_user($user);
    $data = array_merge($user,$data);
    $data['identity'] = $user['username'];
    // Waarnaartoe?
    if ($send_to_admin) {
      $to = $this->config->item('admin_email','ion_auth');
    }
    else {
      $to = el('email',$user, el('email_email',$user));
    }
    // Stuur
    $this->email->clear();
    $this->email->from( $this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth') );
    $this->email->to( $to );
    // Naar welke template in cfg_email?
    $send = $this->email->send_lang( $template,$data );
    if ($send !== TRUE) $this->set_error('activation_email_unsuccessful');
    return $send;
  }


  /**
   * Maak gebruiker (nieuw geregistreerd) actief
   *
   * @param string $user_id
   * @return void
   * @author Jan den Besten
   */
  public function activate_user( $user_id ) {
    $data = array( 'activation_code'=>'','b_active'=>true);
    $this->update( $user_id,$data );
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
   * Test of huidige gebruiker genoeg rechten heeft om nieuwe gebruikers te mogen aanpassen
   *
   * @return bool TRUE als dat zo is
   * @author Jan den Besten
   */
	public function allowed_to_edit_users() {
		return $this->has_rights($this->tables['users'])>=RIGHTS_ALL;
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
   * Geeft de rechten in een array terug van de meegegeven gebruiker
   *
   * @param int $user_id 
   * @return array
   * @author Jan den Besten
   * @internal
   */
	private function create_rights($user_id) {
		$this->db->select('id');
		$this->db->where('cfg_users.id',$user_id);
		$this->db->add_many(array('rel_users__groups'=>array('rights','b_all_users','b_backup','b_tools','b_delete','b_add','b_edit','b_show')));
		$user=$this->db->get_row('cfg_users');
		$rights=array();
		if ($user) {
      foreach ($user['rel_users__groups'] as $id_group => $group) {
        $rights[$id_group]=$group;
        unset($rights[$id_group]['id']);
      }
		}
    $rights=current($rights); // TODO wat als meerdere groepen?
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
	public function has_rights( $item,$id="",$whatRight=0, $user_id=FALSE) {
    
    // Ingelogde gebruiker of een andere?
    if ( !$user_id ) {
      $user_info = $this->get_user();
      $rights = $this->rights;
    }
    else {
      $user_info = $this->get_user($user_id);
      $rights = $this->create_rights( $user_id );
    }
    
    // trace_(['has_rights',$item,$user_id,$rights]);

    // Alleen rechten voor aanpassen van zichzelf
    if (  $item===$this->tables['users'] and !empty($id) and ($id!==-1) ) {
      if ($id===$user_info['user_id']) return RIGHTS_EDIT;
    }
    
    // trace_(['has_rights',$rights]);
		
		$found=array('b_delete'=>FALSE,'b_add'=>FALSE,'b_edit'=>FALSE,'b_show'=>FALSE);
		$pre=get_prefix($item);
		$preAll=$pre."_*";

		$rightsAsNumber=RIGHTS_NO;
    
    // if (isset($rights['stx_specific_rights']) and preg_match("/".$item."=\[(.*)\]/uiUsm", $rights['stx_specific_rights'],$match)) {
    //   $keys=explode(',',$match[1]);
    //   foreach ($keys as $key) {
    //     $name=get_prefix($key,'=');
    //     $value=get_suffix($key,'=');
    //     switch ($name) {
    //       case 'delete':  if ($value) $rightsAsNumber+=RIGHTS_DELETE;break;
    //       case 'add':     if ($value) $rightsAsNumber+=RIGHTS_ADD;break;
    //       case 'edit':    if ($value) $rightsAsNumber+=RIGHTS_EDIT;break;
    //       case 'show':    if ($value) $rightsAsNumber+=RIGHTS_SHOW;break;
    //     }
    //   }
    // }
    // else {
		if ($rights and ($rights['rights']==="*" or (strpos($rights['rights'],$preAll)!==FALSE) or (strpos($rights['rights'],$item)!==FALSE)) ) {
			$this->_change_rights($found,$rights);
		}
		// trace_if($condition,$found);
		if (!empty($found['b_delete'])	and $found['b_delete'])	$rightsAsNumber+=RIGHTS_DELETE;
		if (!empty($found['b_add']) 		and $found['b_add'])		$rightsAsNumber+=RIGHTS_ADD;
		if (!empty($found['b_edit'])		and $found['b_edit'])		$rightsAsNumber+=RIGHTS_EDIT;
		if (!empty($found['b_show'])		and $found['b_show'])		$rightsAsNumber+=RIGHTS_SHOW;
    // }
    
    // trace_($rightsAsNumber);
    // trace_($whatRight);

		if ($whatRight==0)
			return $rightsAsNumber;
		else
			return ($rightsAsNumber>=$whatRight);
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
		$tables=$this->db->list_tables();
		$tableRights=array();
		foreach ($tables as $key => $table) {
			$pre=get_prefix($table);
			if ($pre==$this->config->item('REL_table_prefix')) {
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
	public function get_rights( $user_id=FALSE ) {
    if ( $user_id===FALSE) return $this->rights;
    return $this->create_rights($user_id);
	}

  
  
  /**
   * Geeft rechten als een mooie string
   * 
   * - DELETE = 8
   * - ADD    = 4
   * - EDIT   = 2
   * - SHOW   = 1
   * - NO     = 0 
   *
   * @param string $rights 
   * @return string
   * @author Jan den Besten
   */
  public function rights_to_string($rights) {
    $string = '';
    foreach ($this->_rights_settings as $number => $description) {
      if ( $rights-$number >=0 ) {
        $string = add_string($string,$description,'|');
      }
    }
    return $string;
  }



}
