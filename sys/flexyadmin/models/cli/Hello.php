<?php

/**
 * CLI 'Hello World!'
 * 
 * - hello _string_ login _username_ _password_
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Hello extends CI_Model {

  public function index()  {
    $args = func_get_args();
    $message = (string) el(0,$args,'World');
    echo "Hello $message!".PHP_EOL;
  }
  
}




?>