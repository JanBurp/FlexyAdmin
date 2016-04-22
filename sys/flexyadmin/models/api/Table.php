<?php

/** \ingroup models
 * API table. Geeft de data van een tabel uit de database.
 * 
 * ###Parameters:
 * 
 * - `table`                    // De gevraagde tabel
 * - `[path]`                   // Eventueel op te vragen map voor media/assets (bij table='res_media_files')
 * - `[limit=0]`                // Aantal rijen dat het resultaat moet bevatten. Als `0` dan worden alle rijen teruggegeven.
 * - `[offset=0]`               // Hoeveel rijen vanaf de start worden overgeslagen.
 * - `[sort='']`                // De volgorde van het resultaat, geef een veld, bijvoorbeeld `str_title` of `_str_title` voor DESC
 * - `[filter='']`              // Eventuele string waarop alle data wordt gefilterd
 * - `[as_grid=FALSE]`          // Als `TRUE`, dan wordt de data als specifieke grid formaat teruggegeven zoals het de backend van de CMS wordt getoond. NB Kan onderstaande opties overrulen!
 * - `[txt_abstract=0]`         // Als `TRUE`, dan bevatten velden met de `txt_` prefix een ingekorte tekst zonder HTML tags. Of een integer waarde voor de lengte.
 * - `[as_options=FALSE]`       // Als `TRUE`, dan wordt de data als opties teruggegeven die gebruikt kunnen worden in een dropdown field bijvoorbeeld. (`limit` en `offset` werken dan niet)
 * - `[options=FALSE]`          // Als `TRUE`, dan worden de mogelijke waarden van velden meegegeven.
 * - `[settings=FALSE]`         // Instellingen van de gevraagde tabel
 * - `[schemaform=FALSE]`       // Als TRUE dan wordt een json schemaform van het formulier toegevoegd (zie http://schemaform.io)
 * 
 * 
 * ###Voorbeelden:
 * 
 * - `_api/table?table=tbl_menu`
 * - `_api/table?table=tbl_menu&offset=9&limit=10`
 * - `_api/table?table=tbl_menu&txt_abstract=TRUE`
 * - `_api/table?table=tbl_menu&settings=true`
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
 *         [num_rows] => 5
 *         [total_rows] => 5
 *        )
 * 
 *    
 * @author Jan den Besten
 */

class Table extends Api_Model {
  
  var $needs = array(
    'table'        => '',
    'limit'        => 0,
    'offset'       => false, // met false werkt jump_to_today
    // 'sort'         => '',
    // 'filter'       => '',
    'as_grid'      => false,
    'as_options'   => false,
    'txt_abstract' => 0,
    'settings'     => false,
    'schemaform'   => false,
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
    if (!$this->has_args())                         return $this->_result_wrong_args(); 
    if (!$this->_has_rights($this->args['table']))  return $this->_result_status401();
    if ($this->args['table']==='res_media_files' AND isset($this->args['path']) AND !$this->_has_rights($this->args['path']))  return $this->_result_status401();
    
    // Opties toevoegen bij as_grid en settings
    if ( $this->args['as_grid'] and $this->args['settings'] ) $this->args['options'] = true;
    
    // DEFAULTS
    $items=FALSE;

    // GET DATA
    $items=$this->_get_data();
    
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
    
    $this->data->table( $this->args['table'] );
    
    // Normaal resultaat, of grid resultaat
    if ( el('as_grid',$this->args,false) ) {
      // Grid, pagination, sort
      $this->args['sort'] = el( 'sort', $this->args, '' );
      $this->args['filter'] = el( 'filter', $this->args, '' );
      // Media?
      if ( $this->args['table'] === 'res_media_files' AND isset($this->args['path']) ) $this->args['where'] = array( 'path' => $this->args['path'] );
      // Where?
      if (!isset($this->args['where'])) $this->args['where'] = '';
      $items = $this->data->get_grid( $this->args['limit'], $this->args['offset'], $this->args['sort'], $this->args['filter'], $this->args['where'] );
    }
    else {
      // Normaal: where, txt_abstract, options
      if ( isset($this->args['where']) ) $this->data->where( $this->args['where'] );
      if ( isset($this->args['txt_abstract'])) $this->data->select_txt_abstract( $this->args['txt_abstract'] );
      if ( el('as_options',$this->args,false) ) $this->data->select_abstract( TRUE );
      $items = $this->data->get_result( $this->args['limit'], $this->args['offset'] );
    }
    
    // Info
    $this->info = $this->data->get_query_info();
    
    // Schemaform
    if ( el('schemaform',$this->args,false) ) {
      $this->result['schemaform'] = $this->data->schemaform( current($items), el('table',$this->args) );
    }
    
    return $items;
  }
 

}


?>
