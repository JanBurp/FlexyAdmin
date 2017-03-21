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
 * - `[as_form=FALSE]`          // Als `TRUE`, dan wordt de data als specifiek form formaat teruggegeven zoals het de backend van de CMS wordt getoond.
 * - `[settings=FALSE]`         // Instellingen van de gevraagde tabel
 * 
 * ###Voorbeelden:
 * 
 * - `_api/row?table=tbl_menu&where=3`
 * - `_api/row?table=tbl_menu&where=10`
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
    	}
  

  /**
   * Gets the data and information and returns it
   *
   * @return void
   * @author Jan den Besten
   */
  public function index() {
    
    // Media?
    $is_media = (isset($this->args['path']) and $this->args['table']==='res_assets');
    if (substr($this->args['table'],6)==='media_') {
      $is_media = true;
      $this->args['path']  = substr($this->args['table'],6);
      $this->args['table'] = 'res_assets';
    }
    
    // Check rechten
    $rights = FALSE;
    if ($is_media) {
      $rights = $this->_has_rights('media_'.$this->args['path'], el('where',$this->args));
    }
    else {
      $rights = $this->_has_rights( $this->args['table'], el('where',$this->args));
    }
    if ( !$rights ) return $this->_result_status401();
    
    if ( !$this->has_args() ) {
      return $this->_result_wrong_args();
    }
    
    // DEFAULTS
    $fields=FALSE;
    
    // GET
    if ($this->args['type']=='GET') {
      $this->result['data']=$this->_get_row();
      return $this->_result_ok();
    }

    // POST
    if ($this->args['type']=='POST') {
      
      // INSERT
      if (isset($this->args['data']) and (!isset($this->args['where']) or $this->args['where']===-1) ) {
        if ( $rights < RIGHTS_ADD ) return $this->_result_norights();
        $this->result['data']=$this->_insert_row();
        return $this->_result_ok();
      }
      
      // UPDATE
      if (isset($this->args['data']) and isset($this->args['where']) and $this->args['where']!==-1) {
        if ( $rights < RIGHTS_EDIT ) return $this->_result_norights();
        $this->result['data']=$this->_update_row();
        return $this->_result_ok();
      }
      
      // DELETE
      if (!isset($this->args['data']) and isset($this->args['where'])) {
        if ( $rights < RIGHTS_DELETE ) return $this->_result_norights();
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
    $args=$this->_clean_args(array('table','where','select'));
    $this->data->table( $args['table'] );
    if (!isset($args['where'])) $args['where']=null;
    if (isset($args['select'])) $this->data->select($args['select']);
    if (el('as_form',$this->args,false)) {
      $values = $this->data->get_form( $args['where'] );
    }
    else {
      $values = $this->data->get_row( $args['where'] );
    }
    $this->info=$this->data->get_query_info();
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
    $data=$this->args['data'];
    $fields=array_keys($data);
    $this->data->table( $args['table'] );
    if ($data) {
      // Call plugins
      $current_data = $this->data->get_row( $args['where'] );
      $data = $this->_after_update( $this->args['table'], $current_data, $data);
      // Save
      $id = $this->data->table($this->args['table'])->validate()->update( $data, $args['where'] );
      $this->info = $this->data->get_query_info();
      // Get data back
      $this->data->table( $args['table'] );
      $new_data = $this->data->get_row( $args['where'] );
      return $new_data;
    }
    return false;
  }


  /**
   * Update row
   *
   * @return array
   * @author Jan den Besten
   */
  private function _insert_row() {
    $args=$this->_clean_args(array('table','data'));
    $this->data->table( $args['table'] );
    $data=$this->args['data'];
    $id = false;
    if ($data) {
      // Call plugins
      $old = $this->data->get_defaults();
      $data = $this->_after_update( $this->args['table'], $old, $data);
      // Insert
      $id = $this->data->table($args['table'])->validate()->insert( $data );
      $this->info = $this->data->get_query_info();
      // Get data back
      $this->data->table( $args['table'] );
      $new_data = $this->data->get_row( $id );
      return $new_data;
    }
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
    $this->data->table( $args['table'] );

    $id = false;
    if (isset($args['where'])) {
      
      $primary_key = $this->data->get_setting( 'primary_key' );
      if (!is_array($args['where'])) $args['where'] = array($args['where']);
      
      // Plugins
      $id = current($args['where']);
			$current_data = $this->data->get_row( $id );
      if ( $this->_after_delete( $this->args['table'], $current_data) ) {
        // Delete items
        $id = $this->data->table( $this->args['table'] )->where_in( $primary_key, $args['where'] )->delete();  
      }
      else {
        // Mag niet verwijderen van een plugin
        $id = false;
      }

    }
    $this->info = $this->data->get_query_info();
    return $id;
  }

}


?>
