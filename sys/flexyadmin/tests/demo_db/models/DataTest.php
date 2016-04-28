<?php

require_once('sys/flexyadmin/tests/CITestCase.php');


class DataTest extends CITestCase {
  
  protected function setUp ()  {
    $this->CI->load->model('data/data');
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
    $result = $this->CI->data->list_tables();
    $this->assertInternalType( 'array', $result);
    
    // tbl_menu
    $this->CI->data->table( 'tbl_menu' );
    // ->list_fields()
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','medias_fotos','b_visible','str_module','stx_description','str_keywords'), $this->CI->data->list_fields() );
    // ->get()
    $query = $this->CI->data->get();
    $this->assertEquals( 5, $query->num_rows() );
    $this->assertEquals( 11, $query->num_fields() );

    // tbl_links
    $this->CI->data->table('tbl_links');
    // ->list_fields()
    $this->assertEquals( array('id','str_title','url_url'), $this->CI->data->list_fields() );
    // ->get()
    $query = $this->CI->data->get();
    $this->assertEquals( 3, $query->num_rows() );
    $this->assertEquals( 3, $query->num_fields() );
    
    // tbl_menu, nog een keer om te kijk of wisseling goed gaat
    $this->CI->data->table('tbl_menu');
    // ->list_fields()
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','medias_fotos','b_visible','str_module','stx_description','str_keywords'), $this->CI->data->list_fields() );
    // ->get(2)
    $query = $this->CI->data->get(2);
    $this->assertEquals( 2, $query->num_rows() );
    $this->assertEquals( 11, $query->num_fields() );
    // ->where()
    $query = $this->CI->data->where( 'order <=', '2' )->get();
    $this->assertEquals( 3, $query->num_rows() );
  }
  
  public function test_settings() {
    $this->CI->data->table( 'tbl_menu' );
    // null
    $this->assertEquals( null, $this->CI->data->get_setting( 'not_a_setting' ) );
    // one settings
    $this->assertEquals( 'tbl_menu', $this->CI->data->get_setting( 'table' ) );
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','medias_fotos','b_visible','str_module','stx_description','str_keywords'), $this->CI->data->get_setting( 'fields' ) );
    $this->assertEquals( array( 'str_title','str_module'), $this->CI->data->get_setting( 'abstract_fields' ) );
    $this->assertEquals( 'order', $this->CI->data->get_setting( 'order_by' ) );
  }
  

  public function test_abstractfields() {
    // tbl_menu
    $this->CI->data->table( 'tbl_menu' );
    // ->get_abstract_fields()
    $abstract_fields = $this->CI->data->get_abstract_fields();
    $this->assertEquals( array('str_title','str_module'), $abstract_fields );
    // ->get_compiled_abstract_select()
    $abstract_fields_sql  = $this->CI->data->get_compiled_abstract_select();
    $this->assertEquals( "CONCAT_WS(' | ',`tbl_menu`.`str_title`,`tbl_menu`.`str_module`) AS `abstract`", $abstract_fields_sql );
    // ->select_abstract()
    $query = $this->CI->data->select_abstract()->get();
    $this->assertEquals( 5, $query->num_rows() );
    $this->assertEquals( 2, $query->num_fields() );
    
    // tbl_links
    $this->CI->data->table( 'tbl_links' );
    // ->get_abstract_fields()
    $abstract_fields = $this->CI->data->get_abstract_fields();
    $this->assertEquals( array('str_title'), $abstract_fields );
  }
  
  
  public function test_get_options() {
    // tbl_menu.str_module
    $this->CI->data->table( 'tbl_menu' );
    $options = $this->CI->data->get_options( 'str_module' );
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'data', $options );
    // $this->assertArrayHasKey( 'multiple', $options );
    // $this->assertEquals( false, $options['multiple'] );
    $this->assertEquals( 3, count($options['data']) );
    // tbl_menu
    $options = $this->CI->data->get_options();
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'str_module', $options );
    $this->assertGreaterThanOrEqual( 1, count($options) );
  }
  
  
  
  public function test_setting_with() {
    // tbl_leerlingen
    $this->CI->data->table( 'tbl_leerlingen' );
    // ->get_with()
    $with = $this->CI->data->get_with();
    $this->assertEquals( array(), $with );
    
    // ->with( 'many_to_one' )
    $expected = array(
      'many_to_one'=>array(
        'id_adressen' => array(
          'fields'  =>  array('id','str_address','str_zipcode','str_city'),
          'table'   =>  'tbl_adressen',
          'as'      =>  'tbl_adressen',
          'json'    =>  false,
          'flat'=>false
        ),
        'id_groepen'  => array(
          'fields'  =>  array('id','order','str_title','str_soort','media_tekening','rgb_kleur'),
          'table'   =>  'tbl_groepen',
          'as'      =>  'tbl_groepen',
          'json'    =>  false,
          'flat'    =>  false
        ),
      )
    );
    $this->CI->data->with( 'many_to_one' );
    $this->assertEquals( $expected, $this->CI->data->get_with() );
    
    // ->with( 'many_to_one', array() );
    $this->CI->data->with( 'many_to_one', array() );
    $this->assertEquals( $expected, $this->CI->data->get_with() );
    
    // ->with( 'many_to_one', array( 'tbl_adressen') );
    $this->CI->data->with( 'many_to_one', array( 'id_adressen') );
    $this->assertEquals( $expected, $this->CI->data->get_with() );
    
    // ->reset()
    $this->CI->data->reset();
    
    // ->get_with()
    $this->assertEquals( array(), $this->CI->data->get_with() );
    
    // ->with( 'many_to_one', array( 'tbl_adressen') );
    $expected = array(
      'many_to_one'=>array(
        'id_adressen' => array(
          'fields'  =>  array('id','str_address','str_zipcode','str_city'),
          'table'   =>  'tbl_adressen',
          'as'      =>  'tbl_adressen',
          'json'    =>  false,
          'flat'    =>  false
        ),
      )
    );
    $this->CI->data->with( 'many_to_one', array( 'id_adressen') );
    $this->assertEquals( $expected, $this->CI->data->get_with() );
  }
  
  
  
  public function test_many_to_one_data() {
    $this->CI->data->table( 'tbl_leerlingen' );
    $grid_set = $this->CI->data->get_setting('grid_set');
    $this->assertInternalType( 'array', $grid_set );
    $this->assertInternalType( 'array', $grid_set['relations']['many_to_one'] );
    $this->assertEquals( 2, count($grid_set['relations']['many_to_one']) );

    // tbl_leerlingen - abstract
    $query = $this->CI->data->table( 'tbl_leerlingen' )
                                  ->select( 'id,str_first_name' )
                                  ->with( 'many_to_one', array( 'id_adressen' => 'abstract') )
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
    $this->assertEquals( array('id','str_first_name','tbl_adressen.abstract'), $keys );
    // klopt abstract?
    $this->assertInternalType( 'string', $row['tbl_adressen.abstract'] );

    // tbl_leerlingen - full (automatic 'id')
    $query = $this->CI->data->select('str_first_name')->with( 'many_to_one', array('id_adressen'=>'str_address') )->get();
    $this->assertEquals( 92, $query->num_rows() );
    $this->assertEquals( 4, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 92, count($array) );
    $row = current($array);
    $this->assertEquals( 4, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_first_name','tbl_adressen.id','tbl_adressen.str_address'), $keys );
    $this->assertInternalType( 'string', $row['tbl_adressen.str_address'] );

    // tbl_leerlingen ->get_result()
    $array = $this->CI->data->select('str_first_name')->with( 'many_to_one', array('id_adressen'=>'str_address') )->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertEquals( 92, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_first_name','tbl_adressen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_adressen'] );
    $this->assertEquals( 2, count($row['tbl_adressen']) );

    // tbl_leerlingen ->where()->get_result()
    $array = $this->CI->data->select('str_first_name')
                            ->with( 'many_to_one', array('id_adressen'=>'str_address') )
                            ->where('tbl_adressen.str_address','Schooolstraat 1')->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertLessThan( 92, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_first_name','tbl_adressen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_adressen'] );
    $this->assertEquals( 2, count($row['tbl_adressen']) );
    $this->assertEquals( 'Schooolstraat 1', $row['tbl_adressen']['str_address'] );
  }
  
  public function test_one_to_many_data() {
    // tbl_adressen
    $query = $this->CI->data->table( 'tbl_adressen' )
                            ->select( 'id,str_city' )
                            ->with( 'one_to_many', ['tbl_leerlingen'=>array('str_first_name','str_last_name')] )
                            ->get();
    $this->assertEquals( 92, $query->num_rows() );
    $this->assertEquals( 5, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 92, count($array) );
    $row = current($array);
    $this->assertEquals( 5, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_city','tbl_leerlingen.id','tbl_leerlingen.str_first_name','tbl_leerlingen.str_last_name'), $keys );

    // tbl_adressen - absract
    $query = $this->CI->data->select( 'id,str_city' )
                            ->with( 'one_to_many', ['tbl_leerlingen'=>'abstract'] )
                            ->get();
    $this->assertEquals( 92, $query->num_rows() );
    $this->assertEquals( 4, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 92, count($array) );
    $row = current($array);
    $this->assertEquals( 4, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_city','tbl_leerlingen.id','tbl_leerlingen.abstract'), $keys );
    // klopt abstract?
    $this->assertInternalType( 'string', $row['tbl_leerlingen.abstract'] );

    // tbl_adressen ->get_result()
    $array = $this->CI->data->select( 'id,str_city')
                            ->with( 'one_to_many', array('tbl_leerlingen'=>'str_first_name') )
                            ->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertEquals( 14, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_city','tbl_leerlingen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_leerlingen'] );
    $this->assertEquals( 12, count($row['tbl_leerlingen']) );
   
    // tbl_adressen ->where()->get_result()
    $array = $this->CI->data->select('str_city')
                            ->with( 'one_to_many', array('tbl_leerlingen'=>'str_first_name') )
                            ->where('tbl_leerlingen.str_first_name','Adam')
                            ->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertLessThan( 2, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_city','tbl_leerlingen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_leerlingen'] );
    $this->assertEquals( 1, count($row['tbl_leerlingen']) );
    $this->assertEquals( 'Adam', $row['tbl_leerlingen'][2]['str_first_name'] );
  }
  
  
  public function test_many_to_many_data() {
    // tbl_groepen - abstract
    $query = $this->CI->data->table( 'tbl_groepen' )
                                  ->select('str_title')
                                  ->with( 'many_to_many', array('rel_groepen__adressen' => 'abstract') )
                                  ->get();
    $this->assertEquals( 48, $query->num_rows() );
    $this->assertEquals( 4, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 48, count($array) );
    $row = current($array);
    $this->assertEquals( 4, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_title','tbl_adressen.id','tbl_adressen.abstract'), $keys );
    // klopt abstract?
    $this->assertInternalType( 'string', $row['tbl_adressen.abstract'] );

    // tbl_groepen - full
    $query = $this->CI->data->select('str_title')
                                  ->with( 'many_to_many', array('rel_groepen__adressen'=>'str_address') )
                                  ->get();
    $this->assertEquals( 48, $query->num_rows() );
    $this->assertEquals( 4, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 48, count($array) );
    $row = current($array);
    $this->assertEquals( 4, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_title','tbl_adressen.id','tbl_adressen.str_address'), $keys );
    $this->assertInternalType( 'string', $row['tbl_adressen.str_address'] );

    // tbl_groepen - grouped
    $query = $this->CI->data->select('str_title')
                            ->with_json( 'many_to_many', array('rel_groepen__adressen'=>'str_address') )
                            ->get();
    $this->assertEquals( 8, $query->num_rows() );
    $this->assertEquals( 3, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 8, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_title','tbl_adressen.json'), $keys );
    $this->assertInternalType( 'string', $row['tbl_adressen.json'] );

    // tbl_groepen ->get_result()
    $array = $this->CI->data->select('str_title')->with( 'many_to_many', array('rel_groepen__adressen'=>'str_address') )->get_result();
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
    $array = $this->CI->data->select('str_title')
                                  ->with( 'many_to_many', array('rel_groepen__adressen'=>array('str_address')) )
                                  ->where( 'tbl_adressen.str_address', 'Schooolstraat 1')
                                  ->get_result();
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
    $array = $this->CI->data->select('str_title')
                                  ->with( 'many_to_many', array('rel_groepen__adressen'=>'str_address') )
                                  ->where_exists( 'tbl_adressen.str_address', 'Schooolstraat 1')
                                  ->get_result();
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
  
  public function test_find() {
    $this->CI->data->table('tbl_leerlingen');
    // ruw
    $this->CI->data->find('va');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 25, $info['num_rows'] );
    // word boundaries
    $this->CI->data->find('va',null,array('word_boundaries'=>TRUE));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 0, $info['num_rows'] );
    // specific fields
    $this->CI->data->find('va',array('str_middle_name'));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 25, $info['num_rows'] );
    // specific fields & word boundaries
    $this->CI->data->find('van',array('str_middle_name'),array('word_boundaries'=>TRUE));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 25, $info['num_rows'] );
    // specific fields & word boundaries
    $this->CI->data->find('va',array('str_middle_name'),array('word_boundaries'=>TRUE));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 0, $info['num_rows'] );

    // meerdere termen los (string)
    $this->CI->data->find('van den');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 27, $info['num_rows'] );
    // meerdere termen los (array string)
    $this->CI->data->find( array('van den') );
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 27, $info['num_rows'] );
    // meerdere termen los (array)
    $this->CI->data->find( array('van','den') );
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 27, $info['num_rows'] );
    // meerdere termen vast
    $this->CI->data->find('"van den"');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 2, $info['num_rows'] );
    // meerdere termen AND
    $this->CI->data->find('van den',null, array('and'=>TRUE));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 2, $info['num_rows'] );

    // Zoeken in many_to_one 'va'
    $this->CI->data->with( 'many_to_one' );
    $this->CI->data->find('va');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 48, $info['num_rows'] );
    // Zoeken in many_to_one 'van'
    $this->CI->data->with( 'many_to_one' );
    $this->CI->data->find('van');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 25, $info['num_rows'] );
    // Zoeken in many_to_one 'vak'
    $this->CI->data->with( 'many_to_one' );
    $this->CI->data->find('vak');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 32, $info['num_rows'] );
    // Zoeken in many_to_one 'straat'
    $this->CI->data->with( 'many_to_one' );
    $this->CI->data->find('straat');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 6, $info['num_rows'] );
    // Zoeken in many_to_one 'straat' word_boundaries
    $this->CI->data->with( 'many_to_one' );
    $this->CI->data->find('straat',null,array('word_boundaries'=>TRUE));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 0, $info['num_rows'] );
    
    // // Zoeken in one_to_many 'van' (LET OP query resultaat omdat sommige dubbel kunnen zijn, get_result geeft dan ander aantal)
    // $this->CI->data->table( 'tbl_adressen' );
    // $this->CI->data->with( 'one_to_many' );
    // $this->CI->data->find( 'van' );
    // $query = $this->CI->data->get();
    // // trace_( $query->result_array());
    // // trace_( $this->CI->data->last_query());
    // $info = $this->CI->data->get_query_info();
    // $this->assertEquals( 25, $info['num_rows'] );
    // // Zoeken in many_to_many 'van' ->word_boundaries
    // $this->CI->data->table( 'tbl_adressen' );
    // $this->CI->data->with( 'one_to_many' );
    // $this->CI->data->find( 'van', null, array('word_boundaries'=>TRUE));
    // $query = $this->CI->data->get();
    // $info = $this->CI->data->get_query_info();
    // $this->assertEquals( 25, $info['num_rows'] );
    //
    

    // Zoeken in many_to_many 'straat' (LET OP query resultaat omdat sommige dubbel kunnen zijn, get_result geeft dan ander aantal)
    // $this->CI->data->table( 'tbl_groepen' );
   //  $this->CI->data->with( 'many_to_many' );
   //  $this->CI->data->find( 'straat' );
   //  $query = $this->CI->data->get();
   //  $info = $this->CI->data->get_query_info();
   //  $this->assertEquals( 4, $info['num_rows'] );
   //  // Zoeken in many_to_many 'straat' ->word_boundaries
   //  $this->CI->data->table( 'tbl_groepen' );
   //  $this->CI->data->with( 'many_to_many' );
   //  $this->CI->data->find('straat', null, array('word_boundaries'=>TRUE));
   //  $query = $this->CI->data->get();
   //  $info = $this->CI->data->get_query_info();
   //  $this->assertEquals( 0, $info['num_rows'] );
  }
  
  
  public function test_crud() {
    $this->CI->data->table('tbl_crud');

    // INSERT
    $random_string = 'INSERT '.random_string();
    $this->CI->data->set( array('str_insert'=>$random_string ) );
    $this->CI->data->insert();
    $insert_id = $this->CI->data->insert_id();
    $this->assertGreaterThan( 0, $insert_id ); // Succes of niet...
    // check of de data id hetzelfde is
    $value = $this->CI->data->get_field( 'str_insert', array('id'=>$insert_id));
    $this->assertEquals( $value, $random_string );

    // UPDATE
    $random_string = 'UPDATE '.random_string();
    $this->CI->data->set( array('str_update'=>$random_string ) );
    $this->CI->data->where( 'id', $insert_id );
    $this->CI->data->update();
    // check of de data hetzelfde is
    $this->assertEquals( 1, $this->CI->data->affected_rows() );
    $value = $this->CI->data->get_field( 'str_update', array('id'=>$insert_id));
    $this->assertEquals( $value, $random_string );

    // UPDATE BOTH in een aanroep
    $insert_string = 'INSERT '.random_string();
    $update_string = 'UPDATE '.random_string();
    $this->CI->data->update( array( 'str_insert'=>$insert_string, 'str_update'=>$update_string ), array( 'id' => $insert_id ) );
    // check of de data hetzelfde is
    $this->assertEquals( 1, $this->CI->data->affected_rows() );
    $row = $this->CI->data->select('id,str_insert,str_update')->get_row( array('id'=>$insert_id) );
    $this->assertEquals( array( 'id'=>$insert_id, 'str_insert'=>$insert_string, 'str_update'=>$update_string ), $row );

    // UPDATE Meerdere
    $insert_string = 'INSERT '.random_string();
    $update_string = 'UPDATE '.random_string();
    $this->CI->data->like( 'str_update', 'UPDATE', 'both');
    $this->CI->data->update( array( 'str_insert'=>$insert_string, 'str_update'=>$update_string ) );
    // check of de data hetzelfde is
    $this->assertGreaterThanOrEqual( 1, $this->CI->data->affected_rows() );
    $row = $this->CI->data->select('id,str_insert,str_update')->get_row( array('id'=>$insert_id) );
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
    // trace_($set);
    $this->CI->data->insert( $set );
    $insert_id = $this->CI->data->insert_id();
    // check of de data hetzelfde is
    $this->assertGreaterThanOrEqual( 1, $insert_id );
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( $nr_others, $info['affected_rel_rows'] );
    $row = $this->CI->data->select('id,str_insert,str_update')->with('many_to_many')->where( 'tbl_crud.id',$insert_id )->get_row();
    $this->assertInternalType( 'array', $row );
    $this->assertEquals( $insert_id, $row['id'] );
    $this->assertEquals( $insert_string, $row['str_insert'] );
    $this->assertInternalType( 'array', $row['tbl_crud2'] );
    $this->assertLessThanOrEqual( $nr_others, count($row['tbl_crud2']) );

    // DELETE
    $this->CI->data->like( 'str_update', 'UPDATE', 'both');
    $this->CI->data->limit( 1 );
    $this->CI->data->delete();
    $this->assertEquals( 1, $this->CI->data->affected_rows() );
    // Mag geen many_to_many relatie meer bestaan
    $affected_ids = $this->CI->data->get_query_info('affected_ids');
    $this->CI->data->table('rel_crud__crud2');
    $this->CI->data->where_in( 'id_crud', $affected_ids );
    $result = $this->CI->data->get_result();
    $this->assertEquals( array(), $result );
  }
  
  
  public function test_grid_set() {

    // Page1
    $this->CI->data->table('tbl_leerlingen');
    $page1 = $this->CI->data->get_grid();
    $info = $this->CI->data->get_query_info();
    $this->assertInternalType( 'array', $page1 );
    $this->assertEquals( 20, count($page1) );
    $this->assertEquals( 20, $info['num_rows'] );
    $this->assertEquals( 92, $info['total_rows'] );
    $this->assertEquals( 0, $info['page'] );
    // Page2
    $page2 = $this->CI->data->get_grid( 20, 20 );
    $info = $this->CI->data->get_query_info();
    $this->assertInternalType( 'array', $page2 );
    $this->assertEquals( 20, count($page2) );
    $this->assertEquals( 20, $info['num_rows'] );
    $this->assertEquals( 92, $info['total_rows'] );
    $this->assertEquals( 1, $info['page'] );
    // page1 != page2
    $this->assertFalse(  $page1==$page2 );
    // Last page
    $last_page = $this->CI->data->get_grid( 20, 80 );
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 12, count($last_page) );
    $this->assertEquals( 12, $info['num_rows'] );
    $this->assertEquals( 92, $info['total_rows'] );
    $this->assertEquals( 4, $info['page'] );

    // order_by & abstract test
    $first = current($page1);
    $this->assertEquals( 'Aafje', $first['str_first_name'] );
    $this->assertEquals( 4, $first['id_adressen'] );
    $this->assertEquals( 'Rekenpark 42 | 1234IJ', $first['tbl_adressen.abstract'] );
    $this->assertEquals( 'Handvaardigheid | vak', $first['tbl_groepen.abstract'] );

    // DESC
    $result = $this->CI->data->get_grid( 0,0, '_str_first_name');
    $first = current($result);
    $this->assertEquals( 'Evy', $first['str_first_name'] );

    $result = $this->CI->data->get_grid( 0,0, 'str_last_name');
    $first = current($result);
    $this->assertEquals( 'Aalts', $first['str_last_name'] );

    $result = $this->CI->data->get_grid( 0,0, '_str_last_name');
    $first = current($result);
    $this->assertEquals( 'Evertsen', $first['str_last_name'] );
  }



}

?>