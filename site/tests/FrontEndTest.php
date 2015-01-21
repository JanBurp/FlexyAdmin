<?php

class FrontEndTest extends CIUnit_Framework_TestCase {

    protected function setUp () {
      $this->CI->load->helper('language');
      // Load basic modules
      $this->CI->load->library('Module');
      $this->CI->load->library('Ajax_module');
      $this->CI->load->library('Forms');
    }

    public function test_modules()  {
      $page=array(
        'id'        => 1,
        'uri'       => 'home',
        'str_title' => 'Test',
        'txt_text'  => '<h1>Test</h1>',
      );

      // Example module
      $this->CI->load->library('Example');
      $this->assertEquals( '<h1>Example Module</h1>', $this->CI->example->index($page) );
      $this->assertEquals( '<h1>Example Module.Other</h1>', $this->CI->example->other($page) );
      
      // Ajax example module
      $this->CI->load->library('Ajax_example');
      $this->assertEquals( '{"_message":"Ajax_example","_module":"example","_success":true}', $this->CI->ajax_example->index($page) );
      $this->assertEquals( '{"_message":"Ajax_example","_method":"other","_module":"example","_success":true}', $this->CI->ajax_example->other($page) );
    }


    /**
     * Test of er nog debughelper commando's zijn
     *
     * @return void
     * @author Jan den Besten
     */
    public function test_debug_code() {
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
    
    
    public function test_forms()  {
      // Example module
      // $out=$this->CI->forms->contact();

    }




}

?>