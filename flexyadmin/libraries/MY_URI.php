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

	function MY_URI() {
		parent::CI_URI();
		$this->set_home();
		$this->xdebug="XDEBUG_SESSION_START";
	}

	function set_home($home="",$p=1) {
		$this->home=$home;
		$this->homePart=$p;
	}

	function _segment($s) {
		$s=$this->segment($s);
		if ($s==$this->xdebug)
			return "";
		return $s;
	}

	function _uri_string() {
		$s=$this->uri_string();
		if ($s==$this->xdebug)
			return "";
		return $s;
	}

	function is($is,$s=1) {
		if ($this->_segment($s))
			return ($this->_segment($s)==$is);
		else
			return FALSE;
	}

	function is_home() {
		return $this->is($this->home,$this->homePart);
	}

	function has_more() {
		return $this->total_segments()>1;
	}

	function get($s=0) {
		if ($s==0) {
			return $this->_uri_string();
		}
		else {
			$u=$this->_segment($s);
			if (empty($u) and $s==$this->homePart) $u=$this->home;
			return ($u);
		}
	}

}

?>
