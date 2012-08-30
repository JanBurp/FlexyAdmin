<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Uitbreiding op [CodeIgniters Output Class](http://codeigniter.com/user_guide/libraries/output.html)
 *
 * Heeft twee doelen:
 * 
 * - Toevoegen van pagina's aan statistieken
 * - Checken of pagina's gecached kunnen worden
 * 
 * @package default
 * @author Jan den Besten
 */

class MY_Output extends CI_OUTPUT {

  /**
   * Display en voegt toe aan stats
   * 
   * @param string $output['']
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	public function _display($output = '')	{
		parent::_display($output);
		$this->add_to_stats();
	}
	
  /**
   * Voegt huidige pagina toe aan statistieken, als dat is ingesteld.
   * 
   * Wordt automatisch aangeroepen bij het tonen van een pagina
   *
   * @return void
   * @author Jan den Besten
   */
	public function add_to_stats() {
		global $CFG;
		if (!isset($CFG->config['add_to_statistics']) or $CFG->item('add_to_statistics')) {
			$STATS=&load_class('stats', 'libraries','');
			$STATS->add_current_uri();
		}
	}

  /**
   * Checkt of pagina gecached kan worden
   *
   * @return bool
   * @author Jan den Besten
   * @ignore
   */
	public function _can_cache() {
    global $CFG;
    if (isset($CFG->config['dont_cache_this_page']) and $CFG->config['dont_cache_this_page']) return FALSE;
		return (empty($_POST) and empty($_GET));
	}

  /**
   * Geeft cached pagina of FALSE
   *
   * @param string $CFG
   * @param string $URI 
   * @return mixed
   * @author Jan den Besten
   * @ignore
   */
	public function _display_cache(&$CFG, &$URI) {
		if ($this->_can_cache())
			return parent::_display_cache($CFG,$URI);
		return FALSE;
	}
	
  /**
   * Cache huidige pagina
   *
   * @param string $time tijd in minuten na hoelang de cache wordt gerefreshed
   * @return mixed
   * @author Jan den Besten
   */
	public function cache($time) {
		if ($this->_can_cache())
			return parent::cache($time);
		return $this;
	}
	


}

/* End of file Output.php */
/* Location: ./system/core/Output.php */