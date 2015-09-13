<?php 

class Test extends CI_Controller {
	
	public function __construct()	{
		parent::__construct();
	}
  
  public function index() {
    $this->load->model('tables/table_model');
    $this->load->model('tables/tbl_menu');
    
    $result = $this->tbl_menu->get_result_as_abstract();
    var_dump( $result );
    var_dump( $this->db->last_query() );
    
    return '';
  }
  

}

?>