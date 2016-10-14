<?php

require_once('sys/flexyadmin/tests/CITestCase.php');

class DebugTest extends CITestCase {

  /**
   * Test of er nog debughelper commando's zijn
   *
   * @return void
   * @author Jan den Besten
   */
  public function test_debug_code() {

    $files=scan_map('site','php',true);
    foreach ($files as $file) {
      if (!in_array($file,array(SITEPATH.'tests/frontend/DebugTest.php'))) {
        $lines=file($file);
        foreach ($lines as $key => $line) {
          $found=preg_match("/^\s*\s*(trace_|trace_if|strace_|backtrace_|xdebug_break|var_dump|FIXME|\<\<\<\<\<\<\<|\>\>\>\>\>\>\>)\(/u", $line);
          $this->assertLessThan(1,$found, 'Debug helper found in `'.$file.'` at line '.($key+1)."\n".$line);
          $found=preg_match("/(<<<<<<<|>>>>>>>)/uim", $line);
          $this->assertLessThan(1,$found, 'Subversion text found in `'.$file.'` at line '.($key+1)."\n".$line);
        }
      }
    }
  }

}

?>