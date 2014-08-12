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
	protected $name='';
  
  /**
   * Testmodes
   *
   * @var bool
   */
  private $test = false;
  

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
		return $this->_result(array('_error'=>'Method: `'.ucfirst($function)."()` doesn't exists."));
	}
  
  /**
   * Sets testmode, output is not echod
   *
   * @param string $test[true]
   * @return object $this
   * @author Jan den Besten
   */
  public function _test($test=true) {
    $this->test=$test;
    return $this;
  }


  /**
   * Deze method geeft een JSON terug van de meegegeven array.
   * Gebruik altijd deze method om een gestandardiseerde JSON terug te geven aan de AJAX call.
   *
   * @param array $args an associatieve array
   * @return string JSON
   * @author Jan den Besten
   */
  protected function _result($args) {
    $args['_success']=true;
    if (isset($args['_error']) and !empty($args['_error'])) $args['_success']=false;
    ksort($args);
    if ($this->test) return $args;
    $json=array2json($args);
    return $json;
  }
  

}

?>