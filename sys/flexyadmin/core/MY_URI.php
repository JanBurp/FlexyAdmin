<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FlexyAdmin V1
 *
 * MY_URI.php Created on 9-dec-2008
 *
 * @author Jan den Besten
 */


/**
 * URI Class extension (for frontend)
 */

class MY_URI extends CI_URI {

	var $home;
	var $homePart;
	var $xdebug;
	var $remove;

	function __construct() {
		parent::__construct();
		$this->set_home();
		$this->set_remove();
		$this->xdebug="XDEBUG_SESSION_START";
	}

	function set_home($home="",$p=1) {
		$this->home=$home;
		$this->homePart=$p;
	}

	function set_remove($remove="") {
		if (!is_array($remove)) $remove=array($remove);
		$this->remove=$remove;
	}
	
	function remove_pagination() {
		$CI=&get_instance();
		if ( ! isset($CI->pagination)) return FALSE;
		$parameter=$CI->pagination->auto_uripart;
		$this->set_remove($parameter);
		return $parameter;
	}

	function _segment($s) {
		$s=$this->segment($s);
		if ($s==$this->xdebug) 
			return "";
		return $s;
	}

	function _uri_string() {
		$s=$this->uri_string();
		if ($s==$this->xdebug) $s="";
		if ($s=="") $s=$this->home;
		if (!empty($this->remove)) {
			foreach ($this->remove as $remove) {
				if (!empty($remove)) {
					$pos=strpos($s,$remove);
					if ($pos>0) $s=substr($s,0,$pos-1);
				}
			}
		}
		return $s;
	}

	function is($is,$s=1) {
		if ($this->_segment($s))
			return ($this->_segment($s)==$is);
		else
			return FALSE;
	}

	function is_home() {
		$isHome=$this->is($this->home,$this->homePart);
		if (!$isHome) $isHome=($this->total_segments()==0);
		return $isHome;
	}

	function has_more($n=1) {
		return $this->total_segments()>$n;
	}

	function get($s=0) {
		if ($s==0) {
			$u=$this->_uri_string();
		}
		else {
			$u=$this->_segment($s);
			if (empty($u) and $s==$this->homePart) $u=$this->home;
		}
		if (isset($u[0]) and $u[0]=="/") $u=substr($u,1);
		if (in_array($u,$this->remove)) $u='';
		return $u;
	}

	function get_to($s=0) {
		$u=explode('/',$this->_uri_string());
		$u=array_slice($u,0,$s+1);
		$u=implode('/',$u);
		$u=ltrim($u,'/');
		return $u;
	}

  function get_from_part($parameter,$include=false) {
		$uri=$this->segment_array();
    $segment=array_search($parameter,$uri);
    if (!$segment) {
      return false;
    }
    if ($include) $segment--;
    $u=array_slice($uri,$segment);
		return $u;
  }

	
	function get_last() {
		$u=explode('/',$this->_uri_string());
		return array_pop($u);
	}


	function get_parameter($parameter,$default=FALSE) {
		$uri=$this->segment_array();
		$segment=array_search($parameter,$uri);
		if ( ! $segment) $segment=1;
		return $this->segment($segment+1,$default);
	}
	
	function get_pagination() {
		$CI=&get_instance();
		if ( ! isset($CI->pagination)) return FALSE;
		$parameter=$CI->pagination->auto_uripart;
		return (int) $this->get_parameter($parameter,0);
	}
  

}

?>
