<?php

/** \ingroup models
 * API: media. Geeft een lijst, bewerkt of upload bestanden toe aan een map.
 * De specifieke functie wordt bepaald door de (soort) parameters. Zie hieronder per functie.
 * 
 * ##GET files
 * 
 * Hiermee wordt een lijst opgevraagd van een map
 * 
 * ###Parameters (GET):
 * 
 * - `path`                     // De map is assets waarvan de bestanden worden opgevraagd.
 * - `[offset=0]`               // Sla de eerste bestanden in de lijst over
 * - `[limit=0]`                // Geef een maximaal aantal bestanden terug (bij 0 worden alle bestanden teruggegeven)
 * - `[settings=FALSE]`         // Instellingen van de gevraagde map 
 * 
 * ###Voorbeelden:
 * 
 * - `_api/media?path=pictures`
 * - `_api/media?path=pictures&settings=true`
 * 
 * ###Response:
 * 
 * De `info` response key geeft extra informatie over het resultaat, met de volgende keys:
 * 
 * - `files`        // Het aantal bestanden in `data`.
 * - `total_files`  // Het totaal aantal bestanden van de map die opgevraagd is. (op dit moment nog hetzelfde)
 * 
 * Voorbeeld response (dump) van `_api/media?path=pictures`:
 * 
 *     [success] => TRUE
 *      [api] => 'media'
 *      [args] => (
 *        [path] => 'pictures'
 *        [type] => 'GET'
 *       )
 *      [data] => (
 *        [2] => (
 *          [id] => '2'
 *          [file] => 'test_03.jpg'
 *          [path] => 'pictures'
 *          [full_path] => '_media/pictures/test_03.jpg'
 *          [str_type] => 'jpg'
 *          [str_title] => 'wbCYmaFZ'
 *          [dat_date] => '2014-09-16'
 *          [int_size] => '114'
 *          [int_img_width] => '960'
 *          [int_img_height] => '720'
 *         )
 *       )
 *      [info] => (
 *        [files] => 1
 *        [total_files] => 1
 *       )
 *     )
 * 
 * 
 * ##UPLOAD FILE
 * 
 * Hiermee kan een bestand worden geupload
 * 
 * ###Parameters (POST):
 * 
 * - `path`                     // De map waar het bestand naartoe moet.
 * - `file`                     // De bestandsnaam dat geupload moet worden. NB Zoals het resultaat van een HTML FORM: `<input type="file" name="file" />`. Dus ook in FILES['file'].
 * - `[settings=FALSE]`         // Instellingen van de gevraagde map 
 * 
 * ###Voorbeeld:
 * 
 * - `_api/media` met POST data: `path=pictures&file=test_03.jpg` en de corresponderende FILES data.
 * 
 * ###Response:
 * 
 * Als het uploaden is gelukt komt in `data` de informatie van het bestand (NB de naam kan veranderd zijn na het uploaden!).
 * Als het uploaden om wat voor reden niet is gelukt zal `success` FALSE zijn en komt er in `error` een foutmelding.
 * 
 *     [success] => TRUE
 *      [test] => TRUE
 *      [api] => 'media'
 *      [args] => (
 *        [path] => 'pictures'
 *        [file] => 'test_03.jpg'
 *        [type] => 'POST'
 *       )
 *      [data] => (
 *        [id] => '27'
 *        [b_exists] => '1'
 *        [file] => 'test_03.jpg'
 *        [path] => 'pictures'
 *        [str_type] => 'jpg'
 *        [str_title] => 'test_03'
 *        [dat_date] => '2015-03-29'
 *        [int_size] => '18'
 *        [int_img_width] => '300'
 *        [int_img_height] => '225'
 *       )
 * 
 * 
 * ##UPDATE FILE
 * 
 * Hiermee wordt informatie van een bestand aangepast
 * 
 * ###Parameters (POST):
 * 
 * - `path`                     // De map waar het bestand in staat
 * - `where`                    // Bepaal hiermee welk bestand moet worden aangepast
 * - `data`                     // De aangepaste data (hoeft niet compleet, alleen de aan te passen velden meegeven is genoeg).
 * - `[settings=FALSE]`         // Instellingen van de gevraagde map 
 * 
 * 
 * ###Voorbeeld:
 * 
 * - `_api/media` met POST data: `path=pictures&where=test_03.jpg&data[str_title]=Nieuwe titel`
 * 
 * ###Response:
 * 
 * Als response wordt in `data` TRUE gegeven als het aanpassen is gelukt:
 * 
 *     [success] => TRUE
 *      [test] => TRUE
 *      [format] => 'dump'
 *      [api] => 'media'
 *      [args] => (
 *        [path] => 'pictures'
 *        [where] => 'test_03.jpg'
 *        [data] => (
 *          [str_title] => 'TestTitel'
 *         )
 *        [type] => 'POST'
 *       )
 *      [data] => TRUE
 * 
 * ##DELETE FILE
 * 
 * Hiermee wordt een bestand uit een map verwijderd.
 * 
 * - `path`                     // De map waar het bestand in staat
 * - `where`                    // Bepaal hiermee welk bestand moet worden verwijderd
 * - `[settings=FALSE]`         // Instellingen van de gevraagde map 
 * 
 * 
 * ###Voorbeeld:
 * 
 * - `_api/media` met POST data: `path=pictures&where=test_03.jpg
 * 
 * ###Response:
 * 
 * Als response wordt in `data` TRUE gegeven als het verwijderen is gelukt:
 * 
 *     [success] => TRUE
 *      [test] => TRUE
 *      [format] => 'dump'
 *      [api] => 'media'
 *      [args] => (
 *        [path] => 'pictures'
 *        [where] => 'test_03.jpg'
 *        [type] => 'POST'
 *       )
 *      [data] => TRUE
 * 
 * 
 * @author Jan den Besten
 */

class Media extends Api_Model {
  
  var $needs = array(
    'path'      => '',
    'settings'  => false,
  );
  
	public function __construct() {
		parent::__construct();
    $this->load->model('assets');
	}
  
  public function index() {
    if (!$this->_has_rights('media_'.$this->args['path'])) return $this->_result_status401();
    
    if ( !$this->has_args() ) {
      return $this->_result_wrong_args();
    }

    // Does path exists in media_info?
    if ( ! $this->assets->assets_folder_exists($this->args['path']) ) {
      $this->_set_error('PATH NOT FOUND');
      return $this->_result_ok();
    }

    // BULKUPLOAD
    if ( $this->args['action']==='bulkupload' and $this->assets->has_bulkupload()>=1 ) {
      $this->result['data'] = $this->_bulkupload();
      return $this->_result_ok();
    }

    
    // GET
    if ($this->args['type']=='GET') {
      $this->result['data']=$this->_get_files();
      return $this->_result_ok();
    }

    // POST
    if ($this->args['type']=='POST') {
      
      // UPDATE
      if (isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights('media_'.$this->args['path'])>=RIGHTS_EDIT) return $this->_result_norights();
        $this->result['data']=$this->_update_file();
        return $this->_result_ok();
      }
      // UPLOAD
      if (!isset($this->args['data']) and !isset($this->args['where'])) {
        if (!$this->_has_rights('media_'.$this->args['path'])>=RIGHTS_ADD) return $this->_result_norights();
        $this->result['data']=$this->_upload_file();
        return $this->_result_ok();
      }
      // DELETE
      if (!isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights('media_'.$this->args['path'])>=RIGHTS_DELETE) return $this->_result_norights();
        $this->result['data']=$this->_delete_file();
        return $this->_result_ok();
      }
    }
    
    // ERROR -> Wrong arguments
    return $this->_result_wrong_args();
  }
  
  
  /**
   * GET files
   *
   * @return array
   * @author Jan den Besten
   */
  private function _get_files() {
    $args=$this->args;
    $files=$this->assets->get_files( $args['path'] );
    $this->info = array(
      'files'       => count($files),
      'total_files' => count($files),
    );
    return $files;
  }


  /**
   * UPDATE file
   *
   * @return bool
   * @author Jan den Besten
   */
  private function _update_file() {
    $args   = $this->args;
    $result = $this->assets->update_file( $args['path'], $args['where'], $args['data'] );
    if (!$result) {
      $this->_set_error('MAYBE FILE NOT FOUND');
    }
    return $result;
  }


  /**
   * UPLOAD file
   *
   * @return bool
   * @author Jan den Besten
   */
  private function _upload_file() {
    $args = $this->args;
    $path = $args['path'];
		$file = $this->assets->upload_file( $args['path'] );
    
    // Error
    if ( $file===FALSE ) {
  		$error = $this->assets->get_error();
			$this->_set_error( $error );
      return false;
    }

    // message
    $message = $this->assets->get_message();
    if ($message) {
      $this->_set_message( $message );
    }
    else {
      $this->_set_message( langp("upload_succes",$file) );
    }
    
    // Return file info
    $file_info = $this->assets->get_file_info( $path, $file);
    return $file_info;
  }


  /**
   * DELETE file
   *
   * @return bool
   * @author Jan den Besten
   */
  private function _delete_file() {
    $args=$this->args;
    $result = $this->assets->delete_file($args['path'],$args['where']);
    if (!$result) {
      $this->_set_error('FILE NOT DELETED, MAYBE NOT FOUND OR NO RIGHTS');
    }
    return $result;
  }


  private function _bulkupload() {
    return $this->assets->bulkupload($this->args['path']);
  }
  
  
  
  



}


?>
