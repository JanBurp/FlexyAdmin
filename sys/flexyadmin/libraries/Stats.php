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
    '_api/assets_actions',
    '_api/auth',
    '_api/schoolbase_auth',
    '_api/get_admin_nav',
    '_api/get_help',
    '_api/get_image_list',
    '_api/get_link_list',
    '_api/get_plugin',
    '_api/link_checker',
    '_api/media',
    '_api/row',
    '_api/table',
    '_api/table_order',
    '_api/tools',
    '_api/link_checker',
    '_api/user',
  );

	private $table;

	public function __construct() {
		$this->set_table();
		$this->CI = & get_instance();
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
		$ext = strtolower(get_file_extension($uri));
		if (
			strpos($uri,'assets/')===FALSE
			and strpos($uri,'_media/')===FALSE
			and strpos($uri,'_media/')===FALSE
			and (!in_array($ext,array('ico','jpg','jpeg','gif','png','tiff','cur','tif','tiff')))
		) {
			$AGENT=&load_class('User_agent', 'libraries');

			// Remove authorization and passwords
			$uri = preg_replace('/_authorization=(.*)&/u', '', $uri);
			$uri = preg_replace('/_authorization=(.*)$/u', '', $uri);
		
			// only insert a known (mobile) browser
			if ($AGENT->is_browser() or $AGENT->is_mobile()) {

				// standard PHP database connect
				include(SITEPATH.'/config/database.php');
				$db=$db[$active_group];
        $mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);
        if ($mysqli->connect_errno) {
          echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
				
				$set=array();
				$set['tme_date_time']	=	date('Y-m-d H:i:s');
				$set['str_uri']				=	$uri;

				$result = $mysqli->query("SHOW COLUMNS FROM `".$this->table."` LIKE 'ip_address'");
				if ($result->num_rows>=1) $set['ip_address'] = $this->CI->input->ip_address();
				
				if ($AGENT->is_browser())
					$set['str_browser']	=	$AGENT->browser();
				else
					$set['str_browser']	=	$AGENT->mobile();

				// nicer version info
				$version=substr($AGENT->version(),0,3);
				if (strpos($version,'.')===false) $version=substr($version,0,1);
				$set['str_version']=$version;
				$set['str_referrer']=$AGENT->referrer();
				$set['str_platform']=$AGENT->platform();

				// Insert
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
		$results = $this->CI->fd->get_results($this->table,$nr);
		return $results;
	}

}

?>
