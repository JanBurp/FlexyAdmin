<?php require_once(APPPATH."core/AjaxController.php");

class ApiController extends AjaxController {
  
  protected $args=array();
  protected $check_rights=true;
  protected $table=NULL;
  
  protected $loggedIn=false;
  
  
  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();

    // Get arguments
    $this->args=$this->_get_args($this->args);
    
    // Check Authentication and Rights if not api/auth
    $auth=($this->uri->get(2)=='auth');
    $this->loggedIn=$this->_user_logged_in();
    if (!$auth) {
      if (!$this->loggedIn) {
        return $this->_result(array('_status'=>401));
      }
      if (isset($this->args['table'])) $this->table=$this->args['table'];
      if ($this->check_rights) {
        if (!$this->_has_rights($this->table)) {
          return $this->_result(array('_error'=>'NO RIGHTS'));
        }
      }
    }

    // Standard result
    $this->result['_args']=$this->args;
    $this->result['_api']=$this->name;
    return $this;
	}
  
  
  /**
   * Get arguments from GET or POST
   *
   * @param string $defaults 
   * @return void
   * @author Jan den Besten
   */
  private function _get_args($defaults) {
    $keys=array_keys($defaults);
    $args=array();
    
    // or post
    if (!$args and !empty($_POST)) {
      $type='post';
      foreach ($keys as $key) {
        $value=$this->input->post($key);
        if (isset($value)) $args[$key]=$value;
      }
    }
    
    // or get
    if (!$args and !empty($_SERVER['QUERY_STRING'])) {
      $type='get';
      parse_str($_SERVER['QUERY_STRING'],$_GET);
      foreach ($keys as $key) {
        $value=$this->input->get($key);
        if (isset($value)) $args[$key]=$value;
      }
    }
    
    // or defaults
    if (!$args) {
      $type="none";
      $args=$defaults;
    }
    
    return $args;
  }
  
  protected function _has_rights($item,$id="",$whatRight=0) {
    return $this->user->has_rights($item,$id,$whatRight);
  }
  
  
  


}

?>