<?php 

class Test extends CI_Controller {
	
	public function __construct()	{
		parent::__construct();
    $this->load->library('user');
    $this->load->model('cfg');
    $this->load->model('tables/table_model');
	}
  
  public function index() {
    if (!$this->user->is_super_admin()) return;
    
    // MANY_TO_ONE
    
    // // Oude manier
    // // $this->db->add_foreigns_as_abstracts();
    // // $result = $this->db->get_result('tbl_leerlingen');
    //
    // // Met table_model
    // $this->table_model->table('tbl_leerlingen');
    // $this->table_model->select('str_first_name');
    // $this->table_model->select_abstract();
    // $this->table_model->with( 'many_to_one' );
    // $this->table_model->where( 'id_groepen', 29 );  // 16
    // $this->table_model->where( 'tbl_groepen.str_title', "D" );  // 16
    // $this->table_model->limit(4);
    // $this->table_model->order_by( 'tbl_groepen__str_title', 'DESC' );
    // $this->table_model->find( 'park', 'tbl_adressen.str_address' );
    // $query = $this->table_model->get();
    // $result = $this->table_model->get_result();

    // MANY_TO_MANY

    // Oude manier
    // $this->db->add_many();
    // $this->db->where( 'rel_groepen__adressen.str_address', "Schooolstraat 1" );
    // $result = $this->db->get_result('tbl_groepen');

    // Met table_model
    // $this->table_model->table('tbl_groepen');
    // $this->table_model->with( 'many_to_many' );
    // $this->table_model->with_grouped( 'many_to_many' );
    // $this->table_model->with_grouped( 'many_to_many', array( 'tbl_adressen' => array('str_zipcode')) );
    // $this->table_model->with_grouped( 'many_to_many', array( 'tbl_adressen' => 'abstract' ) );
    // $this->table_model->where_exists( 'tbl_adressen.str_address', "Schooolstraat 1" );
    // $this->table_model->limit(2);
    // $this->table_model->order_by( 'str_title, tbl_adressen.str_address' );
    // $this->table_model->find( 'park straat', 'tbl_adressen.str_address' );
    // $query = $this->table_model->get();
    // $result = $this->table_model->get_result();
    
    
    // PATH (full uri)
    $this->table_model->table('tbl_menu');
    // $this->table_model->select('uri,order,self_parent,str_title');
    $this->table_model->select_txt_maxlen( 200 );
    // $this->table_model->path( 'path','uri' );
    // $this->table_model->path( 'str_title' );
    // $this->table_model->find( '"gelukt om"', array(), true );
    // $query = $this->table_model->get();
    $result = $this->table_model->get_result();
    
    // $this->table_model->table('tbl_menu');
    // $result = $this->table_model->field_data();
    // $result = $this->table_model->get_defaults();
    
    
    trace_( $this->table_model->get_query_info() );
    if (isset($result))  var_dump( $result );
    if (isset($query))   var_dump( $query->result_array() );
    
    
    return '';
  }
  

}

?>