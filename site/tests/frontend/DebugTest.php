<?php

class DebugTest extends CIUnit_Framework_TestCase {

  protected function setUp () {
    // $this->CI->load->model('cfg');
    // $this->CI->load->helper('language');
    // // Load basic modules
    // $this->CI->load->library('Menu');
    // $this->CI->load->library('Forms');
    // $this->CI->load->library('Email');
    // $this->CI->load->library('Module');
    // $this->CI->load->library('Ajax_module');
    // $this->CI->load->model('formaction');

    $this->CI->config->load('unittest');
  }
    

  
  /**
   * Test of er nog debughelper commando's zijn
   *
   * @return void
   * @author Jan den Besten
   */
  public function test_debug_code() {
    if ($this->CI->config->item('check_if_debug_code')) {
      $files=read_map('site','php',true,false,false,false);
      unset($files['sys/flexyadmin/helpers/debug_helper.php']);
      foreach ($files as $file) {
        $lines=file($file['path']);
        foreach ($lines as $key => $line) {
          $found=preg_match("/^\s*\s*(trace_|trace_if|strace_|backtrace_|xdebug_break)\(/u", $line);
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