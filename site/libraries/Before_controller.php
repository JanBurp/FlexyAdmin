<?php 

/**
 * Speciale module die voor de controller wordt aangeroepen.
 * Hier kun je dus zaken doen die altijd moeten worden gedaan.
 * Let op: het menu en de huidige pagina zijn nog niet bekend!
 *
 * @author Jan den Besten
 */
class Before_controller extends Module {

  public function __construct() {
    parent::__construct();
  }

}

?>