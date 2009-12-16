<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class plugin_automenu extends plugin_ {

	var $menuTable;
	var $automationTable;
	var $resultMenu;
	
	var $automationData;

	function init($init=array()) {
		parent::init($init);
		// Fill here on which trigger the plugin must act
		$this->automationTable='cfg_auto_menu';
		if ($this->CI->db->table_exists($this->automationTable)) {
			$this->menuTable=$this->CI->cfg->get('CFG_configurations','str_menu_table');
			$this->resultMenu='res_menu_result';
			$checkTables=array();
			$checkTables[]=$this->menuTable;
			$checkTables[]=$this->automationTable;
			$checkTables[]=$this->resultMenu;
			// get tables from automation data
			$this->automationData=$this->CI->db->get_results($this->automationTable);
			foreach ($this->automationData as $aData) {
				if (!in_array($aData['table'], $checkTables))	$checkTables[]=$aData['table'];
				$foreignTable=$aData['field_group_by'];
				$foreignTable=explode('.',$foreignTable);
				if (isset($foreignTable[1])) {
					$foreignTable=$foreignTable[1];
					$foreignTable=foreign_table_from_key($foreignTable);
					if (!in_array($foreignTable, $checkTables))	$checkTables[]=$foreignTable;
				}
			}
			$this->act_on(array('existingTables'=>'res_menu_result','tables'=>implode(',',$checkTables)));
		}
		else
			$this->act_on();
	}


	function _after_update() {
		$this->_create_auto_menu();
		return FALSE;
	}

	function _after_delete() {
		$this->_create_auto_menu();
		return FALSE;
	}

	
	function _create_auto_menu() {
		// clean current automenu and init some vars
		$this->CI->db->truncate($this->resultMenu);
		$lastId=-1;
		$lastOrder=0;
		
		// Loop through all options in Auto Menu
		foreach ($this->automationData as $autoKey => $autoValue) {
			switch($autoValue['str_type']) {
				case 'from menu table':
					$data=$this->CI->db->get_results($autoValue['table']);
					foreach ($data as $item) {
						$this->_setResultMenuItem($item,true);
						$this->CI->db->set('str_table',$autoValue['table']);
						$this->CI->db->set('str_uri',$item['uri']);
						if (isset($item['self_parent'])) {
							$this->CI->db->set('self_parent',$item['self_parent']);
						}
						$this->CI->db->insert($this->resultMenu);
					}
					break;
		
				case 'from category table':
					$data=$this->CI->db->get_result($autoValue['table']);
					foreach ($data as $item) {
						$this->_setResultMenuItem($item);
						$this->CI->db->set('order',$lastOrder++);
						$this->CI->db->set('self_parent',0);
						$this->CI->db->set('str_table',$autoValue['table']);
						$this->CI->db->set('str_uri',$item['uri']);
						$this->CI->db->insert($this->resultMenu);
					}
					break;
		
				case 'from table group by category':
					$groupField=remove_prefix($autoValue['field_group_by'],'.');
					$groupTable=foreign_table_from_key($groupField);
					$groupData=$this->CI->db->get_result($groupTable);
					foreach ($groupData as $groupId=>$groupData) {
						$this->CI->db->where($autoValue['field_group_by'],$groupId);
						$data=$this->CI->db->get_result($autoValue['table']);
						$lastOrder=0;
						$this->CI->db->where('str_title',$groupData['str_title']);
						$parentData=$this->CI->db->get_row($this->resultMenu);
						$selfParent=$parentData['id'];
						foreach ($data as $item) {
							$this->_setResultMenuItem($item);
							$this->CI->db->set('order',$lastOrder++);
							$this->CI->db->set('self_parent',$selfParent);
							$this->CI->db->set('str_table',$autoValue['table']);
							$this->CI->db->set('str_uri',$item['uri']);
							$this->CI->db->insert($this->resultMenu);
						}
					}
					break;
			}
			$lastId=$this->CI->db->insert_id();
			$lastOrder=$this->CI->db->get_field($this->resultMenu,'order',$lastId);
			
		}

		// update linklist etc
		// $this->CI->editor_lists->create_list("links");
	}

	function _setResultMenuItem($item,$setId=false) {
		if (!$setId) {
			unset($item['id']);
			unset($item['order']);
			unset($item['self_parent']);
		}
		foreach ($item as $key => $value) {
			if ($this->CI->db->field_exists($key,$this->resultMenu)) $this->CI->db->set($key,$value);
		}
	}
	
	
	
}

?>