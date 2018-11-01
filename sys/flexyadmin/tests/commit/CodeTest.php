<?php

require_once(APPPATH.'/tests/CITestCase.php');

class CodeTest extends CITestCase {
  
  private $excludedFolders = array(
    '/assets/',
    '/config/',
    '/language/',
    '/html2pdf/',
    '/tests/',
  );
  
  private $excludedFiles = array(
    '../sys/flexyadmin/models/Cronjob.php',
    '../sys/flexyadmin/tests/commit/CodeTest.php',
    '../sys/flexyadmin/controllers/_admin/Test.php',
    '../sys/flexyadmin/libraries/plugins/Plugin_test.php',
    '../sys/flexyadmin/helpers/debug_helper.php',
  );

  /**
   * Test of er nog debughelper commando's zijn (SLOW)
   *
   * @return void
   * @author Jan den Besten
   */
  public function test_debug_code() {
    $files = scan_map('../sys/flexyadmin','php',true); // sys/flexyadmin
    $this->assertNotEmpty($files,'No code found to test. ('.__DIR__.')');
    foreach ($files as $file) {
      if (!in_array($file,$this->excludedFiles) and !has_string($this->excludedFolders,$file)) {
        $lines=file($file);
        foreach ($lines as $key => $line) {
          $found=preg_match("/^\s*\s*(trace_|backtrace_|xdebug_break|var_dump)\(/u", $line);
          $this->assertLessThan(1,$found, 'Debug helper found in `'.$file.'` at line '.($key+1)."\n".$line);
          // if ($found) echo 'Debug helper found in `'.$file.'` at line '.($key+1)."\n".$line;
          $found=preg_match("/(<<<<<<<|>>>>>>>)/uim", $line);
          $this->assertLessThan(1,$found, 'Version text found in `'.$file.'` at line '.($key+1)."\n".$line);
          // if ($found) echo 'Subversion text found in `'.$file.'` at line '.($key+1)."\n".$line;
        }
      }
    }
  }


}


?>