<?php require_once(APPPATH."core/AjaxController.php");

class ApiController extends AjaxController {
  
  protected $args=NULL;
  
  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();
    $this->args=$this->uri->uri_to_assoc(3);
    // if (!$this->args) $this->args=$_POST;
    if (!$this->args) parse_str($_SERVER['QUERY_STRING'],$this->args);
    if (!el('_ajax',$this->args,false)) $this->_test(true);
    return $this;
	}
  
  protected function _defaults($defaults) {
    $args=array();
    $keys=array_keys($defaults);
    foreach ($keys as $key) {
      if (isset($this->args[$key]))
        $args[$key]=$this->args[$key];
      else
        $args[$key]=$defaults[$key];
    }
    return $args;
  }
  
  protected function _has_rights($item,$id="",$whatRight=0) {
    return $this->user->has_rights($item,$id,$whatRight);
  }


}

?>