<?php

/** \ingroup models
 * API options. Geeft de opties van een (veld) tabel uit de database.
 * 
 * ###Parameters:
 * 
 * - `table`                    // De gevraagde tabel
 * - `[where]                   // Eventueel unieke 'id' oid
 * - `[field]`                  // Eventueel op te vragen map voor media/assets (bij table='res_assets')
 * 
 * ###Response:
 * 
 * De `info` response key geeft extra informatie over het resultaat, met de volgende keys:
 * 
 * - `num_rows`   // Het aantal items in `data`.
 * - `total_rows` // Het totaal aantal items zonder `limit`
 * 
 * ###Voorbeeld response (dump) van `_api/table?table=tbl_menu`:
 * 
 *     [success] => TRUE
 *     [test] => TRUE
 *     [args] => (
 *       [table] => 'tbl_menu'
 *       [type] => 'GET'
 *      )
 *     [data] => (
 *       [field] => (
 *         [id] => '1'
 *         [order] => '0'
 *         [self_parent] => '0'
 *         [uri] => 'gelukt'
 *         [str_title] => 'Gelukt!'
 *         [txt_text] => 'Als je dit ziet is het je gelukt om FlexyAdmin te installeren en werkend te krijgen.'
 *        )
 *    
 * @author Jan den Besten
 */

class Options extends Api_Model {
  
  var $needs = array(
    'table'        => '',
  );

  private $rights = 0;
  
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
    
    if (!$this->has_args()) return $this->_result_wrong_args(); 
    
    // Check rechten
    if ($this->args['table']==='res_assets' AND isset($this->args['path'])) {
      $this->rights = $this->_has_rights('media_'.$this->args['path']);
    }
    else {
      $this->rights = $this->_has_rights($this->args['table']);
    }
    if ( !$this->rights ) {
      return $this->_result_status401();
    }

    // GET DATA
    $options = $this->data->table($this->args['table'])->where($this->args['where'])->get_options( el('field',$this->args), array('one_to_one','many_to_one','many_to_many','one_to_many'));
    
    // RESULT
    $this->result['data']=$options;
    return $this->_result_ok();
  }
 

}


?>
