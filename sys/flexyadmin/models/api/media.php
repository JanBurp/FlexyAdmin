<?


/**
 * GET / UPLOAD media files
 * 
 * GET files
 * - GET => array( 'path'=> ... [, limit=0, offset=0 ]  )
 * 
 // * UPLOAD file
 // * - POST => array( 'path'=> ...  .... )
 * 
 * UPDATE file
 * - POST => array( 'path'=> ... , 'where' => ... , 'data' => array(....)  )
 * 
 * DELETE file
 * - POST => array( 'path'=> ... , 'where' => ...  )
 * 
 *
 * @package default
 * @author Jan den Besten
 */
class Media extends ApiModel {
  
  var $needs = array(
    'path'   => '',
  );
  
	public function __construct() {
		parent::__construct();
    $this->load->model('mediatable');
    $this->load->model('file_manager','filemanager');
	}
  
  public function index() {
    if (!$this->_has_rights($this->args['path'])) return $this->_result_status401();
    
    if ( !$this->has_args() ) {
      return $this->_result_wrong_args();
    }
    // Does path exists in media_info?
    if ( ! $this->cfg->get('cfg_media_info',$this->args['path']) ) {
      $this->_set_error('PATH NOT FOUND');
      return $this->_result_ok();
    }
    
    // CFG
    $this->_get_config(array('media_info','img_info'));
    
    // GET
    if ($this->args['type']=='GET') {
      $this->result['data']=$this->_get_files();
      return $this->_result_ok();
    }

    // POST
    if ($this->args['type']=='POST') {
      
      // UPDATE
      if (isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['path'])>=RIGHTS_EDIT) return $this->_result_norights();
        $this->result['data']=$this->_update_file();
        return $this->_result_ok();
      }
      // UPLOAD
      // if (isset($this->args['data']) and !isset($this->args['where'])) {
      //   if (!$this->_has_rights($this->args['p'])>=RIGHTS_ADD) return $this->_result_norights();
      //   $this->result['data']=$this->_insert_row();
      //   return $this->_result_ok();
      // }
      // DELETE
      if (!isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['path'])>=RIGHTS_DELETE) return $this->_result_norights();
        $this->result['data']=$this->_delete_file();
        return $this->_result_ok();
      }
    }
    
    // ERROR -> Wrong arguments
    return $this->_result_wrong_args();
  }
  
  
  /**
   * GET files
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_files() {
    $args=$this->args;
    $files=$this->mediatable->get_files($args['path'],false);
    return $files;
  }


  /**
   * UPDATE file
   *
   * @return bool
   * @author Jan den Besten
   */
  private function _update_file() {
    $args=$this->args;
    $result=$this->mediatable->edit_info($args['path'].'/'.$args['where'], $args['data']);
    if (!$result) {
      $this->_set_error('MAYBE FILE NOT FOUND');
    }
    return $result;
  }


  /**
   * DELETE file
   *
   * @return bool
   * @author Jan den Besten
   */
  private function _delete_file() {
    $args=$this->args;
    $this->filemanager->initialize($args['path']);
    $result = $this->filemanager->delete_file($args['where']);
    if (!$result) {
      $this->_set_error('FILE NOT DELETED, MAYBE NOT FOUND OR NO RIGHTS');
    }
    else {
      $result=$this->mediatable->delete($args['path'].'/'.$args['where'] );
      if (!$result) {
        $this->_set_message('FILE DELETED, BUT ERROR UPDATING IN DATABASE');
      }
    }
    return $result;
  }
  
  
  
  



}


?>
