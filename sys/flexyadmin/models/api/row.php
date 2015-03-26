<?

/**
 * API row. Geeft, bewerkt of voegt een record toe aan een tabel.
 * De specifieke functie wordt bepaald door de (soort) parameters. Zie hieronder per functie.
 * 
 * ##GET ROW
 * 
 * Hiermee wordt een record uit een tabel opgevraagd.
 * 
 * ###Parameters (GET):
 * 
 * - `table`                    // De tabel waar de record van wordt opgevraagd.
 * - `where`                    // Hiermee wordt bepaald welk record wordt opgevraagd.
 * - `[config[]=table_info]`    // Informatie over de tabel kan op deze manier meegenomen worden in het resultaat.
 * - `[config[]=field_info]`    // Informatie over de velden in de tabel kan op deze manier meegenomen worden in het resultaat.
 * 
 * 
 * ###Voorbeelden:
 * 
 * - `_api/row?table=tbl_menu&where=3`
 * - `_api/row?table=tbl_menu&where=10&config[]=table_info`
 * 
 * ###Response:
 * 
 * Voorbeeld response (dump) van `_api/table?row=tbl_menu&where=3`:
 * 
 *     [success] => TRUE
 *     [test] => TRUE
 *     [args] => (
 *       [table] => 'tbl_menu'
 *       [where] => '3'
 *       [type] => 'GET'
 *      )
 *     [data] => (
 *       [id] => '3'
 *       [order] => '0'
 *       [self_parent] => '2'
 *       [uri] => 'subpagina'
 *       [str_title] => 'Subpagina'
 *       [txt_text] => '<p>Een subpagina</p> ...'
 *      )
 * 
 * 
 * ##INSERT ROW
 * 
 * Hiermee wordt een record uit een tabel toegevoegd.
 * 
 * ###Parameters (POST):
 * 
 * - `table`                    // De tabel waar de record aan wordt toegevoegd.
 * - `data`                     // Het nieuwe record
 * - `[config[]=table_info]`    // Informatie over de tabel kan op deze manier meegenomen worden in het resultaat.
 * - `[config[]=field_info]`    // Informatie over de velden in de tabel kan op deze manier meegenomen worden in het resultaat.
 * 
 * 
 * ###Voorbeeld:
 * 
 * - `_api/row` met POST data: `table=tbl_links&data[str_title]=Test&data[url_url]=www.test.nl`
 * 
 * 
 * ###Response:
 * 
 * Als response wordt in `data` het `id` gegeven van het nieuw aangemaakte record.
 * Voorbeeld response (dump) van bovenstaand voorbeeld:
 * 
 *     [success] => TRUE
 *     [args] => (
 *       [table] => 'tbl_links'
 *       [data] => (
 *        [str_title] => 'Test'
 *        [url_url] => 'www.burp.nl'
 *       )
 *       [type] => 'POST'
 *     )
 *     [data] => (
 *      [id] => 12
 *     )
 * 
 * 
 * 
 * ##UPDATE ROW
 * 
 * Hiermee wordt een record uit een tabel aangepast.
 * 
 * ###Parameters (POST):
 * 
 * - `table`                    // De tabel waar de record aan wordt toegevoegd.
 * - `where`                    // Bepaal hiermee welk record moet worden aangepast
 * - `data`                     // De aangepaste data (hoeft niet compleet, alleen de aan te passen velden meegeven is genoeg).
 * - `[config[]=table_info]`    // Informatie over de tabel kan op deze manier meegenomen worden in het resultaat.
 * - `[config[]=field_info]`    // Informatie over de velden in de tabel kan op deze manier meegenomen worden in het resultaat.
 * 
 * 
 * ###Voorbeeld:
 * 
 * - `_api/row` met POST data: `table=tbl_links&where=3&data[str_title]=Test&data[url_url]=www.test.nl`
 * 
 * 
 * ###Response:
 * 
 * Als response wordt in `data` het `id` gegeven van het aangepaste record.
 * Voorbeeld response (dump) van bovenstaand voorbeeld:
 * 
 *     [success] => TRUE
 *     [args] => (
 *       [table] => 'tbl_links'
 *       [where] => 3
 *       [data] => (
 *        [str_title] => 'Test'
 *        [url_url] => 'www.burp.nl'
 *       )
 *       [type] => 'POST'
 *     )
 *     [data] => (
 *      [id] => 3
 *     )
 * 
 * 
 * ##DELETE ROW
 * 
 * Hiermee wordt een record uit een tabel verwijderd.
 * 
 * ###Parameters (POST):
 * 
 * - `table`                    // De tabel waar de record van wordt verwijderd.
 * - `where`                    // Bepaal hierme welk record wordt verwijderd
 * - `[config[]=table_info]`    // Informatie over de tabel kan op deze manier meegenomen worden in het resultaat.
 * - `[config[]=field_info]`    // Informatie over de velden in de tabel kan op deze manier meegenomen worden in het resultaat.
 * 
 * ###Voorbeeld:
 * 
 * - `_api/row` met POST data: `table=tbl_links&where=3
 * 
 * ###Response:
 * 
 * Als response wordt `data` = TRUE als het verwijderen is gelukt.
 * Voorbeeld response (dump) van bovenstaand voorbeeld:
 * 
 *     [success] => TRUE
 *     [args] => (
 *       [table] => 'tbl_links'
 *       [where] => 3
 *       [type] => 'POST'
 *     )
 *     [data] => TRUE
 * 
 * 
 * @package default
 * @author Jan den Besten
 */


class Row extends Api_Model {
  
  var $needs = array(
    'table'   => '',
    // 'where'   => 'first'
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
    if (!$this->_has_rights($this->args['table'])) return $this->_result_status401();
    
    // DEFAULTS
    $fields=FALSE;
    
    if ( !$this->has_args() ) {
      return $this->_result_wrong_args();
    }
    
    // CFG
    $this->_get_config(array('table_info','field_info'));

    // GET
    if ($this->args['type']=='GET') {
      $this->result['data']=$this->_get_row();
      return $this->_result_ok();
    }

    // POST
    if ($this->args['type']=='POST') {
      
      // UPDATE
      if (isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['table'])>=RIGHTS_EDIT) return $this->_result_norights();
        $this->result['data']=$this->_update_row();;
        return $this->_result_ok();
      }
      // INSERT
      if (isset($this->args['data']) and !isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['table'])>=RIGHTS_ADD) return $this->_result_norights();
        $this->result['data']=$this->_insert_row();
        return $this->_result_ok();
      }
      // DELETE
      if (!isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['table'])>=RIGHTS_DELETE) return $this->_result_norights();
        $this->result['data']=$this->_delete_row();
        return $this->_result_ok();
      }
    }
    
    // ERROR -> Wrong arguments
    return $this->_result_wrong_args();
  }
  
  /**
   * Gets the values from the table row
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_row() {
    $this->db->unselect( el(array('field_info','hidden_fields'),$this->cfg_info,array()) );
    $args=$this->_clean_args(array('table','where'));
    $table=$args['table'];
    unset($args['table']);
    $this->crud->table($table);
    $values = $this->crud->get_row($args);
    // trace_(['_get_row'=>$values,'args'=>$this->args]);
    return $values;
  }
  
  /**
   * Update row
   *
   * @return array
   * @author Jan den Besten
   */
  private function _update_row() {
    $args=$this->_clean_args(array('table','where','data'));
    $table=$args['table'];
    unset($args['table']);
    $this->crud->table($table);
    $id = $this->crud->update($args);
    return array('id'=>$id);
  }


  /**
   * Update row
   *
   * @return array
   * @author Jan den Besten
   */
  private function _insert_row() {
    $args=$this->_clean_args(array('table','data'));
    $table=$args['table'];
    unset($args['table']);
    $this->crud->table($table);
    $id = $this->crud->insert($args);
    return array('id'=>$id);
  }


  /**
   * Delete row
   *
   * @return void
   * @author Jan den Besten
   */
  private function _delete_row() {
    $args=$this->_clean_args(array('table','where'));
    $table=$args['table'];
    unset($args['table']);
    $this->crud->table($table);
    return $this->crud->delete($args['where']);
  }


}


?>
