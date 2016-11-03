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
      'limit'   => 10,
      'offset'  => false,
      'order'   => '',
      'find'    => ''
    );
    $options = object2array(json_decode($this->input->get('options')));
    $options = array_merge($default,$options);
    // Data
    $this->data->table($name);
    $data = $this->data->select_txt_abstract()->get_grid( $options['limit'], $options['offset'], $options['order'], $options['find'] );
    // Fields
    $fields = $this->_prepareFields('grid_set');
    // Show grid
    $grid = array(
      'title'   => $this->ui->get($name),
      'name'    => $name,
      'fields'  => $fields,
      'data'    => $data,
      'info'    => $this->data->get_query_info(),
      'order'   => $options['order'],
      'find'    => $options['find'],
    );
	  $this->view_admin( 'vue/grid', $grid );
	}
  

/**
 * This controls the form view
 *
 * @param string 	$table 	Table name
 * @param mixed 	$id 		id
 */
	public function form( $name='',$id=false ) {
    // Data
    $this->data->table($name);
    $data = $this->data->get_form($id);
    // Fields
    $fields = $this->_prepareFields('form_set');
    $fieldsets = $this->data->get_setting(array('form_set','fieldsets'));
    $fieldsetsKeys = $this->ui->get(array_keys($fieldsets));
    $fieldsets = array_combine($fieldsetsKeys,$fieldsets);
    // Show form
    $form = array(
      'title'     => $this->ui->get($name),
      'name'      => $name,
      'id'        => $id,
      'fields'    => $fields,
      'fieldsets' => $fieldsets,
      'data'      => $data
    );
	  $this->view_admin( 'vue/form', $form );
	}
  
  
  /**
   * Geeft alle velden met nuttige informatie per veld
   *
   * @param string $set [grid_set|form_set]
   * @return array
   * @author Jan den Besten
   */
  private function _prepareFields($set) {
    $fields = $this->data->get_setting(array($set,'fields'));
    $fields = array_combine($fields,$fields);
    foreach ($fields as $field => $info) {
      $fields[$field] = array(
        'name'    => $this->ui->get($field),
        'schema'  => $this->_getSchema($field)
      );
    }
    return $fields;
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


}

?>
