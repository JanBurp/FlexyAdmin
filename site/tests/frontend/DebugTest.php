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
    if ($this->CI->config->item('check_if_debug_code')) {
      $files=scan_map('site','php',true);
      unset($files['sys/flexyadmin/helpers/debug_helper.php']);
      foreach ($files as $file) {
        $lines=file($file['path']);
        foreach ($lines as $key => $line) {
          $found=preg_match("/^\s*\s*(trace_|trace_if|strace_|backtrace_|xdebug_break|var_dump)\(/u", $line);
          $this->assertLessThan(1,$found, 'Debug helper found in `<i><b>'.$file['path'].'</i></b>` at line '.($key+1).':<br><code>'.$line.'</code>');
        }
      }
    }
    else {
      $this->assertTrue(true);
    }
  }

}

?>