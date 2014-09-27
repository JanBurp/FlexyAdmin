<?php require_once(APPPATH."core/ApiController.php");

class get_table extends ApiController {
  
  var $args = array(
    'table'   => '',
    'limit'   => 0,
    'offset'  => 0
  );


	public function __construct($field='') {
		parent::__construct();
    $this->load->model('ui');
	}
  

  public function index() {
    // DEFAULTS
    $items=FALSE;
    $field_info=FALSE;
    $table_info=FALSE;
    
    if ($this->args['table']) {
      
      // FIELD INFO
      $fields=$this->db->list_fields($this->args['table']);
      $field_info=array();
      $unset_fields=array();
      foreach ($fields as $field) {
        $prefix=get_prefix($field);
        $full_name=$this->args['table'].'.'.$field;
        $info=$this->cfg->get('cfg_field_info',$full_name);
        if (!el('b_show_in_grid',$info,true)) {
          $unset_fields[]=$field;
        }
        else {
          if ($info) $info=array_unset_keys($info,array('id','field_field'));
          $field_info[$field]=array(
            'table'     => $this->args['table'],
            'field'     => $field,
            'ui_name'   => $this->ui->get($field),
            'info'      => $info,
            'editable'  => !in_array($field,$this->config->item('NON_EDITABLE_FIELDS')),
            'incomplete'=> in_array($prefix,$this->config->item('INCOMPLETE_DATA_TYPES'))
          );
        }
      }
      
      // TABLE INFO
      $table_info=$this->cfg->get('cfg_table_info',$this->args['table']);
      $table_info['ui_name']  = $this->ui->get($this->args['table']);
      $table_info['sortable'] = !in_array('order',$fields);
      $table_info['tree']     = in_array('self_parent',$fields);

      // GET DATA
      $this->db->unselect($unset_fields);
      $this->db->max_text_len(500);
      $items = $this->crud->get($this->args);

      // PROCESS DATA
      if ($items) {
        // STRIP TAGS
        $txtKeys=array_combine($fields,$fields);
        $txtKeys=filter_by_key($txtKeys,'txt');
        if ($txtKeys) {
          foreach ($items as $id => $row) {
            foreach ($txtKeys as $key) {
              $items[$id][$key]=strip_tags($row[$key]);
            }
          }
        }
        else $txtKeys=array();
      }
    }
    
    // RESULT
    $data=array(
      'table_info'  =>$table_info,
      'field_info'  =>$field_info,
      'items'       =>$items
    );
    
    return $this->_result(array('data'=>$data));
  }

}


?>
