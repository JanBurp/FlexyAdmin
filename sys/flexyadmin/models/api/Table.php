<?

/**
 * API table. Geeft de data van een tabel uit de database.
 * 
 * ###Parameters:
 * 
 * - `table`                    // De gevraagde tabel
 * - `[limit=0]`                // Aantal rijen dat het resultaat moet bevatten. Als `0` dan worden alle rijen teruggegeven.
 * - `[offset=0]`               // Hoeveel rijen vanaf de start worden overgeslagen.
 * - `[txt_as_abstract=FALSE]`  // Als `TRUE`, dan bevatten velden met de `txt_` prefix een ingekorte tekst zonder HTML tags.
 * - `[as_options=FALSE]`       // Als `TRUE`, dan wordt de data als opties teruggegeven die gebruikt kunnen worden in een dropdown field bijvoorbeeld. (`limit` en `offset` werken dan niet)
 * - `[options=FALSE]`          // Als `TRUE`, dan worden de mogelijke waarden van velden meegegeven.
 * - `[config[]=table_info]`    // Informatie over de tabel kan op deze manier meegenomen worden in het resultaat.
 * - `[config[]=field_info]`    // Informatie over de velden in de tabel kan op deze manier meegenomen worden in het resultaat.
 * 
 * 
 * ###Voorbeelden:
 * 
 * - `_api/table?table=tbl_menu`
 * - `_api/table?table=tbl_menu&offset=9&limit=10`
 * - `_api/table?table=tbl_menu&txt_as_abstract=TRUE`
 * - `_api/table?table=tbl_menu&config[]=table_info`
 * 
 * ###Response:
 * 
 * De `info` response key geeft extra informatie over het resultaat, met de volgende keys:
 * 
 * - `rows`       // Het aantal items in `data`.
 * - `total_rows` // Het totaal aantal items zonder `limit`
 * - `table_rows` // Het totaal aantal items in de gevraagde tabel
 * 
 * ###Voorbeeld response (dump) van `_api/table?table=tbl_menu`:
 * 
 *     [success] => TRUE
 *     [test] => TRUE
 *     [args] => (
 *       [table] => 'tbl_menu'
 *       [limit] => 0
 *       [offset] => 0
 *       [type] => 'GET'
 *      )
 *     [data] => (
 *       [1] => (
 *         [id] => '1'
 *         [order] => '0'
 *         [self_parent] => '0'
 *         [uri] => 'gelukt'
 *         [str_title] => 'Gelukt!'
 *         [txt_text] => 'Als je dit ziet is het je gelukt om FlexyAdmin te installeren en werkend te krijgen.'
 *        )
 *       [2] => (
 *         [id] => '2'
 *         [order] => '1'
 *         [self_parent] => '0'
 *         [uri] => 'een_pagina'
 *         [str_title] => 'Een pagina'
 *         [txt_text] => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.'
 *        )
 *       [3] => (
 *         [id] => '3'
 *         [order] => '0'
 *         [self_parent] => '2'
 *         [uri] => 'subpagina'
 *         [str_title] => 'Subpagina'
 *         [txt_text] => 'Een subpagina...'
 *        )
 *       [5] => (
 *         [id] => '5'
 *         [order] => '1'
 *         [self_parent] => '2'
 *         [uri] => 'nog_een_subpagina'
 *         [str_title] => 'Nog een subpagina'
 *         [txt_text] => ''
 *        )
 *       [4] => (
 *         [id] => '4'
 *         [order] => '2'
 *         [self_parent] => '0'
 *         [uri] => 'contact'
 *         [str_title] => 'Contact'
 *         [txt_text] => 'Hier een voorbeeld van een eenvoudig contactformulier.'
 *        )
 *      )
 *     [info] => (
 *         [rows] => 5
 *         [total_rows] => 5
 *         [table_rows] => 5
 *        )
 * 
 *    
 * @package default
 * @author Jan den Besten
 */

class Table extends Api_Model {
  
  var $needs = array(
    'table'   => '',
    'limit'   => 0,
    'offset'  => 0
  );
  
	public function __construct() {
		parent::__construct();
    $this->load->model('ui');
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
    // CFG
    $this->_get_config(array('table_info','field_info'));
    // GET DATA
    $items=$this->_get_data();
    // PROCESS DATA
    if ($items) {
      $items = $this->_process_data($items);
    }
    
    // RESULT
    $this->result['data']=$items;
    return $this->_result_ok();
  }
  
  
  /**
   * Gets the data from the table
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_data() {
    // AS OPTIONS
    if (el('as_options',$this->args,false)) {
      $where=el('where',$this->args,'');
      $items=$this->db->get_options($this->args['table'],$where);
      $this->info = array(
        'rows'        => count($items),
        'total_rows'  => $this->db->last_num_rows_no_limit(),
        'table_rows'  => $this->db->count_all($this->args['table']),
      );
    }
    // NORMAL DATA
    else {
      // UNSELECT Hidden fields
      $this->db->unselect( el(array('field_info','hidden_fields'),$this->cfg_info,array()) );
      // ABSTRACTS of txt?
      if (el('txt_as_abstract',$this->args,false))                $this->db->max_text_len(100);
      // As TREE?
      // if (el( array('table_info','tree'), $this->cfg_info,false)) $this->db->order_as_tree();
      // if (el( array('table_info','tree'), $this->cfg_info,false)) $this->db->order_by('order');
      $items = $this->crud->get($this->args);
      $this->info  = $this->crud->get_info();
    }
    return $items;
  }
  
  
  /**
   * Loop through all rows and process them
   *
   * @param string $items 
   * @param string $table_info 
   * @param string $field_info 
   * @return void
   * @author Jan den Besten
   */
  private function _process_data($items) {
    
    $txt_as_abstract = el('txt_as_abstract',$this->args,false);

    // init STRIP TAGS in txt fields
    if ( $txt_as_abstract ) {
      $fields=$this->cfg_info['table_info']['fields'];
      $txtKeys=array_combine($fields,$fields);
      $txtKeys=filter_by_key($txtKeys,'txt');
    }

    // LOOP ROWS IF NEEDED
    if ($txt_as_abstract) {
      foreach ($items as $id => $row) {
        // STRIP TAGS in txt fields
        if ( $txt_as_abstract ) {
          foreach ($txtKeys as $key) {
            $items[$id][$key]=strip_tags($row[$key]);
          }
        }
      }
    }
    
    return $items;
  }
  

}


?>
