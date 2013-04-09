<?

/**
	* Als de controller een gevraagde module niet kan vinden, dan wordt standaard deze module geladen
	*
	* Je zou hier bijvoorbeeld modules uit de database kunnen laden.
	*
	* @package default
	* @author Jan den Besten
	*/
class Fallback extends Module {
  
  /**
   * @ignore
   */
  public function __construct() {
    parent::__construct();
  }
  

  /**
  	* De fallback module geeft standaard de naam van de module weer, pas aan naar wens
  	*
  	* @param string $page 
  	* @return void
  	* @author Jan den Besten
  	*/
	public function index($page) {
		trace_('Fallback Module: '.strtoupper($this->name));
		return false;
	}

}

?>