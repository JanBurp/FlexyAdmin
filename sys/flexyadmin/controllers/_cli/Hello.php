<?php

/**
 * CLI testje
 *
 * @package default
 * @author Jan den Besten
 */
class Hello extends CI_Controller {

  public function index($to = 'World')  {
    echo "Hello {$to}!".PHP_EOL;
  }
}




?>