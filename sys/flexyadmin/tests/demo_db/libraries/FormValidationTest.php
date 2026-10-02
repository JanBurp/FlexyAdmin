<?php

require_once(APPPATH.'/tests/CITestCase.php');

class FormValidationTest extends CITestCase {

  var $good_data = array(
    array(
      'email_email' => 'info@flexyadmin.com'
    ),
    array(
      'email_email_1' => 'info@flexyadmin.com',
      'email_email_2' => 'jan@flexyadmin.com'
    ),
  );
  
  var $false_data = array(
    array(
      'email_email' => 'inf'
    ),
    array(
      'email_email' => 'info@fl'
    ),
    array(
      'email_email' => 'info@flexyadmi',
    ),
    array(
      'email_email' => 'exyadmin.com'
    ),
  );
  
  protected function setUp() :void   {
    $this->CI->load->library('form_validation');
  }
  
  
  public function testIsOption() {
    
    $options=',one,two,three';
    $values=array('no'=>false,'one'=>true,'two'=>true,'three'=>true,'|'=>false,','=>false,''=>true);
    foreach ($values as $value=>$result) {
      $validated = $this->CI->form_validation->valid_option($value,$options);
      if ($result) {
        $this->assertTrue($validated);
      }
      else {
        $this->assertFalse($validated);
      }
    }

    // tbl_menu.str_module
    $tests=array(
      array('str_module','example',true),
      array('str_module','test',false),
      array('str_module','|',false),
    );
    foreach ($tests as $test) {
      $result=array_pop($test);

      $validated = $this->CI->form_validation->validate_data( array($test[0]=>$test[1]), 'tbl_menu' );
      $errors    = $this->CI->form_validation->get_error_messages();
      
      if ($result) {
        $this->assertTrue($validated);
        $this->assertIsArray($errors);
        $this->assertCount(0,$errors);
      }
      else {
        $this->assertFalse($validated);
        $this->assertIsArray($errors);
        $this->assertCount(1,$errors);
      }

    }
  }


  public function testValidateGoodData() {
    
    // Should be ok
    foreach ($this->good_data as $data) {
      $validated = $this->CI->form_validation->validate_data($data,'tbl_site');
      $errors    = $this->CI->form_validation->get_error_messages();
      
      $this->assertTrue($validated);
      $this->assertIsArray($errors);
      $this->assertCount(0,$errors);
    }
  }

  public function testValidateWrongData() {

    // Should give an error
    foreach ($this->false_data as $data) {
      $validated = $this->CI->form_validation->validate_data($data,'tbl_site');
      $errors    = $this->CI->form_validation->get_error_messages();
      $this->assertFalse($validated);
      $this->assertIsArray($errors);
      $this->assertArrayHasKey('email_email',$errors);
    }
  }

  /**
   * pocketarc/codeigniter's CI_Form_validation::run() uses func_num_args() to decide
   * whether to write prepped field data into $_POST (0-1 args, same as stock CI3 3.1.13
   * always did) or into a &$data reference (2 args, a new pocketarc feature). Controllers
   * like Auth.php call $this->form_validation->run() with 0 args and then read
   * $this->input->post(...) expecting the prepped value - MY_Form_validation::run() must
   * mirror the caller's own argument count when forwarding to parent::run(), or that
   * $_POST repopulation silently stops happening.
   *
   * @return void
   * @author Jan den Besten
   */
  public function testRunWithNoArgsRepopulatesPost() {
    // pocketarc/codeigniter's set_rules() also added a "only for POST requests"
    // gate that stock CI3 didn't have - simulate a real form POST (as Auth.php
    // always runs under) rather than the CLI test runner's own request method.
    $original_method = $_SERVER['REQUEST_METHOD'] ?? null;
    $_SERVER['REQUEST_METHOD'] = 'POST';

    // form_validation is a shared instance across the whole PHPUnit run (CI3 only
    // instantiates it once per singleton, not once per test), and reset_validation()
    // - meant for exactly this ("due to the CI singleton") - doesn't clear
    // $validation_data. Earlier tests here call validate_data(), which sets it via
    // set_data(); clear it too so run() reads from $_POST, like a real fresh
    // request would.
    $this->CI->form_validation->reset_validation();
    $validation_data_prop = new ReflectionProperty('CI_Form_validation', 'validation_data');
    $validation_data_prop->setAccessible(true);
    $validation_data_prop->setValue($this->CI->form_validation, array());

    $_POST = array('trimmed_field' => '  spaced out  ');
    $this->CI->form_validation->set_rules('trimmed_field', 'Trimmed field', 'trim');

    $validated = $this->CI->form_validation->run();

    $this->assertTrue($validated);
    $this->assertSame('spaced out', $_POST['trimmed_field']);

    if (isset($original_method)) {
      $_SERVER['REQUEST_METHOD'] = $original_method;
    } else {
      unset($_SERVER['REQUEST_METHOD']);
    }
  }

}

?>