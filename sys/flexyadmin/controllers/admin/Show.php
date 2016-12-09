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
	public function grid( $name='') {
    // Options for grid result
    $default = array(
      'limit'   => 10,
      'offset'  => false,
      'order'   => '',
      'find'    => ''
    );
    $options = object2array(json_decode($this->input->get('options')));
    $options = array_merge($default,$options);
    $options['find'] = html_entity_decode($options['find']);
    if (substr($options['find'],0,1)==='[') $options['find'] = json2array($options['find']);
    
    // Data
    $this->data->table($name);
    $data = $this->data->select_txt_abstract()->get_grid( $options['limit'], $options['offset'], $options['order'], $options['find'] );
    
    // trace_($options);
    // trace_($this->data->last_query());
    // trace_($data);
    
    // Default order
    $options['order'] = $this->_order($options['order']);
    
    // Fields
    $fields = $this->_prepareFields('grid_set',array(),$data);
    // Show grid
    $grid = array(
      'title'   => $this->ui->get($name),
      'name'    => $name,
      'fields'  => $fields,
      'data'    => $data,
      'info'    => $this->data->get_query_info(),
      'order'   => $options['order'],
      'find'    => is_array($options['find'])?array2json($options['find']):$options['find'],
    );
	  $this->view_admin( 'vue/grid', $grid );
	}
  
	/**
	 * This controls the media view
	 *
	 * @param string $name name of path
	 */
  public function media( $path ) {
    $default = array(
      'limit'   => 10,
      'offset'  => false,
      'order'   => '',
      'find'    => ''
    );
    $options = object2array(json_decode($this->input->get('options')));
    $options = array_merge($default,$options);
    
    // Data
    $this->data->table('res_media_files');
    $this->data->order_by($options['order']);
    $files = $this->data->get_files( $path, $options['find'], $options['limit'], $options['offset'], TRUE );
    
    // Fields
    $fields = $this->_prepareFields('',array(),$files,array('path'=>$path));
    
    // Default order
    $options['order'] = $this->_order($options['order']);
    
    // Show grid
    $grid = array(
      'title'   => $this->ui->get($path),
      'name'    => $path,
      'fields'  => $fields,
      'data'    => $files,
      'info'    => $this->data->get_query_info(),
      'order'   => $options['order'],
      'find'    => $options['find'],
      'type'    => 'media',
    );
	  $this->view_admin( 'vue/grid', $grid );
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
    $isMedia = false;
    $args = func_get_args();
    $name = array_shift($args);
    if ($name === '_media_') {
      $name = 'res_media_files';
      $path = array_shift($args);
      $isMedia = true;
    }
    $id = array_shift($args);
      
    // Data
    $this->data->table($name);
    if ($isMedia) $this->data->select('str_title');
    $data = $this->data->get_form($id);
    $options = $this->data->get_options();
    // Fields
    $fields = $this->_prepareFields('form_set',$options);
    if ($isMedia) {
      $fields = array_keep_keys($fields,array('id','str_title'));
      $fieldsets = array($path=>array_keys($fields));
    }
    else {
      $fieldsets = $this->data->get_setting(array('form_set','fieldsets'));
    }
    $fieldsetsKeys = $this->ui->get(array_keys($fieldsets));
    $fieldsets = array_combine($fieldsetsKeys,$fieldsets);
    // Show form
    $form = array(
      'title'     => ($isMedia)?$this->ui->get($path):$this->ui->get($name),
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
  private function _prepareFields($set,$options=array(),$data=array(),$extra=array()) {
    $fields = array();
    if (empty($set)) {
      if ($data) $fields = array_keys(current($data));
    }
    else {
      $fields = $this->data->get_setting(array($set,'fields'));
    }
    $fields = array_combine($fields,$fields);
    foreach ($fields as $field => $info) {
      $fields[$field] = array(
        'name'    => $this->ui->get($field),
        'schema'  => $this->_getSchema($field,$options,isset($extra['path']))
      );
      if ($validation = $this->data->get_setting(array('field_info',$field,'validation'))) $fields[$field]['schema']['validation'] = implode('|',$validation);
      if ($path = $this->data->get_setting(array('field_info',$field,'path'))) $fields[$field]['path'] = $path;
      $fields[$field] = array_merge($fields[$field],$extra);
    }
    // More fields??
    if ($data) {
      $first = current($data);
      $extraFields = array_diff( array_keys($first),array_keys($fields) );
      if ($extraFields) {
        foreach ($extraFields as $extraField) {
          $fields[$extraField] = array(
            'name'  => $this->ui->get($extraField),
            'schema'=> $this->_getSchema($extraField,$options),
          );
        }
      }
    }
    if (empty($fields)) {
      $fields['id'] = array(
        'name'  => $this->ui->get('id'),
        'schema'=> $this->_getSchema('id',$options,isset($extra['path']))
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
  private function _getSchema($field,$options=array(),$media=FALSE) {
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
    if (isset($options[$field])) {
      $schema['form-type'] = 'select';
    }
    // Only the needed stuff
    $schema=array_unset_keys($schema,array('grid','form','default','format' )); // TODO kan (deels) weg als oude ui weg is
    // Most fields are read-only when media
    if ($media and $field!=='alt') $schema['readonly'] = true;
    if ($field==='media_thumb') $schema['sortable'] = false;
    if ($field==='rawdate') $schema['sortable'] = false;
    return $schema;
  }


}

?>
