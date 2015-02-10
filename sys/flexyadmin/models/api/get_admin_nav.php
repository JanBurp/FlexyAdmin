<?

/**
 * Geeft het admin menu terug
 *
 * @package default
 * @author Jan den Besten
 */

class get_admin_nav extends ApiModel {
  
  var $table = 'cfg_admin_menu';
  
  /**
   * @ignore
   */
	public function __construct($name='') {
    $this->check_rights=false;
		parent::__construct();
    $this->load->model($this->table);
    return $this;
	}
  
  public function index() {
    if (!$this->loggedIn) return $this->result;

    $table=$this->table;
    $data =$this->$table->get();
    $this->result['data']=$data;
    return $this->result;
  }

}


?>
