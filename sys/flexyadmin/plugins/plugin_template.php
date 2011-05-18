<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class Plugin_template extends Plugin_ {

	// You can declare some properties here if needed

	function init($init=array()) {
		parent::init($init);
		// If you need methods like _after_update(), _after_delete(), set in the next line of which tables,fields,types this method must act.
		$this->act_on();
	}
	
	//
	// Here you find short templates of possible methods
	//

	//
	// _admin_logout is a call that's made when user is logging out
	//
	// function _admin_logout() {
	//	 return true;
	// }
	
	
	//
	// _admin_api is a call in admin:
	// admin/plugin/#plugin_name# 
	//
	// function _admin_api($args=NULL) {
	//	// If you need to, check $this->rights
	// 	$this->_add_content(h($this->plugin,1));
	// }



	// These methods can be used to do some actions 

	// function _after_update() {
	// 	return $this->newData;
	// }

	// function _after_delete() {
	// 	return FALSE;
	// }
	
}

?>