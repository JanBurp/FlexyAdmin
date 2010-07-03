<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FlexyAdmin V1
 * @author Jan den Besten
 */


/**
 * Stats Class, keeping track of page statistics
 *
 */

class Stats {

	var $table;

	function Stats() {
		$this->set_table();
	}

	function set_table($table="") {
		if (empty($table)) {
			$CI =& get_instance();
			$table=$CI->config->item('LOG_table_prefix')."_".$CI->config->item('LOG_stats');
		}
		$this->table=$table;
	}

/**
 *
 */

	function add_uri($uri=NULL) {
		if ($uri==NULL) $uri="";
		// only insert page uri's (no images, css etc).
		if (strpos($uri,'.')===FALSE) {
			$CI =& get_instance();
			if (!$CI->agent->is_robot()) {
				$CI->db->set("tme_date_time",standard_date('DATE_ATOM', now()));
				$CI->db->set("str_uri",$uri);
				if ($CI->agent->is_browser()) {
					$CI->db->set("str_browser",$CI->agent->browser());
					$CI->db->set("str_version",$CI->agent->version());
				}
				elseif ($CI->agent->is_mobile()) {
					$CI->db->set("str_browser",$CI->agent->mobile());
					$CI->db->set("str_version",$CI->agent->version());
				}
				else {
					$agent=$CI->agent->agent_string();
					if (!empty($agent))
						$CI->db->set("str_browser",$agent);
					else	
						$CI->db->set("str_browser",'unidentified');
				}
				$CI->db->set("str_referrer",$CI->agent->referrer());
				$CI->db->set("str_platform",$CI->agent->platform());
				$CI->db->insert($this->table);
			}
		}
	}

	function get_top($nr=10) {
		$CI =& get_instance();
		$results=$CI->fd->get_results($this->table,$nr);
		return $results;
	}

}

?>
