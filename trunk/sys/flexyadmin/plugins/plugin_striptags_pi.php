<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class plugin_striptags extends plugin_ {

	function init($init=array()) {
		parent::init($init);
		$this->act_on(array('changedTypes'=>'txt'));
	}
	
	function _after_update() {
		$validHTML=$this->CI->cfg->get('CFG_editor','str_valid_html');
		if (!empty($validHTML)) {
			foreach ($this->fields as $field) {
				if (get_prefix($field)=="txt") {
					$txt=$this->newData[$field];
					$txt=strip_tags($txt,$validHTML);
					$this->newData[$field]=$txt;
				}
			}
		}
		return $this->newData;
	}
	
}

?>