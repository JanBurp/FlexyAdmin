<?php 

class Test extends CI_Controller {
	
	public function __construct()	{
		parent::__construct();
	}
  
  public function index() {
    // $this->load->config( 'tables/tbl_menu', TRUE );
    // trace_( $this->config->item( 'tables/tbl_menu' ) );
    
    $this->load->model('tables/table_model');
    $this->load->model('tables/tbl_menu');
    
    $result = $this->tbl_menu->get_result();
    var_dump( $result );
    
    return '';
  }
  

}

?>