<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 * @ignore
 * @internal
 */

class Plugin_links extends Plugin_ {


	function __construct() {
		parent::__construct();
		$this->CI->load->model('search_replace');
	}
	
	function _after_update() {
		$this->_update_links_in_text();
		$this->_create_link_list();
		return $this->newData;
	}
	
	function _after_delete() {
		$linkTable=$this->CI->cfg->get('CFG_configurations','table');
		$menuTable=get_menu_table();
		if ($this->table==$linkTable or $this->table==$menuTable) {
			$this->newData=array();
			$this->_update_links_in_text();
			$this->_create_link_list();
		}
		return TRUE;
	}
	
	function _create_link_list() {
		if (!isset($this->CI->editor_lists)) $this->CI->load->library('editor_lists');
		$this->CI->queu->add_call(@$this->CI->editor_lists,'create_list','links');
	}
		
	function _update_links_in_text() {
		// what is changed?
		$changedFields=array_diff($this->oldData,$this->newData);
		foreach ($changedFields as $field => $value) {
			$pre=get_prefix($field);
			if (!in_array($field,$this->trigger['fields']) and !in_array($pre,$this->trigger['field_types'])) unset($changedFields[$field]);
		}
		
		// loop through all changed fields, and replace all links with new
		foreach ($changedFields as $field => $value) {
			$oldUrl=$this->oldData[$field];
			if (!empty($oldUrl)) {
				if (isset($this->newData[$field])) {
					$newUrl=$this->newData[$field];
				}
				else
					$newUrl='';
				if ($field=='uri') {
					$oldUrl=$this->_getFullUri($this->oldData['id']);
					$newUrl=remove_suffix($oldUrl,'/').'/'.$newUrl;
				}
				$this->CI->search_replace->links($oldUrl,$newUrl);
			}
		}
	}

	private function _getFullUri($id) {
		$this->CI->db->select('id,uri');
    if ($this->CI->db->field_exists('self_parent',$this->table)) {
  		$this->CI->db->select('self_parent');
  		$this->CI->db->uri_as_full_uri();
  		$this->CI->db->where('id',$id);
  		$full=$this->CI->db->get_row($this->table);
    }
		$this->CI->db->where('id',$id);
		$full=$this->CI->db->get_row($this->table);
		return $full['uri'];
	}
	

}

?>