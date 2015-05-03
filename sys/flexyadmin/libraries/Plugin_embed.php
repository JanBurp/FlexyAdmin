<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 * @ignore
 * @internal
 */


class Plugin_embed extends Plugin {

	public function __construct() {
		parent::__construct();
	}

	function _after_update() {
		$this->_create_embed_list();
		return $this->newData;
	}
	
	function _after_delete() {
		$this->_create_embed_list();
		return TRUE;
	}
	
	function _create_embed_list() {
		if (!isset($this->CI->editor_lists)) $this->CI->load->library('editor_lists');
		$this->CI->queu->add_call(@$this->CI->editor_lists,'create_list','links');
	}
		
}

?>