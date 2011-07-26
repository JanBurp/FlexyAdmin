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

	function __construct() {
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
			// only insert a known (mobile) browser
			if ($CI->agent->is_browser() or $CI->agent->is_mobile()) {
				$CI->db->set("tme_date_time",standard_date('DATE_ATOM', now()));
				$CI->db->set("str_uri",$uri);
				if ($CI->agent->is_browser())
					$CI->db->set("str_browser",$CI->agent->browser());
				else
					$CI->db->set("str_browser",$CI->agent->mobile());
				// nicer version info
				$version=substr($CI->agent->version(),0,3);
				if (strpos($version,'.')===false) $version=substr($version,0,1);
				$CI->db->set("str_version",$version);
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
