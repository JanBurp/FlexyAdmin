<?php

class ApiHelpTest extends CIUnit_Framework_TestCase {

  private $users=array(
    array(
      'username' => 'admin',
      'password' => 'admin',
    ),
    array(
      'username' => 'user',
      'password' => 'user',
    )
  );

  public function __construct() {
    parent::__construct();
    error_reporting(E_ALL - E_NOTICE); // skip session notices
    $this->CI->load->library('user');
    $this->CI->load->model('api/ApiModel');
    $this->CI->load->model('api/get_help');
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
      $result=$this->CI->get_help->index();
      $this->assertCount( 1, $result );
      $this->assertArrayHasKey( 'status', $result );
      $this->assertEquals( 401, $result['status'] );
    }
  }


  public function testWithLogin() {
    foreach ($this->users as $user) {
      $this->CI->user->login($user['username'], $user['password']);
      $result=$this->CI->get_help->index();
      $this->assertArrayNotHasKey( 'status', $result );
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( true, $result['success'] );
      $this->assertArrayHasKey( 'args', $result );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertInternalType( 'array', $result['data'] );
      $this->assertArrayHasKey( 'title', $result['data'] );
      $this->assertInternalType( 'string', $result['data']['title'] );
      $this->assertArrayHasKey( 'common_help', $result['data'] );
      // $this->assertInternalType( 'string', $result['data']['common_help'] );
      $this->assertArrayHasKey( 'help', $result['data'] );
      $this->assertInternalType( 'string', $result['data']['help'] );
    }
  }

  
}


?>