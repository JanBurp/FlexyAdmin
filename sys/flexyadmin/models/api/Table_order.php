<?php

/** \ingroup models
 * API order. Past volgorde van items aan in een tabel
 * 
 * ##UPDATE VOLGORDE
 * 
 * Hiermee wordt van items (id's) in de meegegeven tabel een nieuwe volgorde gemaakt (vanaf een bepaalde waarde)
 * - Als er meerdere ids worden meegegeven dan wordt klakkeloos die volgorde overgenomen en verder niets (geen control op kinderen en tussenliggenden).
 * - Als er één id wordt meegegeven dan wordt gekeken of er kinderen meemoeten en worden tussenliggende items opgeschoven.
 * 
 * ###Parameters (POST):
 * 
 * - `table`                    // De tabel waar de record aan wordt toegevoegd.
 * - `id[]`                     // id's van de items die moeten worden aangepast
 * - `[from=0]`                 // Startwaarde van de nieuwe volgorde van de items
 * 
 * ###Response:
 * 
 * Als response worden de nieuwe volgordes meegegeven.
 * Of in het geval van één meegegeven id, dan wordt alleen de nieuwe volgorde teruggegeven
 * 
 * ###Voorbeeld 1:
 * 
 * - `_api/table_order` met POST data: `table=tbl_menu&id[]=3&id[]=5&id[]=7&from=3`
 * 
 * Voorbeeld response (dump) van bovenstaand voorbeeld:
 * 
 *     [success] => TRUE
 *     [args] => (
 *       [table] => 'tbl_links'
 *       [id] => (
 *                3,
 *                5,
 *                7
 *                )
 *       [from] => 3
 *       [type] => 'POST'
 *     )
 *     [data] => (
 *                0 => array( 'id'=>3, 'order'=>3 ),
 *                1 => array( 'id'=>5, 'order'=>4 ),
 *                2 => array( 'id'=>7, 'order'=>5 ),
 *              )
 * 
 *  ###Voorbeeld 2:
 * 
 * - `_api/table_order` met POST data: `table=tbl_menu&id[]=3&from=3`
 * 
 * Voorbeeld response (dump) van bovenstaand voorbeeld:
 * 
 *     [success] => TRUE
 *     [args] => (
 *       [table] => 'tbl_links'
 *       [id] => 3
 *       [from] => 3
 *       [type] => 'POST'
 *     )
 *     [data] => 3
 * 
 * @author Jan den Besten
 */


class Table_order extends Api_Model {
  
  var $needs = array(
    'table'   => '',
    'id'      => array(),
    'from'    => 0,
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
    if (!$this->_has_rights($this->args['table']))  return $this->_result_status401();
    if (!$this->has_args())                         return $this->_result_wrong_args(); 
    
    // DEFAULTS
    $items=FALSE;

    // GET DATA
    $items=$this->_set_order();
    
    // RESULT
    $this->result['data']=$items;
    return $this->_result_ok();
  }
  
  
  /**
   * Past volgorde aan van meegegeven items
   *
   * @return array
   * @author Jan den Besten
   */
  private function _set_order() {
    $this->load->model('order');
    if ( !is_numeric($this->args['id'])) return FALSE;
    if ( !is_numeric($this->args['from'])) return FALSE;
    // Zet er meerdere
    if (count($this->args['id'])>1) {
      $items = $this->order->set_all( $this->args['table'], $this->args['id'], $this->args['from']);
      return $items;
    }
    // Of één
    if (count($this->args['id'])===1) {
      if (is_array($this->args['id'])) $this->args['id']=current($this->args['id']);
      $new = $this->order->set( $this->args['table'], $this->args['id'], $this->args['from'] );
      return $new;
    }
    //
    return FALSE;
  }
  



}


?>
