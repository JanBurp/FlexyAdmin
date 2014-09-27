<?php require_once(APPPATH."core/ApiController.php");

class get_table extends ApiController {
  
  var $args = array(
    'table'   => '',
    'limit'   => 0,
    'offset'  => 0
  );


	public function __construct($name='') {
		parent::__construct();
    $this->load->model('ui');
	}
  

  public function index() {

    // TODO more queries
    $items = $this->crud->get($this->args);
    
    // Field info
    $first=current($items);
    $names=array_keys($first);
    $field_info=array();
    foreach ($names as $name) {
      $full_name=$this->args['table'].'.'.$name;
      $info=$this->cfg->get('cfg_field_info',$full_name);
      if ($info) $info=array_unset_keys($info,array('id','field_field'));
      $field_info[$name]=array(
        'table'     => $this->args['table'],
        'field'     => $name,
        'ui_name'   => $this->ui->get($name),
        'info'      => $info
      );
    }
    
    // Table info
    $table_info=$this->cfg->get('cfg_table_info',$this->args['table']);
    $table_info['ui_name'] = $this->ui->get($this->args['table']);
    
    $data=array(
      'table_info'  =>$table_info,
      'field_info'  =>$field_info,
      'items'       =>$items
    );
    
    return $this->_result(array('data'=>$data));
  }

}


?>
