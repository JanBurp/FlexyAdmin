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
	var $table;
	var $fields;

	function Content() {
		$this->option_safe_email();
	}

	function option_safe_email($safe=TRUE) {
		$this->safeEmail=$safe;
	}

/**
 *TODO: !content rendering (p,img classes etc)
 */
	function render($txt) {
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
