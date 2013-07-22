<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."libraries/ion_auth.php");


/**
 * Class voor het inloggen, aanmaken etc van gebruikers
 * 
 * Is een uitbreiding op [Ion Auth]({Ion_auth}), dus kijk ook zeker daar voor te gebruiken methods!
 * In de map userguide/ionAuth vindt je ook een handleiding.
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
 * @package default
 * @author Jan den Besten
 */
 
class User Extends Ion_auth {
	
  /**
   * Rechten van huidige gebruiker
   *
   * @var array
   */
	private $rights;

  /**
   * id van huidige gebruiker
   *
   * @var int
   */
	private $user_id;
  
  /**
   * Array van tbl_site
   *
   * @var array
   */
	private $siteInfo;
  
	
  /**
    * @ignore
    */
  public function __construct($tables='') {
		parent::__construct($tables);
		// set standard configurations
		$this->CI->db->select('url_url,str_title,email_email');
		$this->siteInfo = $this->CI->db->get_row('tbl_site');
		$this->CI->config->set_item('site_title', $this->siteInfo['str_title'],'ion_auth');
		$this->CI->config->set_item('admin_email', $this->siteInfo['email_email'],'ion_auth');
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
    // trace_(array('identity'=>$identity,'password'=>$password));
		if ( ! $this->_check_if_userdate_ok()) {
			$this->set_message('update_needed');
		}
		else {
			if ( $this->_check_if_old_password($identity) ) {
        $this->_update_old_passwords();
			}
			if ($this->CI->ion_auth_model->login($identity, $password, $remember)) {
				return TRUE;
			}
		}
		$this->set_error('login_unsuccessful');
		return FALSE;
	}
	
  /**
   * _check_if_userdate_ok()
   *
   * @return bool
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	private function _check_if_userdate_ok() {
		return ($this->CI->db->field_exists('str_username',$this->tables['users']) and $this->CI->db->field_exists('gpw_password',$this->tables['users']) );
	}
	
  /**
   * Check of systeem nog gebruik maakt van oude passwords, nodig voor updaten naar r1280
   *
   * @param string $identity 
   * @return bool
   * @author Jan den Besten
   * @internal
   * @ignore
   * @depricated
   */
	private function _check_if_old_password($identity) {
		$new_password = TRUE;
    if ($this->tables['users']=='cfg_users' and $this->tables['groups']=='cfg_user_groups') {
      $new_password = FALSE;
  		// check if password field length = 40 and the password itself is 32 chars or longer long, that should do it
  		$field_data=$this->CI->db->field_data($this->tables['users']);
  		foreach ($field_data as $field_info) {
  			$field_info=object2array($field_info);
  			if ($field_info['name']=='gpw_password') {
  				$password = $this->CI->db->get_field('cfg_users','gpw_password');
  				$new_password = ( $password and $field_info['max_length']==40 and strlen($password)>=32 );
  			}
  		}
    }
		return ! $new_password;
	}
	
  /**
   * Update oude passwords naar nieuwe, nodig voor updaten naar r1280
   *
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   * @depricated
   */
	private function _update_old_passwords() {
		// update all users:
		$this->CI->db->select('id,gpw_password,id_user_group');
		$users=$this->CI->db->get_results('cfg_users');
		foreach ($users as $id => $user) {
			$set=array();
			// hash password, and get usergroup
			$set['gpw_password']=$this->CI->ion_auth_model->hash_password($user['gpw_password']);
			$set['id_user_group']=$this->CI->db->get_field_where('rel_users__rights','id_rights','id_users',$id);
			// update
			$this->CI->db->set($set);
			$this->CI->db->where('id',$id);
			$this->CI->db->update('cfg_users');
		}
		// remove 'rel_users__rights'
		$this->CI->load->dbforge();
		$this->CI->dbforge->drop_table('rel_users__rights');
		// set a message
		$this->set_message('update_to_safe_passwords');
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
			$this->rights = $this->create_rights( $this->user_id );
		}
		return (bool) $logged_in;
	}
	
	
	/**
	 * Verzorgt het proces als een gebruiker het paswoord is vergeten
	 * 
	 * Verstuurt een mail naar gebruiker met link voor nieuw wachtwoord
	 *
	 * @param string $email Emailadres van gebruiker
	 * @param string $uri URI van pagina waar gebruiker naartoe wordt geleid door de email
	 * @param string $subject['Forgotten Password Verification'] Onderwerp van de te sturen email 
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
	 * Rond het proces van vergeten wachtwoord af
	 *
	 * @param string $code Code die gebruiker heeft gekregen
	 * @param string $subject['New Password']
	 * @return bool TRUE als geslaagd
	 * @author Jan den Besten
	 */
	public function forgotten_password_complete($code,$subject='New Password') {
		$identity = $this->CI->config->item('identity', 'ion_auth');
		$profile  = $this->CI->ion_auth_model->profile($code, true);
		if (!is_object($profile))	{
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}
		$new_password = $this->CI->ion_auth_model->forgotten_password_complete($code, $profile->str_salt);
		if ($new_password) {
			$data = array(
				'identity'     => $profile->{$identity},
				'new_password' => $new_password
			);
			$message = $this->CI->load->view($this->CI->config->item('email_templates', 'ion_auth').$this->CI->config->item('email_forgot_password_complete', 'ion_auth'), $data, true);

			$this->CI->email->clear();
			$config['mailtype'] = $this->CI->config->item('email_type', 'ion_auth');
			$this->CI->email->initialize($config);
			$this->CI->email->from($this->CI->config->item('admin_email', 'ion_auth'), $this->CI->config->item('site_title', 'ion_auth'));
			$this->CI->email->to($profile->email_email);
			$this->CI->email->subject($this->CI->config->item('site_title', 'ion_auth') . ' - '.$subject);
			$this->CI->email->message($message);

			if ($this->CI->email->send())	{
				$this->set_message('password_change_successful');
				return TRUE;
			}
			else {
				$this->set_error('password_change_unsuccessful');
				return FALSE;
			}
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
	 * @return bool TRUE als geslaagd
	 * @author Jan den Besten
	 */
	public function register($username, $password, $email, $additional_data=array(), $group_name = false, $subject='Account Activation', $uri='') {
		if (empty($uri)) $uri=$this->CI->uri->get();
		$email_activation = $this->CI->config->item('email_activation', 'ion_auth');
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
   * Stuur een mail waarmee nieuw geregistreede gebruiker zichzelf kan activeren
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
			'uri'					=> $uri,
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
   * @param string $subject 
   * @return void
   * @author Jan den Besten
   */
	public function send_accepted_mail($id,$subject='Account accepted and activated') {
		return $this->send_mail($id,'email_accepted',$subject);
	}

  /**
   * Stuur gebruiker mail dat account is aangemaakt, met (nieuwe) inloggegevens
   *
   * @param string $id 
   * @param string $subject
   * @param array $data Array met extra gegevens die in de mail worden gestuurd (inloggegevens bv)
   * @return void
   * @author Jan den Besten
   */
	public function send_new_account_mail($id,$subject='New account') {
		$user  = $this->CI->ion_auth_model->get_user($id)->row();
		$email = $user->email_email;
    $code=$this->CI->ion_auth_model->forgotten_password($email);
    $password=$this->CI->ion_auth_model->forgotten_password_complete($code);
    // trace_(array('code'=>$code,'password'=>$password));
		return $this->send_mail($id,'email_new_account',$subject,array('password'=>$password));
	}


  /**
   * Stuur gebruiker mail dat registratie niet is toegestaan
   *
   * @param string $id 
   * @param string $subject 
   * @return void
   * @author Jan den Besten
   */
	public function send_deny_mail($id,$subject='Account denied') {
		return $this->send_mail($id,'email_deny',$subject);
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

		$message = $this->CI->load->view($this->CI->config->item('email_templates', 'ion_auth').$this->CI->config->item($template, 'ion_auth'), $data, true);

		$this->CI->email->clear();
		$config['mailtype'] = $this->CI->config->item('email_type', 'ion_auth');
		$this->CI->email->initialize($config);
		$this->CI->email->from($this->CI->config->item('admin_email', 'ion_auth'), $this->CI->config->item('site_title', 'ion_auth'));
		$this->CI->email->to($email);
		$this->CI->email->subject($this->CI->config->item('site_title', 'ion_auth') . ' - '.$subject);
		$this->CI->email->message($message);

		if ($this->CI->email->send() == TRUE)	{
			return $id;
		}
		$this->set_error('activation_email_unsuccessful');
		return FALSE;
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
   * Test of huidige gebruiker genoeg rechten heeft om admin tools te mogen gebruiken (search/replace, bulk_upload, Automatisch vullen)
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
   * @ignore
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
   * @ignore
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
   * @param string $id[0] Alleen nodig als rows/bestanden aan gebruikers gekoppeld zijn, hiermee kan dat getest worden
   * @param string $whatRight[0] Eventueel checken op welke rechten getest wordt
   * @return int Rechten zie boven
   * @author Jan den Besten
   */
	public function has_rights($item,$id="",$whatRight=0) {
		// No rights if cfg_users and id is smaller (higher rights)
		if ($item==$this->tables['users'] and !empty($id) and ($id!=-1) and ($id<$this->user_id)) return false;
		
		$found=array('b_delete'=>FALSE,'b_add'=>FALSE,'b_edit'=>FALSE,'b_show'=>FALSE);
		$pre=get_prefix($item);
		$preAll=$pre."_*";

		$foundRights=RIGHTS_NO;
		
		// $condition=($item=='media_knipsels');
		// trace_if($condition,$item);
		// trace_if($condition,$this->rights);
		// trace_if($condition,array('item'=>$item,'pre'=>$pre,'preAll'=>$preAll));

		$rights=$this->rights;
		if ($rights['rights']=="*" or (strpos($rights['rights'],$preAll)!==FALSE) or (strpos($rights['rights'],$item)!==FALSE) ) {
			$this->_change_rights($found,$rights);
		}
		// trace_if($condition,$found);
		if (!empty($found['b_delete'])	and $found['b_delete'])	$foundRights+=RIGHTS_DELETE;
		if (!empty($found['b_add']) 		and $found['b_add'])		$foundRights+=RIGHTS_ADD;
		if (!empty($found['b_edit'])		and $found['b_edit'])		$foundRights+=RIGHTS_EDIT;
		if (!empty($found['b_show'])		and $found['b_show'])		$foundRights+=RIGHTS_SHOW;

		// trace_if($condition,$foundRights);
		// trace_if($condition,$whatRight);

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
   * @param string $atLeast[RIGTS_ALL] Minimal rechten die een tabel moet hebben om in her resultaat te komen 
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
	 * @param string $group_name[FALSE]
	 * @param int $time[1209600] tijd geleden in sec (dag=86400, week=604800, 2 weken=1209600, 4 weken=2419200)
	 * @return object met alle inactieve gebruikers
	 * @author Jan den Besten
	 **/
	public function get_inactive_old_users($group_name = false, $time=1209600) {
		return $this->CI->ion_auth_model->get_inactive_old_users($group_name,$time)->result();
	}



}
