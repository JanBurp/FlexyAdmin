<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class Plugin_links extends Plugin_ {

	
	function _after_update() {
		$this->_update_links_in_text();
		$this->_create_link_list();
		return $this->newData;
	}
	
	function _after_delete() {
		$linkTable=$this->CI->cfg->get('cfg_editor','table');
		$menuTable=$this->CI->cfg->get('cfg_configurations','str_menu_table');
		if ($this->CI->db->table_exists('res_auto_menu')) $menuTable='res_auto_menu';
		if ($this->table==$linkTable or $this->table==$menuTable) {
			$this->newData=array();
			$this->_update_links_in_text();
			$this->_create_link_list();
		}
		return FALSE;
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
		
		$languages=$this->CI->config->item('LANGUAGES');
		$languagesRegex=implode('/|',$languages).'/|';
		$languagesRegex=str_replace('/','\/',$languagesRegex);

		// loop through all changed fields
		foreach ($changedFields as $field => $value) {
			$oldUrl=$this->oldData[$field];
			if (!empty($oldUrl)) {
				if (isset($this->newData[$field]))
					$newUrl=$this->newData[$field];
				else
					$newUrl='';
				// loop through all txt fields in all tables
				$tables=$this->CI->db->list_tables();
				foreach($tables as $table) {
					if (get_prefix($table)==$this->CI->config->item('TABLE_prefix')) {
						$fields=$this->CI->db->list_fields($table);
						foreach ($fields as $field) {
							if (get_prefix($field)=="txt") {
								$this->CI->db->select("id,$field");
								$this->CI->db->where("$field !=","");
								$query=$this->CI->db->get($table);
								foreach($query->result_array() as $row) {
									$thisId=$row["id"];
									$txt=$row[$field];
									if (empty($newUrl)) {
										// remove
										$pattern='/<a(.*?)href="('.$languagesRegex.')'.str_replace("/","\/",$oldUrl).'"(.*?)>(.*?)<\/a>/';
										$txt=preg_replace($pattern,'$4',$txt);
									}
									else {
										// replace
										$pattern='/<a(.*?)href="('.$languagesRegex.')'.str_replace("/","\/",$oldUrl).'"(.*?)>(.*?)<\/a>/';
										$txt=preg_replace($pattern,'<a$1href="$2'.$newUrl.'"$3>$4</a>',$txt);
									}
									$res=$this->CI->db->update($table,array($field=>$txt),"id = $thisId");
								}
								$query->free_result();
							}
						}
					}
				}
			}
		}
		return true;
	}
	
	
		
}

?>