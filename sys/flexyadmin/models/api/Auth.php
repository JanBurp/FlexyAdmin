<?php

/** \ingroup models
 * API auth. Hiermee kan worden ingelogd of uitgelogd.
 * 
 * Bij login wordt een auth_token terugegeven die gebruikt moet worden in cross-domain apis in de authentication header.
 * Deze auth_token is een dag geldig.
 * 
 * 
 * ###_api/auth
 * 
 * Parameters: GEEN
 * 
 * Response data:
 * 
 * - `HTTP/1.1 401 Unauthorized` header als niet is ingelogd.
 * - Een array met een aantal gegevens van de gebruiker (zie hieronder).
 * 
 * Voorbeeld:
 * 
 * - `_api/auth`
 * 
 * 
 * ###_api/auth/login
 * 
 * POST Parameters:
 * 
 * - username - De gebruikersnaam van het profiel
 * - password - Het wachtwoord van het profiel
 * 
 * Response data:
 * 
 * - `HTTP/1.1 401 Unauthorized` header als niet is ingelogd
 * - Een array met een aantal gegevens van de gebruiker (zie hieronder).
 * 
 * Voorbeeld:
 * 
 * - `_api/auth/login`
 * - Waarbij de POST data er zo uitziet: `username=profielnaam&password=profielwachtwoord`
 * 
 * 
 * ###_api/auth/logout
 * 
 * Parameters: GEEN
 * 
 * Response data:
 * 
 * - `HTTP/1.1 401 Unauthorized` header.
 * 
 * Voorbeeld:
 * 
 * - `_api/auth/logout`
 * 
 * ###Voorbeeld response (dump) met uitleg:
 * 
 *      [success] => TRUE
 *      [api] => 'auth'
 *      [args] => (
 *        [type] => 'GET'
 *       )
 *      [user] => (
 *        [username] => 'admin'                    // Gebruikersnaam
 *        [group_name] => 'Administrator'          // Groepsnaam
 *        [group_id] => '1'                        // Groeps id
 *      )
 *      [data] => (
 *        [username] => 'admin'                     // Gebruikersnaam
 *        [email] => 'info@flexyadmin.com'          // Emailadres van gebruiker
 *        [language] => 'nl'                        // Taal van de gebruiker
 *        [auth_token] => 'xxxx'                    // Auth token die gebruikt moet worden in de Authorization header bij volgende api aanroepen. Is een dag geldig. Daarna moet een nieuwe worden opgevraagd door in te loggen.
 *       )
 * 
 * @author Jan den Besten
 */
class auth extends Api_Model {
  
  /**
   * Auth mag van alle domeinen, als inlog goed lukt dan wordt dat beperkt tot domein waar vraag vandaan kwam.
   */
  protected $cors = "*";

  
  var $needs = array(
    // 'username'   => '',
    // 'password'   => '',
  );
  
	public function __construct() {
    $checkAuthenticatonHeader = $this->uri->get(3)==='check';
    parent::__construct( !$checkAuthenticatonHeader );
	}
  
  public function index() {
    return $this->check();
  }
  
  /**
   * Checks if a user is logged in
   * 
   * If logged in returns:
   * 
   * array(
   *  'success' => true
   *  'args'    => array with passed arguments
   *  'data'    => array with userdata
   *  'token'   => JWT token for authentication
   * )
   * 
   * If not:
   * 
   * array(
   *  'status' => 401
   * )
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _check() {
    // if not logged in status = 401
    $logged_in = $this->logged_in();
    if ( !$logged_in ) return null;
    
    // Give back user info
    $data = $this->flexy_auth->get_user();
    if (el('user_id',$data)) {
      $data = array_rename_keys($data,array('str_username'=>'username','email_email'=>'email','str_language'=>'language','auth_token'=>'token'),false);
    }
    
    return $data;
  }
  
  /**
   * Check if user is logged in, and if so returns user info and auth token
   *
   * @return void
   * @author Jan den Besten
   */
  public function check() {
    $data = $this->_check();
    if ($data===null) return $this->_result_status401();
    $this->result['data']=$data;
    return $this->_result_ok();
  }
  
  
  /**
   * Login a user
   *
   * @return void
   * @author Jan den Besten
   */
  public function login() {
    // First logout if there is a login
    if ( $this->flexy_auth->logged_in() ) $this->flexy_auth->logout();
    
    // Has POST args?
    if ($this->args['type']!='POST' or !isset($this->args['username']) or !isset($this->args['password']) ) {
      return $this->_result_wrong_args();
    }

    $logged_in = $this->flexy_auth->login( $this->args['username'], $this->args['password'] );
    return $this->check();
  }
  

  /**
   * Logout
   *
   * @return void
   * @author Jan den Besten
   */
  public function logout() {
    $this->flexy_auth->logout();
    return null;
  }
  
  
  /**
   * Send a new password to the given emailadress
   *
   * @return mixed
   * @author Jan den Besten
   */
  public function send_new_password() {
    $email = $this->args['email'];
    $user  = $this->flexy_auth->get_user_by_email($email);
    // No user found
    if (!$user) {
      $this->result['data']=FALSE;
      $this->_set_error('NO USER FOUND');
    }
    else {
      // User found
      $send=$this->flexy_auth->send_new_password( $user );
      // Error when sending
      if (!$send) {
        $this->result['data']=FALSE;
        $this->_set_error('COULD NOT SEND EMAIL');
      }
      else {
        $data=array(
          'username' => $user['username'],
          'email'    => $user['email_email']
        );
        $this->result['data']=$data;
      }
    }
    return $this->_result_ok();
  }
  
}
