<?php require_once(APPPATH."core/AjaxController.php");

/**
 * API Controller
 *
 * @package default
 * @author Jan den Besten
 */

class Api extends AjaxController {
	
	public function __construct()	{
		parent::__construct();
    $this->load->model('api/api_model');
	}
  
  public function index() {
    $args=func_get_args();
    $model=array_shift($args);
    $method=array_shift($args);
    if (!$method) $method='index';
    
    // does api model exists?
    if (strtolower($model)!='apimodel') {

      if (file_exists(APPPATH.'/models/api/'.$model.'.php') or file_exists(SITEPATH.'/models/api/'.$model.'.php')) {
       // Load Model
       $this->load->model('api/'.$model);
       // Call model/method
       $result=$this->$model->$method($args);
       // Result
       $result['api']=$model;
       return $this->_result( $result );
     }
      
    }
    
    // does not exists: just return nothing (empty page)
    return $this->_result( array( 'api'=>$model, 'error'=>'`_api/'.$model."` doesn't exists." ) );
  }


}

?>