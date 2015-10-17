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
      if (!isset($this->args['data']) and !isset($this->args['where'])) {
        if (!$this->_has_rights($this->args['path'])>=RIGHTS_ADD) return $this->_result_norights();
        $this->result['data']=$this->_upload_file();
        return $this->_result_ok();
      }
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
    $args=$this->args;
    $result=$this->mediatable->edit_info($args['path'].'/'.$args['where'], $args['data']);
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
    $args=$this->args;
    $path=$args['path'];
		$types=$this->cfg->get('CFG_media_info',$path,'str_types');
    
    $this->load->model('file_manager');
		$fileManager=new file_manager(array('upload_path'=>$path,'allowed_types'=>$types));
		$result=$fileManager->upload_file();
		$error=$result['error'];
		$file=$result['file'];
    $extra_files=$result['extra_files'];
    
    // Error
    if ($error) {
			if (is_string($error))
				$this->_set_error($error);
			else
				$this->_set_error(langp("upload_error",$file));
      return false;
    }
    
    // Good, add files to media table
    $addFiles=array();
    $addFiles[]=$file;
    if (!empty($extra_files)) {
      foreach ($extra_files as $extra) {
        $addFiles[]=$extra['file'];
      }
    }
    foreach ($addFiles as $addFile) {
      if ( $this->cfg->get('CFG_media_info',$path,'b_user_restricted') ) {
        $this->mediatable->add($addFile,$path,$this->user_id);
      }
      else {
        $this->mediatable->add($addFile,$path);
      }
    }
    
    // message
		$this->_set_message(langp("upload_succes",$file));
    // Return file info
    return $this->mediatable->get_info($path.'/'.$file);
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
