<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."libraries/Ion_auth.php");

use \Firebase\JWT\JWT;


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
   * Gegevens & rechten van huidige gebruiker, zelfde als wat get_user() teruggeeft
   */
  private $current_user = FALSE;
  
  /**
   * De uri waar de vergeten wachtwoorden worden afgehandeld.
   */
  private $forgotten_password_uri = '';
  
  /**
   * Authentication token
   */
  protected $auth_key              = '';  // See $config['sess_cookie_name']
  protected $auth_token            = '';
  
  
  /**
   * construct
   */
  public function __construct() {
    $this->load->model('data/data_core');
    $this->load->model('data/data');
		parent::__construct();
    // Stel site afhankelijke instelling in
    $this->db->select('`str_title` AS `site_title`, `email_email` AS `admin_email`');
    $site_config=$this->db->get('tbl_site')->row_array();
    $this->set_config( $site_config );
    $this->tables = $this->config->item( 'tables', 'ion_auth');
    // Token secret, Expiration of auth_token: each day a new one, add 'unixday' to key
    if (empty($this->auth_key)) $this->auth_key = $this->config->item('sess_cookie_name');
    // $this->auth_key.= ceil((date('U') - (3*TIME_DAY)) / TIME_DAY);
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
      $this->config->set_item( array('ion_auth',$key), $value );
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
      // Token aanmaken en toevoegen aan sessie
      if (empty($this->auth_token)) {
        $token = array(
          'username' => $identity,
          'password' => $password,
        );
        $this->auth_token = JWT::encode( $token, $this->auth_key );
      }
      $currentSession = $this->session->userdata();
      $currentSession['auth_token'] = $this->auth_token;
      $this->session->set_userdata($currentSession);
      if (!$this->current_user) $this->_set_current_user();
      $this->load->model('log_activity');
      $this->log_activity->auth();
			return TRUE;
		}
		return FALSE;
	}
  
  /**
   * Login by checking the authorization header (for API/AJAX calls)
   *
   * @return bool
   * @author Jan den Besten
   */
  public function login_with_authorization_header() {
    $loggedIn = FALSE;
    // Van header
    $this->auth_token = $this->input->get_request_header('Authorization', TRUE);
    // Van GET
    if (empty($this->auth_token) or $this->auth_token==='undefined') {
      $this->auth_token = $this->input->get('_authorization', TRUE);
    }
      
    if (!empty($this->auth_token) and $this->auth_token!=='undefined') {
      $error_reporting = error_reporting();
      error_reporting(0);
      try {
        $auth_data = (array) JWT::decode( $this->auth_token, $this->auth_key, array('HS256') );
      } catch (Exception $e) {
        $auth_data = array();
      }      
      error_reporting($error_reporting);
      if (isset($auth_data['username']) and isset($auth_data['password']) ) {
        $loggedIn = $this->login( $auth_data['username'], $auth_data['password'] );
      }
    }
    // Always remove session when no authentication
    if ( !$loggedIn ) $this->flexy_auth->logout();
    // Return if logged_in
    return $loggedIn;
  }
  
  
  /**
   * Logout
   *
   * @return void
   * @author Jan den Besten
   */
  public function logout() {
    $user_id = el('id',$this->current_user);
    $logout = parent::logout();
    if ($logout) {
      $this->load->model('log_activity');
      $this->log_activity->auth('logout',$user_id);
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
    if ( !$logged_in ) {
      $this->current_user = FALSE;
      return FALSE;
    }
    $this->_set_current_user();
    return (bool) $logged_in;
	}

  /**
   * Helper: Sets current user
   *
   * @return void
   * @author Jan den Besten
   */
  private function _set_current_user() {
    $user = $this->user()->row_array();
    $this->current_user = $this->_create_nice_user($user);
    if (empty($this->auth_token)) {
      $currentSession = $this->session->userdata();
      $this->auth_token = $currentSession['auth_token'];
    }
    $this->current_user['auth_token'] = $this->auth_token;
    if ( isset($this->current_user['id']) AND $this->in_group( 1, $this->current_user['id'] )) $this->current_user['is_super_admin'] = TRUE;
  }
  
  
  /**
   * Creert een mooie user array, de standaard (zinvolle) velden zoals ze in de cfg_users tabel voorkomen inclusief groep en eventueel extra emails:
   *
   * 'id'
   * 'user_id'
   * 'str_username'
   * 'username'
   * 'email'
   * 'email_email'
   * 'extra_email' => array()
   * 'b_active'
   * 'groups' => array(
   *    'id'
   *    'name'
   *    'description'
   * )
   *
   * @param array $user 
   * @return array $user
   * @author Jan den Besten
   */
  private function _create_nice_user($user) {
    // Alleen zinvolle velden
    if (!is_array($user)) return FALSE;
    $user = array_keep_keys( $user, array( 'id', 'str_username','username', 'email_email','email', 'str_language','str_filemanager_view', 'b_active','auth_token') );
    // Voeg groepen toe
    $groups = $this->get_users_groups($user['id'])->result_array();
    $user['groups']=array();
    foreach ($groups as $key => $group) {
      $user['groups'][$group['id']] = $group;
    }
    // Rechten
    $user['rights'] = $this->_create_rights( $user );
    // Hernoem/Alias
    $user['user_id'] = el('id',$user);
    $user['username'] = el('str_username',$user,'');
    $user['email'] = el('email_email',$user,'');
    // Extra emailadressen
    $user['extra_email'] = array();
    $user['extra_email_string'] = '';
    // Kijk in config of er idd extra emails nodig zijn (en van welke tabel)
    $this->config->load('data/cfg_users',true);
    $has_extra_emails = $this->config->get_item(array('data/cfg_users','has_extra_emails'));
    // Zo ja, haal ze op (zonder data/db model!)
    if ( $has_extra_emails ) {
      $sql = 'SELECT `email_email` FROM `'.$has_extra_emails.'` WHERE `id_user` = "'.$user['id'].'"';
      $query=$this->db->query($sql);
      if ($query) {
        $user_emails = $query->result_array();
        foreach ($user_emails as $extra ) {
          array_push($user['extra_email'],$extra['email_email']);
          $user['extra_email_string'] = add_string($user['extra_email_string'],$extra['email_email'],',');
        }
      }
    }
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
  public function get_user( $user_id=NULL, $field='' ) {
    $user = FALSE;
    if (is_null($user_id) and is_array($this->current_user)) {
      $user = $this->current_user;
    }
    else {
      $user = $this->user( $user_id )->row_array();
      if ($user) {
        $user = $this->_create_nice_user($user);
      }
    }
    if ($user) {
      if (!empty($field)) return el($field,$user);
      return $user;
    }
    return FALSE;
  }
  
  /**
   * Geeft rechten van huidige gebruiker
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_rights( $user_id=NULL ) {
    return $this->get_user($user_id,'rights');
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
   * Geeft user gekoppeld aan username
   *
   * @param string $username 
   * @return array
   * @author Jan den Besten
   */
  public function get_user_by_name($username) {
    $query = $this->db->where('str_username',$username)->get('cfg_users');
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
    $result = $this->users($groups)->result_array();
    $users=array();
    foreach ( $result as $key => $user ) {
      $users[$user['id']] = $this->_create_nice_user($user);
    }
    return $users;
  }
  
  
  /**
   * Maakt nieuwe gebruiker aan
   *
   * @param array $data (in ieder geval email_email & str_username)
   * @param array $groups (eventueel mee te geven groepen, als ids)
   * @return int $id
   * @author Jan den Besten
   */
  public function insert_user($data,$groups=array()) {
    // Check of noodzakelijk velden bestaan
    if (!isset($data['email_email']) or !isset($data['str_username'])) return FALSE;
    if ($groups and !is_array($groups)) $groups = array($groups);
    // Voeg eventueel random password toe
    if (!isset($data['gpw_password'])) $data['gpw_password'] = random_string('alnum',12);
    // Additional data
    $additional_data = array_unset_keys($data,array('str_username','gpw_password','email_email'));
    // Insert
    $id = parent::register( $data['str_username'], $data['gpw_password'], $data['email_email'], $additional_data, $groups);
    // Log als gelukt
    if ($id) $this->log_activity->database( $this->db->last_query(), 'cfg_users', $id );
    return $id;
  }
  
  /**
   * Verwijderd gebruiker
   *
   * @param string $user_id 
   * @return void
   * @author Jan den Besten
   */
  public function delete_user($user_id) {
    $success = parent::delete_user($user_id);
    // Log als gelukt
    if ($success) $this->log_activity->database( $this->db->last_query(), 'cfg_users', $user_id );
    return $success;
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
    if ( $this->db->field_exists('user_changed','cfg_users') and $this->current_user['id']) $data['user_changed'] = $this->current_user['id'];
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
  
  
  
  /**
   * Loopt alle users langs en als ze nog geen gebruikersgroep hebben, geef ze de standaard 'user' gebruikersgroep
   *
   * @return $this
   * @author Jan den Besten
   */
  public function auto_set_imported_users() {
    $users = $this->get_users();
    $user_group = 3;
    foreach ($users as $user_id => $user) {
      if (empty($user['groups'])) {
        $this->add_to_group( $user_group, $user_id );
      }
    }
    return $this;
  }
  
  
  
  
	/**
	 * Verstuurt een mail naar gebruiker met een nieuw wachtwoord
	 *
	 * @param string $email Emailadres van gebruiker
	 * @param string $uri 
	 * @param string $subject default='Forgotten Password Verification' Onderwerp van de te sturen email 
	 * @return bool TRUE als proces is gelukt, FALSE als gebruiker niet bekent is
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
      // $config['mailtype'] = $this->config->item('email_type', 'ion_auth');
      // $this->email->initialize($config);
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
   * Stel de uri in van de pagina waar de vergeten wachtwoorden worden afgehandeld
   *
   * @param string $uri 
   * @return $this
   * @author Jan den Besten
   */
  public function set_forgotten_password_uri($uri) {
    $this->forgotten_password_uri = $uri;
    return $this;
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
    if ( !$user ) return FALSE;
    $mail_info = parent::forgotten_password( $user['username'] );
    $mail_info['forgotten_password_uri'] = $this->forgotten_password_uri;
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
    $user = $this->get_user_by_name($mail_info['identity']);
    $mail_info['password'] = $mail_info['new_password'];
    $send = $this->_mail( 'login_new_password', $user, $mail_info );
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
    $sql = 'SELECT `salt` FROM `cfg_users` WHERE `id` = "'.$user_id.'"';
    $salt = $this->db->query($sql)->row_object()->salt;
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
    $successfully_changed_password_in_db = TRUE;
    if (!isset($data['password'])) {
      // Create random password and save it to user
      $password_info = $this->_create_new_password($user['user_id']);
  		$set = array(
  		   'gpw_password'  => $password_info['password'], // Hash gebeurt in data
  		   'remember_code' => NULL,
  		);
      $successfully_changed_password_in_db = $this->data->table('cfg_users')->where('id',$user['user_id'])->set($set)->update();
    }
    // Stuur mail
		if ($successfully_changed_password_in_db) {
      $this->trigger_events(array('post_change_password', 'post_change_password_successful'));
			$this->set_message('password_change_successful');
      if (isset($set)) {
        $data = array_merge($data,$set);
        $data['password'] = $password_info['password'];
      }
      $data['identity'] = $user['username'];
      return $this->_mail($template, $user, $data);
		}
		else {
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
   * @param array $template ['login_new_account'] 
   * @return bool
   * @author Jan den Besten
   */
  public function send_new_account( $user, $data=array(), $template='login_new_account' ) {
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
    $data['identity'] = el('username',$user, el('identity',$user) );
    // Waarnaartoe?
    if ($send_to_admin) {
      $to = $this->config->item('admin_email','ion_auth');
    }
    else {
      $to = el('email',$user, el('email_email',$user));
    }
    // Stuur
    $site = $this->data->table('tbl_site')->get_row();
    $this->email->clear();
    $this->email->from( $site['email_email'], $site['str_title'] );
    $this->email->to( $to );
    // Naar meerdere emailadressen?
    if (el('extra_email',$user)) {
      $this->email->cc( el('extra_email',$user) );
    }
    // Naar welke template in cfg_email?
    $send = $this->email->send_lang( $template, $data );
    // trace_($send);
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
    return el('is_super_admin',$this->current_user,FALSE);
	}
  
  /**
   * Test of huidige gebruiker mag inloggen in backend van cms
   *
   * @return bool
   * @author Jan den Besten
   */
  public function allowed_to_use_cms() {
    $rights = $this->current_user['rights'];
    //return (( el('b_edit',$rights,FALSE) or el('b_add',$rights,FALSE) or el('b_delete',$rights,FALSE) or el('b_all_users',$rights,FALSE)) and !empty($rights['rights']));
    return TRUE;
  }
  
  /**
   * Test of gebruiker op z'n minst in deze groep zit (of een groep die meer rechten heeft)
   * TODO: werkt nu alleen op de volgorde van groepen, wordt alleen gebruikt in AdminMenu
   *
   * @param mixed $group_id [or group name]
   * @return bool
   * @author Jan den Besten
   */
  public function at_least_in_group( $group_id ) {
    if (is_string($group_id)) {
      $group_id = $this->data->table('cfg_user_groups')->select('id')->where('name',$group_id)->get_row();
      $group_id = el('id',$group_id,false);
    }
    $yes = FALSE;
    if (is_array($this->current_user['groups'])) {
      foreach ($this->current_user['groups'] as $id => $group) {
        if ($id<=$group_id) $yes=TRUE;
      }
    }
    return $yes;
  }
	
  /**
   * Test of huidige gebruiker genoeg rechten heeft om nieuwe gebruikers te mogen aanpassen
   *
   * @return bool TRUE als dat zo is
   * @author Jan den Besten
   */
	public function allowed_to_edit_users() {
		return $this->has_rights( $this->tables['users'] ) >= RIGHTS_ALL;
	}
	
  /**
   * Test of huidige gebruiker genoeg rechten heeft om backup van database te maken
   *
   * @return bool TRUE als dat zo is
   * @author Jan den Besten
   */
	public function can_backup() {
		if ($this->current_user['rights']['backup']) return TRUE;
		return FALSE;
	}

  /**
   * Test of huidige gebruiker genoeg rechten heeft om admin tools te mogen gebruiken (search/replace, Automatisch vullen)
   *
   * @return bool TRUE als dat zo is
   * @author Jan den Besten
   */
	public function can_use_tools() {
		if ($this->current_user['rights']['tools']) return TRUE;
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
	private function _create_rights( $user ) {
    if (empty($user['groups'])) return FALSE;
    if (isset($user['rights']) and is_array($user['rights'])) return $user['rights'];
    
    $sql = "SELECT * FROM `cfg_user_groups` WHERE `id` IN(".implode(',',array_keys($user['groups'])).")";
    $query = $this->db->query($sql);
    if (!$query) return FALSE;
    $groups = $query->result_array();
		if (!$groups) return FALSE;
    
    $tables = $this->db->list_tables();
    
    $this->config->load('assets',true);
    $medias = array_keys($this->config->get_item(array('assets','assets')));
    foreach ($medias as $key=>$path) {
      $medias[$key] = 'media_'.$path;
    }
    $items  = array_merge($tables,$medias);
    
		$rights = array(
      'all_users' => FALSE,
      'backup'    => FALSE,
      'tools'     => FALSE,
      'items'     => array_combine($items,array_fill(0,count($items), 0 )),
		);
    
    foreach ($groups as $group) {
      $rights['all_users'] = ($rights['all_users'] OR $group['b_all_users']);
      $rights['backup']    = ($rights['backup'] OR $group['b_backup']);
      $rights['tools']     = ($rights['tools'] OR $group['b_tools']);
      $rights['items']     = $this->_combine_item_rights( $rights['items'], $group );
    }
    
		return $rights;
	}
  
  /**
   * Combineer de verschillende rechten per item TODO: dit nog specifieker maken
   *
   * @param array $item_rights 
   * @param array $group 
   * @return array
   * @author Jan den Besten
   */
  private function _combine_item_rights( $item_rights, $group ) {
    foreach ( $item_rights as $item => $rights) {
      $item_type = get_prefix($item).'_*';
      // RIGHTS_ALL?
      if ( $group['rights']==='*' ) {
        $rights = $this->_add_item_right( $rights, $group );
      }
      // Per groep? cfg_*, tbl_*, media_* etc.
      elseif ( strpos($group['rights'],$item_type)!==FALSE ) {
        $rights = $this->_add_item_right( $rights, $group );
      }
      // Specifiek item?
      elseif ( strpos($group['rights'],$item)!==FALSE ) {
        $rights = $this->_add_item_right( $rights, $group );
      }
      // Update
      $item_rights[$item] = $rights;
    }
    return $item_rights; 
  }
  
  private function _add_item_right( $right, $new_right ) {
    if ( $right===FALSE ) $right = RIGHTS_NO;
    if ( $new_right['b_delete'] and ($right + RIGHTS_DELETE) <= RIGHTS_ALL ) $right+=RIGHTS_DELETE;
    if ( $new_right['b_add']    and ($right + RIGHTS_ADD)    <= RIGHTS_ALL ) $right+=RIGHTS_ADD;
    if ( $new_right['b_edit']   and ($right + RIGHTS_EDIT)   <= RIGHTS_ALL ) $right+=RIGHTS_EDIT;
    if ( $new_right['b_show']   and ($right + RIGHTS_SHOW)   <= RIGHTS_ALL ) $right+=RIGHTS_SHOW;
    return $right;
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
	public function has_rights( $item, $id=FALSE, $whatRight=0, $user_id=FALSE) {
    // Ingelogde gebruiker of een andere?
    if ( !$user_id )
      $user_info = $this->get_user();
    else
      $user_info = $this->get_user($user_id);
  
    $rights = el('rights',$user_info);
    if (!$rights) return RIGHTS_NO;

    // Rechten voor aanpassen van zichzelf als:
    // - cfg_users
    // - user_id === id van huidige user
    if ($id==='current') $id = $user_info['user_id'];
    if ($item==$this->tables['users'] and $id==$user_info['user_id'] ) return RIGHTS_EDIT;

    // Anders normale rechten:
    return el( array('items',$item), $rights, RIGHTS_NO );
	}
	

  /**
   * Test of de rows van tabel/media_map gekoppeld zijn aan gebruikers
   *
   * @param string $item
   * @return bool user_id als dat zo is, anders FALSE
   * @author Jan den Besten
   */
	public function restricted_id( $item ) {
		if ( $this->current_user['rights']['all_users'] ) return FALSE; // Rechten voor alle gebruikers, dus niet restricted
		if ( $this->has_rights($item) ) return $this->current_user['id'];
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
		$tables=$this->data->list_tables();
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
    if ($rights===FALSE) return '';
    $string='';
    foreach ($this->_rights_settings as $number => $description) {
      if ( $rights-$number >=0 ) {
        $string = add_string($string,$description,'|');
      }
    }
    return add_string($string,'=','|');;
  }



}
