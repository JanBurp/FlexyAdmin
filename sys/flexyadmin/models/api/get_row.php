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

class get_row extends ApiModel {
  
  var $needs = array(
    'table'   => '',
    'where'   => 'first'
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
    
    if ( !$this->has_args() ) {
      return $this->_result_wrong_args();
    }
    
    // CFG
    $this->_get_config(array('table_info','field_info'));
    // GET FIELDS
    $row=$this->_get_row();
    
    // RESULT
    $this->result['data']=$row;
    return $this->_result_ok();
  }
  

  
  /**
   * Gets the values from the table row
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_row() {
    $this->db->unselect( el(array('field_info','hidden_fields'),$this->cfg_info,array()) );
    $args=$this->args;
    $table=array_shift($args);
    $this->crud->table($table);
    $values = $this->crud->get_row($args);
    return $values;
  }

}


?>
