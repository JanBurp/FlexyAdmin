<?

class get_form extends ApiModel {
  
  var $args = array(
    'table'   => '',
    'where'   => NULL
  );


	public function __construct($field='') {
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
    if (!$this->_has_rights($this->args['table'])) return $this->result;
    
    // DEFAULTS
    $items=FALSE;
    $field_info=FALSE;
    $table_info=FALSE;
    
    if ($this->args['table']) {
      // FIELD INFO
      $this->fields = $this->db->list_fields($this->args['table']);
      $field_info=$this->_get_field_info();
      // TABLE INFO
      $table_info=$this->_get_table_info();
      // GET FIELDS
      $fields=$this->_get_fields($table_info);
    }
    
    // RESULT
    $this->result['data']=array(
      'table_info'  =>$table_info,
      'field_info'  =>$field_info,
      'fields'      =>$fields
    );
    return $this->result;
  }
  

  
  /**
   * Gets the values from the table row
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_fields($table_info) {
    $this->db->unselect($this->hidden_fields);
    $args=$this->args;
    $table=array_shift($args);
    $this->crud->table($table);
    $values = $this->crud->get_row($args);
    return $values;
  }

}


?>
