<?


/**
 * Eenvoudig voorbeeld van een frontend API.
 * Geeft de tekst van een pagina van de site.
 * 
 * Parameters:
 * 
 * - `uri` - Geef hier de uri van de opgevraagde pagina
 * 
 * Response data:
 * 
 * - `FALSE` - als de pagina niet is gevonden
 * - `array` - van de gevonden pagina zoals die uit de menu tabel komt (meestal `tbl_menu` of `res_menu_result`)
 * 
 * Voorbeeld:
 * 
 * - `/_api/page?uri=contact`
 * 
 * $Author$
 * $Date$
 * $Revision$
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Page extends Api_Model {
  
  var $needs = array(
    'uri'   => '',
  );
  
	public function __construct() {
		parent::__construct();
	}
  
  /**
   * Gets the data and information and returns it
   *
   * @return void
   * @author Jan den Besten
   */
  public function index() {
    if (!$this->_has_rights('tbl_menu')) return $this->_result_status401();
    
    if ( !$this->has_args() ) {
      return $this->_result_wrong_args();
    }
    
    // GET DATA
    $this->load->library('menu');
    $page=$this->menu->get_item($this->args['uri']);
    
    // RESULT
    $this->result['data']=$page;
    return $this->_result_ok();
  }
  
}


?>
