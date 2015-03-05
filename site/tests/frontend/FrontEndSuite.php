<?php


$files=read_map(SITEPATH.'tests/frontend','php',false,false);
unset($files['frontendsuite.php']);
foreach ($files as $file => $value) {
  require_once $file;
}

class FrontEndSuite extends CIUnit_Framework_TestSuite {

  public static function suite () {
    $suite = new FlexyAdminSuite('Frontend tests');
    $files=read_map(SITEPATH.'tests/frontend','php',false,false);
    unset($files['frontendsuite.php']);
    foreach ($files as $file => $value) {
      $suite->addTestSuite(remove_suffix($value['name'],'.'));
    }
    return $suite;
  }
  
  
  
}
?>