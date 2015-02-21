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
  public function check() {
    if ($this->loggedIn or $this->user->logged_in()) {
      // Give back user session if logged in
      $data=$this->user->get_user();
      $data=object2array($data);
      $data=array_rename_keys($data,array('str_username'=>'username','email_email'=>'email','last_login'=>'last_login','str_language'=>'language'),false);
      $args=$this->args;
      $args['password']='***';
      $this->result['data']=$data;
      $this->result['args']=$args;
      unset($this->result['status']);
    }
    else {
      // if not logged in status = 401
      unset($this->result['success']);
      unset($this->result['args']);
      $this->result['status']=401;
      unset($this->result['data']);
    }
    return $this->result;
  }
  
  /**
   * Login a user
   *
   * @return void
   * @author Jan den Besten
   */
  public function login() {
    if ($this->user->login( $this->args['username'], $this->args['password'] )) {
      $this->result['success']=true;
    };
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
