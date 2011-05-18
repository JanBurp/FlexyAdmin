<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class Plugin_links extends Plugin_ {

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
		$linkTable=$this->cfg->get('cfg_editor','table');
		$menuTable=$this->cfg->get('cfg_configurations','str_menu_table');
		if ($this->db->table_exists('res_auto_menu')) $menuTable='res_auto_menu';
		if ($this->table==$linkTable or $this->table==$menuTable) {
			$this->newData=array();
			$this->_update_links_in_text();
			$this->_create_link_list();
		}
		return FALSE;
	}
	
	function _create_link_list() {
		$this->editor_lists->create_list("links");
	}
		
	function _update_links_in_text() {
		// what is changed?
		$changedFields=array_diff($this->oldData,$this->newData);
		foreach ($changedFields as $field => $value) {
			$pre=get_prefix($field);
			if (!in_array($field,$this->actOn['changedFields']['value']) and !in_array($pre,$this->actOn['changedTypes']['value'])) unset($changedFields[$field]);
		}
		
		$languages=$this->config->item('LANGUAGES');
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
				$tables=$this->db->list_tables();
				foreach($tables as $table) {
					if (get_prefix($table)==$this->config->item('TABLE_prefix')) {
						$fields=$this->db->list_fields($table);
						foreach ($fields as $field) {
							if (get_prefix($field)=="txt") {
								$this->db->select("id,$field");
								$this->db->where("$field !=","");
								$query=$this->db->get($table);
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
									$res=$this->db->update($table,array($field=>$txt),"id = $thisId");
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