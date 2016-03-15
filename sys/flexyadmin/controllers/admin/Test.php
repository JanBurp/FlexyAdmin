<?php require_once(APPPATH."core/AdminController.php");

class Test extends AdminController {
	
	public function __construct()	{
		parent::__construct();
	}
  
  public function index() {
    if (!$this->user->is_super_admin()) return;
    
    $this->data_model->table( 'tbl_menu' );
    $options = $this->data_model->get_settings( );
    
    trace_($options);
    
    
    
    return '';
  }
  

}

?>