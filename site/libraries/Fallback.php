<?php 
/**
	* Als de controller een gevraagde module niet kan vinden, dan wordt standaard deze module geladen
	*
	* Je zou hier bijvoorbeeld modules uit de database kunnen laden.
	*
	* @author Jan den Besten
	*/
class Fallback extends Module {
  
  /**
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
		echo('Fallback Module: '.strtoupper($this->name));
		return false;
	}

}

?>