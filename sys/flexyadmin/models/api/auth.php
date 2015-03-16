<?


/**
 * Authentication API
 * 
 * - _api/auth/check              - gives as a result if a user is logged in, if so, returns userdata
 * - _api/auth/login              - needs username/password
 * - _api/auth/logout             - needs username/password
 * - _api/auth/send_new_password  - needs email
 *
 * @package default
 * @author Jan den Besten
 */

class auth extends ApiModel {
  
  var $needs = array(
    // 'username'   => '',
    // 'password'   => '',
  );
  
	public function __construct() {
		parent::__construct();
    $this->load->library('user');
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
   *  'args' => array with passed arguments
   *  'data' => array with userdata
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
    if (!$this->logged_in()) return null;
    // Give back user session if logged in
    $data=$this->user->get_user();
    $data=object2array($data);
    $data=array_rename_keys($data,array('str_username'=>'username','email_email'=>'email','last_login'=>'last_login','str_language'=>'language'),false);
    return $data;
  }
  public function check() {
    $data=$this->_check();
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
    // Has POST args?
    if ($this->args['type']!='POST' or !isset($this->args['username']) or !isset($this->args['password']) ) {
      return $this->_result_wrong_args();
    }
    
    $this->user->login( $this->args['username'], $this->args['password'] );
    return $this->check();
  }
  

  /**
   * Logout
   *
   * @return void
   * @author Jan den Besten
   */
  public function logout() {
    $this->user->logout();
    return $this->check();
  }
  
  
  /**
   * Send a new password to the given emailadress
   *
   * @return mixed
   * @author Jan den Besten
   */
  public function send_new_password() {
    $email=$this->args['email'];
    $user=$this->user->get_user_by_email($email);
    // No user found
    if (!$user) {
      $this->result['data']=FALSE;
      $this->_set_error('NO USER FOUND');
    }
    else {
      // User found
      $send=$this->user->send_new_password_mail($user->id);
      // Error when sending
      if (!$send) {
        $this->result['data']=FALSE;
        $this->_set_error('COULD NOT SEND EMAIL');
      }
      else {
        $data=array(
          'username' => $user->str_username,
          'email'    => $user->email_email
        );
        $this->result['data']=$data;
      }
    }
    return $this->_result_ok();
  }
  
}
