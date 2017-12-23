<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/** \ingroup plugins
 * Laat laatste update info zien op homepage
 * 
 * @author Jan den Besten
 */

class Plugin_update extends Plugin {

  private $search='';
  private $from='';
  private $start='';
  private $end='';

  /**
   * @author Jan den Besten
   * @internal
   */
	public function __construct() {
		parent::__construct();
    $this->CI->load->model('version');
	}

  /**
   * Log activity on homepage
   *
   * @return void
   * @author Jan den Besten
   */
  // public function _admin_homepage() {
  //   return $this->show_last_update();
  // }

  public function _admin_api() {
    return $this->show_last_update();
  }

  private function show_last_update() {
    if ( !$this->CI->flexy_auth->allowed_to_use_cms()) return false;

    $changelog = $this->CI->version->get_changelog();

    foreach ($changelog as $version => $log) {
      $log = $this->_nicelog($log);
      $this->add_message( '<h1>'.$version.'</h1>' );
      $this->add_message( $log );
    }

    return $this->show_messages();
  }

  private function _nicelog($log) {
    $log = preg_replace('/\[([^\]]*)\]\s*/u', '<br><h3>$1</h3>', $log);
    $log = str_replace("\n",'<br>',$log);
    return $log;
  }

}

?>
