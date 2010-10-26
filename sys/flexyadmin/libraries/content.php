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
	var $addPopups;
	var $prePopup;
	var $replaceLanguageLinks;

	function Content() {
		$this->option_safe_email();
		$this->add_classes();
		$this->add_popups("popup_",FALSE);
	}

	function option_safe_email($safe=TRUE) {
		$this->safeEmail=$safe;
	}
	
	function add_classes($classes=TRUE) {
		$this->addClasses=$classes;
	}
	
	// example: $this->content->replace_language_links( array('search'=>'nl','replace'=>'en') );
	function replace_language_links($replace=FALSE) {
		$this->replaceLanguageLinks=$replace;
	}
	
	function add_popups($pre="popup_",$popups=TRUE) {
		$this->prePopup=$pre;
		$this->addPopups=$popups;
	}

	// callback for class replacing
	function _countCallBack($matches) {
		static $img_count=1;
		static $p_count=1;
		static $h_count=array(1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1);
		$class="";
		// is there a class allready?
		if (preg_match("/class=\"(.*?)\"/",$matches[3],$cMatch))
			$class=$cMatch[1]." ";
		if ($matches[1]=="p") {
			$class.="p$p_count";
			if ($p_count++%2) $class.=" odd"; else $class.=" even";
		}
		elseif ($matches[1]=="img") {
			$class.="img$img_count";
			if ($img_count++%2) $class.=" odd"; else $class.=" even";
		}
		else {
			$h=$matches[2];
			$class.="h".$h.$h_count[$h];
			if ($h_count[$h]++%2) $class.=" odd"; else $class.=" even";
		}
		$result="<".$matches[1]." class=\"$class\"".$matches[3].">";
		return $result;
	}

	// callback for popup adding replacing
	function _popupCallBack($matches) {
		$src=$matches[2];
		$info=get_path_and_file($src);
		$popup=$info['path']."/popup_".$info["file"];
		if (file_exists($popup)) {
			$result="<img".$matches[1]." longdesc=\"$popup\" src=\"".$src."\"".$matches[3]." />";
		}
		else
			$result="<img".$matches[1]." src=\"".$src."\"".$matches[3]." />";
		return $result;
	}

	function render($txt) {
		
		if ($this->addClasses) {
			$txt=preg_replace_callback("/<(img|p|h(\d))(.*?)>/",array($this,"_countCallBack"),$txt);
		}

		if ($this->replaceLanguageLinks) {
			$txt=preg_replace('/<a[\s]*href=\"'.$this->replaceLanguageLinks['search'].'\/(.*)\">(.*)<\/a>/','<a href="'.$this->replaceLanguageLinks['replace'].'/$1">$2</a>',$txt);
		}
		
		if ($this->addPopups) {
			$txt=preg_replace_callback("/<img(.*?)src=['|\"](.*?)['|\"](.*?)>/",array($this,"_popupCallBack"),$txt);
		}
		
		if ($this->safeEmail) {
			if (preg_match_all("/<a[\s]*href=\"mailto:(.*)\">(.*)<\/a>/",$txt,$matches)) { 	//<a[\s]*href="(.*)">(.*)</a>
				$search=array();
				$replace=array();
				foreach ($matches[1] as $key=>$adres) {
					$adres=explode("@",$adres);
					$show=str_replace('"',"'",$matches[2][$key]);
					$search[]=$matches[0][$key];
					if (!isset($adres[1])) $adres[1]='';
					$replace[]='<script language="JavaScript" type="text/javascript">nospam("'.str_reverse($adres[0]).'","'.str_reverse($adres[1]).'","'.str_reverse($show).'");</script>';
				}
				$txt=str_replace($search,$replace,$txt);
			}
		}
		
		return $txt;
	}
	
	
	
	
	
}

?>
