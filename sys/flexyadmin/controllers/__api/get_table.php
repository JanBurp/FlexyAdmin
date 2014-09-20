<?php require_once(APPPATH."core/ApiController.php");

class get_table extends ApiController {
  
  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();
    return $this;
	}
  
  public function index() {
    $this->args=$this->_defaults(array('table'=>'','limit'=>0,'offset'=>0));
    // rights?
    if (!$this->_has_rights($this->args['table'])) {
      return $this->_result(array('_error'=>'NO RIGHTS','_api'=>__CLASS__,'_args'=>$this->args));
    }

    // TODO more queries
    $items = $this->crud->get($this->args);
    
    $data=array(
      'items'=>$items
    );
    
    return $this->_result(array('_api'=>__CLASS__,'_args'=>$this->args,'data'=>$data));
  }

}


?>
