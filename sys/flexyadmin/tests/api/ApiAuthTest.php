<?php

class ApiAuthTest extends CIUnit_Framework_TestCase {

  private $users=array(
    array(
      'username' => 'admin',
      'password' => 'admin'
    ),
    array(
      'username' => 'user',
      'password' => 'user'
    )
  );
  
  public function __construct() {
    parent::__construct();
    $this->CI->load->library('user');
    $this->CI->load->model('api/ApiModel');
    $this->CI->load->model('api/auth');
  }

  protected function setUp() {
    $this->logout();
  }

  protected function tearDown() {
    $this->logout();
  }

  private function logout() {
    if ($this->CI->user->logged_in()) $this->CI->user->logout();
  }

  


  public function testLoginLogout() {
    // Check if logged out
    $result=$this->CI->auth->check();
    $this->assertArrayHasKey( 'status', $result );
    $this->assertEquals( 401, $result['status'] );
    // Login as 'admin'
    $this->CI->auth->set_args(array('username'=>'admin','password'=>'admin'));
    $result=$this->CI->auth->login();
    $this->assertArrayHasKey( 'success', $result );
    $this->assertEquals( 'admin', $result['data']['username'] );
    $this->assertEquals( 'info@flexyadmin.com', $result['data']['email'] );
    // Logout
    $result=$this->CI->auth->logout();
    $this->assertArrayHasKey( 'status', $result );
    $this->assertEquals( 401, $result['status'] );
    $this->assertArrayNotHasKey( 'data', $result );
  }



  public function testWrongLogin() {
    $attempts = array(
      array('username'=> $this->users[0]['username'],   'password' => $this->users[1]['password'] ),
      array('username'=> $this->users[1]['username'],   'password' => $this->users[0]['password'] ),
      array('username'=> '',                            'password' => $this->users[0]['password'] ),
      array('username'=> $this->users[0]['username'],   'password' => '' ),
      array('username'=> '',                            'password' =>'' ),
      array('username'=> random_string(),               'password' => random_string() ),
      array('username'=> random_string(),               'password' => random_string() ),
      array('username'=> random_string(),               'password' => random_string() ),
      array('username'=> random_string(),               'password' => random_string() ),
    );

    foreach ($attempts as $attempt) {
      $this->CI->auth->set_args(array('username'=>$attempt['username'],'password'=>$attempt['password']));
      $result=$this->CI->auth->login();
      $this->assertArrayHasKey( 'status', $result );
      $this->assertEquals( 401, $result['status'] );
      $this->assertArrayNotHasKey( 'success', $result );
      $this->assertArrayNotHasKey( 'args', $result );
      $this->assertArrayNotHasKey( 'data', $result );
    }

  }
  
  
  public function testHackAttempts() {
    $message='Login must fail with a SQL injection';
    $attempts = array(
      array( 'username'=> 'OR ""=""',                     'password' => 'OR ""=""'  ),
      array( 'username'=> '1; DROP TABLE cfg_users',      'password' => '1; DROP TABLE cfg_users' ),
      array( 'username'=> '1 or 1=1',                     'password' => '1 or 1=1' ),
      array( 'username'=> "1' or '1' = '1'))/*",          'password' => "1' or '1' = '1'))/*" ),
      array( 'username'=> "1' or '1' = '1')) LIMIT 1/*",  'password' => "1' or '1' = '1')) LIMIT 1/*" ),
      array( 'username'=> "1 AND 1=1",                    'password' =>  "1 AND 1=1" ),
      array( 'username'=> "1 ORDER BY 10--",              'password' =>  "1 ORDER BY 10--" )
    );

    foreach ($attempts as $attempt) {
      $this->CI->auth->set_args(array('username'=>$attempt['username'],'password'=>$attempt['password']));
      $result=$this->CI->auth->login();
      $this->assertArrayHasKey( 'status', $result );
      $this->assertEquals( 401, $result['status'] );
      $this->assertArrayNotHasKey( 'success', $result );
      $this->assertArrayNotHasKey( 'args', $result );
      $this->assertArrayNotHasKey( 'data', $result );
    }
    
  }
  
  public function testLogin() {
    foreach ($this->users as $user) {
      $this->CI->auth->set_args(array('username'=>$user['username'],'password'=>$user['password']));
      $result=$this->CI->auth->login();
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( true, $result['success'] );
      $this->assertArrayHasKey( 'args', $result );
      $this->assertEquals( $user['username'], $result['args']['username'] );
      $this->assertEquals( '***', $result['args']['password'] );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertEquals( $user['username'], $result['data']['username'] );
    }
  }
  
}


?>