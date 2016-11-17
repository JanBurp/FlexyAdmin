<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Res_media_files - autogenerated Table_model for table res_media_files
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Res_media_files extends Data_Core {

  public function __construct() {
    parent::__construct();
		$this->load->model("file_manager");
    // Add current filemanager_view setting:
    $user_id = $this->data_core->get_user_id();
    if ($user_id) $this->settings['grid_set']['grid_view'] = $this->db->query( 'SELECT `str_filemanager_view` FROM `cfg_users` WHERE `id`='.$user_id )->row_object()->str_filemanager_view;
  }
  
  
  /**
   * Als rows worden verwijderd uit 'res_media_files', verwijder dan ook de desbetreffende bestanden.
   *
   * @param mixed $where ['']
   * @param int $limit [NULL]
   * @param bool $reset_data 
   * @return mixed FALSE als niet gelukt, anders array_result van verwijderde data
   * @author Jan den Besten
   */
	public function delete( $where = '', $limit = NULL, $reset_data = TRUE ) {
    $deleted_data = parent::delete($where,$limit,$reset_data);
    return $this->_delete_files( $deleted_data );
  }

  
  
  /**
   * Verwijderd bestanden van de rows die uit res_media_files zijn verwijderd
   *
   * @param string $deleted_data 
   * @return mixed array van verwijderde bestanden
   * @author Jan den Besten
   */
  public function _delete_files( $deleted_data ) {
    if (is_array($deleted_data)) {
      foreach ($deleted_data as $id => $file_row) {
        $this->file_manager->set_path( $file_row['path'] );
        if ( !$this->file_manager->delete_file( $file_row['file'] ) ) {
          unset($deleted_data[$id]);
        }
      }
    }
    return $deleted_data;
  }
  

  /**
   * Geeft informatie terug van alle bestanden in een bepaalde map.
   * 
   * Eventueel gefilterd op:
   * - user   - alleen bestanden van een bepaalde gebruiker (als het user veld bestaat)
   * - type   - alleen bestanden bepaalde type(n) bestanden
   * - ...    - nog meer eigen filters: vergelijkbaar zoals je aan ->where() mee kunt geven
   * 
   * Het resultaat is wat anders dan een standaard database resultaat, de veldnamen wijken af en zijn alsvolgt:
   * 
   * - id         = id in de tabel res_media_files
   * - name       = bestandsnaam
   * - path       = map van bestand inclusief assets map
   * - full_path  = complete beveiligde pad van bestand, deze kun je het best gebruiken om een bestand te tonen of niet
   * - type       = extentie van het bestand
   * - alt        = titel
   * - size       = omvang in kb
   * - rawdate    = datum (YYYY MM DD)
   * - date       = datum mooi opgemaakt (DD mm YYYY)
   * - width      = breedte in pixels, als het bestand een afbeelding is
   * - height     = hoogte in pixels, als het bestand een afbeelding is
   * - user       = user id als dit veld bestaat
   *
   * @param string $path Geef hier de directory waar je de bestanden van wilt.
   * @param mixed $types[''] Type bestanden (extenties) die je wilt, als leeg dan krijg je alle bestanden.
   * @param int $user_id [FALSE] Geef eventueel de user_id als je alleen bestanden van een bepaalde user wilt3
   * @param int $limit [0]
   * @param int $offset [0] 
   * @return array
   * @author Jan den Besten
   */
  public function get_files( $path, $filter=array(), $limit=0, $offset=0 ) {
    
    // Veldnamen
    $this->select( array(
      '`file` AS `name`',
      '`path`',
      '`str_type` AS `type`',
      '`str_title` AS `alt`',
      'DATE_FORMAT(`dat_date`, "%Y %m %d") AS `rawdate`',
      'DATE_FORMAT(`dat_date`,"%d %b %Y") AS `date`',
      '`int_size` AS `size`',
      '`int_img_width` AS `width`',
      '`int_img_height` AS `height`'
    ));
    
    // Van bepaalde user?
    if ( $this->field_exists('user') ) {
      $this->select( 'user' );
    }
    else {
      unset($filter['user']);
    }
    
    return $this->_get_files_result($path,$filter,$limit,$offset);
  }
  
  /**
   * Geeft een abstract van de bestanden uit een bepaalde map.
   *
   * @param string $path 
   * @param string $filter 
   * @param string $limit 
   * @param string $offset 
   * @return array
   * @author Jan den Besten
   */
  public function get_files_abstract( $path,$filter=array(),$limit=0,$offset=0) {
    // Veldnamen
    $this->select('file')->select_abstract()->set_result_key('file');
    return $this->_get_files_result($path,$filter,$limit,$offset);
  }
  
  
  /**
   * Geeft bestanden als opties terug. Eventueel gegroupeerd.
   *
   * @param string $path 
   * @param array $filter 
   * @param int $limit 
   * @param int $offset 
   * @return array
   * @author Jan den Besten
   */
  public function get_files_as_options( $path,$filter=array(), $limit=0, $offset=0 ) {
    $options = array();
    // TODO: niet meer uit cfg_media_info
    $order = $this->cfg->get('CFG_media_info',$path,'str_order');
    $desc = (substr($order,0,1)==='_')?'DESC':'ASC';
    $order = trim($order,'_');
		$order=str_replace(array('width','size','rawdate','name'),array('size','filewidth','dat_date','file'),$order);
    $this->select('file')->select_abstract();
    $this->order_by($order,$desc);
    $query = $this->_get_files($path,$filter,$limit,$offset);
    $options = $this->_make_options_result($query,'file');
    // recent uploads?
    $number_of_recent_uploads = $this->get_setting('number_of_recent_uploads');
    if ($number_of_recent_uploads>0 ) {
      $this->reset();
      $this->select('file')->select_abstract()->order_by('dat_date','DESC');
      $query = $this->_get_files( $path,$filter, $number_of_recent_uploads);
      $recent_uploads_options = $this->_make_options_result($query,'file');
      if ($recent_uploads_options) {
        $options = array(
          langp('form_dropdown_sort_on_last_upload',$number_of_recent_uploads) => $recent_uploads_options,
          lang('form_dropdown_sort_on_name')                                   => $options,
        );
      }
      $this->reset();
    }
    $query->free_result();
    return $options;
  }


  /**
   * Basis voor get_files... methods
   *
   * @param string $path 
   * @param array $filter 
   * @param int $limit 
   * @param int $offset 
   * @return object $query
   * @author Jan den Besten
   */
  private function _get_files( $path, $filter=array(), $limit=0, $offset=0 ) {
    // Alleen de bestanden die bestaan
    $this->group_start();
      $this->where( 'b_exists', TRUE );
      // Bestanden van bepaalde map
      $this->where( 'path', $path );
      // Standaard filters
      $filter = array_rename_keys($filter,array('type'=>'str_type'));
      if ($filter) {
        foreach ($filter as $field=>$value) {
          $this->where( $field, $value);
        }
      }
    $this->group_end();
    $query = $this->get( $limit, $offset, FALSE );
    return $query;
  }
  
  
  /**
   * Basis voor get_files_result... methods
   *
   * @param string $path 
   * @param string $filter 
   * @param string $limit 
   * @param string $offset 
   * @return array
   * @author Jan den Besten
   */
  private function _get_files_result( $path, $filter=array(), $limit=0, $offset=0 ) {
    $query = $this->_get_files( $path, $filter=array(), $limit, $offset );
    if ($query) {
      $result = $this->_make_result_array( $query );
      $query->free_result();
    }
    $this->reset();
    return $result;
  }
  
  
  
  
  
}
