<?php

require_once(APPPATH.'/tests/CITestCase.php');

class Res_assetsTest extends CITestCase {
  
  protected function setUp ()  {
  }
  
  protected function tearDown() {
  }
  
  public function test_config() {
    $assets = $this->CI->assets->get_setting('assets');
    
    $this->assertEquals( 2, count($assets) );
    $this->assertArrayHasKey( 'downloads', $assets );
    $this->assertArrayHasKey( 'pictures', $assets );
    $this->assertArrayHasKey( 'types', $assets['pictures'] );
    $this->assertArrayHasKey( 'resize_img', $assets['pictures'] );
  }
  
  public function test_methods() {
    $paths = $this->CI->assets->get_assets_folders(FALSE);
    $this->assertEquals( 2, count($paths) );
    $this->assertContains( 'downloads', $paths );
    $this->assertContains( 'pictures', $paths );
  }
  


}

?>