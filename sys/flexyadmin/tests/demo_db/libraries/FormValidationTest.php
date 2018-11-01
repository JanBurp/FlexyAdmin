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
  
  protected function setUp ()  {
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
        $this->assertInternalType('array',$errors);
        $this->assertCount(0,$errors);
      }
      else {
        $this->assertFalse($validated);
        $this->assertInternalType('array',$errors);
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
      $this->assertInternalType('array',$errors);
      $this->assertCount(0,$errors);
    }
  }

  public function testValidateWrongData() {
    
    // Should give an error
    foreach ($this->false_data as $data) {
      $validated = $this->CI->form_validation->validate_data($data,'tbl_site');
      $errors    = $this->CI->form_validation->get_error_messages();
      $this->assertFalse($validated);
      $this->assertInternalType('array',$errors);
      $this->assertArrayHasKey('email_email',$errors);
    }
  }


}

?>