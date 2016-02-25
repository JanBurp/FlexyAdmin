<?php require_once(APPPATH."core/AdminController.php");

class Test extends AdminController {
	
	public function __construct()	{
		parent::__construct();
	}
  
  public function index() {
    if (!$this->user->is_super_admin()) return;
    
    $this->load->model('mediatable');
    $files = $this->mediatable->get_files( 'pictures' );
    
    
    trace_($files);
    
    
    
    return '';
  }
  

}

?>