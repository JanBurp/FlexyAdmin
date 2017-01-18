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
 *    - user=(array) with user info
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
 * $Revision: 3780 $
 * @copyright: (c) Jan den Besten
 */


class Api_Model extends CI_Model {
  
  protected $args=array();
  protected $needs=array();
  protected $result=array();
  protected $info=array();
  protected $settings=array();

  /**
   * Eventueel cors domeinen, of FALSE als cors niet is toegestaan
   * http://www.html5rocks.com/en/tutorials/cors/#toc-adding-cors-support-to-the-server
   */
  protected $cors = FALSE;


  private $error='';
  private $message='';
  
  
  /**
   */
	public function __construct( $loginRequest = FALSE) {
		parent::__construct();
    $this->load->library('flexy_auth');
    
    // OPTIONS - preflight
    if ($this->input->server('REQUEST_METHOD')==='OPTIONS') {
      $origin = $this->input->get_request_header('Origin',TRUE);
      header("HTTP/1.1 200 OK");
      header("Access-Control-Allow-Origin: ".$origin);
      header("Access-Control-Allow-Methods: GET, POST");
      header("Access-Control-Allow-Credentials: true");
      header("Access-Control-Allow-Headers: Authorization");
      echo '';
      die();
    }
    
    // Get arguments
    $this->args=$this->_get_args($this->needs);
    
    // Standard result
    $this->result['args']=$this->args;
    $this->result['api']=__CLASS__;

    $loggedIn = FALSE;
    // If not login request (_api/auth/login), check authentication header
    if ( !$loginRequest ) {
      $loggedIn = $this->flexy_auth->login_with_authorization_header();

      // Always remove session when no authentication, and return 401
      if ( !$loggedIn ) {
        $this->flexy_auth->logout();
        return $this->_result_status401();
      }
      
      // Set CORS
      $this->cors = '*';
      
      // Rights for given table?
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
    $loggedIn = $this->flexy_auth->logged_in();
    return $loggedIn;
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
    if (el('settings',$this->args,false) and isset($this->args['table'])) {

      // Init table & settings
      $this->data->table($this->args['table']);
      $this->data->add_schemaform_info();
      
      // Haal alle gevraagde settings op (enkele afzonderlijk, of alles)
      if ( $this->args['settings']!==true and $this->args['settings']!=='true') {
        $this->result['settings'] = array();
        $types = explode('|',$this->args['settings']);
        foreach ($types as $type) {
          $type_elements = explode('.',$type);
          $this->result['settings'][$type] = $this->data->get_setting( $type_elements );
        }
      }
      else {
        $this->result['settings'] = $this->data->get_settings();
      }
      
      // Alleen field_info van gevraagde velden & Voeg er extra data aantoe (UIname,options etc)
      if (isset($this->result['settings']['field_info'])) {
        if (el('as_grid',$this->args,false)) {
          $fields = $this->data->get_setting(array('grid_set','fields'));
        }
        elseif ($this->args['table']==='res_assets' and $this->args['path']) {
          $fields = $this->data->get_setting(array('files','thumb_select'));
          $this->data->add_schemaform_info($fields,array('path'=>$this->args['path']));
          $this->result['settings']['field_info'] = $this->data->get_setting('field_info');
        }
        else {
          $fields = $this->data->get_setting(array('fields'));
        }
        $this->result['settings']['field_info'] = array_keep_keys($this->result['settings']['field_info'],$fields);
        $this->result['settings']['field_info'] = sort_keys($this->result['settings']['field_info'],$fields);
      }
      
      // Prepare fieldsets
      if (isset($this->result['settings']['form_set.fieldsets'])) {
        $fieldsets = $this->result['settings']['form_set.fieldsets'];
        $fieldsetsKeys = $this->ui->get(array_keys($fieldsets));
        $fieldsets = array_combine($fieldsetsKeys,$fieldsets);
        $this->result['settings']['fieldsets'] = $fieldsets;
        if (isset($this->result['settings']['form_set.fieldsets'])) unset($this->result['settings']['form_set.fieldsets']);
      }
      
    }

    // Prepare end result
    $this->result['success'] = true;

    // Add args
    $this->result['args'] = $this->args;
    if (isset($this->result['args']['password'])) $this->result['args']['password']='***';
    if (empty($this->result['args']['config'])) unset($this->result['args']['config']);

    // cleanup result
    unset($this->result['status']);
    unset($this->result['error']);
    unset($this->result['message']);

    // Set error/succes 
    if ($this->error) {
      $this->result['error']=$this->error;
      $this->result['success']=false;
    }
    
    // Add message
    if ($this->message) {
      $this->result['message']=$this->message;
    }
    
    // Add format
    if (isset($this->args['format'])) $this->result['format']=$this->args['format'];
    
    // Add info
    if (isset($this->info) and $this->info) $this->result['info']=$this->info;
    
    // Add user
    $this->result['user'] = FALSE;
    $user = $this->flexy_auth->get_user();
    if ($user) {
      $this->result['user'] = array(
        'username'    => el('str_username',$user,el('username',$user)),
        'auth_token'  => $user['auth_token'],
        // 'group_id'    => $user['group_id'],
        // 'group_name'  => $user['group_name'],
      );
    }
    
    // cors
    if (!empty($this->cors)) {
      $this->result['cors'] = $this->cors;
    }
    
    if (DEBUGGING) {
      $this->result['server'] = $_SERVER;
      $this->result['headers'] = $this->input->request_headers();
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
    
    // table=_media_ ?
    if (isset($args['table']) and $args['table']==='_media_') $args['table'] = 'res_assets';
    
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
    $types=array('GET','POST','OPTIONS');
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
        case 'OPTIONS':
          $_OPTIONS = $ar;
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
  protected function _has_rights( $item, $id="", $whatRight=RIGHTS_NO ) {
    if ( !$this->flexy_auth->logged_in() ) return FALSE;
    $rights = $this->flexy_auth->has_rights( $item, $id, $whatRight );
    return $rights;
  }
  
  
  /**
   * Test if user has super admin rights
   *
   * @return bool
   * @author Jan den Besten
   */
  protected function _is_super_admin() {
    return $this->flexy_auth->is_super_admin();
  }
  
  
  // /**
  //  * Returns settings for table of path
  //  *
  //  * @param string $what table_name or path
  //  * @param string $type [table|path]
  //  * @return array
  //  * @author Jan den Besten
  //  */
  // protected function get_settings( $what ) {
  //
  //   return $settings;
  // }
  
  // /**
  //  * Returns settings for table
  //  *
  //  * @param string $table table_name or path
  //  * @return array
  //  * @author Jan den Besten
  //  */
  // protected function _get_table_settings( $table ) {
  //   $this->data->table( $table );
  //   $settings = $this->data->get_settings();
  //   $settings['options'] = $this->data->get_options();
  //
  //   $this->load->model('ui');
  //   // field ui_names
  //   if (isset($settings['field_info'])) {
  //     foreach ($settings['field_info'] as $field => $info) {
  //       $settings['field_info'][$field]['ui_name'] = $this->ui->get( $field, $settings['table'] );
  //     }
  //   }
  //   $table_info = array();
  //   // table ui_name
  //   $table_info['ui_name']  = $this->ui->get( $settings['table'] );
  //   // sortable | tree
  //   $table_info['sortable'] = !in_array('order',$settings['fields']);
  //   $table_info['tree']     = in_array('self_parent',$settings['fields']);
  //   $settings = array_add_after( $settings, 'table', array('table_info'=>$table_info) );
  //   return $settings;
  // }
  //
  // /**
  //  * Returns settings for path
  //  *
  //  * @param string $path path
  //  * @return array
  //  * @author Jan den Besten
  //  */
  // protected function _get_path_settings( $path ) {
  //   $this->load->model('ui');
  //   $settings['ui_name'] = $this->ui->get( $path );
  //   $settings['str_types'] = $this->cfg->get('cfg_media_info',$path,'str_types');
  //   // $settings = array_keep_keys( $settings, array('str_types') );
  //   $img_info = $this->cfg->get('cfg_img_info',$path);
  //   if ($img_info) {
  //     $img_info = array_keep_keys( $img_info, array('int_min_width','int_min_height') );
  //     $settings = array_merge( $settings,$img_info );
  //   }
  //   return $settings;
  // }
  
}

?>