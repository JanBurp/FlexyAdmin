<?php require_once("ApiTestModel.php");

class ApiAuthTest extends ApiTestModel {

  public function __construct() {
    parent::__construct('auth');
    $this->CI->load->library('email');
    error_reporting(E_ALL - E_NOTICE); // skip session notices
  }


  public function testLoginLogout() {

    // Check if logged out
    $result=$this->CI->auth->check();
    $this->assertArrayHasKey( 'status', $result );
    $this->assertEquals( 401, $result['status'] );
    // Login as 'admin'
    $this->CI->auth->set_args(array('POST'=>array('username'=>'admin','password'=>'admin')));
    $result=$this->CI->auth->login();
    $this->assertArrayHasKey( 'success', $result );
    $this->assertEquals( true, $result['success'] );
    $this->assertEquals( 'admin', $result['user']['username'] );
    // Logout
    $result = $this->CI->auth->logout();
    $this->assertNull( $result );
  }

  public function testWrongLogin() {

    $attempts = array(
      array('username'=> $this->users[0]['username'],   'password' => $this->users[1]['password'] ),
      array('username'=> $this->users[1]['username'],   'password' => $this->users[0]['password'] ),
      array('username'=> '',                            'password' => $this->users[0]['password'] ),
      array('username'=> $this->users[0]['username'],   'password' => '' ),
      array('username'=> '',                            'password' =>'' ),
      // array('user'=>random_string(), 'pwd'=>random_string() ),
      // array('whatu'=>random_string(), 'pass'=>random_string() ),
    );

    foreach ($attempts as $attempt) {
      $this->CI->auth->set_args(array('POST'=>array('username'=>$attempt['username'],'password'=>$attempt['password'])));
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

    // GET
    foreach ($attempts as $attempt) {
      $this->CI->auth->set_args(array('username'=>$attempt['username'],'password'=>$attempt['password']));
      $result=$this->CI->auth->login();
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( false, $result['success'] );
      $this->assertArrayNotHasKey( 'args', $result );
      $this->assertArrayNotHasKey( 'data', $result );
    }

    // POST
    foreach ($attempts as $attempt) {
      $this->CI->auth->set_args(array('POST'=>array('username'=>$attempt['username'],'password'=>$attempt['password'])));
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
      $this->CI->auth->set_args(array('POST'=>array('username'=>$user['username'],'password'=>$user['password'])));
      $result=$this->CI->auth->login();
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( true, $result['success'] );
      $this->assertArrayHasKey( 'args', $result );
      $this->assertEquals( $user['username'], $result['args']['username'] );
      $this->assertEquals( '***', $result['args']['password'] );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertEquals( $user['username'], $result['user']['username'] );
    }
  }
 
  
}


?>