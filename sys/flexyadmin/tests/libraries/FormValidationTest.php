<?php

class FormValidationTest extends CIUnit_Framework_TestCase {

  var $good_data = array(
    array( 'email_email' => 'info@flexyadmin.com' ),
    array(
      'email_email_1' => 'info@flexyadmin.com',
      'email_email_2' => 'jan@flexyadmin.com'
    ),
  );
  
  var $false_data = array(
    array( 'email_email' => 'inf' ),
    array( 'email_email' => 'info@fl' ),
    array(
      'email_email_1' => 'info@flexyadmi',
      'email_email_2' => 'exyadmin.com'
    ),
  );


  protected function setUp ()  {
    $this->CI->load->library('form_validation');
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