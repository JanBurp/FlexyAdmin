<?php

/** \ingroup models
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
 * - `options`                  // Hiermee worden opties voor velden toegevoegd (zoals dropdowns etc)
 * - `schemaform`               // Als TRUE dan wordt een json schemaform van het formulier toegevoegd (zie http://schemaform.io)
 * - `[settings=FALSE]`         // Instellingen van de gevraagde tabel
 * 
 * ###Voorbeelden:
 * 
 * - `_api/row?table=tbl_menu&where=3`
 * - `_api/row?table=tbl_menu&where=10&options=true`
 * 
 * ###Response:
 * 
 * Voorbeeld response (dump) van `_api/table?row=tbl_menu&where=3&options=true&schemaform=true`:
 * 
 *     [success] => TRUE
 *     [test] => TRUE
 *     [args] => (
 *       [table] => 'tbl_menu'
 *       [where] => '3'
 *       [type] => 'GET'
 *      )
 *     [options] => (
 *      )
 *     [schemaform] => (
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
 * Hiermee wordt een record uit een tabel toegevoegd
 * De data wordt altijd eerst gevalideerd.
 * 
 * ###Parameters (POST):
 * 
 * - `table`                    // De tabel waar de record aan wordt toegevoegd.
 * - `data`                     // Het nieuwe record
 * - `[settings=FALSE]`         // Instellingen van de gevraagde tabel
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
 * Of `FALSE` bij een validatiefout, dan komen de volgende keys in `info`:
 * 
 * - `validation`         // Of validatie is gelukt (TRUE|FALSE)
 * - `validation_errors`  // Als validatie niet is gelukt komt hier een array van strings: ['veldnaam'=>'Error..']
 * 
 * Voorbeeld response (dump) van bovenstaand voorbeeld (als validatie is gelukt):
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
 * De data wordt altijd eerst gevalideerd.
 * 
 * ###Parameters (POST):
 * 
 * - `table`                    // De tabel waar de record aan wordt toegevoegd.
 * - `where`                    // Bepaal hiermee welk record moet worden aangepast
 * - `data`                     // De aangepaste data (hoeft niet compleet, alleen de aan te passen velden meegeven is genoeg).
 * - `[options=FALSE]`          // Als `TRUE`, dan worden de mogelijke waarden van velden meegegeven.
 * - `[settings=FALSE]`         // Instellingen van de gevraagde tabel
 * 
 * ###Voorbeeld:
 * 
 * - `_api/row` met POST data: `table=tbl_links&where=3&data[str_title]=Test&data[url_url]=www.test.nl`
 * 
 * 
 * ###Response:
 * 
 * Als response wordt in `data` het `id` gegeven van het aangepaste record.
 * Of `FALSE` bij een validatiefout, dan komen de volgende keys in `info`:
 * 
 * - `validation`         // Of validatie is gelukt (TRUE|FALSE)
 * - `validation_errors`  // Als validatie niet is gelukt komt hier een array van strings: ['veldnaam'=>'Error..']
 * 
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
 * - `where`                    // Bepaal hierme welk record wordt verwijderd, als where een array is worden er meerdere tegelijk verwijderd
 * - `[settings=FALSE]`         // Instellingen van de gevraagde tabel

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
 * @author Jan den Besten
 */


class Row extends Api_Model {
  
  var $needs = array(
    'table'   => '',
    // 'where'   => 'first'
    'settings'     => false,
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
    
    // GET
    if ($this->args['type']=='GET') {
      $this->result['data']=$this->_get_row();
      if (el('schemaform',$this->args,false)==true) {
        $this->result['schemaform'] = $this->table_model->schemaform( $this->result['data'],el('table',$this->args) );
      }
      return $this->_result_ok();
    }

    // POST
    if ($this->args['type']=='POST') {
      
      // UPDATE
      if (isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['table'])>=RIGHTS_EDIT) return $this->_result_norights();
        $this->result['data']=$this->_update_row();
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
    $args=$this->_clean_args(array('table','where'));
    $this->table_model->table( $args['table'] );
    if (!isset($args['where'])) $args['where']=null;
    $values = $this->table_model->get_row( $args['where'] );
    $this->info=$this->table_model->get_query_info();
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
    $this->table_model->table( $args['table'] );
    if (isset($args['where'])) $this->table_model->where( $args['where'] );
    $this->table_model->set( $args['data'] );
    $id = $this->table_model->validate()->update();
    $this->info=$this->table_model->get_query_info();
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
    $this->table_model->table( $args['table'] );
    if (isset($args['where'])) $this->table_model->where( $args['where'] );
    $this->table_model->set( $args['data'] );
    $id = $this->table_model->validate()->insert();
    $this->info=$this->table_model->get_query_info();
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
    $model='table_model';
    // Media?
    if ($args['table']==='res_media_files') {
      $this->load->model('tables/res_media_files');
      $model = 'res_media_files';
    }
    $this->$model->table( $args['table'] );
    if (isset($args['where'])) {
      if (is_array($args['where'])) {
        $primary_key = $this->$model->get_setting( 'primary_key' );
        $this->$model->where_in( $primary_key, $args['where'] ); 
      }
      else {
        $this->$model->where( $args['where'] ); 
      }
    }
    $id = $this->$model->delete();
    $this->info=$this->$model->get_query_info();
    return $id;
  }


}


?>
