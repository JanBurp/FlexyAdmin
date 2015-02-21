<?php

$files=read_map(APPPATH.'tests/api','php');
unset($files['apisuite.php']);
foreach ($files as $file => $value) {
  require_once $file;
}

class ApiSuite extends CIUnit_Framework_TestSuite {

  public static function suite () {
    error_reporting(E_ALL - E_NOTICE); // skip session notices
    $suite = new ApiSuite('Api testing');
    $files=read_map(APPPATH.'tests/api','php');
    unset($files['apisuite.php']);
    foreach ($files as $file => $value) {
      $suite->addTestSuite($value['alt']);
    }
    return $suite;
  }
  
  
  
}

?>