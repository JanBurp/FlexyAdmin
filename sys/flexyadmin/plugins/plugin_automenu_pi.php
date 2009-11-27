<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."libraries/plugin.php");

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class plugin_automenu extends plugin {

	var $menuTable;
	var $automationTable;
	var $resultMenu;

	function init($init=array()) {
		parent::init($init);
		// Fill here on which trigger the plugin must act
		$this->menuTable=$this->CI->cfg->get('CFG_configurations','str_menu_table');
		$this->automationTable='cfg_auto_menu';
		$this->resultMenu='res_menu_result';
		$this->act_on(array('tables'=>"$this->menuTable,$this->automationTable,$this->resultMenu"));
	}
	
	//
	// Create these methods in your plugin (when needed)
	// Start by uncommenting the needed one
	//

	function _after_update() {
		return $this->newData;
	}


	function _after_delete() {
		return FALSE;
	}
	
}

?>