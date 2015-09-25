<?php

require_once('sys/flexyadmin/tests/CITestCase.php');


class TableModelTest extends CITestCase {
  
  protected function setUp ()  {
    $this->CI->load->model('tables/table_model');
    // $this->CI->load->model('tables/tbl_menu');
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
    
    // tbl_menu
    $this->CI->table_model->table( 'tbl_menu' );
    // ->list_fields()
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','str_module','stx_description','str_keywords'), $this->CI->table_model->list_fields() );
    // ->get()
    $query = $this->CI->table_model->get();
    $this->assertEquals( 5, $query->num_rows() );
    $this->assertEquals( 9, $query->num_fields() );

    // tbl_links
    $this->CI->table_model->table('tbl_links');
    // ->list_fields()
    $this->assertEquals( array('id','str_title','url_url'), $this->CI->table_model->list_fields() );
    // ->get()
    $query = $this->CI->table_model->get();
    $this->assertEquals( 3, $query->num_rows() );
    $this->assertEquals( 3, $query->num_fields() );
    
    // tbl_menu, nog een keer om te kijk of wisseling goed gaat
    $this->CI->table_model->table('tbl_menu');
    // ->list_fields()
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','str_module','stx_description','str_keywords'), $this->CI->table_model->list_fields() );
    // ->get(2)
    $query = $this->CI->table_model->get(2);
    $this->assertEquals( 2, $query->num_rows() );
    $this->assertEquals( 9, $query->num_fields() );
    // ->where()
    $query = $this->CI->table_model->where( 'order <=', '2' )->get();
    $this->assertEquals( 3, $query->num_rows() );
  }
  

  public function test_abstractfields() {
    // tbl_menu
    $this->CI->table_model->table( 'tbl_menu' );
    // ->get_abstract_fields()
    $abstract_fields = $this->CI->table_model->get_abstract_fields();
    $this->assertEquals( array('str_title','str_module'), $abstract_fields );
    // ->get_compiled_abstract_select()
    $abstract_fields_sql  = $this->CI->table_model->get_compiled_abstract_select();
    $this->assertEquals( "CONCAT_WS('|',`tbl_menu`.`str_title`,`tbl_menu`.`str_module`) AS `abstract`", $abstract_fields_sql );
    // ->select_abstract()
    $query = $this->CI->table_model->select_abstract()->get();
    $this->assertEquals( 5, $query->num_rows() );
    $this->assertEquals( 2, $query->num_fields() );
  }
  
  
  public function test_get_options() {
    // tbl_menu.str_module
    $this->CI->table_model->table( 'tbl_menu' );
    $options = $this->CI->table_model->get_options( 'str_module' );
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'options', $options );
    $this->assertArrayHasKey( 'multiple_options', $options );
    $this->assertEquals( false, $options['multiple_options'] );
    $this->assertEquals( 3, count($options['options']) );
    // tbl_menu
    $options = $this->CI->table_model->get_options();
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'str_module', $options );
    $this->assertGreaterThanOrEqual( 1, count($options) );
  }
  
  
  
  public function test_setting_with() {
    // tbl_leerlingen
    $this->CI->table_model->table( 'tbl_leerlingen' );
    // ->get_with()
    $with = $this->CI->table_model->get_with();
    $this->assertEquals( array(), $with );
    // ->with( 'many_to_one' )
    $expected = array(
      'many_to_one'=>array(
        'tbl_adressen' => array('fields'=>array(),'grouped'=>false),
        'tbl_groepen'  => array('fields'=>array(),'grouped'=>false),
      )
    );
    $this->CI->table_model->with( 'many_to_one' );
    $this->assertEquals( $expected, $this->CI->table_model->get_with() );
    // ->with( 'many_to_one', array() );
    $this->CI->table_model->with( 'many_to_one', array() );
    $this->assertEquals( $expected, $this->CI->table_model->get_with() );
    // ->with( 'many_to_one', array( 'tbl_adressen') );
    $this->CI->table_model->with( 'many_to_one', array( 'tbl_adressen') );
    $this->assertEquals( $expected, $this->CI->table_model->get_with() );
    // ->reset()
    $this->CI->table_model->reset();
    // ->get_with()
    $this->assertEquals( array(), $this->CI->table_model->get_with() );
    // ->with( 'many_to_one', array( 'tbl_adressen') );
    $expected = array(
      'many_to_one'=>array(
        'tbl_adressen' => array('fields'=>array(),'grouped'=>false),
      )
    );
    $this->CI->table_model->with( 'many_to_one', array( 'tbl_adressen') );
    $this->assertEquals( $expected, $this->CI->table_model->get_with() );
  }
  
  
  
  public function test_many_to_one_data() {
    // tbl_leerlingen - abstract
    $query = $this->CI->table_model->table( 'tbl_leerlingen' )
                                   ->select('id,str_first_name')
                                   ->with( 'many_to_one', array('tbl_adressen' => 'abstract') )
                                   ->get();
    $this->assertEquals( 92, $query->num_rows() );
    $this->assertEquals( 3, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 92, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_first_name','tbl_adressen__abstract'), $keys );
    // klopt abstract?
    $this->assertInternalType( 'string', $row['tbl_adressen__abstract'] );
    
    // tbl_leerlingen - full (automatic 'id')
    $query = $this->CI->table_model->select('str_first_name')->with( 'many_to_one', array('tbl_adressen'=>'str_address') )->get();
    $this->assertEquals( 92, $query->num_rows() );
    $this->assertEquals( 3, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 92, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_first_name','tbl_adressen__str_address'), $keys );
    $this->assertInternalType( 'string', $row['tbl_adressen__str_address'] );

    // tbl_leerlingen ->get_result()
    $array = $this->CI->table_model->select('str_first_name')->with( 'many_to_one', array('tbl_adressen'=>'str_address') )->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertEquals( 92, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_first_name','tbl_adressen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_adressen'] );
    $this->assertEquals( 1, count($row['tbl_adressen']) );

    // tbl_leerlingen ->where()->get_result()
    $array = $this->CI->table_model->select('str_first_name')->with( 'many_to_one', array('tbl_adressen'=>'str_address') )
                                                             ->where('tbl_adressen.str_address','Schooolstraat 1')->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertLessThan( 92, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_first_name','tbl_adressen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_adressen'] );
    $this->assertEquals( 1, count($row['tbl_adressen']) );
    $this->assertEquals( 'Schooolstraat 1', $row['tbl_adressen']['str_address'] );
  }
  
  
  public function test_many_to_many_data() {
    // tbl_groepen - abstract
    $query = $this->CI->table_model->table( 'tbl_groepen' )
                                   ->select('str_title')
                                   ->with( 'many_to_many', array('tbl_adressen' => 'abstract') )
                                   ->get();
    $this->assertEquals( 52, $query->num_rows() );
    $this->assertEquals( 3, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 52, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_title','tbl_adressen__abstract'), $keys );
    // klopt abstract?
    $this->assertInternalType( 'string', $row['tbl_adressen__abstract'] );
    
    // tbl_groepen - full
    $query = $this->CI->table_model->select('str_title')->with( 'many_to_many', array('tbl_adressen'=>'str_address') )->get();
    $this->assertEquals( 52, $query->num_rows() );
    $this->assertEquals( 3, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 52, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_title','tbl_adressen__str_address'), $keys );
    $this->assertInternalType( 'string', $row['tbl_adressen__str_address'] );

    // tbl_groepen - grouped
    $query = $this->CI->table_model->select('str_title')->with_grouped( 'many_to_many', array('tbl_adressen'=>'str_address') )->get();
    $this->assertEquals( 8, $query->num_rows() );
    $this->assertEquals( 3, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 8, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_title','tbl_adressen'), $keys );
    $this->assertInternalType( 'string', $row['tbl_adressen'] );


    // tbl_groepen ->get_result()
    $array = $this->CI->table_model->select('str_title')->with( 'many_to_many', array('tbl_adressen'=>'str_address') )->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertEquals( 8, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_title','tbl_adressen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_adressen'] );
    $this->assertGreaterThan( 1, count($row['tbl_adressen']) );


    // tbl_groepen ->where()->get_result()
    $array = $this->CI->table_model->select('str_title')->with( 'many_to_many', array('tbl_adressen'=>'str_address') )
                                                        ->where( 'tbl_adressen.str_address', 'Schooolstraat 1')->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertLessThan( 8, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_title','tbl_adressen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_adressen'] );
    $this->assertEquals( 1, count($row['tbl_adressen']) );
    $sub=current($row['tbl_adressen']);
    $this->assertEquals( 'Schooolstraat 1', $sub['str_address'] );

    // tbl_groepen ->where_exists()->get_result()
    $array = $this->CI->table_model->select('str_title')->with( 'many_to_many', array('tbl_adressen'=>'str_address') )
                                                        ->where_exists( 'tbl_adressen.str_address', 'Schooolstraat 1')->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertLessThan( 8, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_title','tbl_adressen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_adressen'] );
    $this->assertGreaterThan( 1, count($row['tbl_adressen']) );
    $found = find_row_by_value( $row['tbl_adressen'], 'Schooolstraat 1', 'str_address');
    $this->assertGreaterThanOrEqual( 1, count($found) );
    $this->assertLessThan( count($row['tbl_adressen']), count($found) );

  }
  
  
  
  
  public function test_crud() {
    
    $this->CI->table_model->table('tbl_crud');
    
    // INSERT
    $random_string = 'INSERT '.random_string();
    $this->CI->table_model->set( array('str_insert'=>$random_string ) );
    $this->CI->table_model->insert();
    $insert_id = $this->CI->table_model->insert_id();
    $this->assertGreaterThan( 0, $insert_id ); // Succes of niet...
    // check of de data id hetzelfde is
    $value = $this->CI->table_model->get_field( 'str_insert', array('id'=>$insert_id));
    $this->assertEquals( $value, $random_string );
    
    // UPDATE
    $random_string = 'UPDATE '.random_string();
    $this->CI->table_model->set( array('str_update'=>$random_string ) );
    $this->CI->table_model->where( 'id', $insert_id );
    $this->CI->table_model->update();
    // check of de data hetzelfde is
    $this->assertEquals( 1, $this->CI->table_model->affected_rows() );
    $value = $this->CI->table_model->get_field( 'str_update', array('id'=>$insert_id));
    $this->assertEquals( $value, $random_string );

    // UPDATE BOTH in een aanroep
    $insert_string = 'INSERT '.random_string();
    $update_string = 'UPDATE '.random_string();
    $this->CI->table_model->update( array( 'str_insert'=>$insert_string, 'str_update'=>$update_string ), array( 'id' => $insert_id ) );
    // check of de data hetzelfde is
    $this->assertEquals( 1, $this->CI->table_model->affected_rows() );
    $row = $this->CI->table_model->select('id,str_insert,str_update')->get_row( array('id'=>$insert_id) );
    $this->assertEquals( array( 'id'=>$insert_id, 'str_insert'=>$insert_string, 'str_update'=>$update_string ), $row );

    // UPDATE Meerdere
    $insert_string = 'INSERT '.random_string();
    $update_string = 'UPDATE '.random_string();
    $this->CI->table_model->like( 'str_update', 'UPDATE', 'both');
    $this->CI->table_model->update( array( 'str_insert'=>$insert_string, 'str_update'=>$update_string ) );
    // check of de data hetzelfde is
    $this->assertGreaterThanOrEqual( 1, $this->CI->table_model->affected_rows() );
    $row = $this->CI->table_model->select('id,str_insert,str_update')->get_row( array('id'=>$insert_id) );
    $this->assertEquals( array( 'id'=>$insert_id, 'str_insert'=>$insert_string, 'str_update'=>$update_string ), $row );
    
    // INSERT / UPDATE many_to_many
    
    
    // DELETE
    $this->CI->table_model->like( 'str_update', 'UPDATE', 'both');
    $this->CI->table_model->limit( 1 );
    $this->CI->table_model->delete();
    $this->assertEquals( 1, $this->CI->table_model->affected_rows() );
    
    
    
    
    
  }
  


}

?>