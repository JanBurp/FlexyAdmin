<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class plugin_links extends plugin_ {

	function init($init=array()) {
		parent::init($init);
		$this->act_on(array('changedFields'=>'uri,self_parent','changedTypes'=>'url,email'));
	}
	
	function _after_update() {
		$this->_update_links_in_text();
		$this->_create_link_list();
		return FALSE;
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
		$this->CI->editor_lists->create_list("links");
	}
		
	function _update_links_in_text() {
		// what is changed?
		$changedFields=array_diff($this->oldData,$this->newData);
		foreach ($changedFields as $field => $value) {
			$pre=get_prefix($field);
			if (!in_array($field,$this->actOn['changedFields']['value']) and !in_array($pre,$this->actOn['changedTypes']['value'])) unset($changedFields[$field]);
		}
		
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
										$pattern='/<a(.*?)href="'.str_replace("/","\/",$oldUrl).'"(.*?)>(.*?)<\/a>/';
										$txt=preg_replace($pattern,'\\3',$txt);
									}
									else {
										$txt=str_replace("href=\"$oldUrl","href=\"$newUrl",$txt);
									}
									$res=$this->CI->db->update($table,array($field=>$txt),"id = $thisId");
								}
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