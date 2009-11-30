<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class plugin_stats extends plugin_ {

	// You can declare some properties here if needed

	function init($init=array()) {
		parent::init($init);
	}
	
	function _admin_api($args=NULL) {
		$this->CI->_add_content(h($this->plugin,1));
	}
	
}

?>