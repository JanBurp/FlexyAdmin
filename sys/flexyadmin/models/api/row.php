<?


/**
 * GET / INSERT / UPDATE / DELETE row from a table from the database
 * 
 * GET ROW
 * - GET => array( 'table'=> ... , 'where' => ....)
 * 
 * INSERT ROW
 * - POST => array( 'table'=> ... , 'data' => array(....)  )
 * 
 * UPDATE ROW
 * - POST => array( 'table'=> ... , 'where' => ... , 'data' => array(....)  )
 * 
 * DELETE ROW
 * - POST => array( 'table'=> ... , 'where' => ...  )
 * 
 *
 * @package default
 * @author Jan den Besten
 */

class Row extends ApiModel {
  
  var $needs = array(
    'table'   => '',
    // 'where'   => 'first'
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

    // INSERT/UPDATE/DELETE
    if ($this->args['type']=='POST') {
      $this->result['data']=$this->_update_insert_delete_row();
    }
    // GET
    else {
      $this->result['data']=$this->_get_row();
    }
    
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
    $table=$args['table'];
    unset($args['table']);
    unset($args['config']);
    unset($args['type']);
    $this->crud->table($table);
    $values = $this->crud->get_row($args);
    // trace_(['_get_row'=>$values,'args'=>$this->args]);
    return $values;
  }
  
  /**
   * Updates or Inserts new row
   *
   * @return void
   * @author Jan den Besten
   */
  private function _update_insert_delete_row() {
    $args=$this->args;
    $table=$args['table'];
    unset($args['table']);
    unset($args['config']);
    unset($args['type']);
    $this->crud->table($table);

    // UPDATE / DELETE
    if (isset($args['where'])) {

      // DELETE
      if (!isset($args['data']) or empty($args['data'])) {
        return $this->crud->delete($args['where']);
      }
      // UPDATE
      else {
        $id = $this->crud->update($args);
      }
    }
    // INSERT
    else {
      $id = $this->crud->insert($args);
    }

    return array('id'=>$id);
  }

}


?>
