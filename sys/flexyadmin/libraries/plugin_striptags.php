<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 * @ignore
 * @internal
 */

class Plugin_striptags extends Plugin {

	function init($init=array()) {
		parent::init($init);
		$this->act_on(array('changedTypes'=>'txt'));
	}
	
	function _after_update() {
		$validHTML=$this->CI->cfg->get('CFG_configurations','str_valid_html');
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