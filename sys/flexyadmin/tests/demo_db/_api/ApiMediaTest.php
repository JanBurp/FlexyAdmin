<?php require_once("ApiTestModel.php");

class ApiMediaTest extends ApiTestModel {

  private $paths = array('pictures','downloads');
  // private $upload_path = '/test_afbeeldingen/test_groot';

  public function __construct() {
    parent::__construct('media');
    // $this->upload_path = $_SERVER['DOCUMENT_ROOT'].$this->upload_path;
  }

  public function testWithoutLogin() {
    $this->_testWithoutAuth('media');
  }


  public function testWithLogin() {
    
    // First login
    $user=current($this->users);
    $this->CI->flexy_auth->login( $user['username'], $user['password'] );
    
    // Test with wrong paths
    for ($i=0; $i < 2; $i++) {
      $path = random_string();
      $this->CI->media->set_args( array('path'=>$path) );
      $result = $this->CI->media->index();
      $this->assertArrayHasKey( 'status', $result );
      $this->assertEquals( '401', $result['status'] );
    }
    
    // Test with good folders & cfg
    foreach ($this->paths as $path) {
      $settings=FALSE;
      $this->CI->media->set_args( array('path'=>$path, 'settings'=>$settings) );
      $result = $this->CI->media->index();
      
      $this->assertArrayNotHasKey( 'status', $result );
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( true, $result['success'] );
      $this->assertArrayNotHasKey( 'error', $result );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertInternalType( 'array', $result['data'] );
      // settings
      if ($settings) {
        $this->assertArrayHasKey( 'settings', $result );
        $this->assertInternalType( 'array', $result['settings'] );
        $this->assertArrayHasKey( 'path', $result['settings'] );
        $this->assertArrayHasKey( 'img_info', $result['settings'] );
      }
      else {
        $this->assertArrayNotHasKey( 'settings', $result );
      }
    }
    
    
    // Test update some files
    $files=$this->CI->assets->get_files('pictures');
    $files=array_slice($files,0,3);
    foreach ($files as $id => $file) {
      $path=$file['path'];
      $name=$file['file'];
      $old_title=$file['alt'];
      $new_title=random_string();
      // update
      $this->CI->media->set_args( array('POST'=>array('path'=>$path, 'where'=>$name, 'data'=>array('alt'=>$new_title)) ) );
      $result = $this->CI->media->index();
      
      $this->assertArrayNotHasKey( 'status', $result );
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( true, $result['success'] );
      $this->assertArrayNotHasKey( 'error', $result );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertEquals( true, $result['data'] );
    }
    // Test update with wrong args
    for ($i=0; $i < 3 ; $i++) { 
      $name=random_string();
      $new_title=random_string();
      // update
      $this->CI->media->set_args( array('POST'=>array('path'=>$path, 'where'=>$name, 'data'=>array('alt'=>$new_title)) ) );
      $result = $this->CI->media->index();
      
      $this->assertArrayNotHasKey( 'status', $result );
      $this->assertArrayHasKey( 'success', $result );
      $this->assertEquals( false, $result['success'] );
      $this->assertArrayHasKey( 'error', $result );
      $this->assertArrayHasKey( 'data', $result );
      $this->assertEquals( false, $result['data'] );
    }
    
    // UPLOADING files
    // $upload_files=scandir($this->upload_path);
    // $upload_files=array_slice($upload_files,2,2);
    // $this->CI->load->view('../upload',array('files'=>$upload_files));
    // foreach ($upload_files as $file) {
    //   trace_($this->upload_path.'/'.$file);
    //   $_FILES      = array( 'file' => array(
    //     'name'     => $file,
    //     'tmp_name' => '/tmp/php42up23',
    //     // 'type'     => 'text/plain',
    //     // 'size'     => 42,
    //     // 'error'    => 0
    //   ));
    //
    // };


    
    
    
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
    for ($i=0; $i < 3; $i++) { 
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
  
}


?>
