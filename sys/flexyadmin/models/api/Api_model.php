<?php

/** \ingroup models
 * Core API model: Roept de opgevraagde API aan en verzorgt het resultaat
 * 
 * - Arguments are set in $_POST or $_GET
 * - See for all possible API calls the other files in sys/flexyadmin/models/api
 * - Common arguments are:
 *    - settings=true
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
 *    - settings=(array) setings if asked for
 *    - info=(array) information about the data
 * 
 * Voorbeelden:
 * 
 * - _api/get_table?table=tbl_links
 * - _api/get_table?table=tbl_links&settings=true
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */


class Api_Model extends CI_Model {
  
  protected $args=array();
  protected $needs=array();
  protected $result=array();
  protected $info=array();
  protected $settings=array();

  /**
   * Eventuel cors domeinen, of FALSE als cors niet is toegestaan
   * http://www.html5rocks.com/en/tutorials/cors/#toc-adding-cors-support-to-the-server
   */
  protected $cors = FALSE;
  
  private $error='';
  private $message='';
  
  
  /**
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
  
  /**
   * Geeft terug of er is ingelogd of niet
   *
   * @return bool
   * @author Jan den Besten
   */
  protected function logged_in() {
    return $this->user->logged_in();
  }
  
  /**
   * Geeft eventuele cors domeinen terug
   *
   * @return mixed
   * @author Jan den Besten
   */
  public function get_cors() {
    return $this->cors;
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
    if (empty($args['settings'])) unset($args['settings']);
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
    // Add settings
    if (el('settings',$this->args,false)) {
      if (isset($this->args['table'])) {
        $this->result['settings'] = $this->_get_settings( $this->args['table'], 'table' );
      }
      if (isset($this->args['path'])) {
        $this->result['settings'] = $this->_get_settings( $this->args['path'], 'path' );
      }
    }
    // Prepare end result
    $this->result['success'] = true;
    // args
    $this->result['args'] = $this->args;
    if (isset($this->result['args']['password'])) $this->result['args']['password']='***';
    if (empty($this->result['args']['config'])) unset($this->result['args']['config']);
    // unset some
    unset($this->result['status']);
    unset($this->result['error']);
    unset($this->result['message']);
    // error/succes
    if ($this->error) {
      $this->result['error']=$this->error;
      $this->result['success']=false;
    }
    // message
    if ($this->message) {
      $this->result['message']=$this->message;
    }
    // format
    if (isset($this->args['format'])) $this->result['format']=$this->args['format'];
    // info
    if (isset($this->info) and $this->info) $this->result['info']=$this->info;
    // options
    if (isset($this->args['options'])) $this->result['options']=$this->_add_options();
    // user
    $this->result['user'] = FALSE;
    if ($this->user->user_name) {
      $this->result['user'] = array(
        'user_name'   => $this->user->user_name,
        'group_id'    => $this->user->group_id,
        'group_name'  => $this->user->rights['str_description'],
      );
    }
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
    $keys=array_merge($keys,array('settings','format'));
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
    
    // create booleans and numbers from strings
    foreach ($args as $key => $value) {
      if (is_string($value)) {
        $value==strtolower($value);
        if ($value==='true')        $args[$key]=TRUE;
        elseif ($value==='false')   $args[$key]=FALSE;
        elseif (is_numeric($value)) $args[$key]=(int) $value;
      }
    }
    
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
   * @param array $keep  default=array('table','where','data') of keys that needs to be kept
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
   * @param string $id default=''
   * @param string $whatRight default=RIGHTS_NO 
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
   * Returns settings for table of path
   * 
   * @param string $what table_name or path
   * @param string $type [table|path]
   * @return array
   * @author Jan den Besten
   */
  protected function _get_settings( $what, $type = 'table' ) {
    $settings = null;
    if ($type == 'table' and !empty($what) ) {
      $settings = $this->_get_table_settings( $what );
    }
    elseif ($type == 'path' and !empty($what) ) {
      $settings = $this->_get_path_settings( $what );
    }
    return $settings;
  }
  
  /**
   * Returns settings for table
   * 
   * @param string $table table_name or path
   * @return array
   * @author Jan den Besten
   */
  protected function _get_table_settings( $table ) {
    $this->table_model->table( $table );
    $settings = $this->table_model->get_setting(array('table','fields','abstract_fields','field_info','grid_set','form_set'));
    
    $this->load->model('ui');
    // field ui_names
    if (isset($settings['field_info'])) {
      foreach ($settings['field_info'] as $field => $info) {
        $settings['field_info'][$field]['ui_name'] = $this->ui->get( $field, $settings['table'] );
      }
    }

    $table_info = array();
    // table ui_name
    $table_info['ui_name']  = $this->ui->get( $settings['table'] );
    // sortable | tree
    $table_info['sortable'] = !in_array('order',$settings['fields']);
    $table_info['tree']     = in_array('self_parent',$settings['fields']);
    $settings = array_add_after( $settings, 'table', array('table_info'=>$table_info) );
    return $settings;
  }
  
  /**
   * Returns settings for path
   * 
   * @param string $path path
   * @return array
   * @author Jan den Besten
   */
  protected function _get_path_settings( $path ) {
    $this->load->model('ui');
    $settings['ui_name'] = $this->ui->get( $path );
    $settings['str_types'] = $this->cfg->get('cfg_media_info',$path,'str_types');
    // $settings = array_keep_keys( $settings, array('str_types') );
    $img_info = $this->cfg->get('cfg_img_info',$path);
    if ($img_info) {
      $img_info = array_keep_keys( $img_info, array('int_min_width','int_min_height') );
      $settings = array_merge( $settings,$img_info );
    }
    return $settings;
  }
  

  
  // /**
  //  * Adds (old) config data to result if asked for
  //  *
  //  * @return void
  //  * @author Jan den Besten
  //  */
  // protected function _get_config( $asked_for=array() ) {
  //   $config = el('config',$this->args,array());
  //   $asked_for = array_merge( $asked_for, $config );
  //   $asked_for = array_unique($asked_for);
  //   foreach ($asked_for as $cfg_key) {
  //     $method='_get_'.$cfg_key;
  //     if (method_exists($this,$method)) {
  //       $this->cfg_info[$cfg_key] = $this->$method();
  //     }
  //     else {
  //       $this->cfg_info[$cfg_key] = FALSE;
  //     }
  //   }
  //   return $this->cfg_info;
  // }
  //
  //
  //
  // /**
  //  * Gets information about the table (in args)
  //  *
  //  * @return array
  //  * @author Jan den Besten
  //  */
  // protected function _get_table_info($table='') {
  //   $table=el('table',$this->args,$table);
  //   if (empty($table)) return FALSE;
  //   $table_info=$this->cfg->get('cfg_table_info',$table);
  //   $table_info['fields']   = $this->db->list_fields($table);
  //   $table_info['ui_name']  = $this->ui->get( $table );
  //   $table_info['sortable'] = !in_array('order',$table_info['fields']);
  //   $table_info['tree']     = in_array('self_parent',$table_info['fields']);
  //   return $table_info;
  // }
  //
  // /**
  //  * Gets information about all fields of the table in args
  //  *
  //  * @return array
  //  * @author Jan den Besten
  //  */
  // protected function _get_field_info($table='') {
  //   $table=el('table',$this->args,$table);
  //   if (empty($table)) return FALSE;
  //   $hidden_fields=array();
  //   $field_info=array();
  //   $fields=$this->db->list_fields($table);
  //   foreach ($fields as $field) {
  //     $prefix=get_prefix($field);
  //     $full_name=$table.'.'.$field;
  //     $info=$this->cfg->get('cfg_field_info',$full_name);
  //     if (!el('b_show_in_grid',$info,true)) {
  //       $hidden_fields[]=$field;
  //     }
  //     $type=get_prefix($field);
  //     if (empty($type)) $type=$field;
  //     if ($info) $info=array_unset_keys($info,array('id','field_field'));
  //     $field_info[$field]=array(
  //       'table'     => $table,
  //       'field'     => $field,
  //       'type'      => $type,
  //       'ui_name'   => $this->ui->get($field),
  //       'info'      => $info,
  //       'editable'  => !in_array($field, $this->config->item('NON_EDITABLE_FIELDS') ),
  //       'incomplete'=> in_array($prefix, $this->config->item('INCOMPLETE_DATA_TYPES') )
  //     );
  //   }
  //   $this->cfg_info['table_info']['hidden_fields'] = $hidden_fields;
  //   return $field_info;
  // }
  //
  // /**
  //  * Gets media info
  //  *
  //  * @return void
  //  * @author Jan den Besten
  //  */
  // protected function _get_media_info($path='') {
  //   $path=el('path',$this->args,$path);
  //   if (empty($path)) return FALSE;
  //   $media_info = $this->cfg->get('cfg_media_info',$path);
  //   return $media_info;
  // }
  //
  // /**
  //  * Gets img info
  //  *
  //  * @author Jan den Besten
  //  */
  // protected function _get_img_info($path='') {
  //   $path=el('path',$this->args,$path);
  //   if (empty($path)) return FALSE;
  //   $img_info = $this->cfg->get('cfg_img_info',$path);
  //   return $img_info;
  // }
  //
  
  /**
   * Voegt opties voor velden toe (voor dropdown velden bijvoorbeeld)
   *
   * @return string
   * @author Jan den Besten
   */
  protected function _add_options() {
    $table=el('table',$this->args);
    if (empty($table)) return FALSE;
    if ( el('options',$this->args,false)===FALSE) return FALSE;
    $options=array();
    $fields=$this->db->list_fields($table);
    foreach ($fields as $field) {
      // options set in cfg_field_info
      $full_name=$table.'.'.$field;
      $info=$this->cfg->get('cfg_field_info',$full_name);
      if (!empty($info['str_options'])) {
        $opts=explode('|',$info['str_options']);
        $opts=array_combine($opts,$opts);
        if (el('b_multi_options',$info,0)>0) {
          $options[$field]['multiple_options']=$opts;
        }
        else {
          $options[$field]['options']=$opts;
        }
      }
      // options for foreign keys
      $prefix=get_prefix($field);
      if ($prefix==PRIMARY_KEY and $field!=PRIMARY_KEY) {
        // get options from db
        $foreignTable=foreign_table_from_key($field);
        if ($foreignTable) {
          // Zijn er teveel opties?
          if ($this->db->count_all($foreignTable)>20) {
            // Geef dan de api aanroep terug waarmee de opties opgehaald kunnen worden
            $options[$field]='_api/table?table='.$foreignTable.'&as_options=true';
          }
          else {
            // Anders geef gewoon de opties terug
            $options[$field]['options']=$this->db->get_options($foreignTable);
          }
          
        }
        
      }
      
      
      
    }
    return $options;
  }
  
  
  

}

?>