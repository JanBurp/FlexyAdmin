<?php

$files=read_map(SITEPATH.'tests/plugins','php',false,false);
unset($files['pluginssuite.php']);
foreach ($files as $file => $value) {
  require_once $file;
}

class PluginsSuite extends CIUnit_Framework_TestSuite {

  public static function suite () {
    $suite = new FlexyAdminSuite('Plugins tests');
    $files=read_map(SITEPATH.'tests/plugins','php',false,false);
    unset($files['pluginssuite.php']);
    foreach ($files as $file => $value) {
      $suite->addTestSuite(remove_suffix($value['name'],'.'));
    }
    return $suite;
  }
  
  
  
}

?>