<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Met deze plugin worden de assets in de database gereset.
 * Handig in het geval van handmatig toevoegen van bestanden of als er fouten zijn opgetreden
 * 
 * Gebruik:
 * 
 * - reset_assets        : Alles blijft bestaan in de database, als een bestand niet meer bestaat wordt b_exists = FALSE. Verder worden de file info velden (datum, width & height etc. gereset)
 * - reset_assets/reset  : Helemaal gereset: Eerst wordt res_assets geleegd, dus alle info data zijn verloren.
 * - reset_assets/remove : Idem én alle bestanden die niet gebruikt worden worden verwijderd.
 * 
 * @author Jan den Besten
 */
class Plugin_reset_assets extends Plugin {

  /**
   */
  public function __construct() {
		parent::__construct();
    ini_set('max_execution_time', 600); // 10 minuten mag het script erover doen.
	}

  /**
   * Plugin wordt met URL worden aangeroepen
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   */
	function _admin_api($args=NULL) {
    if ( !$this->CI->flexy_auth->is_super_admin()) return false;

    $arg = el(0,$args,FALSE);
    $this->CI->load->model('assets');
    $paths = $this->CI->assets->refresh('',($arg==='reset'),($arg==='remove'));
    $this->add_message('<ul>');
    foreach ($paths as $path) {
      $this->add_message('<li>`'.$path.'` refreshed.</li>');
    }
    $this->add_message('</ul>');
    return $this->show_messages();
	}

	
}

?>