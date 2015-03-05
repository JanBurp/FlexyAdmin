<?php

class FormActionsTest extends CIUnit_Framework_TestCase {

  private $settings=array();

  protected function setUp () {
    $this->CI->load->model('cfg');
    $this->CI->load->helper('language');
    // Load basic modules
    $this->CI->load->library('Email');
    $this->CI->load->model('formaction');

    $this->CI->config->load('unittest');
  }
    

  /**
   * Test formactions
   *
   * @return void
   * @author Jan den Besten
   */
  public function test_formactions()  {
    
    $formactions=$this->CI->config->item('formactions');
    
    $this->assertTrue( true ); // just to be sure some test is done...
    

    $error_reporting=error_reporting();
    error_reporting(0);
    
    if (isset($formactions['formaction_mail'])) {
      if (! $this->CI->email->can_send()) {
        unset($formactions['formaction_mail']);
      }
    }
    
    foreach ($formactions as $formaction => $info) {
      
      $this->CI->load->model($formaction);
      $this->CI->$formaction->initialize($info['settings']);
      $this->CI->$formaction->fields($info['fields']);
      $data=$this->CI->db->random_data($info['fields']);
      $result=$this->CI->$formaction->go($data);
      
      $this->assertGreaterThanOrEqual(1,$result, el('message',$info,'Formaction `'.$formaction.'` did not give success as result.') );
    }
    error_reporting($error_reporting);

  }
    

}

?>