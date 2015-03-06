<?php require_once("ApiTestModel.php");

class ApiMediaTest extends ApiTestModel {

  private $paths = array('pictures','downloads');

  public function __construct() {
    parent::__construct('media');
  }

  public function testWithoutLogin() {
    $this->_testWithoutAuth('media');
  }


  public function testWithLogin() {
    // First login
    $user=current($this->users);
    $this->CI->user->login($user['username'], $user['password']);
    
    // Test with wrong paths
    for ($i=0; $i < 10; $i++) {
      $path = random_string();
      $this->CI->media->set_args( array('path'=>$path) );
      $result = $this->CI->media->index();
      
      $this->assertArrayNotHasKey( 'status', $result );
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( false, $result['success'] );
      $this->assertArrayHasKey( 'error', $result );
    }
    
    // Test with good folders & cfg
    foreach ($this->paths as $path) {
      $config=FALSE;
      if ($path=='pictures') $config=array('media_info','img_info');
      if ($config)
        $this->CI->media->set_args( array('path'=>$path, 'config'=>$config) );
      else
        $this->CI->media->set_args( array('path'=>$path ) );
      $result = $this->CI->media->index();
      
      $this->assertArrayNotHasKey( 'status', $result );
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( true, $result['success'] );
      $this->assertArrayNotHasKey( 'error', $result );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertInternalType( 'array', $result['data'] );
      // cfg?
      if ($config) {
        $this->assertArrayHasKey( 'config', $result );
        $this->assertArrayHasKey( 'media_info', $result['config'] );
        $this->assertInternalType( 'array', $result['config']['media_info'] );
        $this->assertArrayHasKey( 'img_info', $result['config'] );
        $this->assertInternalType( 'array', $result['config']['media_info'] );
      }
      else {
        $this->assertArrayNotHasKey( 'config', $result );
      }
    }
    
    
    // Test update some files
    $files=$this->CI->mediatable->get_files('pictures',false);
    $files=array_slice($files,0,10);
    foreach ($files as $id => $file) {
      $path=$file['path'];
      $name=$file['file'];
      $old_title=$file['str_title'];
      $new_title=random_string();
      // update
      $this->CI->media->set_args( array('POST'=>array('path'=>$path, 'where'=>$name, 'data'=>array('str_title'=>$new_title)) ) );
      $result = $this->CI->media->index();
      
      $this->assertArrayNotHasKey( 'status', $result );
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( true, $result['success'] );
      $this->assertArrayNotHasKey( 'error', $result );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertEquals( true, $result['data'] );
    }
    // Test update with wrong args
    for ($i=0; $i < 10 ; $i++) { 
      $name=random_string();
      $new_title=random_string();
      // update
      $this->CI->media->set_args( array('POST'=>array('path'=>$path, 'where'=>$name, 'data'=>array('str_title'=>$new_title)) ) );
      $result = $this->CI->media->index();
      
      $this->assertArrayNotHasKey( 'status', $result );
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( false, $result['success'] );
      $this->assertArrayHasKey( 'error', $result );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertEquals( false, $result['data'] );
    }
    
    // Delete some files
    // $files=array_slice($files,0,2);
    // foreach ($files as $id => $file) {
    //   $path=$file['path'];
    //   $name=$file['file'];
    //   // delete
    //   $this->CI->media->set_args( array('POST'=>array('path'=>$path, 'where'=>$name) ) );
    //   $result = $this->CI->media->index();
    //
    //   $this->assertArrayNotHasKey( 'status', $result );
    //   $this->assertArrayHasKey( 'success', $result );
    //   $this->assertEquals( true, $result['success'] );
    //   $this->assertArrayNotHasKey( 'error', $result );
    //   $this->assertArrayHasKey( 'data', $result );
    //   $this->assertEquals( true, $result['data'] );
    // }

    // Try to delete wrong filenames
    for ($i=0; $i < 10; $i++) { 
      $name=random_string();
      // delete
      $this->CI->media->set_args( array('POST'=>array('path'=>$path, 'where'=>$name) ) );
      $result = $this->CI->media->index();
      $this->assertArrayNotHasKey( 'status', $result );
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( false, $result['success'] );
      $this->assertArrayHasKey( 'error', $result );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertEquals( false, $result['data'] );
    }

    
    
    
    
  }


  // public function testWithWrongParameters() {
  //   $this->_testWithWrongParameters('media');
  // }


  // public function testConfig() {
  //   // Test config of tbl_menu
  //   $this->_testWithAuth(array(
  //     'model'   => 'media',
  //     'args'    => array(
  //       'path'  => 'pictures',
  //       'config'=>array('media_info','img_info'),
  //     ),
  //     'asserts' => array(
  //       'config' => array( 'type'   => 'array' ),
  //       'config' => array( 'hasKey' => 'media_info' ),
  //       'config' => array( 'hasKey' => 'img_info' ),
  //       'config|media_info' => array( 'type' => 'array' ),
  //       'config|media_info' => array( 'type' => 'array' ),
  //     )
  //   ));
  // }
  //
  //
  //
  
}


?>
