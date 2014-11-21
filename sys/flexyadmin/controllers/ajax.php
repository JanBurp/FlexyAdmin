<?
/**
 * For testing frontend ajax calls
 *
 * @package default
 * @author Jan den Besten
 */

class Ajax extends CI_Controller {
	
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
  
      // url query becomes post data
      parse_str($_SERVER['QUERY_STRING'],$_POST);
      
      // call the ajax library
      $json  = $this->$ajax_library->$method();
      $array = json2array($json);
      
      $out=h('Test Frontend Ajax Module: '.$library.'->'.$method.'()');
      $out.='<h2>JSON result:</h2><code>'.$json.'</code>';
      $out.='<h2>As a trace</h2><pre>'.print_ar($array,true).'</pre>';
      
      echo $out;
    }
  }

}

?>