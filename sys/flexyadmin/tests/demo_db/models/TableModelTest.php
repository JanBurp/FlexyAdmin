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
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','b_visible','str_module','stx_description','str_keywords'), $this->CI->table_model->list_fields() );
    // ->get()
    $query = $this->CI->table_model->get();
    $this->assertEquals( 5, $query->num_rows() );
    $this->assertEquals( 10, $query->num_fields() );

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
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','b_visible','str_module','stx_description','str_keywords'), $this->CI->table_model->list_fields() );
    // ->get(2)
    $query = $this->CI->table_model->get(2);
    $this->assertEquals( 2, $query->num_rows() );
    $this->assertEquals( 10, $query->num_fields() );
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
    
    // tbl_links
    $this->CI->table_model->table( 'tbl_links' );
    // ->get_abstract_fields()
    $abstract_fields = $this->CI->table_model->get_abstract_fields();
    $this->assertEquals( array('str_title'), $abstract_fields );
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
    $this->CI->table_model->table( 'tbl_leerlingen' );
    $grid_set = $this->CI->table_model->get_setting('grid_set');
    $this->assertInternalType( 'array', $grid_set );
    $this->assertInternalType( 'array', $grid_set['with']['many_to_one'] );
    $this->assertEquals( 2, count($grid_set['with']['many_to_one']) );
    $this->assertEquals( 'abstract', $grid_set['with']['many_to_one']['tbl_groepen'] );
    $this->assertEquals( 'abstract', $grid_set['with']['many_to_one']['tbl_adressen'] );
    
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
    
    // INSERT many_to_many
    $insert_string = '_INSERT '.random_string();
    $update_string = '_UPDATE '.random_string();
    $nr_others = rand(3,6);
    $other_ids  = array();
    for ($i=0; $i < $nr_others; $i++) { 
      $other_ids[] = $this->CI->db->random_field_value( 'id_crud2', array() );
    }
    $set = array(
      'str_insert' => $insert_string,
      'str_update' => $update_string,
      'tbl_crud2'  => $other_ids,
    );
    $this->CI->table_model->insert( $set );
    $insert_id = $this->CI->table_model->insert_id();
    // check of de data hetzelfde is
    $this->assertGreaterThanOrEqual( 1, $insert_id );
    $this->assertEquals( $nr_others, $this->CI->table_model->get_query_info('affected_rel_rows') );
    $row = $this->CI->table_model->select('id,str_insert,str_update')->with('many_to_many')->where( 'tbl_crud.id',$insert_id )->get_row();
    $this->assertInternalType( 'array', $row );
    $this->assertEquals( $insert_id, $row['id'] );
    $this->assertEquals( $insert_string, $row['str_insert'] );
    $this->assertInternalType( 'array', $row['tbl_crud2'] );
    $this->assertLessThanOrEqual( $nr_others, count($row['tbl_crud2']) );
    
    // DELETE
    $this->CI->table_model->like( 'str_update', 'UPDATE', 'both');
    $this->CI->table_model->limit( 1 );
    $this->CI->table_model->delete();
    $this->assertEquals( 1, $this->CI->table_model->affected_rows() );
    // Mag geen many_to_many relatie meer bestaan
    $affected_ids = $this->CI->table_model->get_query_info('affected_ids');
    $this->CI->table_model->table('rel_crud__crud2');
    $this->CI->table_model->where_in( 'id_crud', $affected_ids );
    $result = $this->CI->table_model->get_result();
    $this->assertEquals( array(), $result );
  }
  
  
  
  
  public function test_grid_set() {
    
    // Page1
    $this->CI->table_model->table('tbl_leerlingen');
    $page1 = $this->CI->table_model->get_grid();
    $info = $this->CI->table_model->get_query_info();
    $this->assertInternalType( 'array', $page1 );
    $this->assertEquals( 20, count($page1) );
    $this->assertEquals( 20, $info['num_rows'] );
    $this->assertEquals( 92, $info['total_rows'] );
    $this->assertEquals( 0, $info['page'] );
    // Page2
    $page2 = $this->CI->table_model->get_grid( 1 );
    $info = $this->CI->table_model->get_query_info();
    $this->assertInternalType( 'array', $page2 );
    $this->assertEquals( 20, count($page2) );
    $this->assertEquals( 20, $info['num_rows'] );
    $this->assertEquals( 92, $info['total_rows'] );
    $this->assertEquals( 1, $info['page'] );
    // page1 != page2
    $this->assertFalse(  $page1==$page2 );
    // Last page
    $last_page = $this->CI->table_model->get_grid( 4 );
    $info = $this->CI->table_model->get_query_info();
    $this->assertEquals( 12, count($last_page) );
    $this->assertEquals( 12, $info['num_rows'] );
    $this->assertEquals( 92, $info['total_rows'] );
    $this->assertEquals( 4, $info['page'] );
    
    // order_by & abstract test
    $first = current($page1);
    $this->assertEquals( 'Aafje', $first['str_first_name'] );
    $this->assertEquals( 'Rekenpark 42|1234IJ', $first['tbl_adressen']['abstract'] );
    $this->assertEquals( 'Gym|vak', $first['tbl_groepen']['abstract'] );
    
    // DESC
    $result = $this->CI->table_model->get_grid(0, '_str_first_name');
    $first = current($result);
    $this->assertEquals( 'Evy', $first['str_first_name'] );
    //
    $result = $this->CI->table_model->get_grid(0, 'str_last_name');
    $first = current($result);
    $this->assertEquals( 'Aalts', $first['str_last_name'] );
    //
    $result = $this->CI->table_model->get_grid(0, '_str_last_name');
    $first = current($result);
    $this->assertEquals( 'Evertsen', $first['str_last_name'] );
    
    
  }
  


}

?>