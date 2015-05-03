<?php

$files=read_map(APPPATH.'tests/flexyadmin','php');
unset($files['flexyadminsuite.php']);
foreach ($files as $file => $value) {
  require_once $file;
}

class FlexyAdminSuite extends CIUnit_Framework_TestSuite {

  public static function suite () {
    $suite = new FlexyAdminSuite('Core FlexyAdmin tests');
    $files=read_map(APPPATH.'tests/flexyadmin','php');
    unset($files['flexyadminsuite.php']);
    foreach ($files as $file => $value) {
      $suite->addTestSuite($value['alt']);
    }
    return $suite;
  }
  
  
  
}

?>