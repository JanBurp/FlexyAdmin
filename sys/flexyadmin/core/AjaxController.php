<?php require_once(APPPATH."core/BasicController.php");


/**
 * Basis class voor alle frontend ajax modules. Zo begint je eigen module dus:
 *
 *      class Mijn_ajax_module extends Ajax_module
 * 
 * Standaard wordt een JSON object als resultaat teruggegeven met enkele standaard velden:
 * 
 * - _module: de naam van de aangeroepen ajax module
 * - _success: true/false
 * - _message: eventueel deze standaard waarde waarin een tekstuele melding teruggegeven kan worden
 * - _error: idem maar dan voor foutmeldingen
 * 
 * Het is goed gebruik om aan het eind van elke method ook nog de eigen naam terug te geven met:
 * 
 * - _method
 *
 * @package default
 * @author Jan den Besten
 */

class AjaxController extends BasicController {
  
  /**
   * Naam van deze Ajax Controller
   *
   * @var string
   */
	protected $name=__CLASS__;
  
  /**
   * Output
   *
   * @var array
   */
  protected $result=array();
  
  /**
   * Testmodes
   *
   * @var bool
   */
  private $test = false;
  
  /**
   * Set type of return [''|'json']
   *
   * @var string
   */
  private $format = '';
  

  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();
		if (empty($name) or is_array($name)) $name=strtolower(get_class($this));
    $this->name=$name;
    return $this;
	}

  /**
   * if method can't be found, print a simple warning
   *
   * @param string $function 
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @internal
   * @ignore
   */
	public function __call($function, $args) {
		return $this->_result(array('error'=>'Method: `'.ucfirst($function)."()` doesn't exists."));
	}
  
  /**
   * Set type
   *
   * @param string $type[''] or 'json' 
   * @return this
   * @author Jan den Besten
   */
  public function set_format($format='') {
    $this->format=$format;
    return $this;
  }
  
  /**
   * Deze method geeft een JSON terug van de meegegeven array.
   * Gebruik altijd deze method om een gestandardiseerde JSON terug te geven aan de AJAX call.
   * Als het geen AJAX request is wordt een trace van het resultaat gegegeven.
   *
   * @param array $args an associatieve array
   * @return string JSON
   * @author Jan den Besten
   */
  protected function _result($result) {
    $status = false;
    
    // status = 401 Unauthorized ?
    if ( isset($result['status']) and $result['status']==401) {
      $status="HTTP/1.1 401 Unauthorized";
      $result['status']=401;
      $result['error']=$status;
    }
    
    // Check result, and order it
    if (!$status) {
      $result=array_merge($this->result,$result);
      $result['success']=true;
      if (isset($result['error']) and !empty($result['error'])) $result['success']=false;
      ksort($result);
    }
    
    // If a ajax message, add to result
    if ( isset($this->message)) {
      $message=$this->message->get_ajax();
      if ($message) {
        $first=$message;
        if (is_array($first)) $first=current($first);
        if (has_string('TRACE',$first)) {
          $result['trace']=$message;
        }
        else {
          $result['message']=$message;          
        }
      }
      $this->message->reset_ajax();
    }

    // Test output - if not AJAX
    if ( ! $this->input->is_ajax_request() ) {
      $result['test']=true;
    }

    // 401 output
    if ( $status and !el('test',$result,false))  {
      header($status);
      exit;
    }
    
    // Order of result
    $result=$this->_sort_result($result);
    
    // Output format
    switch (el('format',$result,'default')) {

      case 'xml':
        $output = array2xml($result);
        break;

      case 'json':
        $output = array2json($result);
        break;

      case 'php':
        $output = array2php($result);
        break;
        
      case 'dump':
        $output = trace_($result,false);
        break;

      default:
        if (isset($result['test']) and $result['test']) {
          $result['format']='dump';
          $result=$this->_sort_result($result);
          $output = trace_($result,false);
        }
        else {
          $result['format']='json';
          $result=$this->_sort_result($result);
          $output = array2json($result);
        }
        break;
    }
    echo $output;
    return $output;
  }
  
  
  private function _sort_result($result) {
    return sort_keys($result,array('status','success','test','error','message','format','api','args','data','config'));
  }
  

}

?>