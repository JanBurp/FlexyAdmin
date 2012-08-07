<?

/**
 * Toont een lijst met links
 *
 * De getoonde links komen uit de tabel Links (tbl_links)
 *
 * <h2>Bestanden</h2>
 * - site/views/links.php - De view waarin de lijst met links terecht komen
 *
 * <h2>Installatie</h2>
 * - Pas de view (en styling) aan indien nodig
 *
 * @author Jan den Besten
 * @package FlexyAdmin_comments
 *
 */

class Links extends Module {

  /**
   * Hier wordt de module aangeroepen
   *
   * @param string $page
   * @return string 
   * @author Jan den Besten
   */
	public function index($page) {
		if ( $this->CI->db->table_exists('tbl_links')) {
			$links=$this->CI->db->get_results('tbl_links');
			return $this->CI->view('links',array('links'=>$links),true);
		}
		return FALSE;
	}

}

?>