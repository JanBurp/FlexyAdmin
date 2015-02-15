<?php

class ApiGetTableTest extends CIUnit_Framework_TestCase {

  private $users=array(
    array(
      'username' => 'admin',
      'password' => 'admin',
      'tables'   => array('tbl_site','tbl_menu','cfg_users','log_login','res_media_result')
    ),
    array(
      'username' => 'user',
      'password' => 'user',
      'tables'   => array('tbl_site','tbl_menu')
    )
  );
  private $tables = array('tbl_site','tbl_menu','cfg_users','log_login','res_media_result');
  

  public function __construct() {
    parent::__construct();
    $this->CI->load->library('user');
    $this->CI->load->model('api/ApiModel');
    $this->CI->load->model('api/get_table');
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
        $this->CI->get_table->set_args(array('table'=>$table));
        $result=$this->CI->get_table->index();
        $this->assertArrayNotHasKey( '_success', $result );
        $this->assertArrayNotHasKey( '_args', $result );
        $this->assertArrayNotHasKey( 'data', $result );
        $this->assertArrayHasKey( '_status', $result );
        $this->assertEquals( 401, $result['_status'] );
      }
    }
  }


  public function testWithLogin() {
    // foreach ($this->users as $user) {
    //   $this->CI->user->login($user['username'], $user['password']);
    //   foreach ($this->tables as $table) {
    //     $this->CI->get_table->set_args(array('table'=>$table));
    //     $result=$this->CI->get_table->index();
    //     trace_([$user,$table,$result]);
    //     if (in_array($table,$user['tables'])) {
    //       // user has rights for this table
    //       $this->assertArrayHasKey( '_success', $result );
    //       $this->assertArrayHasKey( '_args', $result );
    //       $this->assertArrayHasKey( 'data', $result );
    //       $this->assertArrayNotHasKey( '_status', $result );
    //     }
    //     else {
    //       // user has no rights for this table
    //       $this->assertArrayNotHasKey( '_success', $result );
    //       $this->assertArrayNotHasKey( '_args', $result );
    //       $this->assertArrayNotHasKey( 'data', $result );
    //       $this->assertArrayHasKey( '_status', $result );
    //       $this->assertEquals( 401, $result['_status'] );
    //     }
    //   }
    // }
  }

  
}


?>