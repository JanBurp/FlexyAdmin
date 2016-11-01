<?php require_once(APPPATH."core/AdminController.php");

/** \ingroup controllers
 * This Controller shows a grid or form
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Show extends AdminController {
	
	public function __construct() {
		parent::__construct();
    $this->config->load('schemaform');
    $this->load->model('ui');
	}


	public function index() {
		$this->view_admin( 'admin_404' );
	}


	/**
	 * This controls the grid view
	 *
	 * @param string $name name of datamodel
	 */
	public function grid( $name='' ) {
    
    // Options for grid result
    $default = array(
      'limit'   => 5,
      'offset'  => false,
      'order'   => '',
      'find'   => ''
    );
    $options = object2array(json_decode($this->input->get('options')));
    $options = array_merge($default,$options);
    
    // Get result
    $data = $this->data->table($name)
              ->select_txt_abstract()
              ->get_grid( $options['limit'], $options['offset'], $options['order'], $options['find'] );
  
    // Prepare fields
    $fields = $this->data->get_setting(array('grid_set','fields'));
    $fields = array_combine($fields,$fields);
    foreach ($fields as $field => $info) {
      $fields[$field] = array(
        'name'    => $this->ui->get($field),
        'schema'  => $this->_getSchema($field)
      );
    }
    
    // Show grid
    $grid = array(
      'title'   => $this->ui->get($name),
      'fields'  => $fields,
      'data'    => $data,
      'info'    => $this->data->get_query_info(),
      'order'   => $options['order'],
      'find'    => $options['find'],
    );
    
	  $this->view_admin( 'vue/vue-grid', $grid );
	}
  
  /**
   * Geeft schema(form) van gegeven veld
   *
   * @param string $field 
   * @return array
   * @author Jan den Besten
   */
  private function _getSchema($field) {
    $schema = $this->config->item('FIELDS_default');
    // from prefix
    $fieldPrefix = get_prefix($field);
    $cfgPrefix  = $this->config->item('FIELDS_prefix');
    if ( $prefixSchema = el($fieldPrefix,$cfgPrefix) ) {
      $schema = array_merge($schema,$prefixSchema);
    }
    // Special fields
    $cfgSpecial = $this->config->item('FIELDS_special');
    if ( $specialSchema = el($field,$cfgSpecial)) {
      $schema = array_merge($schema,$specialSchema);
    }
    // Only the needed stuff
    $schema=array_unset_keys($schema,array('grid','form','default','format' )); // TODO kan (deels) weg als oude ui weg is
    return $schema;
  }




/**
 * This controls the form view
 *
 * @param string 	$table 	Table name
 * @param mixed 	$id 		id
 */
	public function form( $table='',$id=false ) {
	}


}

?>
