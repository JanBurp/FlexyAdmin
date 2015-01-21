<?php

require_once 'CodeTest.php';
require_once 'AuthTest.php';

class FlexyAdminSuite extends CIUnit_Framework_TestSuite {

  public static function suite () {
    $suite = new FlexyAdminSuite('Core FlexyAdmin tests');
    $suite->addTestSuite('CodeTest'); 
    $suite->addTestSuite('AuthTest'); 
    return $suite;
  }
  
  
  
}

?>