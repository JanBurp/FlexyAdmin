<?php 
/** \ingroup libraries
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
 * @author Jan den Besten
 */

class Ajax_module extends Parent_module_plugin {

  /**
   */
	function __construct($name='') {
		parent::__construct($name);
	}

  /**
   * if method can't be found, print a simple warning
   *
   * @param string $function 
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @internal
   */
	public function __call($function, $args) {
		return $this->result(array('error'=>'Method: `'.ucfirst($function)."()` doesn't exists.", 'success'=>false));
	}

	/**
	 * Deze method wordt standaard aangeroepen als er geen method is meegegeven in de AJAX call
	 *
	 * @param string $args
	 * @return string
	 * @author Jan den Besten
	 */
  public function index($args) {
    return $this->result(array('message'=>__CLASS__));
  }

  /**
   * Deze method geeft een JSON terug van de meegegeven array.
   * Gebruik altijd deze method om een gestandardiseerde JSON terug te geven aan de AJAX call.
   *
   * @param array $args an associatieve array
   * @return string JSON
   * @author Jan den Besten
   */
  public function result($args) {
    $args['module']=str_replace('ajax_','',$this->name);
    if (!isset($args['success'])) $args['success']=true;
    ksort($args);
    return array2json($args);
    // return json_encode($args)
  }
  

}

?>