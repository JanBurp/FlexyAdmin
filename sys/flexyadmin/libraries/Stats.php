<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Stats Class, keeping track of page statistics
 *
 * @author Jan den Besten
 * @internal
 */

class Stats {

  private $excluded = array(
    'sys',
    'site',
    '_media',
    '_admin',
    '_rss',
    '_api/auth',
    '_api/table',
    '_api/table_order',
    '_api/media',
    '_api/row',
    '_api/admin_nav',
    '_api/image_list',
    '_api/link_list',
    '_api/plugin',
    '_api/user',
    '_api/link_checker',
  );

	private $table;

	public function __construct() {
		$this->set_table();
	}

	public function set_table($table="") {
		if (empty($table)) {
			$table='log_stats';
		}
		$this->table=$table;
	}

	public function add_current_uri() {
		global $URI;
		$thisUri = $URI->uri_string();
    $queryStr = el('QUERY_STRING',$_SERVER,'');
    if (!empty($queryStr)) $thisUri.='?'.$queryStr;
    if (!has_string($this->excluded,$thisUri)) {
      $removes = $URI->get_remove();
      if (!empty($removes)) {
  			foreach ($removes as $remove) {
  				if (!empty($remove)) {
  					$pos = strpos($thisUri,$remove);
            if (substr($thisUri,$pos-1,1)=='/') $pos-=1;
  					if ($pos>0) $thisUri=substr($thisUri,0,$pos);
  				}
  			}
      }
			$this->add_uri(trim($thisUri,'/'));
		}
	}

	public function add_uri($uri=NULL) {
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
				include(SITEPATH.'/config/database.php');
				$db=$db[$active_group];
        $mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);
        if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
				$sql="INSERT INTO `$this->table` (";
				$values='VALUES (';
				foreach ($set as $key => $value) {
					$sql.="`$key`,";
					$values.="'$value',";
				}
				$sql=substr($sql,0,strlen($sql)-1).') '.substr($values,0,strlen($values)-1).')';
        $mysqli->query($sql);
			}
		}
	}

	public function get_top($nr=10) {
		$CI =& get_instance();
		$results=$CI->fd->get_results($this->table,$nr);
		return $results;
	}

}

?>
