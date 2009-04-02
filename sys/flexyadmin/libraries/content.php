<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FlexyAdmin V1
 *
 * frontend_menu.php Created on 9-dec-2008
 *
 * @author Jan den Besten
 */


/**
 * Content Class
 *
 */

class Content {

	var $safeEmail;
	var $addClasses;

	function Content() {
		$this->option_safe_email();
		$this->add_classes();
	}

	function option_safe_email($safe=TRUE) {
		$this->safeEmail=$safe;
	}
	
	function add_classes($classes=TRUE) {
		$this->addClasses=$classes;
	}

	// callback for class replacing
	function _countCallBack($matches) {
		static $img_count=1;
		static $p_count=1;
		if ($matches[1]=="p") {
			$class="p$p_count";
			if ($p_count++%2) $class.=" odd"; else $class.=" even";
		}
		else {
			$class="img$img_count";
			if ($img_count++%2) $class.=" odd"; else $class.=" even";
		}
		return "<".$matches[1]." class=\"$class ".$matches[4]."\" ".$matches[2]." ".$matches[5].">";
	}

	function render($txt) {
		
		if ($this->addClasses) {
		 	// add classes (odd even nrs to p and img tags)
			$txt=preg_replace_callback("/<(img|p)(.*?)(class=\"(.*?)\")?(.*?)>/",array($this,"_countCallBack"),$txt);
		}
		
		if ($this->safeEmail) {
			if (preg_match_all("/<a[\s]*href=\"mailto:(.*)\">(.*)<\/a>/",$txt,$matches)) { 	//<a[\s]*href="(.*)">(.*)</a>
				$search=array();
				$replace=array();
				foreach ($matches[1] as $key=>$adres) {
					$adres=explode("@",$adres);
					$show=$matches[2][$key];
					$search[]=$matches[0][$key];
					$replace[]='<script language="JavaScript" type="text/javascript">nospam("'.str_reverse($adres[0]).'","'.str_reverse($adres[1]).'","'.str_reverse($show).'");</script>';
				}
				$txt=str_replace($search,$replace,$txt);
			}
		}
		
		return $txt;
	}
	
	
	
	
	
}

?>
