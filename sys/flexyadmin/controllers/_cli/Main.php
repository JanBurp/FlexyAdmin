<?php

class Main extends CI_Controller {

  public function index($to = 'World')  {
    echo "Hello {$to}!".PHP_EOL;
  }
  
}

?>