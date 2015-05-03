<?php require_once("ApiTestModel.php");

class ApiAuthTest extends ApiTestModel {

  public function __construct() {
    parent::__construct('auth');
    $this->CI->load->library('email');
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
      $this->assertEquals( $user['username'], $result['data']['username'] );
    }
  }
  
  public function testNewPasswordSend() {
    // Is it possible to send emails?
    $error_reporting=error_reporting();
    error_reporting(0);
    $can_send = $this->CI->email->can_send();
    error_reporting($error_reporting);
    if (!$can_send) return FALSE;

    // first create new users
    $cleanup_ids=array();
    foreach ($this->test_users as $user) {
      $cleanup_ids[]=$this->CI->crud->table('cfg_users')->insert(array('data'=>$user));
    }
    
    // Try 10 times with random emails
    for ($i=0; $i < 10 ; $i++) { 
      $random_email=random_string().'@'.random_string.'.'.random_string('alpha',3);
      $this->CI->auth->set_args(array('email'=>$random_email));
      $result=$this->CI->auth->send_new_password();
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( false, $result['success'] );
      $this->assertArrayHasKey( 'args', $result );
      $this->assertInternalType( 'array', $result['args'] );
      $this->assertEquals( $random_email, $result['args']['email'] );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertEquals( false, $result['data'] );
    }
    
    // send new password
    foreach ($this->test_users as $user) {
      $this->CI->auth->set_args(array('email'=>$user['email_email']));
      $result=$this->CI->auth->send_new_password();
      
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( true, $result['success'] );
      $this->assertArrayHasKey( 'args', $result );
      $this->assertInternalType( 'array', $result['args'] );
      $this->assertEquals( $user['email_email'], $result['args']['email'] );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertInternalType( 'array', $result['data'] );
      $this->assertEquals( $user['email_email'], $result['data']['email'] );
      $this->assertEquals( $user['str_username'], $result['data']['username'] );
      
      // Test if password is not same anymore
      $new_password_hash=$this->CI->db->get_field_where('cfg_users','gpw_password','email_email',$user['email_email']);
      $this->assertNotEquals( $user['gpw_password'], $new_password_hash );
      
    }
    
    // cleanup testusers
    $this->CI->crud->table('cfg_users')->delete(array('id'=>$cleanup_ids));
  }
  
}


?>