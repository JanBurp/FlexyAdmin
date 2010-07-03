<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class plugin_embed extends plugin_ {

	function init($init=array()) {
		parent::init($init);
		$this->act_on(array('tables'=>'tbl_embeds')); // 'changedFields'=>'str_title,stx_embed'
	}
	
	function _after_update() {
		$this->_create_embed_list();
		return FALSE;
	}
	
	function _after_delete() {
		$this->_create_embed_list();
		return FALSE;
	}
	
	function _create_embed_list() {
		$this->CI->editor_lists->create_list('embed');
	}
		
}

?>