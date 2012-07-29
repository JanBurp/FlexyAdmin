<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 * @ignore
 * @internal
 */


class Plugin_embed extends Plugin_ {

	function _after_update() {
		$this->_create_embed_list();
		return FALSE;
	}
	
	function _after_delete() {
		$this->_create_embed_list();
		return FALSE;
	}
	
	function _create_embed_list() {
		if (!isset($this->CI->editor_lists)) $this->CI->load->library('editor_lists');
		$this->CI->queu->add_call(@$this->CI->editor_lists,'create_list','links');
	}
		
}

?>