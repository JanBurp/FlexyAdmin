<?php require_once(APPPATH."core/ApiController.php");

/**
 * API Controller
 *
 * @package default
 * @author Jan den Besten
 */

class Api extends ApiController {
	
	public function __construct()	{
		parent::__construct();
	}
  
  public function index() {
    $args=func_get_args();
    $model=array_shift($args);
    
    // does api model exists?
    if (file_exists(APPPATH.'/models/api/'.$model.'.php')) {
      // Load Model
      $this->load->model('api/'.$model);
      // Call model
      $result=$this->$model->index($args);
      // Result
      $result['_api']=$model;
      $result['_args']=$args;
      return $this->_result( $result );
    }
    else {
      // does not exists: just return nothing (empty page)
      return $this->_result(
        array(  '_api'=>$model,
                '_error'=>'API Model: `'.ucfirst($model)."()` doesn't exists.",
                '_args'=>$this->args)
              );
    }
    
  }
  

}

?>