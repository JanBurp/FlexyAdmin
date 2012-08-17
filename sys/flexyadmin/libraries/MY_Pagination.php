<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Uitbreiding op CodeIgniters Pagination class
 *
 * @author Jan den Besten
 * @version $Id$
 * @copyright , 17 August, 2012
 * @package default
 **/

 /**
  * Uitbreiding op CodeIgniters Pagination class
  *
  * @package default
  * @author Jan den Besten
  */
class MY_Pagination extends CI_Pagination {

	// auto pagination
	var $auto = FALSE;
	var $auto_uripart = 'offset';

	// new config
	var $total_tag_open = '<span class="pagination_total">';
	var $total_tag_close = '</span>';
	// default config
	var $first_link			= '&lt;&lt;';
	var $last_link			= '&gt;&gt;';
	var $full_tag_close = '</ul>';
	var $num_tag_open = '<li class="pager">';
	var $num_tag_close = '</li>';
	var $cur_tag_open = '<li class="current">';
	var $cur_tag_close = '</li>';
	var $first_tag_open = '<li class="pager pagination_first">';
	var $first_tag_close = '</li>';
	var $last_tag_open = '<li class="pager pagination_last">';
	var $last_tag_close = '</li>';
	var $prev_tag_open = '<li class="pager pagination_prev">';
	var $prev_tag_close = '</li>';
	var $next_tag_open = '<li class="pager pagination_next">';
	var $next_tag_close = '</li>';
	
	function __construct($params = array()) {
		parent::__construct($params);
	}

	function create_links() 	{
		// auto pagination?
		if ($this->auto) {
			$this->_auto_set();
		}
		
		// go on with normal method
		$output = parent::create_links();
		
		// voorkom lege uri
		$output=str_replace('/'.$this->auto_uripart.'/"','/'.$this->auto_uripart.'/0"',$output);
		// extra info
		$output.=$this->total_tag_open.$this->total_rows.$this->total_tag_close;
		return $output;
	}
	
	function auto($auto=true,$part='offset') {
		$this->auto_uripart=$part;
		$this->auto=$auto;
	}
	
	private function _auto_set() {
		$CI=&get_instance();
		$uri=$CI->uri->segment_array();
		// find segment and base_url
		$segment=array_search($this->auto_uripart,$uri);
		if ( ! $segment) $segment=count($uri)+1;
		// create base_url
		$base_url=array_slice($uri,0,$segment-1);
		$base_url=site_url(implode('/',$base_url).'/'.$this->auto_uripart);
		// we need the uri part after the auto_uripart
		$segment++; 
		$this->uri_segment=$segment;
		$this->base_url=$base_url;
		return true;
	}
	
	
}

/* End of file Pagination.php */
/* Location: ./system/libraries/Pagination.php */