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


}

/* End of file Output.php */
/* Location: ./system/core/Output.php */