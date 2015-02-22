<?php

class ApiTestModel extends CIUnit_Framework_TestCase {

  protected $apiModel=FALSE;

  /**
   * Users for testing
   */
  protected $users=array(
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
  
  /**
   * Tables for testing
   */
  protected $tables = array('tbl_site','tbl_menu','cfg_users','log_login','res_media_files');
  
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
    error_reporting(E_ALL - E_NOTICE); // skip session notices
    $this->CI->load->library('user');
    $this->CI->load->model('api/ApiModel');
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
  
  protected function _message($message,$args=FALSE,$user=FALSE) {
    $message='<span class="text-warning">'.$message;
    if ($args) $message.=' $args='.array2php($args,0,'');
    if ($user) $message.=' $username=`'.$user['username'].'`';
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
   * Actuel testing with auth and asserts...
   *
   * @param string $apiModel 
   * @return void
   * @author Jan den Besten
   */
  public function _testWithAuth( $params=array('model'=>'','args'=>array(),'asserts'=>array()) ) {
    $apiModel=$params['model'];
    
    // test for all users
    foreach ($this->users as $user) {
      // login
      $this->CI->user->login($user['username'], $user['password']);
      
      // which tables?
      if (isset($params['args']['table'])) {
        $tables=$params['args']['table'];
        if (!is_array($tables)) $tables=array($tables);
      }
      else {
        $tables=$this->tables;
      }
      
      foreach ($tables as $table) {
        $args=array_merge( $params['args'], array('table'=>$table) );
        
        $this->CI->$apiModel->set_args($args);
        $result=$this->CI->$apiModel->index();
        
        if (empty($table) or in_array($table,$user['tables'])) {

          // user has rights for this table
          $this->assertArrayNotHasKey( 'status', $result );
          $this->assertArrayHasKey( 'success', $result );
          $this->assertEquals( true, $result['success'] );

          // args
          $this->assertArrayHasKey( 'args', $result );
          $this->assertInternalType( 'array', $result['args'] );
          $this->assertEquals( $args, $result['args'] );
          
          // data
          $this->assertArrayHasKey( 'data', $result );
          $this->assertInternalType( 'array', $result['data'] );

          // other asserts
          foreach ( $params['asserts'] as $key => $assert) {
            $keys=explode('|',$key);
            if (count($keys)<=1) $keys=$key;
            $keyResult=el($keys,$result);
            
            foreach ($assert as $type => $value) {
              switch ($type) {

                case 'type':
                  $this->assertInternalType(
                    $value,
                    $keyResult,
                    $this->_message('Type of <b>'.$key.'</b> in <i>result</i> needs to be <b>'.$value.'</b>.', $args, $user)
                  );
                  break;

                case 'hasKey':
                  $this->assertArrayHasKey(
                    $value,
                    $keyResult,
                    $this->_message('<b>'.$key.'</b> in <i>result</i> should have a key <b>'.$value.'</b>.', $args, $user)
                  );
                  break;

                case 'count':
                trace_([$keys,$value,$keyResult]);
                  $this->assertCount(
                    $value,
                    $keyResult,
                    $this->_message('<b>'.$key.'</b> in <i>result</i> should have '.$value.' keys.', $args, $user)
                  );
                  break;
                  
                case 'countGreaterOrEqual':
                // trace_([$keys,$keyResult,$count,$value]);
                  $count=count($keyResult);
                  $this->assertGreaterThanOrEqual(
                    $value,
                    $count,
                    $this->_message('<b>'.$key.'</b> in <i>result</i> should have '.$value.' or more keys.', $args, $user)
                  );
                  break;
                  
                case 'Equals':
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
          $this->assertCount( 1, $result, $this->_message('Result without AUTH is more than 1',$args,$user) );
          $this->assertArrayHasKey( 'status', $result );
          $this->assertEquals( 401, $result['status'] );
        }
      }
    }
  }
  
  
  
  
  
  

}


?>