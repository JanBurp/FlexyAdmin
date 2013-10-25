<?

/**
  * Ajax Example
  *  
	* Voorbeeld van een AJAX module, gebruik deze als basis voor je eigen AJAX modules. Lees ook [Modules maken]({Modules-maken}).
	*
	* @author Jan den Besten
	*/
 
 class Ajax_example extends Ajax_module {

  /**
    * Initialiseer module
    *
    * @author Jan den Besten
    */
  public function __construct() {
    parent::__construct();
  }

  /**
	 * Deze method wordt standaard aangeroepen als er geen method is meegegeven in de AJAX call
	 * Bijvoorbeeld door het jQuery statement:
	 * 
   *    $.post('example');
	 * 
	 * Geef altijd de waarden terug door het aanroepen van:
	 * 
	 *    return $this->result( array() );
	 * 
	 * Met als argument een associatieve array met de waarden die je terug wilt geven.
	 * Er wordt dan een JSON teruggegeven met enkele standaard velden:
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
	 * @param string $args
	 * @return string
	 * @author Jan den Besten
	 */
  public function index($args) {
    return $this->result(array('_message'=>__CLASS__));
  }


  /**
  	* Eventueel andere methods, aan te roepen door (jQuery):
  	* 
    *   $.post('example/other');
  	*
  	* @param string $page 
  	* @return mixed
  	* @author Jan den Besten
  	*/
	public function other($args) {
    return $this->result(array('_method'=>'other','_message'=>__CLASS__));
	}


}

?>