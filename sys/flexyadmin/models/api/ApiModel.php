<?

/**
 * Calls the right API and returns the (ajax)result or a 401
 * 
 * - Arguments are set in $_POST or $_GET
 * - See for all possible API calls the other files in sys/flexyadmin/models/api
 * - Common arguments are:
 *    - config[]=table_info
 *    - config[]=field_info
 *    - type=json
 *    - table=  (get_table,get_form)
 *    - where=  (get_form)
 * - result can have these keys (same as AJAX controller returns):
 *    - status=401 // if call is unauthorized
 *    - success=[true|false]
 *    - error=(string)
 *    - message=(string)
 *    - args=(array) given arguments
 *    - api=(string) name of the api call
 *    - data=(mixed) the returned data
 * 
 * Examples:
 * 
 * - _api/get_table?table=tbl_links
 * - _api/get_table?table=tbl_links&config[]=table_info&config[]=field_info
 * 
 * @package default
 * @author Jan den Besten
 */


class ApiModel extends CI_Model {
  
  protected $args=array();
  protected $result=array();
  protected $cfg_info=array();
  
  
  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
    // Get arguments
    $this->args=$this->_get_args($this->args);
    // Standard result
    $this->result['args']=$this->args;
    $this->result['api']=__CLASS__;
    // Check Authentication and Rights if not api/auth
    $auth=($this->uri->get(2)=='auth');
    $loggedIn=$this->user->logged_in();
    if (!$auth) {
      if (!$loggedIn) {
        return $this->_result_status401();
      }
      if (isset($this->args['table']) and !$this->_has_rights($this->args['table'])) {
        return $this->_result_norights();
      }
    }
	}
  
  protected function logged_in() {
    return $this->user->logged_in();
  }


  /**
   * Returns a status 401 result: not existing model
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _result_status401() {
    $this->result=array( 'status' => 401 );
    return $this->result;
  }
  
  
  /**
   * Returns a 'no rights' for this api.
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _result_norights() {
    $this->result=array(
      'success' => false,
      'error' => 'NO RIGHTS',
    );
    return $this->result;
  }
  
  
  /**
   * Returns data if everything is ok, and merge data with config data if asked for
   *
   * @return void
   * @author Jan den Besten
   */
  protected function _result_ok() {
    // Add config data if asked for
    $this->_get_config();
    if (!empty($this->args['config'])) {
      $this->result['config'] = array();
      foreach ($this->args['config'] as $cfg_key) {
        $this->result['config'][$cfg_key] = $this->cfg_info[$cfg_key];
      }
    }
    // Prepare end result
    unset($this->result['status']);
    $this->result['success'] = true;
    $this->result['args'] = $this->args;
    if (isset($this->result['args']['password'])) $this->result['args']['password']='***';
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
    
    if (!isset($args['config'])) {
      $args['config'] = array();
    }
    
    return $args;
  }
  
  
  /**
   * Set arguments
   *
   * @param array $args 
   * @return thi
   * @author Jan den Besten
   */
  public function set_args($args=array()) {
    $keys=array_keys($this->args);
    foreach ($keys as $key) {
      if (isset($args[$key])) $this->args[$key]=$args[$key];
    }
    $this->args=$args;
    return $this;
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
    if (!$this->user->logged_in()) return FALSE;
    $rights=$this->user->has_rights($item,$id,$whatRight);
    // if no normal rights and cfg_users and current user, TODO: safety check this!!
    if ($rights<2 and $item=='cfg_users') {
      if (el('where',$this->args,null)=='current') $rights=2;
    }
    return $rights;
  }
  
  
  
  
  /**
   * Adds config data to result if asked for
   *
   * @return void
   * @author Jan den Besten
   */
  protected function _get_config( $asked_for=array() ) {
    $asked_for = array_merge( $asked_for, el('config',$this->args,array()) );
    foreach ($asked_for as $cfg_key) {
      $method='_get_'.$cfg_key;
      if (method_exists($this,$method)) {
        $this->cfg_info[$cfg_key] = $this->$method();
      }
      else {
        $this->cfg_info[$cfg_key] = FALSE;
      }
    }
    return $this->cfg_info;
  }
  
  
  
  /**
   * Gets information about the table (in args)
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_table_info() {
    $table_info=$this->cfg->get('cfg_table_info',$this->args['table']);
    $table_info['fields']   = $this->db->list_fields($this->args['table']);
    $table_info['ui_name']  = $this->ui->get( $this->args['table'] );
    $table_info['sortable'] = !in_array('order',$table_info['fields']);
    $table_info['tree']     = in_array('self_parent',$table_info['fields']);
    return $table_info;
  }
  
  /**
   * Gets information about all fields of the table in args
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_field_info() {
    $hidden_fields=array();
    $field_info=array();
    foreach ($this->cfg_info['table_info']['fields'] as $field) {
      $prefix=get_prefix($field);
      $full_name=$this->args['table'].'.'.$field;
      $info=$this->cfg->get('cfg_field_info',$full_name);
      // if (!el('b_show_in_grid',$info,true)) {
        $hidden_fields[]=$field;
      // }
      // else {
        if ($info) $info=array_unset_keys($info,array('id','field_field'));
        $field_info[$field]=array(
          'table'     => $this->args['table'],
          'field'     => $field,
          'ui_name'   => $this->ui->get($field),
          'info'      => $info,
          'editable'  => !in_array($field, $this->config->item('NON_EDITABLE_FIELDS') ),
          'incomplete'=> in_array($prefix, $this->config->item('INCOMPLETE_DATA_TYPES') )
        );
      // }
    }
    $this->cfg_info['table_info']['hidden_fields'] = $hidden_fields;
    return $field_info;
  }
  
  
  

}

?>