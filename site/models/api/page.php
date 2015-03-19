<?


/**
 * Example api for getting a page
 * 
 * Arguments:
 * - uri
 * 
 * Example:
 * - /_api/page?uri=contact
 * 
 *
 * @params string uri Geef hier de uri van de pagina die je op wilt vragen
 * @package example_api
 * @author Jan den Besten
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
