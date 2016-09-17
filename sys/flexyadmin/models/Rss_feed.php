<?php
/**
 * Standaard RSS feed, gaat uit van de standaard demo database
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class RSS_feed extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
  
  /**
   * Geeft RSS feed data terug in formaat:
   * array(
   *  'uri'       => ''
   *  'str_title' => ''
   *  ['txt_text'  => '',]
   *  ['dat_date'  => '',]
   * )
   *
   * @return void
   * @author Jan den Besten
   */
  public function index() {
    return $this->data->table( get_menu_table() )
                      ->select('uri,str_title')
                      ->get_result( 10 );
  }


}

?>
