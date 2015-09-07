<?php

require_once('sys/flexyadmin/tests/CITestCase.php');

class EmailHelperTest extends CITestCase {    

  public function setUp() {
    $this->CI->load->helper('email');
  }

  public function testEmailValidation() {
    echo "fast/helpers/EmailHelperTest/".__METHOD__."\n";
    
    $this->assertTrue(valid_email('test@test.com'));
    $this->assertFalse(valid_email('test#testcom'));
  }

}

?>