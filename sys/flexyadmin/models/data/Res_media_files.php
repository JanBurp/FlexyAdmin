<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Res_media_files - autogenerated Table_model for table res_media_files
 * 
 * @author: Jan den Besten
 * %Generated: Thu 14 January 2016, 12:16
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

Class Res_media_files extends Data_Model {

  public function __construct() {
    parent::__construct();
		$this->load->model("file_manager");
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
    foreach ($deleted_data as $id => $file_row) {
      $this->file_manager->set_path( $file_row['path'] );
      if ( !$this->file_manager->delete_file( $file_row['file'] ) ) {
        unset($deleted_data[$id]);
      }
    }
    return $deleted_data;
  }
  
}
