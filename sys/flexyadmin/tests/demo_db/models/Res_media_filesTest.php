<?php

require_once('sys/flexyadmin/tests/CITestCase.php');


class Res_media_filesTest extends CITestCase {
  
  protected function setUp ()  {
    $this->CI->load->model('tables/res_media_files');
  }
  
  protected function tearDown() {
  }
  
  public function test_delete() {
    $deleted_data = array(
      '25' => array(
        "id" => 25,
        "b_exists" => 1,
        "file" => "test_01.jpg",
        "path" => "pictures",
        "str_type" => "jpg",
        "str_title" => "2Tn3Rx06",
        "dat_date" => "2015-11-23",
        "int_size" => 60,
        "int_img_width" => 300,
        "int_img_height" => 400
      ),
      '26' => array(
        "id" => 26,
        "b_exists" => 1,
        "file" => "test_02.jpg",
        "path" => "pictures",
        "str_type" => "jpg",
        "str_title" => "vQjLW45d",
        "dat_date" => "2015-11-23",
        "int_size" => 33,
        "int_img_width" => 300,
        "int_img_height" => 225
      ),
      '27' => array(
        "id" => 27,
        "b_exists" => 1,
        "file" => "test_03.jpg",
        "path" => "pictures",
        "str_type" => "jpg",
        "str_title" => "mP4JqhR3",
        "dat_date" => "2015-11-23",
        "int_size" => 12,
        "int_img_width" => 300,
        "int_img_height" => 225
      ),
    );
    
    $this->assertCount( 3, $deleted_data );

    // $deleted_files = $this->CI->res_media_files->_delete_files( $deleted_data, TRUE );
    // $this->assertInternalType( 'array', $deleted_files );
    // $this->assertCount( 3, $deleted_files );
    // $this->assertEquals( 'test_01.jpg', $deleted_files[25]['file'] );

  }
  


}

?>