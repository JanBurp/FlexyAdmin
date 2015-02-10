<?

class auth extends ApiModel {
  
  var $args = array(
    'username'   => '',
    'password'   => '',
    'email'      => ''
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
   * @return void
   * @author Jan den Besten
   */
  public function check() {
    // Give back user session if logged in
    if ($this->loggedIn or $this->user->logged_in()) {
      $data=$this->user->get_user();
      $data=object2array($data);
      $data=array_rename_keys($data,array('str_username'=>'username','email_email'=>'email','last_login'=>'last_login','str_language'=>'language'),false);
      $args=$this->args;
      $args['password']='***';
      $this->result['data']=$data;
      $this->result['_args']=$args;
      return $this->result;
    }
    // if not logged in, status 401
    $this->result['_status']=401;
    return $this->result;
  }
  
  /**
   * Login a user
   *
   * @return void
   * @author Jan den Besten
   */
  public function login() {
    $this->loggedIn = $this->user->login( $this->args['username'], $this->args['password'] );
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
    $this->result['_status']=401;
    return $this->result;
  }
  
  
  /**
   * Send a new password to the given emailadress TODO
   *
   * @return void
   * @author Jan den Besten
   */
  public function send_new_password() {
    $email=$this->args['email'];
    $user=$this->user->get_user_by_email($email);
    $user=object2array($user);
    
    $this->result['data']=$user;
    return $this->result;
  }
  
}
