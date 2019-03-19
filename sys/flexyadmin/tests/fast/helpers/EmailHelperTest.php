<?php

require_once(APPPATH.'/tests/CITestCase.php');

class EmailHelperTest extends CITestCase {    

  protected function setUp() :void  {
    $this->CI->load->helper('email');
  }

  public function testEmailValidation() {
    
    $this->assertTrue(valid_email('test@test.com'));
    $this->assertFalse(valid_email('test#testcom'));
  }

}

?>