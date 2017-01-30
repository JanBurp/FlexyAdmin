<?php

require_once(APPPATH.'/tests/CITestCase.php');

class AuthTest extends CITestCase {
  
  private $can_send_mail = FALSE;

  private $users=array(
    array(
      'username' => 'admin',
      'password' => 'admin',
      'rights'   => array(
        'all_users' => true,
        'backup'    => true,
        'tools'     => true,
        'items'     => array(
          'cfg_configurations'    => 15,
          'cfg_email'             => 15,
          'cfg_sessions'          => 15,
          'cfg_ui'                => 15,
          'cfg_user_groups'       => 15,
          'cfg_users'             => 15,
          'log_activity'          => 15,
          'log_login_attempts'    => 15,
          'log_stats'             => 15,
          'rel_crud__crud2'       => 15,
          'rel_groepen__adressen' => 15,
          'rel_users__groups'     => 15,
          'res_assets'            => 15,
          'tbl_adressen'          => 15,
          'tbl_crud'              => 15,
          'tbl_crud2'             => 15,
          'tbl_groepen'           => 15,
          'tbl_kinderen'          => 15,
          'tbl_links'             => 15,
          'tbl_menu'              => 15,
          'tbl_site'              => 15,
          'media_pictures'        => 15,
          'media_downloads'       => 15,
        )
      ),
    ),
    array(
      'username' => 'user',
      'password' => 'user',
      'rights'   => array(         
        'all_users' => false,
        'backup'    => false,
        'tools'     => false,
        'items'     => array(
          'cfg_configurations'    => 0,
          'cfg_email'             => 0,
          'cfg_sessions'          => 0,
          'cfg_ui'                => 0,
          'cfg_user_groups'       => 0,
          'cfg_users'             => 0,
          'log_activity'          => 0,
          'log_login_attempts'    => 0,
          'log_stats'             => 0,
          'rel_crud__crud2'       => 0,
          'rel_groepen__adressen' => 0,
          'rel_users__groups'     => 0,
          'res_assets'            => 0,
          'tbl_adressen'          => 15,
          'tbl_crud'              => 15,
          'tbl_crud2'             => 15,
          'tbl_groepen'           => 15,
          'tbl_kinderen'          => 15,
          'tbl_links'             => 15,
          'tbl_menu'              => 15,
          'tbl_site'              => 15,
          'media_pictures'        => 15,
          'media_downloads'       => 15,
        )
      ),
    ),
    array(
      'username' => 'test',
      'password' => 'test',
      'rights'   => array(         
        'all_users' => false,
        'backup'    => true,
        'tools'     => true,
        'items'     => array(
          'cfg_configurations'    => 0,
          'cfg_email'             => 0,
          'cfg_sessions'          => 0,
          'cfg_ui'                => 0,
          'cfg_user_groups'       => 0,
          'cfg_users'             => 15,
          'log_activity'          => 0,
          'log_login_attempts'    => 0,
          'log_stats'             => 0,
          'rel_crud__crud2'       => 0,
          'rel_groepen__adressen' => 0,
          'rel_users__groups'     => 0,
          'res_assets'       => 0,
          'tbl_adressen'          => 15,
          'tbl_crud'              => 15,
          'tbl_crud2'             => 15,
          'tbl_groepen'           => 15,
          'tbl_kinderen'    => 15,
          'tbl_links'             => 15,
          'tbl_menu'              => 15,
          'tbl_site'              => 15,
          'media_pictures'        => 15,
          'media_downloads'       => 15,
        )
      ),
    )
  );
  
  public function __construct() {
    parent::__construct();
    error_reporting(E_ALL - E_NOTICE); // skip session notices
    $this->CI->load->library('flexy_auth');
    $this->CI->load->library('email');
    // Is it possible to send emails?
    $error_reporting=error_reporting();
    error_reporting(0);
    $this->can_send_mail = $this->CI->email->can_send();
    error_reporting($error_reporting);
  }
  
  protected function setUp() {
    // start always with logged out user
    // if ($this->CI->flexy_auth->logged_in()) $this->CI->flexy_auth->logout();
  }
  
  protected function tearDown() {
    // always logout
    // if ($this->CI->flexy_auth->logged_in()) $this->CI->flexy_auth->logout();
  }
  


  public function testIfLogout() {
    $this->assertFalse( $this->CI->flexy_auth->logged_in(), 'Must be logged out at start of test.');
  }


  public function testWrongLogin() {
    $this->assertFalse( $this->CI->flexy_auth->login( $this->users[0]['username'], $this->users[1]['password'] ), 'Login must fail with wrong username/password');
    $this->assertFalse( $this->CI->flexy_auth->login( $this->users[1]['username'], $this->users[0]['password'] ), 'Login must fail with wrong username/password');
    
    $this->assertFalse( $this->CI->flexy_auth->login( '', $this->users[0]['password'] ), 'Login must fail with empty username');
    $this->assertFalse( $this->CI->flexy_auth->login( $this->users[0]['username'], '' ), 'Login must fail with empty password');
    $this->assertFalse( $this->CI->flexy_auth->login( '', '' ), 'Login must fail with empty password/username');
    
    $this->assertFalse( $this->CI->flexy_auth->login( random_string(), random_string() ), 'Login must fail with random username/password');
    $this->assertFalse( $this->CI->flexy_auth->login( random_string(), random_string() ), 'Login must fail with random username/password');
    
  }
  
  
  public function testHackAttempt() {
    $message='Login must fail with a SQL injection';
    $this->assertFalse( $this->CI->flexy_auth->login( 'OR ""=""', 'OR ""=""'  ), $message);
    $this->assertFalse( $this->CI->flexy_auth->login( '1; DROP TABLE cfg_users', '1; DROP TABLE cfg_users' ), $message);
    $this->assertFalse( $this->CI->flexy_auth->login( '1 or 1=1', '1 or 1=1' ), $message);
    $this->assertFalse( $this->CI->flexy_auth->login( "1' or '1' = '1'))/*", "1' or '1' = '1'))/*" ), $message);
    $this->assertFalse( $this->CI->flexy_auth->login( "1' or '1' = '1')) LIMIT 1/*", "1' or '1' = '1')) LIMIT 1/*" ), $message);
    $this->assertFalse( $this->CI->flexy_auth->login( "1 AND 1=1", "1 AND 1=1" ), $message);
    $this->assertFalse( $this->CI->flexy_auth->login( "1 ORDER BY 10--", "1 ORDER BY 10--" ), $message);    
  }
  
  
  public function testLogin() {
    foreach ($this->users as $user) {
      // Login
      $this->assertTrue( $this->CI->flexy_auth->login( $user['username'], $user['password'] ), 'Login must work with good username/password ['.$user['username'].'/'.$user['password'].']');
      $this->assertTrue( $this->CI->flexy_auth->logged_in(), 'Login must work with good username/password: '.$user['username']);
      $get_user = $this->CI->flexy_auth->get_user();
      $this->assertArrayHasKey( 'rights', $get_user, 'User must have rights');
      $this->assertEquals( $user['rights'], $get_user['rights'], 'User has wrong rights');
    }
  }
  
  
  public function testCreateUser() {
    $identity   = 'TEST_'.time();
    $password   = random_string();
    $email      = $identity.'@flexyadmin.com';
    $additional = array();
    $groups     = array(3);    // user
    $expected_rights = $this->users[1]['rights'];

    $user_id = $this->CI->flexy_auth->register( $identity, $password, $email, $additional, $groups);
    $this->assertInternalType( 'integer', $user_id, 'Attempt to create user `'.$identity.'` Failed');
    $this->assertGreaterThan( 3, $user_id, 'Attempt to create user `'.$identity.'` Failed');

    // Try to login, check rights, logout and remove user
    if ($user_id) {
      // Login
      $this->assertTrue( $this->CI->flexy_auth->login( $identity, $password ), 'Created user must be able to login '.$identity);
      // Check rights
      $rights = $this->CI->flexy_auth->get_rights($user_id);
      $this->assertArrayHasKey( 'items', $rights, 'New user must have rights '.$identity);
      $this->assertEquals( $expected_rights, $rights, 'User has wrong rights '.$identity);
      if ($this->can_send_mail) {
        // Send new account mail
        $this->assertTrue( $this->CI->flexy_auth->send_new_account( $user_id ), 'New user should get a `new account` email '.$identity);
        // Send new password mail
        $this->assertTrue( $this->CI->flexy_auth->send_new_password( $user_id ), 'New user should get a `new password` email '.$identity);
        // Send new forgotten password mail
        $this->assertTrue( $this->CI->flexy_auth->forgotten_password( $email ), 'New user should get a `forgotten_password` email '.$identity);
      }
      // Remove user
      $this->assertTrue( $this->CI->flexy_auth->delete_user($user_id) );
    }

    // Remove all created users
    $this->CI->db->where('ip_address','127.0.0.1');
    $this->CI->db->like('str_username','TEST_');
    $users = $this->CI->flexy_auth->get_users(4);
    foreach ($users as $key => $user) {
      $this->assertTrue( $this->CI->flexy_auth->delete_user($user['user_id']) );
    }

  }

  
}


?>
