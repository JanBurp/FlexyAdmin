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

  private $error        = '';
  private $message      = '';
  private $message_type = '';


  /**
   */
	public function __construct( $loginRequest = FALSE) {
    parent::__construct();
    $this->load->library('flexy_auth');

    // OPTIONS - preflight
    if ($this->input->server('REQUEST_METHOD')==='OPTIONS') {
      // $loginRequest word bij preflight niet goed meegegeven.. dus nog een keer.
      $loginRequest = $this->uri->get(3)==='login';
      $uri = $this->uri->uri_string();
      $origin = $this->input->get_request_header('Origin',TRUE);
      header("HTTP/1.1 200 OK");
      header("Access-Control-Allow-Origin: ".$origin);
      if (!$loginRequest) {
        header("Access-Control-Allow-Methods: GET, POST");
      }
      else {
        header("Access-Control-Allow-Methods: POST");
      }
      header("Access-Control-Allow-Credentials: true");
      if (!$loginRequest) {
        header("Access-Control-Allow-Headers: Authorization");
      }
      echo '';
      die();
    }

    // Get arguments
    $this->args=$this->_get_args($this->needs);

    // Standard result
    $this->result['args'] = $this->args;
    $this->result['api']  = __CLASS__;

    $loggedIn = FALSE;

    if ( $loginRequest ) {
      unset($_POST['_authorization']);
      unset($_GET['_authorization']);
    }
    else {
      $loggedIn = $this->flexy_auth->login_with_authorization_header();
      // Set CORS
      $this->cors = '*';
      // Rights for given table?
      if (isset($this->args['table']) and !$this->_has_rights($this->args['table'])) {
        return $this->_result_norights();
      }
    }

    // Always remove session when no authentication, and return 401
    if ( !$loggedIn and !defined('PHPUNIT_TEST')) {
      $this->flexy_auth->logout();
      return $this->_result_status401();
    }

    $this->load->model('plugin_handler');
    $this->plugin_handler->init_plugins();
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
    $this->lang->load('vue');
    log_message('info', 'API NO RIGHTS : '.array2json($this->args));
    $this->result=array(
      'success' => false,
      'error'   => lang('vue_api_error_401'),
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
    unset($this->result['status']);
    unset($this->result['message']);
    unset($this->result['args']['_authorization']);
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
    $this->load->model('log_activity');
    $log_args = $this->args;
    if (isset($log_args['_authorization'])) $log_args['_authorization'] = '...';
    if (isset($log_args['password']))       $log_args['password'] = '***';
    $this->log_activity->api($log_args,$this->flexy_auth->get_user(null,'id'));


    // Add settings
    if (el('settings',$this->args,false) and isset($this->args['table'])) {

      // Init table & settings
      $this->data->table($this->args['table']);
      if (isset($this->args['path']) && $this->args['table']==='res_assets') {
        $this->data->set_path($this->args['path']);
      }

      // Haal alle gevraagde settings op (enkele afzonderlijk, of alles)
      $defaults = array('abstract_fields','abstract_delimiter');
      if ( $this->args['settings']!==true and $this->args['settings']!=='true') {
        $this->result['settings'] = array();
        $types = explode('|',$this->args['settings']);
        $types = array_merge($defaults,$types);
        foreach ($types as $type) {
          $this->result['settings'][$type] = $this->data->get_setting( $type );
        }
      }
      else {
        $this->result['settings'] = $this->data->get_settings();
      }

      // Assets?
      if (isset($this->args['path'])) {
        $assets = $this->config->get_item(array('assets','assets',$this->args['path']));
        if ($assets) {
          $assets = array_keep_keys($assets,array('types','scale','img_width','img_height','min_width','min_height'));
          $this->result['settings']['assets'] = $assets;
        }
      }
    }

    // Prepare end result
    $this->result['success'] = true;

    // Add args
    if (is_array($this->args)) {
      $this->result['args'] = $this->args;
      if (isset($this->result['args']['password'])) $this->result['args']['password']='***';
      if (empty($this->result['args']['config'])) unset($this->result['args']['config']);
      unset($this->result['args']['_authorization']);
    }

    // cleanup result
    // unset($this->result['args']);
    unset($this->result['status']);
    unset($this->result['error']);
    unset($this->result['message']);
    unset($this->result['message_type']);

    // Set error/succes
    if ($this->error) {
      $this->result['error']   = $this->error;
      $this->result['success'] = false;
    }

    // Add message
    if ($this->message) {
      $this->result['message'] = $this->message;
      if (!empty($this->message_type)) $this->result['message_type'] = $this->message_type;
    }

    // Add info
    if (isset($this->info) and $this->info) $this->result['info']=$this->info;

    // Add user
    $this->result['user'] = FALSE;
    $user = $this->flexy_auth->get_user();
    if ($user) {
      $this->result['user'] = array(
        'username'    => el('str_username',$user,el('username',$user)),
        // 'auth_token'  => $user['auth_token'],
      );
    }

    // cors
    if (!empty($this->cors)) {
      $this->result['cors'] = $this->cors;
    }

    // if (DEBUGGING) {
    //   $this->result['server'] = $_SERVER;
    //   $this->result['headers'] = $this->input->request_headers();
    // }

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
   * Sets message type
   *
   * @param string $type
   * @return this
   * @author Jan den Besten
   */
  protected function _set_message_type($type) {
    $this->message_type = $type;
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
    return $this->args;
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
   * Gives clean args and decodes (array) data if needed
   *
   * @param array $keep  default=array('table','where','data') of keys that needs to be kept
   * @return array
   * @author Jan den Besten
   */
  protected function _clean_args($keep=array('table','where','data')) {
    $data = array_keep_keys($this->args,$keep);
    if (isset($data['data'])) {
      foreach ($data['data'] as $field => $value) {
        if (get_suffix($field,'__')==='array') {
          $data['data'][remove_suffix($field,'__')] = json2array($value);
          unset($data['data'][$field]);
        }
      }
    }
    return $data;
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


  /**
   * PLUGIN STUFF
   */
	protected function _init_plugin($table,$oldData=NULL,$newData=NULL) {
		$this->plugin_handler->set_data('old',$oldData);
		$this->plugin_handler->set_data('new',$newData);
		$this->plugin_handler->set_data('table',$table);
	}

	protected function _before_grid($table) {
		$this->_init_plugin($table,NULL,NULL);
		return $this->plugin_handler->call_plugins_before_grid_trigger();
	}

	protected function _after_delete($table,$oldData=NULL) {
		$this->_init_plugin($table,$oldData,NULL);
		return $this->plugin_handler->call_plugins_after_delete_trigger();
	}

  protected function _before_form($table,$data) {
		$this->_init_plugin($table,$data,NULL);
		$data=$this->plugin_handler->call_plugins_before_form_trigger();
    return $data;
  }

	protected function _after_update($table,$oldData=NULL,$newData=NULL) {
		$this->_init_plugin($table,$oldData,$newData);
		$newData = $this->plugin_handler->call_plugins_after_update_trigger();
    $messages = $this->plugin_handler->get_plugins_messages();
    if ($messages) {
      if (is_array($messages)) $messages = implode($messages);
      $this->message = $messages;
      $this->message_type = 'popup';
    }
		return $newData;
	}


}

?>
