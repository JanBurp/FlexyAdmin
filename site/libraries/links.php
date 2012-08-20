<?php

/**
	* Toont een lijst met links
	*
	* De getoonde links komen uit de tabel Links (tbl_links)
	*
	* Bestanden
	* ----------------
	*
	* - site/views/links.php - De view waarin de lijst met links terecht komen
	*
	* Installatie
	* ----------------
	*
	* - Pas de view (en styling) aan indien nodig
	*
	* @author Jan den Besten
	* @package FlexyAdmin_comments
	*
	*/

class Links extends Module {

  /**
   * @ignore
   */
  public function __construct() {
    parent::__construct();
  }

	/**
		* Hier wordt de module aangeroepen
		*
		* @param string  $page
		* @return string
		* @author Jan den Besten
		* @ignore
		*/
	public function index( $page ) {
		if ( $this->CI->db->table_exists( 'tbl_links' ) ) {
			$links=$this->CI->db->get_results( 'tbl_links' );
			return $this->CI->view( 'links', array( 'links'=>$links ), true );
		}
		return FALSE;
	}

}

?>
