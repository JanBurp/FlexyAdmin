<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FlexyAdmin V1
 *
 * frontend_menu.php Created on 9-dec-2008
 *
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

	function add_uri($uri) {
		$CI =& get_instance();
		if (!$CI->agent->is_robot()) {
			$CI->db->set("tme_date_time",standard_date('DATE_ATOM', now()));
			$CI->db->set("str_uri",$uri);
			$CI->db->set("ip_ip",$_SERVER['REMOTE_ADDR']);
			if (isset($_SERVER["HTTP_HOST"]))
				$CI->db->set("str_host",$_SERVER["HTTP_HOST"]);
			elseif (isset($_SERVER["REMOTE_HOST"]))
				$CI->db->set("str_host",$_SERVER["REMOTE_HOST"]);
			if ($CI->agent->is_browser()) {
				$CI->db->set("str_browser",$CI->agent->browser());
				$CI->db->set("str_version",$CI->agent->version());
				$CI->db->set("str_platform",$CI->agent->platform());
				$CI->db->set("str_referrer",$CI->agent->referrer());
			}
			else {
				$CI->db->set("str_platform",$CI->agent->mobile());
			}
			$CI->db->insert($this->table);
		}
	}

	function get_top($nr=10) {
		$CI =& get_instance();
		$results=$CI->fd->get_results($this->table,$nr);
		return $results;
	}

}

?>
