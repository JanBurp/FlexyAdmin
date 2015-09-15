<?php 

class Test extends CI_Controller {
	
	public function __construct()	{
		parent::__construct();
	}
  
  public function index() {
    $this->load->config( 'tables/tbl_menu', TRUE );
    
    trace_( $this->config->item('table', 'tables/tbl_menu') );
    
    
    $this->load->model('tables/table_model');
    $this->load->model('tables/tbl_menu');
    
    $this->load->model('schoolbase/schoolbase_notifications','notifications');
    $items = $this->notifications->create_newsletter_items( 'all', 'week ');
    
    var_dump( $items );
    
    return '';
  }
  

}

?>