<?php require_once(APPPATH."core/ApiController.php");

class get_table extends ApiController {
  
  var $args = array(
    'table'   => '',
    'limit'   => 0,
    'offset'  => 0
  );


	public function __construct($name='') {
		parent::__construct();
	}
  

  public function index() {

    // TODO more queries
    $items = $this->crud->get($this->args);
    
    $data=array(
      'items'=>$items
    );
    
    return $this->_result(array('data'=>$data));
  }

}


?>
