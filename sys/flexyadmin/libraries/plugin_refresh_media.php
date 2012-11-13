<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Refresh res_media_files, als die bestaat.
 * 
 * @package default
 * @author Jan den Besten
 */
class Plugin_refresh_media extends Plugin {

  /**
   * @ignore
   */
  function __construct() {
		parent::__construct();
    $this->CI->load->model('mediatable');
	}

  /**
   * Plugin wordt met URL worden aangeroepen
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	function _admin_api($args=NULL) {
    $clean=TRUE;
    if (isset($args[0]) and $args[0]=='reset') $clean=FALSE;
    if ($this->CI->mediatable->exists()) {
      $paths=$this->CI->mediatable->refresh('',$clean);
      foreach ($paths as $path) {
        $this->add_message($path.' Refreshed.');
      }
    }
    else {
      $this->add_message('`res_media_files` doesn\'t exist.');
    }
    return $this->view();
	}


	
}

?>