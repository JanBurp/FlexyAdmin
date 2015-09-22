<?php 

class Test extends CI_Controller {
	
	public function __construct()	{
		parent::__construct();
    $this->load->model('cfg');
    $this->load->model('tables/table_model');
	}
  
  public function index() {
    
    // Oude manier
    // $this->db->add_foreigns_as_abstracts();
    // $result = $this->db->get_result('tbl_leerlingen');
    
    
    // Met table_model
    $this->table_model->table('tbl_leerlingen');
    // $this->table_model->select('str_first_name');
    $this->table_model->with( 'many_to_one' );
    $result = $this->table_model->get_result();
    
    echo( $this->db->last_query() );
    var_dump( $result );
    
    return '';
  }
  

}

?>