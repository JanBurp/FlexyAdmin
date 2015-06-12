<?php

require_once('sys/flexyadmin/tests/CITestCase.php');

class AuthTest extends CITestCase {

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
    error_reporting(E_ALL - E_NOTICE); // skip session notices
    $this->CI->load->library('user');
  }
  
  protected function setUp() {
    // start always with logged out user
    if ($this->CI->user->logged_in()) $this->CI->user->logout();
  }
  
  protected function tearDown() {
    // always logout
    if ($this->CI->user->logged_in()) $this->CI->user->logout();
  }
  


  public function testIfLogout() {
    $this->assertFalse( $this->CI->user->logged_in(), 'Must be logged out at start of test.');
  }


  public function testWrongLogin() {
    $this->assertFalse( $this->CI->user->login( $this->users[0]['username'], $this->users[1]['password'] ), 'Login must fail with wrong username/password');
    $this->assertFalse( $this->CI->user->login( $this->users[1]['username'], $this->users[0]['password'] ), 'Login must fail with wrong username/password');
    
    $this->assertFalse( $this->CI->user->login( '', $this->users[0]['password'] ), 'Login must fail with empty username');
    $this->assertFalse( $this->CI->user->login( $this->users[0]['username'], '' ), 'Login must fail with empty password');
    $this->assertFalse( $this->CI->user->login( '', '' ), 'Login must fail with empty password/username');

    $this->assertFalse( $this->CI->user->login( random_string(), random_string() ), 'Login must fail with random username/password');
  }
  
  
  public function testHackAttempt() {
    $message='Login must fail with a SQL injection';
    $this->assertFalse( $this->CI->user->login( 'OR ""=""', 'OR ""=""'  ), $message);
    $this->assertFalse( $this->CI->user->login( '1; DROP TABLE cfg_users', '1; DROP TABLE cfg_users' ), $message);
    $this->assertFalse( $this->CI->user->login( '1 or 1=1', '1 or 1=1' ), $message);
    $this->assertFalse( $this->CI->user->login( "1' or '1' = '1'))/*", "1' or '1' = '1'))/*" ), $message);
    $this->assertFalse( $this->CI->user->login( "1' or '1' = '1')) LIMIT 1/*", "1' or '1' = '1')) LIMIT 1/*" ), $message);
    $this->assertFalse( $this->CI->user->login( "1 AND 1=1", "1 AND 1=1" ), $message);
    $this->assertFalse( $this->CI->user->login( "1 ORDER BY 10--", "1 ORDER BY 10--" ), $message);    
  }
  
  
  public function testLogin() {
    foreach ($this->users as $user) {
      $this->assertTrue( $this->CI->user->login( $user['username'], $user['password'] ), 'Login must work with good username/password ['.$user['username'].'/'.$user['password'].']');
      $this->assertTrue( $this->CI->user->logged_in(), 'Login must work with good username/password: '.$user['username']);
      $this->assertArrayHasKey( 'rights', $this->CI->user->get_rights(), 'User must have rights');
    }
  }
  
}


?>