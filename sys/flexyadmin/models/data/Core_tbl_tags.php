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

  public function cleanup_tags() {
    $used_tags = [];
    $tables = $this->data->list_tables();
    $tables = filter_by($tables,'tbl');
    foreach ($tables as $table) {
      if ($table!=='tbl_tags') {
        $fields = $this->data->table($table)->list_fields();
        if ( in_array('str_tags',$fields) ) {
          $result = $this->data->table($table)->select('str_tags')->get_result();
          foreach ($result as $row) {
            $row_tags = $row['str_tags'];
            if (!empty($row_tags)) {
              $row_tags = explode('|',$row_tags);
              $used_tags = array_merge($used_tags,$row_tags);
            }
          }
        }
      }
    }

    $used_tags = array_unique($used_tags);

    $this->db->truncate('tbl_tags');
    foreach ($used_tags as $tag) {
      $this->data->table('tbl_tags')->set('str_tag',$tag)->insert();
    }

    return $used_tags;
  }

  /**
   * Voeg cleanup actie toe aan header
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_setting_grid_set() {
    $grid_set = parent::get_setting_grid_set();
    $grid_set['actions'] = array(
      array(
        'name'  => 'Cleanup unused tags',
        'icon'  => 'remove',
        'url'   => 'tags_cleanup',
        'class' => 'text-warning',
      ),
    );

    return $grid_set;
  }

}
