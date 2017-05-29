<?php

require_once(APPPATH.'/tests/CITestCase.php');


class DataTest extends CITestCase {
  
  protected function setUp ()  {
    $this->CI->load->model('data/data');
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
    // tbl_menu
    $this->CI->data->table( 'tbl_menu' );
    // ->list_fields()
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','medias_fotos','b_visible','str_module','stx_description','str_keywords'), $this->CI->data->list_fields() );
    // ->get()
    $query = $this->CI->data->get();
    $this->assertEquals( 6, $query->num_rows() );
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
    
    // gridset
    $grid_set = $this->CI->data->get_setting('grid_set');
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','medias_fotos','b_visible','str_module'), $grid_set['fields'] );
    $this->assertEquals( array(), $grid_set['with'] );
    $this->assertEquals( 'select', $grid_set['field_info']['str_module']['type'] );
    $this->assertEquals( 'media', $grid_set['field_info']['medias_fotos']['type'] );
    $this->assertEquals( 'pictures', $grid_set['field_info']['medias_fotos']['path'] );
    
    // formset
    $form_set = $this->CI->data->get_setting('form_set');
    $this->assertEquals( array('id','order','self_parent','uri','str_title','txt_text','medias_fotos','b_visible','str_module','stx_description','str_keywords'), $form_set['fields'] );
    $this->assertEquals( array(), $form_set['with'] );
    $this->assertEquals( 'select', $form_set['field_info']['str_module']['type'] );
    $this->assertEquals( 'media', $form_set['field_info']['medias_fotos']['type'] );
    $this->assertInternalType( 'array', $form_set['field_info']['str_module']['_options']);
    $this->assertInternalType( 'array', $form_set['field_info']['str_module']['_options']['data']);
    $this->assertInternalType( 'array', $form_set['field_info']['medias_fotos']['_options']);
    $this->assertInternalType( 'array', $form_set['field_info']['medias_fotos']['_options']['data']);
    $this->assertEquals( 'pictures', $grid_set['field_info']['medias_fotos']['path'] );
  }


  public function test_abstractfields() {
    // tbl_menu
    $this->CI->data->table( 'tbl_menu' );
    // ->get_abstract_fields()
    $abstract_fields = $this->CI->data->get_abstract_fields();
    $this->assertEquals( array('str_title','str_module'), $abstract_fields );
    // ->get_compiled_abstract_select()
    $abstract_fields_sql  = $this->CI->data->get_compiled_abstract_select();
    $this->assertEquals( "REPLACE( CONCAT_WS(' | ',`tbl_menu`.`str_title`,`tbl_menu`.`str_module`), ' |  | ','' )  AS `abstract`", $abstract_fields_sql );
    // ->select_abstract()
    $query = $this->CI->data->select_abstract()->get();
    $this->assertEquals( 6, $query->num_rows() );
    $this->assertEquals( 2, $query->num_fields() );

    // tbl_links
    $this->CI->data->table( 'tbl_links' );
    // ->get_abstract_fields()
    $abstract_fields = $this->CI->data->get_abstract_fields();
    $this->assertEquals( array('str_title','url_url'), $abstract_fields );
  }


  public function test_get_options() {
    // tbl_menu
    $this->CI->data->table( 'tbl_menu' );
    $options = $this->CI->data->get_options();
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'str_module', $options );
    $this->assertGreaterThanOrEqual( 3, count($options) );
    // tbl_menu.str_module
    $options = $this->CI->data->get_options( 'str_module' );
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'field', $options );
    $this->assertEquals( 'str_module', $options['field'] );
    // $this->assertArrayHasKey( 'multiple', $options );
    // $this->assertEquals( FALSE, $options['multiple'] );
    $this->assertArrayHasKey( 'data', $options );
    $this->assertEquals( 3, count($options['data']) );
    $this->assertArrayHasKey( 'value', current($options['data']));
    $this->assertArrayHasKey( 'name', current($options['data']));
    // tbl_menu.medias_fotos
    $options = $this->CI->data->get_options( 'medias_fotos' );
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'field', $options );
    $this->assertEquals( 'medias_fotos', $options['field'] );
    $this->assertArrayHasKey( 'model', $options );
    $this->assertEquals( 'media', $options['model'] );
    $this->assertArrayHasKey( 'path', $options );
    $this->assertEquals( 'pictures', $options['path'] );
    $this->assertArrayHasKey( 'multiple', $options );
    $this->assertEquals( TRUE, $options['multiple'] );
    $this->assertArrayHasKey( 'data', $options );
    $this->assertEquals( 2, count($options['data']) );
    // $current = current($options['data']);
    // $this->assertArrayHasKey( 'name', $current);
    // $this->assertInternalType( 'array', $current['name']);
    // $this->assertArrayHasKey( 'value', $current);
    // $this->assertInternalType( 'string', $current['value']);
    // tbl_menu.self_parent
    $options = $this->CI->data->get_options( 'self_parent' );
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'field', $options );
    $this->assertEquals( 'self_parent', $options['field'] );
    $this->assertArrayNotHasKey( 'multiple', $options );
    $this->assertArrayHasKey( 'data', $options );
    $this->assertEquals( 7, count($options['data']) );
    $current = current($options['data']);
    $this->assertArrayHasKey( 'name', $current);
    $this->assertArrayHasKey( 'value', $current);
    $this->assertInternalType( 'string', $current['name']);
    $this->assertInternalType( 'string', $current['value']);

    // tbl_kinderen
    $this->CI->data->table( 'tbl_kinderen' );
    $options = $this->CI->data->get_options();
    $this->assertInternalType( 'array', $options );
    $this->assertEquals( 2, count($options) );
    // tbl_kinderen.id_adressen
    $options = $this->CI->data->get_options('id_adressen');
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'field', $options );
    $this->assertEquals( 'id_adressen', $options['field'] );
    $this->assertArrayHasKey( 'table', $options );
    $this->assertEquals( 'tbl_adressen', $options['table'] );
    $this->assertArrayNotHasKey( 'multiple', $options );
    $this->assertArrayHasKey( 'data', $options );
    $this->assertEquals( 14, count($options['data']) );
    $current = current($options['data']);
    $this->assertArrayHasKey( 'name', $current);
    $this->assertArrayHasKey( 'value', $current);
    $this->assertInternalType( 'string', $current['name']);
    // $this->assertInternalType( 'string', $current['value']);
    // tbl_kinderen.id_groepen
    $options = $this->CI->data->get_options('id_groepen');
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'field', $options );
    $this->assertEquals( 'id_groepen', $options['field'] );
    $this->assertArrayHasKey( 'table', $options );
    $this->assertEquals( 'tbl_groepen', $options['table'] );
    $this->assertArrayNotHasKey( 'multiple', $options );
    $this->assertArrayHasKey( 'data', $options );
    $this->assertEquals( 8, count($options['data']) );
    $current = current($options['data']);
    $this->assertArrayHasKey( 'name', $current);
    $this->assertArrayHasKey( 'value', $current);
    $this->assertInternalType( 'string', $current['name']);
    // $this->assertInternalType( 'string', $current['value']);
    
    // tbl_groepen
    $this->CI->data->table( 'tbl_groepen' );
    $options = $this->CI->data->get_options();
    $this->assertInternalType( 'array', $options );
    $this->assertGreaterThanOrEqual( 2, count($options) );
    // tbl_groepen.media_tekening
    $options = $this->CI->data->get_options('media_tekening');
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'field', $options );
    $this->assertEquals( 'media_tekening', $options['field'] );
    $this->assertArrayHasKey( 'model', $options );
    $this->assertEquals( 'media', $options['model'] );
    $this->assertArrayHasKey( 'path', $options );
    $this->assertEquals( 'pictures', $options['path'] );
    $this->assertArrayNotHasKey( 'multiple', $options );
    $this->assertArrayHasKey( 'data', $options );
    $this->assertEquals( 2, count($options['data']) );
    // $current = current($options['data']);
    // $this->assertArrayHasKey( 'name', $current);
    // $this->assertArrayHasKey( 'value', $current);
    // $this->assertInternalType( 'array', $current['name']);
    // $this->assertInternalType( 'string', $current['value']);
    // tbl_groepen.tbl_adressen
    $options = $this->CI->data->get_options('tbl_adressen');
    $this->assertInternalType( 'array', $options );
    $this->assertArrayHasKey( 'table', $options );
    $this->assertEquals( 'tbl_adressen', $options['table'] );
    $this->assertArrayHasKey( 'multiple', $options );
    $this->assertEquals( TRUE, $options['multiple'] );
    $this->assertArrayHasKey( 'data', $options );
    $this->assertEquals( 14, count($options['data']) );
    $current = current($options['data']);
    $this->assertArrayHasKey( 'name', $current);
    $this->assertArrayHasKey( 'value', $current);
    $this->assertInternalType( 'string', $current['name']);
    $this->assertInternalType( 'integer', $current['value']);
    
    
  }



  public function test_setting_with() {
    // tbl_kinderen
    $this->CI->data->table( 'tbl_kinderen' );
    
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
          'fields'  =>  array('id','uri','order','str_title','str_soort','media_tekening','rgb_kleur'),
          'table'   =>  'tbl_groepen',
          'as'      =>  'tbl_groepen',
          'json'    =>  false,
          'flat'    =>  false
        ),
        'user_changed' => array(
          'table'  => 'cfg_users',
          'fields' => array('id','str_username','email_email','str_language','str_filemanager_view','b_active'),
          'json'   => false,
          'as'     => '_user_changed',
          'flat'   => false,
        ),
      )
    );
    $this->CI->data->with( 'many_to_one' );
    $with = $this->CI->data->get_with();
    $this->assertEquals( $expected, $with );


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
    $this->CI->data->table( 'tbl_kinderen' );
    $grid_set = $this->CI->data->get_setting('grid_set');
    $this->assertInternalType( 'array', $grid_set );
    $this->assertInternalType( 'array', $grid_set['with']['many_to_one'] );

    // tbl_kinderen - abstract
    $query = $this->CI->data->table( 'tbl_kinderen' )
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

    // tbl_kinderen - full (automatic 'id')
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

    // tbl_kinderen ->get_result()
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

    // tbl_kinderen ->where()->get_result()
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
                            ->with( 'one_to_many', ['tbl_kinderen'=>array('str_first_name','str_last_name')] )
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
    $this->assertEquals( array('id','str_city','tbl_kinderen.id','tbl_kinderen.str_first_name','tbl_kinderen.str_last_name'), $keys );

    // tbl_adressen - absract
    $query = $this->CI->data->select( 'id,str_city' )
                            ->with( 'one_to_many', ['tbl_kinderen'=>'abstract'] )
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
    $this->assertEquals( array('id','str_city','tbl_kinderen.id','tbl_kinderen.abstract'), $keys );
    // klopt abstract?
    $this->assertInternalType( 'string', $row['tbl_kinderen.abstract'] );

    // tbl_adressen ->get_result()
    $array = $this->CI->data->select( 'id,str_city')
                            ->with( 'one_to_many', array('tbl_kinderen'=>'str_first_name') )
                            ->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertEquals( 14, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_city','tbl_kinderen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_kinderen'] );
    $this->assertEquals( 11, count($row['tbl_kinderen']) );

    // tbl_adressen ->where()->get_result()
    $array = $this->CI->data->select('str_city')
                            ->with( 'one_to_many', array('tbl_kinderen'=>'str_first_name') )
                            ->where('tbl_kinderen.str_first_name','Adam')
                            ->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertLessThan( 2, count($array) );
    $row = current($array);
    $this->assertEquals( 3, count($row) );
    // kloppen keys in row en subdata?
    $keys = array_keys($row);
    $this->assertEquals( array('id','str_city','tbl_kinderen'), $keys );
    $this->assertInternalType( 'array', $row['tbl_kinderen'] );
    $this->assertEquals( 1, count($row['tbl_kinderen']) );
    $this->assertEquals( 'Adam', $row['tbl_kinderen'][2]['str_first_name'] );

    // Met afwijkende namen als standaard cfg_users
    $result = $this->CI->data->table( 'cfg_users' )
                             ->with( 'many_to_many' )
                             ->get_result();
    // data, klopt num_rows & num_fields?
    $this->assertEquals( 3, $this->CI->data->num_rows() );
    $this->assertEquals( 7, $this->CI->data->num_fields() );
    // kloppen keys in row?
    $row=current($result);
    $keys = array_keys($row);
    $this->assertEquals( array( 'id', 'str_username', 'email_email', 'str_language', 'str_filemanager_view', 'b_active', 'cfg_user_groups' ), $keys );


    // Users resultaat zoals in admin
    $result = $this->CI->data->table( 'cfg_users' )->get_grid();
    // data, klopt num_rows & num_fields?
    $this->assertEquals( 3, $this->CI->data->num_rows() );
    $this->assertEquals( 6, $this->CI->data->num_fields() );
    // kloppen keys in row?
    $row=current($result);
    $keys = array_keys($row);
    $this->assertEquals( array( 'id','action_user_invite','str_username', 'email_email', 'cfg_user_groups', 'str_language', 'b_active' ), $keys );

    // Users resultaat zoals in admin
    $result = $this->CI->data->table( 'cfg_users' )->get_form();
    // data, klopt num_rows & num_fields?
    $this->assertEquals( 3, $this->CI->data->num_rows() );
    $this->assertEquals( 6, $this->CI->data->num_fields() );
    // kloppen keys in row?
    $keys = array_keys($row);
    $this->assertEquals( array( 'id','action_user_invite','str_username', 'email_email', 'cfg_user_groups', 'str_language', 'b_active' ), $keys );
  }

 
  public function test_many_to_many_data() {
    // tbl_groepen - abstract
    $query = $this->CI->data->table( 'tbl_groepen' )
                              ->select('str_title')
                              ->with( 'many_to_many', array('rel_groepen__adressen' => 'abstract') )
                              ->get();
    $this->assertEquals( 46, $query->num_rows() );
    $this->assertEquals( 4, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 46, count($array) );
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
    $this->assertEquals( 46, $query->num_rows() );
    $this->assertEquals( 4, $query->num_fields() );
    // data, klopt num_rows & num_fields?
    $array = $query->result_array();
    $this->assertEquals( 46, count($array) );
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
  
  public function test_order() {
    $this->CI->data->table( 'tbl_menu' );

    $this->CI->data->order_by('uri');
    $first = $this->CI->data->select('id,uri,str_title,str_module')->get_row();
    $this->assertEquals( 'blog', $first['uri'] );

    $this->CI->data->order_by('uri','DESC');
    $first = $this->CI->data->select('id,uri,str_title,str_module')->get_row();
    $this->assertEquals( 'subpagina', $first['uri'] );

    $this->CI->data->order_by('uri DESC');
    $first = $this->CI->data->select('id,uri,str_title,str_module')->get_row();
    $this->assertEquals( 'subpagina', $first['uri'] );

    $this->CI->data->order_by('_uri');
    $first = $this->CI->data->select('id,uri,str_title,str_module')->get_row();
    $this->assertEquals( 'subpagina', $first['uri'] );
  }

  public function test_find() {
    $this->CI->data->table('tbl_kinderen');
    // ruw
    $this->CI->data->find('va');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 26, $info['num_rows'] );

    // word boundaries
    $this->CI->data->find('va',null,array('equals'=>'word'));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 0, $info['num_rows'] );
    
    // specific fields
    $this->CI->data->find('va',array('str_middle_name'));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 26, $info['num_rows'] );
    // specific fields & word boundaries
    $this->CI->data->find('van',array('str_middle_name'),array('equals'=>'word'));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 26, $info['num_rows'] );
    // specific fields & word boundaries
    $this->CI->data->find('va',array('str_middle_name'),array('equals'=>'word'));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 0, $info['num_rows'] );

    // meerdere termen los (string)
    $this->CI->data->find('van den');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 28, $info['num_rows'] );
    // meerdere termen los (array string)
    $this->CI->data->find( array('van den') );
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 28, $info['num_rows'] );
    // meerdere termen los (array)
    $this->CI->data->find( array('van','den') );
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 28, $info['num_rows'] );
    // meerdere termen vast
    $this->CI->data->find('"van den"');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 2, $info['num_rows'] );

    // meerdere termen AND
    $this->CI->data->find('van den','str_middle_name', array('and'=>TRUE));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 2, $info['num_rows'] );

    // Zoeken in many_to_one 'va'
    $this->CI->data->with( 'many_to_one' );
    $this->CI->data->find( 'va' );
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 49, $info['num_rows'] );
    // Zoeken in many_to_one 'van'
    $this->CI->data->with( 'many_to_one' );
    $this->CI->data->find('van');
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 26, $info['num_rows'] );
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
    $this->CI->data->find('straat',null,array('equals'=>'word'));
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 0, $info['num_rows'] );

    // Zoeken in one_to_many 'van' (LET OP query resultaat omdat sommige dubbel kunnen zijn, get_result geeft dan ander aantal)
    $this->CI->data->table( 'tbl_adressen' );
    $this->CI->data->with( 'one_to_many' );
    $this->CI->data->find( 'van' );
    $query = $this->CI->data->get();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 26, $info['num_rows'] );

    // Zoeken in many_to_many 'van' ->word_boundaries
    $this->CI->data->table( 'tbl_adressen' );
    $this->CI->data->with( 'one_to_many' );
    $this->CI->data->find( 'van', null, array('equals'=>'word'));
    $query = $this->CI->data->get();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 26, $info['num_rows'] );

    // Zoeken in many_to_many 'straat' (LET OP query resultaat omdat sommige dubbel kunnen zijn, get_result geeft dan ander aantal)
    $this->CI->data->table( 'tbl_groepen' );
    $this->CI->data->with( 'many_to_many' );
    $this->CI->data->find( 'straat' );
    $query = $this->CI->data->get();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 22, $info['num_rows'] );

    // Zoeken in many_to_many 'straat' result
    $this->CI->data->table( 'tbl_groepen' );
    $this->CI->data->with( 'many_to_many' );
    $this->CI->data->find( 'straat' );
    $result = $this->CI->data->get_result();
    $info = $this->CI->data->get_query_info();
    $this->assertEquals( 4, $info['num_rows'] );
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
    
    // UPDATE error (omdat er geen conditie is meegegeven)
    try {
      $random_string = 'UPDATE '.random_string();
      $this->CI->data->set( array('str_update'=>$random_string ) );
      $this->CI->data->update();
    } catch (Exception $ex) {
      $this->assertContains( "no condition set", $ex->getMessage());
    }

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
    $this->CI->data->table('rel_crud__crud2');
    for ($i=0; $i < $nr_others; $i++) {
      $other_ids[] = $this->CI->data->random_field_value( 'id_crud2' );
    }
    $set = array(
      'str_insert' => $insert_string,
      'str_update' => $update_string,
      'tbl_crud2'  => $other_ids,
    );
    $this->CI->data->table( 'tbl_crud' );
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

    // Test update relaties met afwijkende naam 'cfg_users'
    $this->CI->data->table('cfg_users');
    $row = $this->CI->data->where( 'str_username', 'test')
                          ->with( 'many_to_many' )
                          ->get_row();
    $this->assertInternalType( 'array', $row['cfg_user_groups'] );
    $this->assertEquals( 2, current($row['cfg_user_groups'])['id'] );
    $id = $row['id'];
    // Update and check again
    $result = $this->CI->data->where( 'str_username', 'test')
                             ->with( 'many_to_many' )
                             ->set(array('cfg_user_groups'=>3))
                             ->update();
    $this->assertEquals( $result, $id );
    // trace_($this->CI->data->get_query_info());
    $row = $this->CI->data->where( 'str_username', 'test')
                          ->with( 'many_to_many' )
                          ->get_row();
    $this->assertEquals( 3, current($row['cfg_user_groups'])['id'] );
    $this->assertInternalType( 'array', $row['cfg_user_groups'] );
    // Update and check again
    $result = $this->CI->data->where( 'str_username', 'test')
                             ->with( 'many_to_many' )
                             ->update( array('cfg_user_groups'=>2) );
    $this->assertEquals( $result, $id );
    $row = $this->CI->data->where( 'str_username', 'test')
                          ->with( 'many_to_many' )
                          ->get_row();
    $this->assertEquals( 2, current($row['cfg_user_groups'])['id'] );
    $this->assertInternalType( 'array', $row['cfg_user_groups'] );

    // Test aanpassen wachtwoord, een leeg wachtwoord mag niet in de set blijven staan
    $this->CI->data->table('cfg_users');
    $result = $this->CI->data->where( 'str_username', 'test' )
                             ->set( array('gpw_password'=>'') )
                             ->update();
    $this->assertEquals( $result, FALSE );

  }
  
  
  public function test_create_uri() {
    $this->CI->data->table('tbl_menu');

    // INSERT
    $this->CI->flexy_auth->login( 'admin', 'admin' );
    $this->CI->row->set_args(array(
      'POST' => array(
        'table' => 'tbl_menu',
        'data'  => array('str_title' => 'New Uri' )
      ),
    ));
    $result = $this->CI->row->index();
    $insert_id = $result['info']['insert_id'];
    $this->assertGreaterThan( 0, $insert_id );
    
    // Check of de uri is aangemaakt
    $row = $this->CI->data->get_row($insert_id);
    $this->assertEquals( 'New Uri', $row['str_title'] );
    $this->assertEquals( 'new_uri', $row['uri'] );

    // UPDATE
    $this->CI->flexy_auth->login( 'admin', 'admin' );
    $this->CI->row->set_args(array(
      'POST' => array(
        'table' => 'tbl_menu',
        'where' => $insert_id,
        'data'  => array('str_title' => 'Andere Uri' )
      ),
    ));
    $result = $this->CI->row->index();
    // Check of de uri is aangemaakt
    $row = $this->CI->data->get_row($insert_id);
    $this->assertEquals( 'Andere Uri', $row['str_title'] );
    $this->assertEquals( 'andere_uri', $row['uri'] );

    // Delete
    $this->CI->data->delete( $insert_id );
  }


  public function test_grid_set() {

    // Page1
    $this->CI->data->table('tbl_kinderen');
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
    $this->assertEquals( '{"4":"Rekenpark 42 | 1234IJ"}', $first['id_adressen'] );
    $this->assertEquals( '{"31":"Gym | vak"}', $first['id_groepen'] );

    // DESC
    $result = $this->CI->data->order_by('_str_first_name')->get_grid();
    $first = current($result);
    $this->assertEquals( 'Evy', $first['str_first_name'] );

    $result = $this->CI->data->order_by('str_last_name')->get_grid();
    $first = current($result);
    $this->assertEquals( 'Aalts', $first['str_last_name'] );

    $result = $this->CI->data->order_by('_str_last_name')->get_grid();
    $first = current($result);
    $this->assertEquals( 'Evertsen', $first['str_last_name'] );
    
    // $result = $this->CI->data->order_by('id_adressen')->get_grid();
    // $first = current($result);
    // $this->assertEquals( 'Ada', $first['id_adressen'] );
  }
  
  
  public function testCaching() {
    $this->CI->data->table('tbl_groepen');
    // Simple
    $result = $this->CI->data->cache()->get_result();
    $info   = $this->CI->data->get_query_info();
    $cached_result = $this->CI->data->cache()->get_result();
    $cached_info   = $this->CI->data->get_query_info();
    $this->assertEquals($result,$cached_result);
    $this->assertFalse($info['from_cache']);
    $this->assertTrue($cached_info['from_cache']);

    // Where, order, limit
    $result = $this->CI->data->cache()->where('str_soort','groep')->order_by('id')->get_result(3);
    $info   = $this->CI->data->get_query_info();
    $cached_result = $this->CI->data->cache()->where('str_soort','groep')->order_by('id')->get_result(3);
    $cached_info   = $this->CI->data->get_query_info();
    $this->assertEquals($result,$cached_result);
    $this->assertFalse($info['from_cache']);
    $this->assertTrue($cached_info['from_cache']);

    // Relations
    $result = $this->CI->data->cache()->with('many_to_many')->get_result(3);
    $info   = $this->CI->data->get_query_info();
    $cached_result = $this->CI->data->cache()->with('many_to_many')->get_result(3);
    $cached_info   = $this->CI->data->get_query_info();
    $this->assertEquals($result,$cached_result);
    $this->assertFalse($info['from_cache']);
    $this->assertTrue($cached_info['from_cache']);
    
    $this->CI->data->clear_cache();
  }



}

?>