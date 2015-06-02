<?php

require_once('sys/flexyadmin/tests/CITestCase.php');

class CodeTest extends CITestCase {

  /**
   * Test of er nog debughelper commando's zijn (SLOW)
   *
   * @return void
   * @author Jan den Besten
   */
  public function test_debug_code() {
    $files=read_map('sys/flexyadmin','php',true,false,false,false);
    unset($files['sys/flexyadmin/helpers/debug_helper.php']);
    foreach ($files as $file) {
      $lines=file($file['path']);
      foreach ($lines as $key => $line) {
        $found=preg_match("/^\s*\s*(trace_|trace_if|strace_|backtrace_|xdebug_break)\(/u", $line);
        $this->assertLessThan(1,$found, 'Debug helper found in `<i><b>'.$file['path'].'</i></b>` at line '.($key+1).':<br><code>'.$line.'</code>');
      }
    }
  }


}


?>