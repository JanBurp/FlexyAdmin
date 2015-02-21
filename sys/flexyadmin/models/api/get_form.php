<?


/**
 * Returns formdata / row from a table from the database
 * 
 * Arguments:
 * - table
 * - where
 *
 * @package default
 * @author Jan den Besten
 */

class get_form extends ApiModel {
  
  var $args = array(
    'table'   => '',
    'where'   => NULL
  );


	public function __construct() {
		parent::__construct();
    $this->load->model('ui');
	}
  

  /**
   * Gets the data and information and returns it
   *
   * @return void
   * @author Jan den Besten
   */
  public function index() {
    if (!$this->_has_rights($this->args['table'])) return $this->_result_status401();
    
    // DEFAULTS
    $fields=FALSE;
    
    if ($this->args['table']) {
      // CFG
      $this->_get_config(array('table_info','field_info'));
      // GET FIELDS
      $fields=$this->_get_fields();
    }
    
    // RESULT
    $data=array(
      'fields'       =>$fields
    );
    $this->result['data']=$data;
    return $this->_result_ok();
  }
  

  
  /**
   * Gets the values from the table row
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_fields() {
    $this->db->unselect( el(array('field_info','hidden_fields'),$this->cfg_info,array()) );
    $args=$this->args;
    $table=array_shift($args);
    $this->crud->table($table);
    $values = $this->crud->get_row($args);
    return $values;
  }

}


?>
