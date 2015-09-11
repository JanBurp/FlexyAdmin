<?php

require_once('sys/flexyadmin/tests/CITestCase.php');


class TableModelTest extends CITestCase {
  
  protected function setUp ()  {
    $this->CI->load->model('tables/table_model');
    $this->CI->load->model('tables/tbl_menu');
  }
  
  protected function tearDown() {
  }
  
  
  public function testQueryBuilder() {
    $query = $this->CI->tbl_menu->get(2);
    $this->assertEquals( 2, $query->num_rows() );
    $this->assertEquals( 9, $query->num_fields() );

    $query = $this->CI->tbl_menu->where( 'order <=', '2' )
                                ->get();
    $this->assertEquals( 3, $query->num_rows() );

    $query = $this->CI->tbl_menu->get_one( 1 );
    $this->assertEquals( 1, $query->num_rows() );

    $query = $this->CI->tbl_menu->where( 'order <=', '2' )
                                ->get_one( 1 );
    $this->assertEquals( 1, $query->num_rows() );

    $query = $this->CI->tbl_menu->get_one_by( 'uri',  'test' );
    $this->assertEquals( 0, $query->num_rows() );
    
    // $result = $this->CI->tbl_menu->not_existing();
  }


}

?>