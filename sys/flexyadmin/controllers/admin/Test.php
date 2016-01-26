<?php require_once(APPPATH."core/AdminController.php");

class Test extends AdminController {
	
	public function __construct()	{
		parent::__construct();
    $this->load->library('user');
    $this->load->model('cfg');
    $this->load->model('data/data_model');
	}
  
  public function index() {
    if (!$this->user->is_super_admin()) return;
    
    
    // MANY_TO_ONE
    
    // // Oude manier
    // // $this->db->add_foreigns_as_abstracts();
    // // $result = $this->db->get_result('tbl_leerlingen');
    //
    // // Met data_model
    // $this->data_model->table('tbl_leerlingen');
    // $this->data_model->select('id,str_first_name');
    // $this->data_model->select_abstract();
    // $this->data_model->with( 'many_to_one', ['tbl_adressen'] );
    // $this->data_model->with_flat_many_to_one( ['tbl_adressen'=>'abstract'] );
    // $this->data_model->where( 'id_groepen', 29 );  // 16
    // $this->data_model->where( 'tbl_groepen.str_title', "D" );  // 16
    // $this->data_model->limit(4);
    // $this->data_model->order_by( 'tbl_groepen__str_title' );
    // $this->data_model->order_by( 'str_first_name', 'DESC' );
    // $this->data_model->find( 'park', 'tbl_adressen.str_address' );
    // $query = $this->data_model->get();
    // $result = $this->data_model->get_result();
    // $result = $this->data_model->get_grid( );
    // $result = $this->data_model->get_grid( 0, 'str_first_name' );

    $this->data_model->table('tbl_crud');
    // $query = $this->data_model->get( 5 );
    // $result = $this->data_model->get_result();
    $result = $this->data_model->get_grid();
    // $result = $this->data_model->get_grid( 20, FALSE, 'tme_last_changed' );


    // MANY_TO_MANY

    // Oude manier
    // $this->db->add_many();
    // $this->db->where( 'rel_groepen__adressen.str_address', "Schooolstraat 1" );
    // $result = $this->db->get_result('tbl_groepen');

    // Met data_model
    // $this->data_model->table('tbl_groepen');
    // $this->data_model->with( 'many_to_many' );
    // $this->data_model->with_grouped( 'many_to_many' );
    // $this->data_model->with_grouped( 'many_to_many', array( 'tbl_adressen' => array('str_zipcode')) );
    // $this->data_model->with_grouped( 'many_to_many', array( 'tbl_adressen' => 'abstract' ) );
    // $this->data_model->where_exists( 'tbl_adressen.str_address', "Schooolstraat 1" );
    // $this->data_model->limit(2);
    // $this->data_model->order_by( 'str_title, tbl_adressen.str_address' );
    // $this->data_model->find( 'park straat', 'tbl_adressen.str_address' );
    // $query = $this->data_model->get();
    // $result = $this->data_model->get_result();
    
    
    // PATH (full uri)
    // $this->data_model->table('tbl_menu');
    // $this->data_model->select('uri,order,self_parent,str_title');
    // $this->data_model->select_txt_abstract( 200 );
    // $this->data_model->path( 'path','uri' );
    // $this->data_model->path( 'str_title' );
    // $this->data_model->find( '"gelukt om"', array(), true );
    // $query = $this->data_model->get();
    // $result = $this->data_model->get_result();
    
    // $this->data_model->table('tbl_groepen');
    // $this->data_model->select_abstract();
    // $result = $this->data_model->get_result();
    
    // UPDATE
    // $this->data_model->table('tbl_crud');
    // $this->data_model->set('str_insert','TEST');
    // $this->data_model->limit(5);
    // $this->data_model->update();
    
    
    
    
    
    // trace_( $this->data_model->get_query_info() );
    // trace_sql( $this->data_model->last_query() );
    // if (isset($result))  var_dump( $result );
    // if (isset($query))   var_dump( $query->result_array() );
    trace_( $this->data_model->get_query_info() );
    echo( $this->data_model->last_query() );
    if (isset($result))  var_dump( $result );
    if (isset($query))   var_dump( $query->result_array() );
    
    return '';
  }
  

}

?>