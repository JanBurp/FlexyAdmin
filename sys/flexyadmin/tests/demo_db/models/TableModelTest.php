<?php

require_once('sys/flexyadmin/tests/CITestCase.php');


class TableModelTest extends CITestCase {
  
  protected function setUp ()  {
    $this->CI->load->model('tables/table_model');
    $this->CI->load->model('tables/tbl_menu');
  }
  
  protected function tearDown() {
  }
  
  
  /**
   * Test of normale db functies werken en dat goede return waarden
   *
   * @return void
   * @author Jan den Besten
   */
  public function test_db() {
    // Moet array teruggeven
    $result = $this->CI->table_model->list_tables();
    $this->assertInternalType( 'array', $result);
    
    // Standard model:
    
    // tbl_menu
    $this->CI->table_model->table('tbl_menu');
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','str_module','stx_description','str_keywords'), $this->CI->table_model->list_fields() );
    $query = $this->CI->table_model->get();
    $this->assertEquals( 5, $query->num_rows() );
    $this->assertEquals( 9, $query->num_fields() );
    // tbl_links
    $this->CI->table_model->table('tbl_links');
    $this->assertEquals( array('id','str_title','url_url'), $this->CI->table_model->list_fields() );
    $query = $this->CI->table_model->get();
    $this->assertEquals( 3, $query->num_rows() );
    $this->assertEquals( 3, $query->num_fields() );
    
    // tbl_menu Model:
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','str_module','stx_description','str_keywords'), $this->CI->tbl_menu->list_fields() );
    $query = $this->CI->tbl_menu->get(2);
    $this->assertEquals( 2, $query->num_rows() );
    $this->assertEquals( 9, $query->num_fields() );

    $query = $this->CI->tbl_menu->where( 'order <=', '2' )->get();
    $this->assertEquals( 3, $query->num_rows() );

    $query = $this->CI->tbl_menu->get_one( 1 );
    $this->assertEquals( 1, $query->num_rows() );

    $query = $this->CI->tbl_menu->where( 'order <=', '2' )->get_one( 1 );
    $this->assertEquals( 1, $query->num_rows() );

    $query = $this->CI->tbl_menu->get_one_by( 'uri',  'test' );
    $this->assertEquals( 0, $query->num_rows() );
  }
  

  // public function test_abstractfields() {
  //   $fields = $this->CI->tbl_menu->list_fields();
  //   $abstract_fields = $this->CI->tbl_menu->get_abstract_fields( $fields );
  //   $this->assertEquals( array('str_title','str_module'), $abstract_fields );
  // }


}

?>