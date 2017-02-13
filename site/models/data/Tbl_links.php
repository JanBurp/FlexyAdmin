<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Zorgt ervoor dat:
 * - Na het aanpassen van een link, die link in alle teksten ook word aangepast
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class tbl_links extends Data_Core {

  public function __construct() {
    parent::__construct();
  }
  
  
  /**
   * Als een link is verwijderd, vervang dan alle links in content.
   *
   * @param mixed $where 
   * @param int $limit 
   * @param bool $reset_data 
   * @return mixed
   * @author Jan den Besten
   */
  public function delete( $where = '', $limit = NULL, $reset_data = TRUE ) {
    // Normale update uitvoeren
    $deleted = parent::delete( $where, $limit, $reset_data );

    // Vervang alle oude links
    if ($deleted) {
      $this->load->model('search_replace');
      $this->query_info['deleted_links'] = array();
      foreach ($deleted as $item) {
        $old_link = $item['url_url'];
        $this->query_info['deleted_links'][] = $this->search_replace->links($old_link,'');
      }
    }
    
    return $deleted;
  }
  
  
  
  /**
   * Als een link is aangepast, vervang dan alle links in content.
   *
   * @param mixed $set 
   * @param mixed $where 
   * @param int $limit 
   * @return mixed
   * @author Jan den Besten
   */
	public function update( $set = NULL, $where = NULL, $limit = NULL) {
    if (!$where) throw new Exception( __CLASS__.": When updating `tbl_links` allways set WHERE with update() method instead of where() method to make sure links are replaced in content.");

    // Nieuwe data
    $new_data = $set;
    if ( !$new_data ) $new_data = $this->tm_set;

    // Oude link
    if (isset($new_data['url_url'])) {
      $new_link = $new_data['url_url'];
      $old_link = $this->_get_old_link( $where );
    }
    
    // Normale update uitvoeren
    $id = parent::update( $set, $where, $limit );
    
    // Vervang alle links
    if ($id and isset($old_link) and $old_link) {
      $this->load->model('search_replace');
      $this->query_info['replaced_links'] = $this->search_replace->links($old_link,$new_link);
    }
    
    return $id;
  }
  
  /**
   * Haalt oude link op uit database
   *
   * @param int $id 
   * @return mixed
   * @author Jan den Besten
   */
  private function _get_old_link( $id ) {
    $old_link = FALSE;
    $sql = 'SELECT `url_url` FROM `'.$this->settings['table'].'` WHERE `'.$this->settings['primary_key'].'` = '.$id;
    $query = $this->db->query($sql);
    if ($query) {
      $row = $query->row_array();
      $old_link = $row['url_url'];
    }
    return $old_link;
  }
  

}
