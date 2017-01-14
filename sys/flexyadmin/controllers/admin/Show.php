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
	public function grid($name='',$path='') {
    // Options for grid result
    $default = array(
      'limit'   => 10,
      'offset'  => 0,
      'order'   => '',
      'filter'  => ''
    );
    $options = object2array(json_decode($this->input->get('options')));
    $options = array_merge($default,$options);
    
    // Data Api
    $this->data->table($name);
    if ($path) $name=$path;
    $api = 'table';
    
    // Default order
    $options['order'] = $this->_order($options['order']);
    
    // Show grid
    $grid = array(
      'title'    => $this->ui->get($name),
      'name'     => $name,
      'api'      => $api,
      'order'    => $options['order'],
      'offset'   => $options['offset'],
      'limit'    => $options['limit'],
      'filter'   => $options['filter'],
      'type'     => ($path)?'media':'table',
    );
	  $this->view_admin( 'vue/grid', $grid );
	}
  
	/**
	 * This controls the media view
	 *
	 * @param string $name name of path
	 */
  public function media( $path ) {
    $this->grid('res_assets',$path);
  }

  /**
   * Geeft standaard order
   *
   * @param string $order 
   * @return void
   * @author Jan den Besten
   */
  private function _order($order) {
    if (empty($order)) {
      $order = $this->data->get_setting('order_by');
      if (!empty($order)) {
        $order = explode(',',$order)[0];
        $order = explode(' ',$order);
        if (isset($order[1]) and strtoupper($order[1])==='DESC') {
          $order = '_'.$order[0];
        }
        else {
          $order = $order[0];
        }
      }
    }
    return $order;
  }
  
  

/**
 * This controls the form view
 *
 * @param string 	$table 	Table name
 * @param mixed 	$id 		id
 */
	public function form() {
    $args = func_get_args();
    $name = array_shift($args);
    $id = array_shift($args);
      
    // Data
    $this->data->table($name);
    $data = $this->data->get_form($id);
    
    $options = $this->data->get_options();
    // Fields
    $fields = $this->_prepareFields('form_set',$options);
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
      'data'      => $data,
      'options'   => $options,
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
  private function _prepareFields($set='grid_set',$options=array()) {
    $fields = $this->data->get_setting(array($set,'fields'));
    $fields = array_combine($fields,$fields);

    foreach ($fields as $field => $info) {
      $fields[$field] = array(
        'name'    => $this->ui->get($field),
        'schema'  => $this->_getSchema($field,el($field,$options))
      );
      if ($validation = $this->data->get_setting(array('field_info',$field,'validation'))) $fields[$field]['schema']['validation'] = implode('|',$validation);
      if ($path = $this->data->get_setting(array('field_info',$field,'path'))) $fields[$field]['path'] = $path;
      // $fields[$field] = array_merge($fields[$field],$extra);
    }
    if (empty($fields)) {
      $fields['id'] = array(
        'name'  => $this->ui->get('id'),
        'schema'=> $this->_getSchema('id',$options)
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
  private function _getSchema($field,$options) {
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
    // Has options??
    if ($options) {
      $schema['form-type'] = 'select';
      if ($fieldPrefix==='media' or $fieldPrefix==='medias') $schema['form-type'] = 'media';
    }
    // Only the needed stuff
    $schema=array_unset_keys($schema,array('grid','form','default','format' )); // TODO kan (deels) weg als oude ui weg is
    return $schema;
  }


}

?>
