<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."libraries/plugin.php");

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class plugin_template extends plugin {

	function init($init=array()) {
		parent::init($init);
		// Fill here omn which trigger the plugin must act 
		$this->act_on();
	}
	
	//
	// Create these methods in your plugin (when needed)
	// Start by uncommenting the needed one
	//

	// function _after_update() {
	// 	return $this->newData;
	// }


	// function _after_delete() {
	// 	return FALSE;
	// }
	
	


	
}

?>