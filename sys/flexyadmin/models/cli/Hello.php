<?php

/**
 * CLI testje
 *
 * @package default
 * @author Jan den Besten
 */
class Hello extends CI_Model {

  public function index()  {
    $args = func_get_args();
    $message = (string) el(0,$args,'World');
    echo "Hello $message!".PHP_EOL;
  }
  
  public function help()  {
    return "== `hello` echos a 'Hello World!' message, where 'World' can be a given string ==".PHP_EOL."hello".PHP_EOL."hello <string>".PHP_EOL;
  }
  
}




?>