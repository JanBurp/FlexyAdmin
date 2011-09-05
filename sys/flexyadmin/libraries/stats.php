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
			// $CI =& get_instance();
			// $table=$CI->config->item('log_stats');
			$table='log_stats';
		}
		$this->table=$table;
	}

/**
 *
 */


	function add_current_uri() {
		global $URI;
		$thisUri=$URI->uri_string();
		$firstSegment=$URI->get(1);
		if ( ! in_array($firstSegment,array('site','sys','admin','rss','file')) ) {
			$this->add_uri(trim($thisUri,'/'));
		}
	}

	function add_uri($uri=NULL) {
		if ($uri==NULL) $uri="";
		// only insert page uri's (no images, css etc).
		if (strpos($uri,'.')===FALSE) {
			$AGENT=&load_class('User_agent', 'libraries');
			// only insert a known (mobile) browser
			if ($AGENT->is_browser() or $AGENT->is_mobile()) {
				$set=array();
				$set['tme_date_time']=date('Y-m-d H:i:s');
				$set['str_uri']=$uri;
				if ($AGENT->is_browser())
					$set['str_browser']=$AGENT->browser();
				else
					$set['str_browser']=$AGENT->mobile();
				// nicer version info
				$version=substr($AGENT->version(),0,3);
				if (strpos($version,'.')===false) $version=substr($version,0,1);
				$set['str_version']=$version;
				$set['str_referrer']=$AGENT->referrer();
				$set['str_platform']=$AGENT->platform();

				// standard PHP database connect
				include('site/config/database.php');
				$db=$db[$active_group];
				$con = mysql_connect($db['hostname'],$db['username'],$db['password']);
				if (!$con) { die('Could not connect to database for stats: ' . mysql_error()); }
				mysql_select_db($db['database'], $con);
				$sql="INSERT INTO `$this->table` (";
				$values='VALUES (';
				foreach ($set as $key => $value) {
					$sql.="`$key`,";
					$values.="'$value',";
				}
				$sql=substr($sql,0,strlen($sql)-1).') '.substr($values,0,strlen($values)-1).')';
				mysql_query($sql);
				mysql_close($con);
				// echo "<pre>";
				// echo $sql;
				// echo "\n";
				// print_r($db);
				// echo "</pre>";
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
