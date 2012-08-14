<?

/**
 * Als de controller een gevraagde module niet kan vinden, dan wordt standaard deze module geladen
 *
 * Je zou hier bijvoorbeeld modules uit de database kunnen laden.
 *
 * @package default
 * @author Jan den Besten
 */
class Fallback extends Module {

  /**
   * Geeft standaard de naam van de module weer
   *
   * @param string $page 
   * @return void
   * @author Jan den Besten
   */
	public function index($page) {
		$content='<h1 id="fallback_module">Fallback Module: '.$this->name.'</h1>';
		return $content;
	}

}

?>