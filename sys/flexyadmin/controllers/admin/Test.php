<?php 

class Test extends CI_Controller {
	
	public function __construct()	{
		parent::__construct();
    $this->load->model('cfg');
    $this->load->model('tables/table_model');
	}
  
  public function index() {
    
    // // Oude manier
    // // $this->db->add_foreigns_as_abstracts();
    // // $result = $this->db->get_result('tbl_leerlingen');
    //
    // // Met table_model
    // $this->table_model->table('tbl_leerlingen');
    // // $this->table_model->select('str_first_name');
    // $this->table_model->with( 'many_to_one' );
    // // $this->table_model->where( 'id_groepen', 29 );  // 16
    // $this->table_model->where( 'tbl_groepen.str_title', "D" );  // 16
    // $result = $this->table_model->get_result();


    // Oude manier
    // $this->db->add_many();
    // $this->db->where( 'rel_groepen__adressen.str_address', "Schooolstraat 1" );
    // $result = $this->db->get_result('tbl_groepen');

    // Met table_model
    $this->table_model->table('tbl_groepen');
    $this->table_model->with( 'many_to_many', array( 'rel_groepen__adressen' ) );
    $this->table_model->where( 'tbl_adressen.str_address', "Schooolstraat 1" );
    // $query = $this->table_model->get();
    $result = $this->table_model->get_result();
    
    
    echo( $this->db->last_query() );
    // var_dump( $query );
    // var_dump( $query->result_array() );
    trace_( current($result)['rel_groepen__adressen'] );
    var_dump( $result );
    
    
    return '';
  }
  

}

?>