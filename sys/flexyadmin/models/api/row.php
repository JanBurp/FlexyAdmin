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

    // GET
    if ($this->args['type']=='GET') {
      $this->result['data']=$this->_get_row();
      return $this->_result_ok();
    }

    // POST
    if ($this->args['type']=='POST') {
      
      // UPDATE
      if (isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['table'])>=RIGHTS_EDIT) return $this->_result_norights();
        $this->result['data']=$this->_update_row();;
        return $this->_result_ok();
      }
      // INSERT
      if (isset($this->args['data']) and !isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['table'])>=RIGHTS_ADD) return $this->_result_norights();
        $this->result['data']=$this->_insert_row();
        return $this->_result_ok();
      }
      // DELETE
      if (!isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['table'])>=RIGHTS_DELETE) return $this->_result_norights();
        $this->result['data']=$this->_delete_row();
        return $this->_result_ok();
      }
    }
    
    // ERROR -> Wrong arguments
    return $this->_result_wrong_args();
  }
  
  /**
   * Gets the values from the table row
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_row() {
    $this->db->unselect( el(array('field_info','hidden_fields'),$this->cfg_info,array()) );
    $args=$this->_clean_args(array('table','where'));
    $table=$args['table'];
    unset($args['table']);
    $this->crud->table($table);
    $values = $this->crud->get_row($args);
    // trace_(['_get_row'=>$values,'args'=>$this->args]);
    return $values;
  }
  
  /**
   * Update row
   *
   * @return array
   * @author Jan den Besten
   */
  private function _update_row() {
    $args=$this->_clean_args(array('table','where','data'));
    $table=$args['table'];
    unset($args['table']);
    $this->crud->table($table);
    $id = $this->crud->update($args);
    return array('id'=>$id);
  }


  /**
   * Update row
   *
   * @return array
   * @author Jan den Besten
   */
  private function _insert_row() {
    $args=$this->_clean_args(array('table','data'));
    $table=$args['table'];
    unset($args['table']);
    $this->crud->table($table);
    $id = $this->crud->insert($args);
    return array('id'=>$id);
  }


  /**
   * Delete row
   *
   * @return void
   * @author Jan den Besten
   */
  private function _delete_row() {
    $args=$this->_clean_args(array('table','where'));
    $table=$args['table'];
    unset($args['table']);
    $this->crud->table($table);
    return $this->crud->delete($args['where']);
  }


}


?>
