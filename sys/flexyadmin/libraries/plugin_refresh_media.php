<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Met deze plugin kan de mediatabel gereset worden.
 * Handig in het geval van handmatig toevoegen van bestanden of als er fouten zijn opgetreden
 * 
 * Gebruik:
 * 
 * - ../admin/plugins/refresh_media : De res_media_files wordt gereset: alles blijft bestaan, alleen de info data wordt gereset (width & height etc.)
 * - ../admin/plugins/refresh_media/reset : Idem, maar eerst wordt res_media_files helemaal geleegd, dus alle info data zijn verloren.
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
    $clean=FALSE;
    $remove=TRUE;
    if (isset($args[0]) and $args[0]=='reset') $clean=TRUE;
    if (isset($args[0]) and $args[0]=='remove') $remove=TRUE;
    if ($this->CI->mediatable->exists()) {
      $paths=$this->CI->mediatable->refresh('',$clean,$remove);
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