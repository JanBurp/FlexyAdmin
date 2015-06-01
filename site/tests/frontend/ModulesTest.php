<?php

require_once('sys/flexyadmin/tests/CITestCase.php');

class ModulesTest extends CITestCase {

  private $settings=array();

  protected function setUp () {
    $this->CI->load->model('cfg');
    $this->CI->load->helper('language');
    // Load basic modules
    $this->CI->load->library('Menu');
    // $this->CI->load->library('Forms');
    $this->CI->load->library('Module');
  }
    
  /**
   * Test Modules
   */
  public function test_modules()  {
    $page=$this->CI->config->item('page');
    $modules = $this->CI->config->item('modules');
    
    foreach ($modules as $name => $info) {
      $this->CI->load->library($name);
      // calls
      foreach ($info as $call => $assert) {
        $module=remove_suffix($call,'.');
        $method=get_suffix($call,'.');
        if (empty($method) or $method==$module) $method='index';
        // assert##( result , call($page) )
        $result=array_pop($assert);
        $assert=$assert[0];
        $this->$assert( $result, $this->CI->$module->$method($page) );
      }
    }

  }


}

?>