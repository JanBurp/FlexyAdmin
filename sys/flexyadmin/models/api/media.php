<?

/**
 * API media. Geeft een lijst, bewerkt of uploade bestanden toe aan een map.
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
 * - `[config[]=media_info]`    // Informatie over de map kan op deze manier meegenomen worden in het resultaat.
 * - `[config[]=img_info]`      // Informatie over de afbeeldingen in de map kan op deze manier meegenomen worden in het resultaat.
 * 
 * 
 * ###Voorbeelden:
 * 
 * - `_api/media?path=pictures`
 * - `_api/media?path=pictures&config[]=media_info`
 * 
 * ###Response:
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
 *          [b_exists] => '1'
 *          [file] => 'test_03.jpg'
 *          [path] => 'pictures'
 *          [str_type] => 'jpg'
 *          [str_title] => 'wbCYmaFZ'
 *          [dat_date] => '2014-09-16'
 *          [int_size] => '114'
 *          [int_img_width] => '960'
 *          [int_img_height] => '720'
 *         )
 *       )
 *     ) 
 * 
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
 * - `[config[]=media_info]`    // Informatie over de map kan op deze manier meegenomen worden in het resultaat.
 * - `[config[]=img_info]`      // Informatie over de afbeeldingen in de map kan op deze manier meegenomen worden in het resultaat.
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
 * - `[config[]=media_info]`    // Informatie over de map kan op deze manier meegenomen worden in het resultaat.
 * - `[config[]=img_info]`      // Informatie over de afbeeldingen in de map kan op deze manier meegenomen worden in het resultaat.
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
 * @package default
 * @author Jan den Besten
 */

class Media extends Api_Model {
  
  var $needs = array(
    'path'   => '',
  );
  
	public function __construct() {
		parent::__construct();
    $this->load->model('mediatable');
    $this->load->model('file_manager','filemanager');
	}
  
  public function index() {
    if (!$this->_has_rights($this->args['path'])) return $this->_result_status401();
    
    if ( !$this->has_args() ) {
      return $this->_result_wrong_args();
    }
    // Does path exists in media_info?
    if ( ! $this->cfg->get('cfg_media_info',$this->args['path']) ) {
      $this->_set_error('PATH NOT FOUND');
      return $this->_result_ok();
    }
    
    // CFG
    $this->_get_config(array('media_info','img_info'));
    
    // GET
    if ($this->args['type']=='GET') {
      $this->result['data']=$this->_get_files();
      return $this->_result_ok();
    }

    // POST
    if ($this->args['type']=='POST') {
      
      // UPDATE
      if (isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['path'])>=RIGHTS_EDIT) return $this->_result_norights();
        $this->result['data']=$this->_update_file();
        return $this->_result_ok();
      }
      // UPLOAD
      // if (isset($this->args['data']) and !isset($this->args['where'])) {
      //   if (!$this->_has_rights($this->args['p'])>=RIGHTS_ADD) return $this->_result_norights();
      //   $this->result['data']=$this->_insert_row();
      //   return $this->_result_ok();
      // }
      // DELETE
      if (!isset($this->args['data']) and isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['path'])>=RIGHTS_DELETE) return $this->_result_norights();
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
    $files=$this->mediatable->get_files($args['path'],false);
    return $files;
  }


  /**
   * UPDATE file
   *
   * @return bool
   * @author Jan den Besten
   */
  private function _update_file() {
    $args=$this->args;
    $result=$this->mediatable->edit_info($args['path'].'/'.$args['where'], $args['data']);
    if (!$result) {
      $this->_set_error('MAYBE FILE NOT FOUND');
    }
    return $result;
  }


  /**
   * DELETE file
   *
   * @return bool
   * @author Jan den Besten
   */
  private function _delete_file() {
    $args=$this->args;
    $this->filemanager->initialize($args['path']);
    $result = $this->filemanager->delete_file($args['where']);
    if (!$result) {
      $this->_set_error('FILE NOT DELETED, MAYBE NOT FOUND OR NO RIGHTS');
    }
    else {
      $result=$this->mediatable->delete($args['path'].'/'.$args['where'] );
      if (!$result) {
        $this->_set_message('FILE DELETED, BUT ERROR UPDATING IN DATABASE');
      }
    }
    return $result;
  }
  
  
  
  



}


?>
