<?
/**
 * For testing frontend ajax calls
 *
 * @author Jan den Besten
 */

class Ajax extends CI_Controller {
	
	public function __construct()	{
		parent::__construct();
    $this->load->library('parent_module_plugin');
    $this->load->library('ajax_module');
    $this->load->helper('html');
	}

  public function index() {
    $args=func_get_args();
    $library=array_shift($args);
    if ($library and file_exists(SITEPATH.'libraries/Ajax_'.$library.'.php')) {
      $ajax_library='ajax_'.$library;
      $this->load->library($ajax_library);
      $method=array_shift($args);
      if (empty($method)) $method='index';
  
      // url query becomes post data
      parse_str($_SERVER['QUERY_STRING'],$_POST);
      
      // call the ajax library
      $json  = $this->$ajax_library->$method($args);
      $array = json2array($json);
      
      $out=h('Test Frontend Ajax Module: '.$library.'->'.$method.'()');
      $out.='<h2>JSON result:</h2><code>'.htmlentities($json).'</code>';
      $out.='<h2>As a trace</h2><pre>'.trace_($array,false).'</pre>';
      
      echo $out;
    }
    else {
      $out=h('Test Frontend Ajax Module: _ajax/...');
      echo $out;
    }
  }

}

?>