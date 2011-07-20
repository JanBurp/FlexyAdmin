<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination {

	// default and new config
	var $full_tag_close = '</ul>';
	var $num_tag_open = '<li>';
	var $num_tag_close = '</li>';
	var $cur_tag_open = '<li class="current">';
	var $cur_tag_close = '</li>';
	var $first_tag_open = '<li class="pagination_first">';
	var $first_tag_close = '</li>';
	var $last_tag_open = '<li class="pagination_last">';
	var $last_tag_close = '</li>';
	var $prev_tag_open = '<li class="pagination_prev">';
	var $prev_tag_close = '</li>';
	var $next_tag_open = '<li class="pagination_next">';
	var $next_tag_close = '</li>';
	// new config
	var $total_tag_open = '<span class="pagination_total">';
	var $total_tag_close = '</span>';
	
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