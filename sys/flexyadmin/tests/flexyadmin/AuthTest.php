<?php

class AuthTest extends CIUnit_Framework_TestCase {

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
  
  private $user;


  protected function setUp() {
    $this->CI->load->library('user');
    $this->user=$this->CI->user;
    // start always with logged out user
    if ($this->user->logged_in()) $this->user->logout();
  }


  public function testIfLogout() {
    $this->assertFalse( $this->user->logged_in(), 'Must be logged out at start of test.');
  }


  public function testWrongLogin() {
    $this->assertFalse( $this->user->login( $this->users[0]['username'], $this->users[1]['password'] ), 'Login must fail with wrong username/password');
    $this->assertFalse( $this->user->login( $this->users[1]['username'], $this->users[0]['password'] ), 'Login must fail with wrong username/password');
    
    $this->assertFalse( $this->user->login( '', $this->users[0]['password'] ), 'Login must fail with empty username');
    $this->assertFalse( $this->user->login( $this->users[0]['username'], '' ), 'Login must fail with empty password');
    $this->assertFalse( $this->user->login( '', '' ), 'Login must fail with empty password/username');

    $this->assertFalse( $this->user->login( random_string(), random_string() ), 'Login must fail with random username/password');
    $this->assertFalse( $this->user->login( random_string(), random_string() ), 'Login must fail with random username/password');
    $this->assertFalse( $this->user->login( random_string(), random_string() ), 'Login must fail with random username/password');
    $this->assertFalse( $this->user->login( random_string(), random_string() ), 'Login must fail with random username/password');
  }
  
  
  public function testHackAttempt() {
    $message='Login must fail with a SQL injection';
    $this->assertFalse( $this->user->login( 'OR ""=""', 'OR ""=""'  ), $message);
    $this->assertFalse( $this->user->login( '1; DROP TABLE cfg_users', '1; DROP TABLE cfg_users' ), $message);
    $this->assertFalse( $this->user->login( '1 or 1=1', '1 or 1=1' ), $message);
    $this->assertFalse( $this->user->login( "1' or '1' = '1'))/*", "1' or '1' = '1'))/*" ), $message);
    $this->assertFalse( $this->user->login( "1' or '1' = '1')) LIMIT 1/*", "1' or '1' = '1')) LIMIT 1/*" ), $message);
    $this->assertFalse( $this->user->login( "1 AND 1=1", "1 AND 1=1" ), $message);
    $this->assertFalse( $this->user->login( "1 ORDER BY 10--", "1 ORDER BY 10--" ), $message);    
  }
  
  
  public function testLogin() {
    foreach ($this->users as $user) {
      $this->assertTrue( $this->user->login( $user['username'], $user['password'] ), 'Login must work with good username/password');
      $this->assertTrue( $this->user->logged_in(), 'Login must work with good username/password: '.$user['username']);
      $this->assertArrayHasKey( 'rights', $this->user->get_rights(), 'User must have rights');
    }
  }



  
  protected function tearDown() {
    // always logout
    if ($this->user->logged_in()) $this->user->logout();
  }
  
}


?>