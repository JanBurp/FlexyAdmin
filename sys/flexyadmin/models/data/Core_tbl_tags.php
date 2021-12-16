<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * tbl_tags
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Core_tbl_tags extends Data_Core {

  public function __construct() {
    parent::__construct();
  }

  /**
   * Dit geeft de linklijst aan de API call get_tag_list
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_tag_list($find) {
    $result = $this->data->table('tbl_tags')->find( $find, array('str_tag'), array('equals'=>'like') )->get_result();

    $currentTags = array();
    foreach ($result as $id => $row) {
      $currentTags[] = trim($row['str_tag']);
    }
    return $currentTags;
  }

}
