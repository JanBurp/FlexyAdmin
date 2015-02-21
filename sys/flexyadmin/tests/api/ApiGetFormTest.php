<?php

class ApiGetFormTest extends CIUnit_Framework_TestCase {

  private $users=array(
    array(
      'username' => 'admin',
      'password' => 'admin',
      'tables'   => array('tbl_site','tbl_menu','cfg_users','log_login','res_media_files')
    ),
    array(
      'username' => 'user',
      'password' => 'user',
      'tables'   => array('tbl_site','tbl_menu')
    )
  );
  private $tables = array('tbl_site','tbl_menu','cfg_users','log_login','res_media_files');
  
  private $config_settings = array(
    array(),
    array('table_info'),
    array('field_info'),
    array('table_info','field_info'),
  );
  

  public function __construct() {
    parent::__construct();
    $this->CI->load->library('user');
    $this->CI->load->model('api/ApiModel');
    $this->CI->load->model('api/get_form');
  }

  private function logout() {
    if ($this->CI->user->logged_in()) $this->CI->user->logout();
  }

  protected function setUp() {
    $this->logout();
  }

  protected function tearDown() {
    $this->logout();
  }
 
  
  public function testWithoutLogin() {
    foreach ($this->users as $user) {
      foreach ($this->tables as $table) {
        $this->CI->get_form->set_args(array('table'=>$table));
        $result=$this->CI->get_form->index();
        $this->assertCount( 1, $result );
        $this->assertArrayHasKey( 'status', $result );
        $this->assertEquals( 401, $result['status'] );
      }
    }
  }


  public function testWithLogin() {
    foreach ($this->users as $user) {
      $this->CI->user->login($user['username'], $user['password']);
      foreach ($this->tables as $table) {
        $this->CI->get_form->set_args(array('table'=>$table,'where'=>'first'));
        $result=$this->CI->get_form->index();
        if (in_array($table,$user['tables'])) {
          // user has rights for this table
          $this->assertArrayNotHasKey( 'status', $result );
          $this->assertArrayHasKey( 'success', $result );
          $this->assertEquals( true, $result['success'] );
          $this->assertArrayHasKey( 'args', $result );
          $this->assertArrayHasKey( 'data', $result );
          $this->assertInternalType( 'array', $result['data'] );
          $this->assertArrayHasKey( 'fields', $result['data'] );
        }
        else {
          // user has no rights for this table
          $this->assertCount( 1, $result );
          $this->assertArrayHasKey( 'status', $result );
          $this->assertEquals( 401, $result['status'] );
        }
      }
    }
  }


  public function testConfig() {

    foreach ($this->users as $user) {
      $this->CI->user->login($user['username'], $user['password']);

      // Test config of tbl_menu
      $this->CI->get_form->set_args( array('table'=>'tbl_menu','where'=>'first','config'=>array('table_info','field_info')) );
      $result=$this->CI->get_form->index();
      $this->assertArrayHasKey( 'config', $result );
      $this->assertInternalType( 'array', $result['config'] );
      $this->assertArrayHasKey( 'table_info', $result['config'] );
      $this->assertInternalType( 'array', $result['config']['table_info'] );
      $this->assertArrayHasKey( 'field_info', $result['config'] );
      $this->assertInternalType( 'array', $result['config']['field_info'] );
      $this->assertArrayHasKey( 'tree', $result['config']['table_info'] );
      $this->assertEquals( true, $result['config']['table_info']['tree'] );
      $this->assertArrayHasKey( 'sortable', $result['config']['table_info'] );
      $this->assertEquals( false, $result['config']['table_info']['sortable'] );
      $this->assertInternalType( 'array', $result['config']['table_info']['fields'] );
      $this->assertArrayHasKey( 'ui_name', $result['config']['table_info'] );
      $this->assertInternalType( 'string', $result['config']['table_info']['ui_name'] );
      
      // all set tables and there config
      foreach ($this->tables as $table) {
        foreach ($this->config_settings as $cfg) {
          $this->CI->get_form->set_args( array('table'=>$table,'config'=>$cfg) );
          $result=$this->CI->get_form->index();
          if (in_array($table,$user['tables'])) {
            // user has rights for this table, test if it has config data...
            if (!empty($cfg)) {
              $this->assertArrayHasKey( 'config', $result );
            }
            foreach ($cfg as $cfg_type) {
              $this->assertArrayHasKey( $cfg_type, $result['config'], 'Key '.$cfg_type.' should exists in result' );
            }
          }
          else {
            // user has no rights for this table
            $this->assertCount( 1, $result );
            $this->assertArrayHasKey( 'status', $result );
            $this->assertEquals( 401, $result['status'] );
          }
        }
      }
    }
    
  }


  
}


?>