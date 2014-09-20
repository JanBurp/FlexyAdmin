<?php require_once(APPPATH."core/ApiController.php");

/**
 * API/get_admin_nav
 * Geeft het admin menu terug
 *
 * @package default
 * @author Jan den Besten
 */


class get_admin_nav extends ApiController {
  
  private $table = 'cfg_admin_menu';
  
  /**
   * @ignore
   */
	public function __construct($name='') {
		parent::__construct();
    $this->load->model($this->table);
    return $this;
	}
  
  public function index() {
    $table=$this->table;
    // If no rights -> Error
    if (!$this->_has_rights($table)) {
      return $this->_result(array('_error'=>'NO RIGHTS','_api'=>__CLASS__,'_args'=>$this->args));
    }
    // Get cfg_admin_table
    $data = $this->$table->get();
    return $this->_result(array('_api'=>__CLASS__,'_args'=>$this->args,'data'=>$data));
  }

}


?>
