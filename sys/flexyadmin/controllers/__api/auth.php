<?php require_once(APPPATH."core/ApiController.php");

class auth extends ApiController {
  
  var $args = array(
    'username'   => '',
    'password'   => ''
  );
  
	public function __construct() {
		parent::__construct();
    $this->load->library('user');
	}
  

  /**
   * Login, or returns session
   *
   * @return void
   * @author Jan den Besten
   */
  public function index() {
    // logout?
    if (isset($this->args['logout'])) {
      $this->user->logout();
      return $this->_result(array('_status'=>401));
    }
    
    // If username and password given, try to login
    if (!empty($this->args['username']) and !empty($this->args['password'])) {
      $this->loggedIn = $this->user->login( $this->args['username'], $this->args['password'] );
    }
    
    // Give back user session if logged in
    if ($this->loggedIn or $this->user->logged_in()) {
      $data=$this->user->get_user();
      $data=object2array($data);
      $data=array_rename_keys($data,array('str_username'=>'username','email_email'=>'email','last_login'=>'last_login','str_language'=>'language'),false);
      return $this->_result(array('data'=>$data,'_args'=>'***'));
    }

    // if not logged in, status 401
    return $this->_result(array('_status'=>401));
  }
  
}
