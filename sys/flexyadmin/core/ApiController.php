<?php require_once(APPPATH."core/AjaxController.php");

class ApiController extends AjaxController {
  
  protected $args=array();
  protected $check_rights=true;
  protected $table=NULL;
  protected $type='trace';
  
  
  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();
    // Get arguments
    $this->args=$this->_get_args($this->args);
    // Check rights
    if (isset($this->args['table'])) $this->table=$this->args['table'];
    if ($this->check_rights) {
      if (!$this->_has_rights($this->table)) {
        unset($this->result['_args_type']);
        $this->_result(array('_error'=>'NO RIGHTS'));
        die();
      }
    }
    // Output type
    if (isset($this->args['_type'])) $this->type=$this->args['_type'];
    if ($this->type!='json') $this->_test(true);
    // Standard result
    $this->result['_result_type']=$this->type;
    $this->result['_args']=$this->args;
    $this->result['_api']=$this->name;
    return $this;
	}
  
  private function _get_args($defaults) {
    $defaults['_type']=$this->type;
    $keys=array_keys($defaults);
    $args=array();
    
    // uri
    $type='uri';
    $args=$this->uri->uri_to_assoc(3);
    if ($args) $args=array_merge($defaults,$args);
    
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
    
    $this->result['_args_type']=$type;
    return $args;
  }
  
  protected function _has_rights($item,$id="",$whatRight=0) {
    return $this->user->has_rights($item,$id,$whatRight);
  }
  
  
  


}

?>