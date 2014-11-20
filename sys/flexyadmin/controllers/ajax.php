<?php require_once(APPPATH."core/FrontendController.php");

/**
 * For testing frontend ajax calls
 *
 * @package default
 * @author Jan den Besten
 */

class Ajax extends FrontendController {
	
	public function __construct()	{
		parent::__construct();
    $this->load->library('ajax_module');
	}

  public function index() {
    $args=func_get_args();
    $library=array_shift($args);
    if ($library and file_exists(SITEPATH.'libraries/ajax_'.$library.'.php')) {
      $ajax_library='ajax_'.$library;
      $this->load->library($ajax_library);
      $method=array_shift($args);
      if (empty($method)) $method='index';
      
      // call the ajax library
      $result = $this->$ajax_library->$method();
      
      $out=h('Test Frontend Ajax Module: '.$library.'->'.$method.'()');
      $out.=trace_($result,false);
      
      echo $out;
    }
  }

}

?>