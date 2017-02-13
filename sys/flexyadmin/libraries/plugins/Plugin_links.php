<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 * @internal
 */

class Plugin_links extends Plugin {

	public function __construct() {
		parent::__construct();
		$this->CI->load->model('search_replace');
	}
	
	public function _after_update() {
		$this->_update_links_in_text();
		return $this->newData;
	}
	
	public function _after_delete() {
		$linkTable='tbl_links';
		$menuTable=get_menu_table();
		if ($this->table==$linkTable or $this->table==$menuTable) {
			$this->newData=array();
			$this->_update_links_in_text();
		}
		return TRUE;
	}
  
	
	public function _update_links_in_text() {
		// what is changed?
    $changedFields=array_diff_multi($this->oldData,$this->newData);
		foreach ($changedFields as $field => $value) {
			$pre=get_prefix($field);
			if (!in_array($field,$this->trigger['fields']) and !in_array($pre,$this->trigger['field_types'])) unset($changedFields[$field]);
		}
    
		// loop through all changed fields, and replace all links with new
		foreach ($changedFields as $field => $value) {
			$oldUrl=$this->oldData[$field];
			if (!empty($oldUrl)) {
				$newUrl='';
				if (isset($this->newData[$field])) {
					$newUrl=$this->newData[$field];
				}
				if ($field=='uri' and isset($this->oldData['self_parent'])) {
					$oldUrl=$this->_getFullParentUri($this->oldData);
					$newUrl=remove_suffix($oldUrl,'/').'/'.$newUrl;
				}
				$this->CI->search_replace->links($oldUrl,$newUrl);
			}
		}
	}

	private function _getFullParentUri($data) {
    $this->CI->data->table( $this->table );
    $this->CI->data->tree('uri');
    $this->CI->data->where($data['id']);
		$full = $this->CI->data->get_row();
    return $full['uri'];
	}
	

}

?>