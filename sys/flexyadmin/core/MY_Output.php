<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Output extends CI_OUTPUT {

	function _display($output = '')	{
		parent::_display($output);
		$this->add_to_stats();
	}
	
	function add_to_stats() {
		global $CFG;
		if ($CFG->item('add_to_statistics')) {
			$STATS=&load_class('stats', 'libraries','');
			$STATS->add_current_uri();
		}
	}

	function _can_cache() {
		return (empty($_POST));
	}


	function _display_cache(&$CFG, &$URI) {
		if ($this->_can_cache())
			return parent::_display_cache($CFG,$URI);
		return FALSE;
	}
	
	function cache($time) {
		if ($this->_can_cache())
			return parent::cache($time);
		return $this;
	}
	


}

/* End of file Output.php */
/* Location: ./system/core/Output.php */