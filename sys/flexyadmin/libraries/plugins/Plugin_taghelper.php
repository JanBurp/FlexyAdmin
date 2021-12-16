<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Voeg tags toe aan str_tags
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Plugin_taghelper extends Plugin {

	public function _after_update() {
    if ( !$this->CI->flexy_auth->allowed_to_use_cms()) return false;

    $this->_update_tags_table();

		return $this->newData;
	}

  private function _update_tags_table() {
    $result = $this->CI->data->table('tbl_tags')->get_result();
    $currentTags = array();
    foreach ($result as $id => $row) {
      $currentTags[] = strtolower((trim($row['str_tag'])));
    }

    $newTags = explode('|',$this->newData['str_tags']);
    foreach ($newTags as $key => $tag) {
      $newTags[$key] = strtolower(trim($tag));
    }

    $diff = array_diff($newTags,$currentTags);

    if (!empty($diff)) {
      foreach ($diff as $tag) {
        $this->CI->data->table('tbl_tags')->set('str_tag',$tag)->insert();
      }
    }

  }
	
}

?>
