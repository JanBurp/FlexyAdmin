<?php

require_once('sys/flexyadmin/tests/CITestCase.php');

class ApiTestModel extends CITestCase {

  protected $apiModel=FALSE;
  protected $numberOfWrongTests = 10;

  /**
   * Users for testing
   */
  protected $users=array(
    array(
      'username' => 'admin',
      'password' => 'admin',
      'tables'   => array('tbl_site','tbl_menu','tbl_links','cfg_users','res_media_files')
    ),
    array(
      'username' => 'user',
      'password' => 'user',
      'tables'   => array('tbl_site','tbl_menu','tbl_links')
    )
  );
  protected $test_users=array(
    array(
      'str_username'  => 'test',
      'id_user_group' => 3,
      'gpw_password'  => 'test',
      'email_email'   => 'test@flexyadmin.com',
    )
  );
  
  
  
  /**
   * Tables for testing
   */
  protected $tables = array('tbl_site','tbl_menu','cfg_users','res_media_files');
  
  /**
   * Test possible config loading
   */
  protected $config_settings = array(
    array(),
    array('table_info'),
    array('field_info'),
    array('table_info','field_info'),
  );
  
  /**
   * Global loading of externals & error reporting
   *
   * @author Jan den Besten
   */
  public function __construct($apiModel) {
    parent::__construct();
    error_reporting(E_ALL - E_NOTICE - E_WARNING); // skip session notices
    $this->CI->load->library('user');
    $this->CI->load->model('api/Api_Model');
    $this->apiModel=$apiModel;
    $this->CI->load->model('api/'.$apiModel);
  }
  

  /**
   * Setup & Teardown
   *
   * @return void
   * @author Jan den Besten
   */
  protected function setUp() { 
    $this->logout();
  }
  protected function tearDown() {
    $this->logout();
  }
  private function logout() {
    if ($this->CI->user->logged_in()) $this->CI->user->logout();
  }
  
  protected function setModel($apiModel) {
    $this->apiModel=$apiModel;
  }
  
  protected function _message($message,$args=FALSE,$user=FALSE,$result=FALSE) {
    $message='<span class="text-warning">'.$message;
    if ($args) $message.="\n".'$args='.array2php($args,0,'');
    if ($user) $message.="\n".'$username=`'.$user['username'].'`';
    if ($result) $message.="\n".'$result='.array2php($result,0,'');
    $message.='</span>';
    return $message;
  }
  

  /**
   * Testing Apis without auth
   *
   * @author Jan den Besten
   */
  protected function _testWithoutAuth($apiModel) {
    foreach ($this->users as $user) {
      foreach ($this->tables as $table) {
        $this->CI->$apiModel->set_args(array('table'=>$table));
        $result=$this->CI->$apiModel->index();
        $this->assertCount( 1, $result );
        $this->assertArrayHasKey( 'status', $result );
        $this->assertEquals( 401, $result['status'] );
      }
    }
  }
  
  
  /**
   * Test with wrong params
   *
   * @author Jan den Besten
   */
  protected function _testWithWrongParameters($apiModel) {
    // test for all users
    foreach ($this->users as $user) {
      // login
      $this->CI->user->login($user['username'], $user['password']);
      for ($i=0; $i < $this->numberOfWrongTests; $i++) {
        $args = $this->_randomArgs();
        $this->CI->$apiModel->set_args($args);
        $result=$this->CI->$apiModel->index();

        $this->assertArrayHasKey( 'success', $result );
        $this->assertEquals( false, $result['success'] );
        $this->assertArrayHasKey( 'error', $result );
        $this->assertInternalType( string, $result['error'] );
        $this->assertEquals( 'WRONG ARGUMENTS', $result['error'] );
      }
    }
  }
  
  private function _randomArgs() {
    $args=array();
    $numberOfArgs = rand(2,5);
    for ($i=0; $i < $numberOfArgs; $i++) { 
      $args[random_string()] = random_string();
    }
    return $args;
  }
 
  
  private function _tableFromArgs($args) {
    if (isset($args['table'])) {
      $tables=$args['table'];
    }
    elseif (isset($args['GET']['table'])) {
      $tables=$args['GET']['table'];
    }
    elseif (isset($args['POST']['table'])) {
      $tables=$args['POST']['table'];
    }
    else {
      $tables=$this->tables;
    }

    if (!is_array($tables)) $tables=array($tables);
    return $tables;
  }
  
  private function _tableInArgs($args,$table) {
    if (isset($args['GET'])) {
      $args['GET']['table']=$table;
      if ($args['GET']['table']=='') unset($args['GET']['table']);
    }
    elseif (isset($args['POST'])) {
      $args['POST']['table']=$table;
      if ($args['POST']['table']=='') unset($args['POST']['table']);
    }
    else {
      $args['table']=$table;
      if ($args['table']=='') unset($args['table']);
    }
    if (isset($args['table']) and $args['table']=='') unset($args['table']);
    return $args;
  }
  
  
  
  /**
   * Actuel testing with auth and asserts...
   *
   * @param string $apiModel 
   * @return void
   * @author Jan den Besten
   */
  public function _testWithAuth( $params=array('model'=>'','args'=>array(),'asserts'=>array()) ) {
    $apiModel=$params['model'];
    
    $results = array();
    
    // test for all users
    foreach ($this->users as $user) {
      // login
      $this->CI->user->login($user['username'], $user['password']);
      
      // which tables?
      $tables = $this->_tableFromArgs($params['args']);
      
      foreach ($tables as $table) {
        
        $args=$this->_tableInArgs($params['args'],$table);
        // trace_([$params,$table,$args]);
        
        $this->CI->$apiModel->set_args($args);
        $result    = $this->CI->$apiModel->index();
        $results[] = $result;
        
        // trace_([$args,$result]);
        
        // trace_([$table,$user]);
        if (empty($table) or in_array($table,$user['tables'])) {

          // trace_([$table,$args,$result,$user]);

          // user has rights for this table
          $this->assertArrayNotHasKey( 'status', $result );
          $this->assertArrayHasKey( 'success', $result );
          $this->assertEquals( true, $result['success'] );
          $this->assertArrayNotHasKey( 'error', $result );

          // args
          $this->assertArrayHasKey( 'args', $result );
          $this->assertInternalType( 'array', $result['args'] );
          
          // data
          $this->assertArrayHasKey( 'data', $result );
          // $this->assertInternalType( 'array', $result['data'] );

          // other asserts
          foreach ( $params['asserts'] as $key => $assert) {
            $keys=explode('|',$key);
            if (count($keys)<=1) $keys=$key;
            $keyResult=el($keys,$result);
            
            foreach ($assert as $type => $value) {
              switch ($type) {

                case 'type':
                  // trace_([$args,$result]);
                  $this->assertInternalType(
                    $value,
                    $keyResult,
                    $this->_message('Type of <b>'.$key.'</b> in <i>result</i> needs to be <b>'.$value.'</b>.', $args, $user)
                  );
                  break;

                case 'hasKey':
                  // trace_(['args'=>$args,'assert'=>$assert,'result'=>$result]);
                  $this->assertArrayHasKey(
                    $value,
                    $keyResult,
                    $this->_message('<b>'.$key.'</b> in <i>result</i> should have a key <b>'.$value.'</b>.', $args, $user)
                  );
                  break;

                case 'count':
                  // trace_(['args'=>$args,'result'=>$result]);
                  $this->assertCount(
                    $value,
                    $keyResult,
                    $this->_message('<b>'.$key.'</b> in <i>result</i> should have '.$value.' keys.', $args, $user, $result)
                  );
                  break;
                  
                case 'countGreaterOrEqual':
                  // trace_([$args,$result]);
                  $count=count($keyResult);
                  $this->assertGreaterThanOrEqual(
                    $value,
                    $count,
                    $this->_message('<b>'.$key.'</b> in <i>result</i> should have '.$value.' or more keys.', $args, $user)
                  );
                  break;
                  
                case 'Equals':
                  // trace_([$params,$table,$args]);
                  $this->assertEquals(
                    $value,
                    $keyResult,
                    $this->_message('<b>'.$key.'</b> in <i>result</i> should Equals<b>'.$value.'</b>.', $args, $user)
                  );
                  break;
                  

              }
            }
          }
        }
        else {
          // user has no rights for this table
          // trace_([$user,$args,$result]);
          $this->assertCount( 1, $result, $this->_message('Result without AUTH is more than 1 ',$args,$user) );
          $this->assertArrayHasKey( 'status', $result );
          $this->assertEquals( 401, $result['status'] );
        }
      }
    }
    
    return $results;
  }
  
  
  

}


?>