<?

/**
 * Calls the right API and returns the (ajax)result or a 401
 * 
 * - Arguments are set in $_POST or $_GET
 * - See for all possible API calls the other files in sys/flexyadmin/models/api
 * - Common arguments are:
 *    - config[]=table_info
 *    - config[]=field_info
 *    - format=json
 *    - table=(get_table,get_form)
 *    - where=(get_form)
 * - result can have these keys (same as AJAX controller returns):
 *    - status=401 // if call is unauthorized
 *    - success=[true|false]
 *    - error=(string)
 *    - message=(string)
 *    - args=(array) given arguments
 *    - needs=array of needed arguments (and there defaults)
 *    - api=(string) name of the api call
 *    - format=json
 *    - data=(mixed) the returned data
 *    - config=(array) config data if asked for
 *    - info=(array) information about the data
 * 
 * Examples:
 * 
 * - _api/get_table?table=tbl_links
 * - _api/get_table?table=tbl_links&config[]=table_info&config[]=field_info
 * 
 * @package default
 * @author Jan den Besten
 */


class Api_Model extends CI_Model {
  
  protected $args=array();
  protected $needs=array();
  protected $result=array();
  protected $info=array();
  protected $cfg_info=array();
  
  private $error='';
  private $message='';
  
  
  /**
   * @ignore
   */
	public function __construct() {
		parent::__construct();
    // Get arguments
    $this->args=$this->_get_args($this->needs);
    // Standard result
    $this->result['args']=$this->args;
    $this->result['api']=__CLASS__;
    // $this->result['format']='json';
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
    log_message('info', 'API 401 : '.array2json($this->args));
    $this->result['status']=401;
    $this->result=array_keep_keys($this->result,array('status','format'));
    return $this->result;
  }
  
  
  /**
   * Returns a 'no rights' for this api.
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _result_norights() {
    log_message('info', 'API NO RIGHTS : '.array2json($this->args));
    $this->result=array(
      'success' => false,
      'error' => 'NO RIGHTS',
    );
    return $this->result;
  }
  
  
  /**
   * Returns a 'no arguments' for this api.
   *
   * @return array
   * @author Jan den Besten
   */
  protected function _result_wrong_args() {
    log_message('info', 'API WRONG ARGUMENTS : '.array2json($this->args));
    $args=$this->args;
    if (empty($args['config'])) unset($args['config']);
    $this->result['success'] = false;
    $this->result['error']   = 'WRONG ARGUMENTS';
    // $this->result['needs']   = $this->needs;
    unset($this->result['status']);
    unset($this->result['message']);
    return $this->result;
  }
  
  
  /**
   * Returns data if everything is ok, and merge data with config data if asked for
   *
   * @return void
   * @author Jan den Besten
   */
  protected function _result_ok() {
    log_message('info', 'API OK : '.array2json($this->args));
    // Add config data if asked for
    $this->_get_config();
    unset($this->result['config']);
    if (!empty($this->args['config'])) {
      $this->result['config'] = array();
      foreach ($this->args['config'] as $cfg_key) {
        $this->result['config'][$cfg_key] = $this->cfg_info[$cfg_key];
      }
    }
    // Prepare end result
    $this->result['success'] = true;
    $this->result['args'] = $this->args;
    if (isset($this->result['args']['password'])) $this->result['args']['password']='***';
    if (empty($this->result['args']['config'])) unset($this->result['args']['config']);
    unset($this->result['status']);
    unset($this->result['error']);
    unset($this->result['message']);
    if ($this->error) {
      $this->result['error']=$this->error;
      $this->result['success']=false;
    }
    if ($this->message) {
      $this->result['message']=$this->message;
    }
    if (isset($this->args['format'])) $this->result['format']=$this->args['format'];
    if (isset($this->info) and $this->info) $this->result['info']=$this->info;
    return $this->result;
  }
  
  /**
   * Sets error in result
   *
   * @param string $error 
   * @return this
   * @author Jan den Besten
   */
  protected function _set_error($error) {
    $this->error = $error;
    return $this;
  }

  /**
   * Sets message in result
   *
   * @param string $message 
   * @return this
   * @author Jan den Besten
   */
  protected function _set_message($message) {
    $this->message = $message;
    return $this;
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
    $keys=array_merge($keys,array('config','format'));
    $args=array();
    
    // GET
    if (!$args and (!empty($_SERVER['QUERY_STRING']) or !empty($_GET))) {
      if (empty($_GET)) parse_str($_SERVER['QUERY_STRING'],$_GET);
      $args=$this->input->get();
      $args['type']='GET';
    }

    // POST
    if (!$args and !empty($_POST)) {
      $args=$this->input->post();
      $args['type']='POST';
    }
    
    // merge with defaults
    $args=array_merge($this->needs,$args);
    
    // config
    if (!isset($args['config'])) {
      $args['config'] = array();
    }
    if (!is_array($args['config'])) $args['config']=array($args['config']);
    
    if (isset($args['format'])) $this->result['format']=$args['format'];
    if (!isset($args['type'])) $args['type']='GET';
    
    // trace_(['defaults'=>$defaults,'POST'=>$_POST,'GET'=>$_GET,'args'=>$args]);
    
    return $args;
  }
  
  
  /**
   * Set arguments
   *
   * @param array $args 
   * @return this
   * @author Jan den Besten
   */
  public function set_args($args=array()) {
    $this->error='';
    $this->message='';
    unset($_GET);
    unset($_POST);
    $types=array('GET','POST');
    $types_exists = FALSE;
    foreach ($types as $type) {
      $types_exists = ($types_exists or array_key_exists($type,$args));
    }
    if (!$types_exists) $args=array('GET'=>$args);
    
    // Set types
    foreach ($args as $type => $ar) {
      switch ($type) {
        case 'GET':
          $_GET = $ar;
          break;
        case 'POST':
          $_POST = $ar;
          break;
      }
    }
    $this->args=$this->_get_args( $this->needs );
    return $this-args;
  }
  

  /**
   * Test if call gives needed arguments
   *
   * @return bool
   * @author Jan den Besten
   */
  protected function has_args($type='') {
    if (!empty($type) and ($this->args['type']!==$type)) return FALSE;
    $has_args = TRUE;
    foreach ($this->needs as $key => $value) {
      $has_args = ( $has_args AND isset($this->args[$key]) AND $this->args[$key]!=='' );
    }
    return $has_args;
  }
  
  
  /**
   * Gives clean args
   *
   * @param array of keys that needs to be kept [array('table','where','data')]
   * @return array
   * @author Jan den Besten
   */
  protected function _clean_args($keep=array('table','where','data')) {
    return array_keep_keys($this->args,$keep);
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
  protected function _has_rights($item,$id="",$whatRight=RIGHTS_NO) {
    if (!$this->user->logged_in()) return FALSE;
    $rights=$this->user->has_rights($item,$id,$whatRight);
    // if no normal rights and cfg_users and current user rights are ok, TODO: safety check this!!
    if ($rights<RIGHTS_EDIT and $item=='cfg_users') {
      if (el('where',$this->args,null)=='current') $rights=RIGHTS_EDIT;
    }
    return $rights;
  }
  
  
  /**
   * Test if user has super admin rights
   *
   * @return bool
   * @author Jan den Besten
   */
  protected function _is_super_admin() {
    return $this->user->is_super_admin();
  }
  
  
  /**
   * Adds config data to result if asked for
   *
   * @return void
   * @author Jan den Besten
   */
  protected function _get_config( $asked_for=array() ) {
    $config = el('config',$this->args,array());
    $asked_for = array_merge( $asked_for, $config );
    $asked_for = array_unique($asked_for);
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
  protected function _get_table_info($table='') {
    $table=el('table',$this->args,$table);
    if (empty($table)) return FALSE;
    $table_info=$this->cfg->get('cfg_table_info',$table);
    $table_info['fields']   = $this->db->list_fields($table);
    $table_info['ui_name']  = $this->ui->get( $table );
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
  protected function _get_field_info($table='') {
    $table=el('table',$this->args,$table);
    if (empty($table)) return FALSE;
    $hidden_fields=array();
    $field_info=array();
    $fields=$this->db->list_fields($table);
    foreach ($fields as $field) {
      $prefix=get_prefix($field);
      $full_name=$table.'.'.$field;
      $info=$this->cfg->get('cfg_field_info',$full_name);
      if (!el('b_show_in_grid',$info,true)) {
        $hidden_fields[]=$field;
      }
      // else {
        if ($info) $info=array_unset_keys($info,array('id','field_field'));
        $field_info[$field]=array(
          'table'     => $table,
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

  /**
   * Gets media info
   *
   * @return void
   * @author Jan den Besten
   */
  protected function _get_media_info($path='') {
    $path=el('path',$this->args,$path);
    if (empty($path)) return FALSE;
    $media_info = $this->cfg->get('cfg_media_info',$path);
    return $media_info;
  }
  
  /**
   * Gets img info
   *
   * @author Jan den Besten
   */
  protected function _get_img_info($path='') {
    $path=el('path',$this->args,$path);
    if (empty($path)) return FALSE;
    $img_info = $this->cfg->get('cfg_img_info',$path);
    return $img_info;
  }
  
  
  

}

?>