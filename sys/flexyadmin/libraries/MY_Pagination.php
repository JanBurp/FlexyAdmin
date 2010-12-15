<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination {

	var $total_tag_open='Total rows ';
	var $total_tag_close='';
	
	function MY_Pagination($params = array()) {
		parent::CI_Pagination($params);
	}

	function create_links() 	{
		$output = parent::create_links();
		// voorkom lege uri
		$output=str_replace('/offset/"','/offset/0"',$output);
		// extra info
		$output.=$this->total_tag_open.$this->total_rows.$this->total_tag_close;
		return $output;
	}
}

/* End of file Pagination.php */
/* Location: ./system/libraries/Pagination.php */