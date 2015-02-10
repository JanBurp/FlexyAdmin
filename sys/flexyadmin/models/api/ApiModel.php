<?

class ApiModel extends CI_Model {
  
  protected $args=array();
  protected $result=array();

  protected $check_rights=true;
  protected $loggedIn=false;

  protected $table=NULL;
  protected $fields;
  protected $hidden_fields;
  
  
  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();

    // Get arguments
    $this->args=$this->_get_args($this->args);
    
    // Check Authentication and Rights if not api/auth
    $auth=($this->uri->get(2)=='auth');
    $this->loggedIn=$this->user->logged_in();
    if (!$auth) {
      if (!$this->loggedIn) {
        $this->result['status']=401;
        return $this->result;
      }
      if (isset($this->args['table'])) $this->table=$this->args['table'];
      if ($this->check_rights) {
        if (!$this->_has_rights($this->table)) {
          $this->result['_error']='NO RIGHTS';
          return $this->result;
        }
      }
    }

    // Standard result
    $this->result['_args']=$this->args;
    $this->result['_api']=__CLASS__;
    return $this->result;
	}
  
  
  /**
   * Get arguments from GET or POST
   *
   * @param string $defaults 
   * @return void
   * @author Jan den Besten
   */
  private function _get_args($defaults) {
    $keys=array_keys($defaults);
    $args=array();
    
    // post
    if (!$args and !empty($_POST)) {
      $type='post';
      $args=$_POST;
      foreach ($keys as $key) {
        $value=$this->input->post($key);
        if (isset($value)) $args[$key]=$value;
      }
    }
    
    // or get
    if (!$args and !empty($_SERVER['QUERY_STRING'])) {
      $type='get';
      parse_str($_SERVER['QUERY_STRING'],$_GET);
      $args=$_GET;
      foreach ($keys as $key) {
        $value=$this->input->get($key);
        if (isset($value)) $args[$key]=$value;
      }
    }
    
    // or defaults
    if (!$args) {
      $type="none";
      $args=$defaults;
    }
    
    if (isset($args['_type'])) {
      $this->set_type($args['_type']);
    }
    
    return $args;
  }
  
  /**
   * Test rights for item
   *
   * @param string $item
   * @param string $id['] 
   * @param string $whatRight[0] 
   * @return boolean
   * @author Jan den Besten
   */
  protected function _has_rights($item,$id="",$whatRight=0) {
    $rights=$this->user->has_rights($item,$id,$whatRight);
    // if no normal rights and cfg_users and current user, TODO: safety check this!!
    if ($rights<2 and $item=='cfg_users') {
      if (el('where',$this->args,null)=='current') $rights=2;
    }
    return $rights;
  }
  
  
  /**
   * Gets information about all fields of the table in args
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _get_field_info() {
    $this->hidden_fields=array();
    $field_info=array();
    foreach ($this->fields as $field) {
      $prefix=get_prefix($field);
      $full_name=$this->args['table'].'.'.$field;
      $info=$this->cfg->get('cfg_field_info',$full_name);
      if (!el('b_show_in_grid',$info,true)) {
        $this->hidden_fields[]=$field;
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
    return $field_info;
  }
  
  
  /**
   * Gets information about the table (in args)
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _get_table_info() {
    $table_info=$this->cfg->get('cfg_table_info',$this->args['table']);
    $table_info['ui_name']  = $this->ui->get($this->args['table']);
    $table_info['sortable'] = !in_array('order',$this->fields);
    $table_info['tree']     = in_array('self_parent',$this->fields);
    return $table_info;
  }
  
  
  
  


}

?>