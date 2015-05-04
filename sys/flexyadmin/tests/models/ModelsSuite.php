<?php

require_once 'OrderTest.php';

class ModelsSuite extends CIUnit_Framework_TestSuite {

  public static function suite() {
    $suite = new LibrariesSuite('CodeIgniters Models Suite');
    $suite->addTestSuite('OrderTest');
    return $suite;
  }
}

?>